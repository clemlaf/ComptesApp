<?php

namespace ClemLaf\ComptesAppBundle\Entity\Comptes;

use Doctrine\ORM\Mapping as ORM;

/**
 * Periodic
 */
class Periodic
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $lastDate;

    /**
     * @var \DateTime
     */
    private $endDate;

    /**
     * @var integer
     */
    private $mois;

    /**
     * @var integer
     */
    private $jours;

    /**
     * @var string
     */
    private $com;

    /**
     * @var integer
     */
    private $cpS;

    /**
     * @var integer
     */
    private $cpD;

    /**
     * @var integer
     */
    private $moy;

    /**
     * @var integer
     */
    private $prix;

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
     * Set lastDate
     *
     * @param \DateTime $lastDate
     * @return Periodic
     */
    public function setLastDate($lastDate)
    {
        $this->lastDate = $lastDate;

        return $this;
    }

    /**
     * Get lastDate
     *
     * @return \DateTime 
     */
    public function getLastDate()
    {
        return $this->lastDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return Periodic
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set mois
     *
     * @param integer $mois
     * @return Periodic
     */
    public function setMois($mois)
    {
        $this->mois = $mois;

        return $this;
    }

    /**
     * Get mois
     *
     * @return integer 
     */
    public function getMois()
    {
        return $this->mois;
    }

    /**
     * Set jours
     *
     * @param integer $jours
     * @return Periodic
     */
    public function setJours($jours)
    {
        $this->jours = $jours;

        return $this;
    }

    /**
     * Get jours
     *
     * @return integer 
     */
    public function getJours()
    {
        return $this->jours;
    }

    /**
     * Set com
     *
     * @param string $com
     * @return Periodic
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
     * Set cpS
     *
     * @param integer $cpS
     * @return Periodic
     */
    public function setCpS($cpS)
    {
        $this->cpS = $cpS;

        return $this;
    }

    /**
     * Get cpS
     *
     * @return integer 
     */
    public function getCpS()
    {
        return $this->cpS;
    }

    /**
     * Set cpD
     *
     * @param integer $cpD
     * @return Periodic
     */
    public function setCpD($cpD)
    {
        $this->cpD = $cpD;

        return $this;
    }

    /**
     * Get cpD
     *
     * @return integer 
     */
    public function getCpD()
    {
        return $this->cpD;
    }

    /**
     * Set moy
     *
     * @param integer $moy
     * @return Periodic
     */
    public function setMoy($moy)
    {
        $this->moy = $moy;

        return $this;
    }

    /**
     * Get moy
     *
     * @return integer 
     */
    public function getMoy()
    {
        return $this->moy;
    }

    /**
     * Set prix
     *
     * @param integer $prix
     * @return Periodic
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return integer 
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set category
     *
     * @param \ClemLaf\ComptesAppBundle\Entity\Comptes\Category $category
     * @return Periodic
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
}
