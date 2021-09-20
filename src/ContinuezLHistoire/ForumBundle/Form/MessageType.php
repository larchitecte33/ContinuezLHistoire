<?php

namespace ContinuezLHistoire\ForumBundle\Form;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MessageType extends AbstractType 
{    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('corps', 'textarea', array('label' => 'Message', 'attr' => array('class' => 'tinymce')))
        ; 
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
          'data_class' => 'ContinuezLHistoire\ForumBundle\Entity\Message'
        ));
    }
    
    public function getName() {
        return 'continuezlhistoire_forumbundle_messagetype';
    }    
}
