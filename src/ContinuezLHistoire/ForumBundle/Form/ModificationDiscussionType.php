<?php

namespace ContinuezLHistoire\ForumBundle\Form;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ModificationDiscussionType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('sujet', 'text');
        ; 
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
          'data_class' => 'ContinuezLHistoire\ForumBundle\Entity\Discussion'
        ));
    }
    
    public function getName() {
        return 'continuezlhistoire_forumbundle_modificationdiscussiontype';
    } 
}

?>
