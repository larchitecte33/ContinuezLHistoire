<?php

namespace ContinuezLHistoire\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Note
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="ContinuezLHistoire\SiteBundle\Entity\NoteRepository")
 */
class Note
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
     * @ORM\ManyToOne(targetEntity="ContinuezLHistoire\SiteBundle\Entity\Histoire")
     * @ORM\JoinColumn(nullable=false)
     */
    private $histoire;
    
    /**
     * @ORM\ManyToOne(targetEntity="ContinuezLHistoire\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="ContinuezLHistoire\SiteBundle\Entity\AvisUtilisateur")
     * @ORM\JoinColumn(nullable=false)
     */
    private $avisUtilisateur;

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
     * Set histoire
     *
     * @param \ContinuezLHistoire\SiteBundle\Entity\Histoire $histoire
     * @return Note
     */
    public function setHistoire(\ContinuezLHistoire\SiteBundle\Entity\Histoire $histoire = null)
    {
        $this->histoire = $histoire;
    
        return $this;
    }

    /**
     * Get histoire
     *
     * @return \ContinuezLHistoire\SiteBundle\Entity\Histoire 
     */
    public function getHistoire()
    {
        return $this->histoire;
    }

    /**
     * Set user
     *
     * @param \ContinuezLHistoire\UserBundle\Entity\User $user
     * @return Note
     */
    public function setUser(\ContinuezLHistoire\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \ContinuezLHistoire\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set avisUtilisateur
     *
     * @param \ContinuezLHistoire\SiteBundle\Entity\AvisUtilisateur $avisUtilisateur
     * @return Note
     */
    public function setAvisUtilisateur(\ContinuezLHistoire\SiteBundle\Entity\AvisUtilisateur $avisUtilisateur = null)
    {
        $this->avisUtilisateur = $avisUtilisateur;
    
        return $this;
    }

    /**
     * Get avisUtilisateur
     *
     * @return \ContinuezLHistoire\SiteBundle\Entity\AvisUtilisateur 
     */
    public function getAvisUtilisateur()
    {
        return $this->avisUtilisateur;
    }
}