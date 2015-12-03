<?php
namespace App\FrontBundle\Handler;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use App\FrontBundle\Entity\User;
use App\FrontBundle\Entity\Loginfailure;
use App\FrontBundle\Entity\Userlock;

class AuthenticationFailureHandler extends DefaultAuthenticationFailureHandler {

    private $container;

    const MAX_FAILURES = 4;

    public function __construct(HttpKernelInterface $httpKernel, HttpUtils $httpUtils, array $options, $container) {
        parent::__construct($httpKernel, $httpUtils, $options);
        $this->container = $container;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        $em = $this->container->get('doctrine')->getManager();

        // Decides the user provider according to domain host
        if($request->server->get('HTTP_HOST') == $this->container->getParameter('domain_admin')){
            $userRepo = $em->getRepository('AppFrontBundle:Professional');
        } else {
            $userRepo = $em->getRepository('AppFrontBundle:Consumer');
        }

        // Check the user instance with the username
        $user = $userRepo->findUserByUsername($request->get('_username'));
        $clientIp = $request->server->get('REMOTE_ADDR');
        if($user instanceof User){
            // check is the user locked or not ?
            if(!$user->isAccountNonLocked()){
                if($this->container->get('security.password_encoder')->isPasswordValid($user, $request->get('_password'))){
                    $error_msg = $this->container->get('translator')->trans('login.messages.locked');
                } else {
                    $error_msg = $this->container->get('translator')->trans('login.messages.invalid');
                }
            } else {
                // creates new failure entry
                $failure = new Loginfailure();
                $failure->setUser($user);
                $failure->setIp($clientIp);
                $failure->setFailedOn(new \Datetime('now'));
                $em->persist($failure);

                // fetches last 30 minutes failure count and set the userlock
                $failureRepo = $em->getRepository('AppFrontBundle:Loginfailure');
                $failures = $failureRepo->getRecentFailures($user, $clientIp);
                if(count($failures) >= self::MAX_FAILURES){
                    $userlock = new Userlock();
                    $userlock->setIp($clientIp);
                    $userlock->setUser($user);
                    $userlock->setLockedOn(new \Datetime('now'));
                    $em->persist($userlock);
                    $error_msg = $this->container->get('translator')->trans('login.messages.max_tries');
                } else {
                    $error_msg = $this->container->get('translator')->trans('login.messages.invalid');
                }
                
                $em->flush();
            }
        } else {
            $error_msg = $this->container->get('translator')->trans('login.messages.invalid');
        }

        $request->getSession()->getFlashBag()->add('login_error', $error_msg);
        return parent::onAuthenticationFailure($request, $exception);
    }
}