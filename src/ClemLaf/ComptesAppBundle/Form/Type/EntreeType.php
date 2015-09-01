<?php
namespace ClemLaf\ComptesAppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class EntreeType extends AbstractType
{
    private $addopts;

    public function __construct(array $ao){
	$this->addopts=$ao;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	$builder->add('cpS','choice',array(
	    'choices' => $this->addopts['cpchoices'],
	    'choices_as_values' => true,
	    'placeholder' => 'Choisir un compte',
	    'mapped'=> false,
	    'empty_data' => null,
	    'multiple' => true,
	))
	->add('cpD','choice',array(
	    'choices' => $this->addopts['cpchoices'],
	    'choices_as_values' => true,
	    'placeholder' => '---------',
	    'empty_data' => null,
	    'required' => false,
	    'mapped'=> false,
	    'multiple' => true
	))
	->add('deb','integer',array('mapped' => false,'required' => false))
	->add('nb','integer',array('mapped' => false,'required' => false))
	->add('date1', 'date', array('widget' => 'single_text', 'mapped' => false,'required' => false))
	->add('date2','date',array('widget' => 'single_text', 'mapped' => false,'required' => false))
	->add('category','choice', array('choices' => $this->addopts['catchoices'],
	    'choices_as_values' => true,
	    'placeholder' => '-------',
	    'empty_data' => null,
	    'required' => false,
	    //'multiple' => true
	))
	->add('moyen','choice', array('choices' => $this->addopts['moychoices'],
	    'choices_as_values' => true,
	    'placeholder' => '---',
	    'empty_data' => null,
	    'required' => false
	))
	->add('com','text',array('required'=> false))
	->add('type','choice', array('choices' => array('table'=> 'Tableau',
	    'solde' => 'Évolution solde',
	    'pie' => 'Camembert des dépenses',
	    'rev_dep'=> 'Revenus et Dépenses mensuels'),
	'placeholder' => false,
	'required' => false,
	'mapped'=> false,
	'expanded' => true,
	'multiple' => false,

    ))
    ->add('save', 'submit')
    ->add('reset','reset');
    }

    public function getName()
    {
	return 'entree';
    }
}
?>
