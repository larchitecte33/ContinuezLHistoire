<?php

namespace ContinuezLHistoire\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Discussion
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="ContinuezLHistoire\ForumBundle\Entity\DiscussionRepository")
 */
class Discussion
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
     * @ORM\Column(name="sujet", type="string", length=50)
     */
    private $sujet;

    /**
     * @ORM\ManyToOne(targetEntity="ContinuezLHistoire\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $auteur;
    
    /**
     * @var integer
     * 
     * @ORM\Column(name="nbDeMessages", type="integer")
     */
    private $nbDeMessages;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
    
    /**
     * @var boolean
     * 
     * @ORM\Column(name="actif", type="boolean")
     */
    private $actif;

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
     * Set sujet
     *
     * @param string $sujet
     * @return Discussion
     */
    public function setSujet($sujet)
    {
        $this->sujet = $sujet;
    
        return $this;
    }

    /**
     * Get sujet
     *
     * @return string 
     */
    public function getSujet()
    {
        return $this->sujet;
    }

    /**
     * Set auteur
     *
     * @param \ContinuezLHistoire\UserBundle\Entity\User $auteur
     * @return Discussion
     */
    public function setAuteur(\ContinuezLHistoire\UserBundle\Entity\User $auteur)
    {
        $this->auteur = $auteur;
    
        return $this;
    }

    /**
     * Get auteur
     *
     * @return \ContinuezLHistoire\UserBundle\Entity\User 
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * Set nbDeMessages
     *
     * @param integer $nbDeMessages
     * @return Discussion
     */
    public function setNbDeMessages($nbDeMessages)
    {
        $this->nbDeMessages = $nbDeMessages;
    
        return $this;
    }

    /**
     * Get nbDeMessages
     *
     * @return integer 
     */
    public function getNbDeMessages()
    {
        return $this->nbDeMessages;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Discussion
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
     * Set actif
     *
     * @param boolean $actif
     * @return Discussion
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Get actif
     *
     * @return boolean 
     */
    public function getActif()
    {
        return $this->actif;
    }
}
