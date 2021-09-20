<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ContinuezLHistoire\SiteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Description of ChoixUtilisateurType
 *
 * @author GA
 */
class ChoixUtilisateurType extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder
        ->add('username','entity',array(
              'class' => 'ContinuezLHistoireUserBundle:User',
              'property' => 'username',
              'multiple' => false,
              'expanded' => false   
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
        return 'continuezlhistoire_userbundle_choixutilisateurtype';
    }    
}
