<?php

namespace App\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\FrontBundle\Entity\User;
Use App\FrontBundle\Entity\Product;
use App\FrontBundle\Form\ProductType;
use App\FrontBundle\Document\User as MongoUser;

class DefaultController extends Controller {

    public function viewAction($name) {
        //$post = new Post();
        //$post->setPrivate(true);
        //$this->denyAccessUnlessGranted('view', $post, 'You dont have access to this post.');

        /* alternative
          if (!$this->get('security.authorization_checker')->isGranted('view', $post)) {
          throw $this->createAccessDeniedException();
          } */

        return $this->render('AppFrontBundle:Default:post.html.twig', array('name' => $name));
    }

    public function editAction($name) {

        /* $post = new Post();
          $post->setPrivate(true);
          $postUser = new User();
          $postUser->setId(2);
          $post->setOwner($postUser);

          $this->denyAccessUnlessGranted('edit', $post, 'You dont have access to this post.'); */

        return $this->render('AppFrontBundle:Default:post.html.twig', array('name' => $name));
    }

    public function indexAction($id = null) {
        /*if ($id) {
            $id = $this->get('nzo_url_encryptor')->decrypt($id);
            $repo = $this->getDoctrine()->getManager()->getRepository('AppFrontBundle:Product');
            $product = $repo->find($id);

            // share object with other user ACL
            /* $aclProvider = $this->get('security.acl.provider');
              $objectIdentity = ObjectIdentity::fromDomainObject($product);
              $acl = $aclProvider->findAcl($objectIdentity);

              if($acl){
              // retrieving the security identity of another user
              $userRepo = $this->getDoctrine()->getManager()->getRepository('AppFrontBundle:Professional');
              $user = $userRepo->loadUserByUsername('bebeto');

              if($user){
              $securityIdentity = UserSecurityIdentity::fromAccount($user);

              // grant owner access
              $builder = new MaskBuilder();
              $builder
              ->add('view')
              ->add('edit');
              $mask = $builder->get();
              $acl->insertObjectAce($securityIdentity, $mask);
              $aclProvider->updateAcl($acl);
              }
              } */

           /* if (!$product instanceof Product) {
                $this->get('session')->getFlashBag()->add('error', 'product.msg.notfound');
                return $this->redirect($this->generateUrl('app_front_homepage'));
            }

            // ACL access validation
            $authorizationChecker = $this->get('security.authorization_checker');
            if (false === $authorizationChecker->isGranted('EDIT', $product)) {
              throw new AccessDeniedException();
            }

            // VOTER access validation
            //$this->denyAccessUnlessGranted('edit', $product, Product::NO_ACCESS);
        } else {
            $product = new Product();
        }

        $form = $this->createForm(new ProductType(), $product);
        return $this->render('AppFrontBundle:Default:index.html.twig', array('form' => $form->createView(), 'id' => $id));*/
        
        //mongoDB
//        $dm = $this->get('doctrine_mongodb')->getManager();
//        $user = new MongoUser();
//        $user->setFirstname('Bebeto');
//        $user->setLastname('Jefry');
//        $dm->persist($user);
//        $dm->flush();
        
        return $this->render('AppFrontBundle:Default:index.html.twig');
        
    }

    public function consumerAction() {
        return $this->render('AppFrontBundle:Default:consumer.html.twig');
    }

    public function setlangAction($route, Request $request) {
        return $this->redirect($this->get('nzo_url_encryptor')->decrypt($route));
    }

    public function consumersAction() {
        $repo = $this->getDoctrine()->getManager()->getRepository('AppFrontBundle:Consumer');
        return $this->render('AppFrontBundle:Default:consumers.html.twig', array('consumers' => $repo->findAll()));
    }

    public function saveAction(Request $request, $id = null) {
        $product_image = '';
        if ($id) {
            $id = $this->get('nzo_url_encryptor')->decrypt($id);
            $repo = $this->getDoctrine()->getManager()->getRepository('AppFrontBundle:Product');
            $product = $repo->find($id);

            // check product existance
            if (!$product instanceof Product) {
                $this->get('session')->getFlashBag()->add('error', 'product.msg.notfound');
                return $this->redirect($this->generateUrl('app_front_homepage'));
            }

            // ACL access validation
            /* $authorizationChecker = $this->get('security.authorization_checker');
              if (false === $authorizationChecker->isGranted('EDIT', $product)) {
              throw new AccessDeniedException();
              } */

            // VOTER access validation
            $this->denyAccessUnlessGranted('edit', $product, Product::NO_ACCESS);

            $product_image = $product->getImage();
        } else {
            $product = new Product();
        }

        $form = $this->createForm(new ProductType(), $product);
        $form->bind($request);
        if ($form->isValid()) {
            $product = $form->getData();
            $product->setUser($this->getUser());
            $file = $product->getImage();
            if ($file) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move($this->getParameter('products_upload_dir'), $fileName);
                $product->setImage($fileName);
                @unlink($this->getParameter('products_upload_dir') . $product_image);
            } else {
                $product->setImage($product_image);
            }
            $dm = $this->getDoctrine()->getManager();
            $dm->persist($product);
            $dm->flush();
            if ($id) {
                $this->get('session')->getFlashBag()->add('notice', 'product.msg.updated');
            } else {
                $this->get('session')->getFlashBag()->add('notice', 'product.msg.created');
            }
        } else {
            $this->get('session')->getFlashBag()->add('error', $form->getErrorsAsString());
            return $this->redirect($this->generateUrl('app_front_homepage_edit', array('id' => $this->get('nzo_url_encryptor')->encrypt($id))));
        }
        return $this->redirect($this->generateUrl('app_front_homepage'));
    }

    public function removeAction($id) {
        $id = $this->get('nzo_url_encryptor')->decrypt($id);
        $dm = $this->getDoctrine()->getManager();
        $repo = $dm->getRepository('AppFrontBundle:Product');
        $product = $repo->find($id);
        if (!$product instanceof Product) {
            $this->get('session')->getFlashBag()->add('error', 'product.msg.notfound');
            return $this->redirect($this->generateUrl('app_front_homepage'));
        }
        $this->denyAccessUnlessGranted('delete', $product, Product::NO_ACCESS);
        $dm->remove($product);
        $dm->flush();
        $this->get('session')->getFlashBag()->add('notice', 'product.msg.removed');
        return $this->redirect($this->generateUrl('app_front_homepage'));
    }

    public function dqlAction(Request $request) {
        $result = array();
        if ($request->isMethod('POST')) {
            try {
                $em = $this->getDoctrine()->getManager();
                $result = $em->createQuery($request->get('dql'))->getResult();
            } catch (\Exception $e) {
                $result = $e->getMessage();
            }
        }
        return $this->render('AppFrontBundle:Default:dql.html.twig', array('result' => $result));
    }

}
