<?php

namespace ClemLaf\ComptesAppBundle\Entity\Comptes;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entree
 */
class Entree
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var string
     */
    private $com;

    /**
     * @var integer
     */
    private $pr;

    /**
     * @var boolean
     */
    private $poS;

    /**
     * @var boolean
     */
    private $poD;

    /**
     * @var \ClemLaf\ComptesAppBundle\Entity\Comptes\Category
     */
    private $category;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Entree
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set com
     *
     * @param string $com
     * @return Entree
     */
    public function setCom($com)
    {
        $this->com = $com;

        return $this;
    }

    /**
     * Get com
     *
     * @return string 
     */
    public function getCom()
    {
        return $this->com;
    }

    /**
     * Set pr
     *
     * @param integer $pr
     * @return Entree
     */
    public function setPr($pr)
    {
        $this->pr = $pr;

        return $this;
    }

    /**
     * Get pr
     *
     * @return integer 
     */
    public function getPr()
    {
        return $this->pr;
    }

    /**
     * Set poS
     *
     * @param boolean $poS
     * @return Entree
     */
    public function setPoS($poS)
    {
        $this->poS = $poS;

        return $this;
    }

    /**
     * Get poS
     *
     * @return boolean 
     */
    public function getPoS()
    {
        return $this->poS;
    }

    /**
     * Set poD
     *
     * @param boolean $poD
     * @return Entree
     */
    public function setPoD($poD)
    {
        $this->poD = $poD;

        return $this;
    }

    /**
     * Get poD
     *
     * @return boolean 
     */
    public function getPoD()
    {
        return $this->poD;
    }

    /**
     * Set category
     *
     * @param \ClemLaf\ComptesAppBundle\Entity\Comptes\Category $category
     * @return Entree
     */
    public function setCategory(\ClemLaf\ComptesAppBundle\Entity\Comptes\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \ClemLaf\ComptesAppBundle\Entity\Comptes\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }
    /**
     * @var \ClemLaf\ComptesAppBundle\Entity\Comptes\Moyen
     */
    private $moyen;


    /**
     * Set moyen
     *
     * @param \ClemLaf\ComptesAppBundle\Entity\Comptes\Moyen $moyen
     * @return Entree
     */
    public function setMoyen(\ClemLaf\ComptesAppBundle\Entity\Comptes\Moyen $moyen = null)
    {
        $this->moyen = $moyen;

        return $this;
    }

    /**
     * Get moyen
     *
     * @return \ClemLaf\ComptesAppBundle\Entity\Comptes\Moyen 
     */
    public function getMoyen()
    {
        return $this->moyen;
    }
    
    /**
     * Reverse 
     */
    public function reverse($cp_tab)
    {
	if($cp_tab!=null && $this->cpD!=null){
	    foreach($cp_tab as $cpid){
		if($this->cpS->getId()==$cpid->getId())
		    break;
		if($this->cpD->getId()==$cpid->getId() &&
		    $this->cpS->getId()!=$cpid->getId()){
		    $this->cpD=$this->cpS;
		    $this->cpS=$cpid;
		    $potr=$this->poD;
		    $this->poD=$this->poS;
		    $this->poS=$potr;
		    $this->pr=-1*$this->pr;
		    break;
		}
	    }
	}
    }
    /**
     * @var \ClemLaf\ComptesAppBundle\Entity\Comptes\Compte
     */
    private $cpD;

    /**
     * @var \ClemLaf\ComptesAppBundle\Entity\Comptes\Compte
     */
    private $cpS;


    /**
     * Set cpD
     *
     * @param \ClemLaf\ComptesAppBundle\Entity\Comptes\Compte $cpD
     * @return Entree
     */
    public function setCpD(\ClemLaf\ComptesAppBundle\Entity\Comptes\Compte $cpD = null)
    {
        $this->cpD = $cpD;

        return $this;
    }

    /**
     * Get cpD
     *
     * @return \ClemLaf\ComptesAppBundle\Entity\Comptes\Compte 
     */
    public function getCpD()
    {
        return $this->cpD;
    }

    /**
     * Set cpS
     *
     * @param \ClemLaf\ComptesAppBundle\Entity\Comptes\Compte $cpS
     * @return Entree
     */
    public function setCpS(\ClemLaf\ComptesAppBundle\Entity\Comptes\Compte $cpS = null)
    {
        $this->cpS = $cpS;

        return $this;
    }

    /**
     * Get cpS
     *
     * @return \ClemLaf\ComptesAppBundle\Entity\Comptes\Compte 
     */
    public function getCpS()
    {
        return $this->cpS;
    }
    /**
     * to Array
     * */
    public function toArray(){
        return array(
            'id'=>$this->id,
            'date'=>($this->date?$this->date->format('d/m/Y'):null),
            'cpS'=> ($this->cpS ?$this->cpS->getId():null),
            'cpD'=> ($this->cpD ?$this->cpD->getId():null),
            'category'=> ($this->category?$this->category->getId():null),
            'moyen'=> ($this->moyen?$this->moyen->getId():null),
            'com'=> $this->com,
            'pr'=> $this->pr,
            'poS'=> $this->poS
        );
    }
}
