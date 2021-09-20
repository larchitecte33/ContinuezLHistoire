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
 * Description of ChoixCommentaireType
 *
 * @author GA
 */
class ChoixCommentaireType extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder
        ->add('libelleAvis','entity',array(
              'class' => 'ContinuezLHistoireSiteBundle:AvisUtilisateur',
              'property' => 'libelleAvis',
              'multiple' => false,
              'expanded' => false,
              'label' => false
        ))
      ; 
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
      $resolver->setDefaults(array(
        'data_class' => 'ContinuezLHistoire\SiteBundle\Entity\AvisUtilisateur'
      ));
    }
    
    public function getName() {
        return 'continuezlhistoire_sitebundle_choixcommentairetype';
    }    
}
