<?php

namespace App\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    public function loginAction(Request $request)
    {
        if($this->getUser()){
            $token = $this->get('security.token_storage')->getToken();
            if($token->getProviderKey() == 'admin'){
                return $this->redirect($this->generateUrl('app_front_admin_products'));
            } else {
                return $this->redirect($this->generateUrl('app_front_homepage'));
            }
        }
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
		
        return $this->render('AppFrontBundle:User:login.html.twig', array(
            // last username entered by the user
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

	public function logincheckAction(){
		
	}

	public function registerAction(){
//		$user = $this->get('app.front.entity.consumer');
//		$user->regenerateSalt();
//		$user->setPassword('bebeto12345');
//		$user->setFirstname('Jasper');
//		$user->setLastname('Prince');
//		echo $user->getSalt().'<br/>';
//		echo $user->getPassword();
		exit;
	}
}
