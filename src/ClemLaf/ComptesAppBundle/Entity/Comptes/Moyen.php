<?php

namespace ClemLaf\ComptesAppBundle\Entity\Comptes;

use Doctrine\ORM\Mapping as ORM;

/**
 * Moyen
 */
class Moyen
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $mNam;


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
     * Set mNam
     *
     * @param string $mNam
     * @return Moyen
     */
    public function setMNam($mNam)
    {
        $this->mNam = $mNam;

        return $this;
    }

    /**
     * Get mNam
     *
     * @return string 
     */
    public function getMNam()
    {
        return $this->mNam;
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
     * @return Moyen
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
}
