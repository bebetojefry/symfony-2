<?php

namespace App\FrontBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('label' => 'product.name', 'attr' => array('data-minlength' => 5, 'maxlength' => 15)))
            ->add('description', 'textarea', array('label' => 'product.desc', 'attr' => array('data-minlength' => 15, 'maxlength' => 50)))
            ->add('price', 'money', array('label' => 'product.price', 'attr' => array('pattern' => '[0-9]+((\.|\,)[0-9][0-9]?)?', 'data-error' => 'Please enter valid price')))
            ->add('quantity', 'number', array('label' => 'product.qty', 'attr' => array('pattern' => '^[_0-9]{1,}$', 'data-error' => 'Please enter valid quantity')))
            ->add('image', 'file', array('label' => 'product.image', 'data_class' => null, 'required' => false))
            ->add('private', 'checkbox', array('label' => 'product.private', 'required' => false))
            ->add('status', 'checkbox', array('label' => 'product.status', 'required' => false))
            ->add('Submit', 'submit', array('label' => 'product.submit'));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\FrontBundle\Entity\Product'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'product';
    }
}
