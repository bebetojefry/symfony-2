<?php

namespace App\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\FrontBundle\Helper\FormHelper;
Use App\FrontBundle\Entity\Product;
Use App\FrontBundle\Form\ProductType;

class ProductController extends Controller {
    
    public function newAction(Request $request){
        $form = $this->createForm(new ProductType(), new Product());
        $code = FormHelper::FORM;
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                $product = $form->getData();
                $product->setUser($this->getUser());
                if($file = $product->getImage()) {
                    $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                    $file->move($this->getParameter('products_upload_dir'), $fileName);
                    $product->setImage($fileName);
                }
                $dm = $this->getDoctrine()->getManager();
                $dm->persist($product);
                $dm->flush();
                $this->get('session')->getFlashBag()->add('success', 'product.msg.created');
                $code = FormHelper::REFRESH;
            } else {
                $code = FormHelper::REFRESH_FORM;
            }
        }
        
        $body = $this->renderView('AppFrontBundle:Product:form.html.twig',
            array('form' => $form->createView())
        );
        
        return new Response(json_encode(array('code' => $code, 'data' => $body)));
    }
    
    public function editAction($id, Request $request){
        // decrypt id and fetch product object
        $id = $this->get('nzo_url_encryptor')->decrypt($id);
        $repo = $this->getDoctrine()->getManager()->getRepository('AppFrontBundle:Product');
        $product = $repo->find($id);
        
        // ACL access validation
        $authorizationChecker = $this->get('security.authorization_checker');
        if (!$authorizationChecker->isGranted('EDIT', $product)) {
          throw new AccessDeniedException();
        }
        
        $productImg = $product->getImage();
        $product_old_image = $this->getParameter('products_upload_dir') . $product->getImage();
        $form = $this->createForm(new ProductType(), $product);
        $code = FormHelper::FORM;
        
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                $product = $form->getData();
                $product->setUser($this->getUser());
                if($file = $product->getImage()) {
                    $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                    $file->move($this->getParameter('products_upload_dir'), $fileName);
                    $product->setImage($fileName);
                    // remove old product image
                    if(file_exists($product_old_image)){
                        @unlink($product_old_image);
                    }
                } else {
                    $product->setImage($productImg);
                }
                $dm = $this->getDoctrine()->getManager();
                $dm->persist($product);
                $dm->flush();
                $this->get('session')->getFlashBag()->add('success', 'product.msg.updated');
                $code = FormHelper::REFRESH;
            } else {
                $code = FormHelper::REFRESH_FORM;
            }
        }
        
        $body = $this->renderView('AppFrontBundle:Product:form.html.twig',
            array('form' => $form->createView())
        );
        
        return new Response(json_encode(array('code' => $code, 'data' => $body)));
    }
    
    public function deleteAction($id, Request $request){
        $id = $this->get('nzo_url_encryptor')->decrypt($id);
        $dm = $this->getDoctrine()->getManager();
        $repo = $dm->getRepository('AppFrontBundle:Product');
        $product = $repo->find($id);
        $product_image = $this->getParameter('products_upload_dir') . $product->getImage();
        $dm->remove($product);
        $dm->flush();
        // remove product image
        if(file_exists($product_image)){
            @unlink($product_image);
        }
        $this->get('session')->getFlashBag()->add('success', 'product.msg.removed');
        return new Response(json_encode(array('code' => FormHelper::REFRESH)));
    }
}