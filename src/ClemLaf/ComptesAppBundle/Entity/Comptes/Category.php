<?php

namespace ClemLaf\ComptesAppBundle\Entity\Comptes;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 */
class Category
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $cNam;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set cNam
     *
     * @param string $cNam
     * @return Category
     */
    public function setCNam($cNam)
    {
        $this->cNam = $cNam;

        return $this;
    }

    /**
     * Get cNam
     *
     * @return string 
     */
    public function getCNam()
    {
        return $this->cNam;
    }

    /**
     * Add entries
     *
     * @param \ClemLaf\ComptesAppBundle\Entity\Comptes\Entree $entries
     * @return Category
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
    private $periodics;


    /**
     * Add periodics
     *
     * @param \ClemLaf\ComptesAppBundle\Entity\Comptes\Periodic $periodics
     * @return Category
     */
    public function addPeriodic(\ClemLaf\ComptesAppBundle\Entity\Comptes\Periodic $periodics)
    {
        $this->periodics[] = $periodics;

        return $this;
    }

    /**
     * Remove periodics
     *
     * @param \ClemLaf\ComptesAppBundle\Entity\Comptes\Periodic $periodics
     */
    public function removePeriodic(\ClemLaf\ComptesAppBundle\Entity\Comptes\Periodic $periodics)
    {
        $this->periodics->removeElement($periodics);
    }

    /**
     * Get periodics
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPeriodics()
    {
        return $this->periodics;
    }
}
