<?php

namespace App\FrontBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProductRestController extends FOSRestController
{
    public function getProductsAction($username){
        $consumers = $this->getDoctrine()->getRepository('AppFrontBundle:Professional')->findOneByUsername($username);
        $view = $this->view($consumers->getProducts(), 200)
            ->setTemplate("AppFrontBundle:Rest:getProducts.html.twig")
            ->setTemplateVar('products');

        return $this->handleView($view);
    }
}
