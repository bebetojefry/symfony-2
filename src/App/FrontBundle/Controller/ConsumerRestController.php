<?php

namespace App\FrontBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ConsumerRestController extends FOSRestController
{
    public function getConsumersAction(){
        $consumers = $this->getDoctrine()->getRepository('AppFrontBundle:Consumer')->findAll();
        $view = $this->view($consumers, 200)
            ->setTemplate("AppFrontBundle:Rest:getConsumers.html.twig")
            ->setTemplateVar('consumers');

        return $this->handleView($view);
    }
    
    public function getConsumerAction($username){
        $consumer = $this->getDoctrine()->getRepository('AppFrontBundle:Consumer')->findOneByUsername($username);
        if(!is_object($consumer)){
            throw $this->createNotFoundException();
        }
        return $consumer;
    }

    /**
     * 
     * @View(serializerGroups={"Default","Me","Details"})
     */
    public function getMeAction(){
        $this->forwardIfNotAuthenticated();
        return $this->getUser();
    }

    /**
     * Shortcut to throw a AccessDeniedException($message) if the user is not authenticated
     * @param String $message The message to display (default:'warn.user.notAuthenticated')
     */
    protected function forwardIfNotAuthenticated($message='warn.user.notAuthenticated'){
        if (!is_object($this->getUser()))
        {
            throw new AccessDeniedException($message);
        }
    }
}
