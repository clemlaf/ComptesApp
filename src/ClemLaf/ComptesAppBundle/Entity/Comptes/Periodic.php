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
    private $prix;

    /**
     * @var \ClemLaf\ComptesAppBundle\Entity\Comptes\Compte
     */
    private $cpD;

    /**
     * @var \ClemLaf\ComptesAppBundle\Entity\Comptes\Compte
     */
    private $cpS;

    /**
     * @var \ClemLaf\ComptesAppBundle\Entity\Comptes\Category
     */
    private $category;

    /**
     * @var \ClemLaf\ComptesAppBundle\Entity\Comptes\Moyen
     */
    private $moyen;


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
     * Set cpD
     *
     * @param \ClemLaf\ComptesAppBundle\Entity\Comptes\Compte $cpD
     * @return Periodic
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
     * @return Periodic
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

    /**
     * Set moyen
     *
     * @param \ClemLaf\ComptesAppBundle\Entity\Comptes\Moyen $moyen
     * @return Periodic
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
}
