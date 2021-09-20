<?php

namespace ContinuezLHistoire\SiteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use ContinuezLHistoire\UserBundle\Entity\User;
use ContinuezLHistoire\SiteBundle\Controller\SiteController;

class DroitsType extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = new User();
          
        
        $builder
          ->add('roles','choice',array('choices' => $user->getRolesNames(),
            'required'  => true,
            'multiple'  => true,
            'expanded'  => true
          ))
        ; 
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
      $resolver->setDefaults(array(
        'data_class' => 'ContinuezLHistoire\UserBundle\Entity\User'
      ));
    }
    
    public function getName() {
        return 'continuezlhistoire_userbundle_droitstype';
    }
}
