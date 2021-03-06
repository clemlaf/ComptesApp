<?php

namespace ClemLaf\ComptesAppBundle\Entity\Comptes;

use Doctrine\ORM\Mapping as ORM;

/**
 * Compte
 */
class Compte
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $cpNam;


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
     * Set cpNam
     *
     * @param string $cpNam
     * @return Compte
     */
    public function setCpNam($cpNam)
    {
        $this->cpNam = $cpNam;

        return $this;
    }

    /**
     * Get cpNam
     *
     * @return string 
     */
    public function getCpNam()
    {
        return $this->cpNam;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $entries;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->entries = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add entries
     *
     * @param \ClemLaf\ComptesAppBundle\Entity\Comptes\Entree $entries
     * @return Compte
     */
    public function addEntry(\ClemLaf\ComptesAppBundle\Entity\Comptes\Entree $entries)
    {
        $this->entries[] = $entries;

        return $this;
    }

    /**
     * Remove entries
     *
     * @param \ClemLaf\ComptesAppBundle\Entity\Comptes\Entree $entries
     */
    public function removeEntry(\ClemLaf\ComptesAppBundle\Entity\Comptes\Entree $entries)
    {
        $this->entries->removeElement($entries);
    }

    /**
     * Get entries
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEntries()
    {
        return $this->entries;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $entriesD;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $entriesS;


    /**
     * Add entriesD
     *
     * @param \ClemLaf\ComptesAppBundle\Entity\Comptes\Entree $entriesD
     * @return Compte
     */
    public function addEntriesD(\ClemLaf\ComptesAppBundle\Entity\Comptes\Entree $entriesD)
    {
        $this->entriesD[] = $entriesD;

        return $this;
    }

    /**
     * Remove entriesD
     *
     * @param \ClemLaf\ComptesAppBundle\Entity\Comptes\Entree $entriesD
     */
    public function removeEntriesD(\ClemLaf\ComptesAppBundle\Entity\Comptes\Entree $entriesD)
    {
        $this->entriesD->removeElement($entriesD);
    }

    /**
     * Get entriesD
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEntriesD()
    {
        return $this->entriesD;
    }

    /**
     * Add entriesS
     *
     * @param \ClemLaf\ComptesAppBundle\Entity\Comptes\Entree $entriesS
     * @return Compte
     */
    public function addEntriesS(\ClemLaf\ComptesAppBundle\Entity\Comptes\Entree $entriesS)
    {
        $this->entriesS[] = $entriesS;

        return $this;
    }

    /**
     * Remove entriesS
     *
     * @param \ClemLaf\ComptesAppBundle\Entity\Comptes\Entree $entriesS
     */
    public function removeEntriesS(\ClemLaf\ComptesAppBundle\Entity\Comptes\Entree $entriesS)
    {
        $this->entriesS->removeElement($entriesS);
    }

    /**
     * Get entriesS
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEntriesS()
    {
        return $this->entriesS;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $periodsD;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $periodsS;


    /**
     * Add periodsD
     *
     * @param \ClemLaf\ComptesAppBundle\Entity\Comptes\Periodic $periodsD
     * @return Compte
     */
    public function addPeriodsD(\ClemLaf\ComptesAppBundle\Entity\Comptes\Periodic $periodsD)
    {
        $this->periodsD[] = $periodsD;

        return $this;
    }

    /**
     * Remove periodsD
     *
     * @param \ClemLaf\ComptesAppBundle\Entity\Comptes\Periodic $periodsD
     */
    public function removePeriodsD(\ClemLaf\ComptesAppBundle\Entity\Comptes\Periodic $periodsD)
    {
        $this->periodsD->removeElement($periodsD);
    }

    /**
     * Get periodsD
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPeriodsD()
    {
        return $this->periodsD;
    }

    /**
     * Add periodsS
     *
     * @param \ClemLaf\ComptesAppBundle\Entity\Comptes\Periodic $periodsS
     * @return Compte
     */
    public function addPeriodsS(\ClemLaf\ComptesAppBundle\Entity\Comptes\Periodic $periodsS)
    {
        $this->periodsS[] = $periodsS;

        return $this;
    }

    /**
     * Remove periodsS
     *
     * @param \ClemLaf\ComptesAppBundle\Entity\Comptes\Periodic $periodsS
     */
    public function removePeriodsS(\ClemLaf\ComptesAppBundle\Entity\Comptes\Periodic $periodsS)
    {
        $this->periodsS->removeElement($periodsS);
    }

    /**
     * Get periodsS
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPeriodsS()
    {
        return $this->periodsS;
    }
}
