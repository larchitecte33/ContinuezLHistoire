<?php
// src/Sdz/BlogBundle/Form/ArticleType.php
 
namespace ContinuezLHistoire\SiteBundle\Form;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class HistoireType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('titre', 'text')
      ->add('image', new ImageType(), array('required' => false, 'label' => false))
    ; 
  }
 
  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'ContinuezLHistoire\SiteBundle\Entity\Histoire'
    ));
  }
 
  public function getName()
  {
    return 'continuezlhistoire_sitebundle_histoiretype';
  }
}