<?php
namespace ClemLaf\ComptesAppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PeriodicType extends AbstractType
{
  private $addopts;

  public function __construct(array $ao){
    $this->addopts=$ao;
  }

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->add('lastDate', 'date', array('widget' => 'single_text'))
      ->add('endDate','date',array('widget' => 'single_text','required' => false))
      ->add('mois','integer',array('empty_data'=>null))
      ->add('jours','integer',array('empty_data'=>null))
      ->add('cpS','choice',array(
				       'choices' => $this->addopts['cpchoices'],
	    'choices_as_values' => true,
				       'placeholder' => 'Choisir un compte',
				       'empty_data' => null,
				       ))
      ->add('cpD','choice',array(
				 'choices' => $this->addopts['cpchoices'],
	    'choices_as_values' => true,
				 'placeholder' => '---------',
				 'empty_data' => null,
				 ))
      ->add('category','choice', array('choices' => $this->addopts['catchoices'],
	    'choices_as_values' => true,
				  'placeholder' => '-------',
				  'empty_data' => null,
				  ))
      ->add('moyen','choice', array('choices' => $this->addopts['moychoices'],
	    'choices_as_values' => true,
				  'placeholder' => '---',
				  'empty_data' => null,
				  ))
      ->add('com','text',array('empty_data'=>null))
      ->add('prix','integer',array('empty_data'=>null))
      ->add('save', 'submit', array('attr'=>array('style'=>'display:block;float:left')))
      ->add('reset','reset',array('attr'=>array('style'=>'display:block;float:left')));
  }
  
  public function getName()
  {
    return 'periodic';
  }
}
?>
