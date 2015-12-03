<?php
namespace App\FrontBundle\Handler;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler {

    private $container;
    public function __construct(HttpUtils $httpUtils, array $options, $container) {
        parent::__construct($httpUtils, $options);
        $this->container = $container;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token) {
        $em = $this->container->get('doctrine')->getManager();
        $failureRepo = $em->getRepository('AppFrontBundle:Loginfailure');
        $failureRepo->removeFailures($token->getUser(), $request->server->get('REMOTE_ADDR'));
        if($request->isXmlHttpRequest()) {
            $response = new JsonResponse(array('success' => true, 'username' => $token->getUsername()));
        } else {
            $referer = $request->getSession()->get('_security.'.$token->getProviderKey().'.target_path');
            if($referer){
                $response = new RedirectResponse($referer);
            } else {
                $response = parent::onAuthenticationSuccess($request, $token);
            }
        }
        
        return $response;
    }
}