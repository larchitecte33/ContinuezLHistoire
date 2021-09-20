<?php

namespace ContinuezLHistoire\SiteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MessageContactType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder
        ->add('sujet', 'text', array('label' => 'Sujet : '))
        ->add('corps', 'textarea', array('label' => 'Message : ', 'attr' => array('class' => 'tinymce')))
      ; 
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
      $resolver->setDefaults(array(
        'data_class' => 'ContinuezLHistoire\SiteBundle\Entity\MessageContact'
      ));
    }
    
    public function getName() {
        return 'continuezlhistoire_sitebundle_messagecontact';
    }
}

?>
