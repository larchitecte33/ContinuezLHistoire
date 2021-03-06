<?php

namespace ContinuezLHistoire\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AvisUtilisateur
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="ContinuezLHistoire\SiteBundle\Entity\AvisUtilisateurRepository")
 */
class AvisUtilisateur
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="libelleAvis", type="string", length=255)
     */
    private $libelleAvis;

    /**
     * @var integer
     * 
     * @ORM\Column(name="poidsAvis", type="integer")
     */
    private $poidsAvis;

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
     * Set libelleAvis
     *
     * @param string $libelleAvis
     * @return AvisUtilisateur
     */
    public function setLibelleAvis($libelleAvis)
    {
        $this->libelleAvis = $libelleAvis;
    
        return $this;
    }

    /**
     * Get libelleAvis
     *
     * @return string 
     */
    public function getLibelleAvis()
    {
        return $this->libelleAvis;
    }

    /**
     * Set poidsAvis
     *
     * @param integer $poidsAvis
     * @return AvisUtilisateur
     */
    public function setPoidsAvis($poidsAvis)
    {
        $this->poidsAvis = $poidsAvis;
    
        return $this;
    }

    /**
     * Get poidsAvis
     *
     * @return integer 
     */
    public function getPoidsAvis()
    {
        return $this->poidsAvis;
    }
}