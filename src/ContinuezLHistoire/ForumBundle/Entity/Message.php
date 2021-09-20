<?php

namespace ContinuezLHistoire\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="ContinuezLHistoire\ForumBundle\Entity\MessageRepository")
 */
class Message
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
     * @var boolean
     * 
     * @ORM\Column(name="actif", type="boolean")
     */
    private $actif;
    
    /**
     * @ORM\ManyToOne(targetEntity="ContinuezLHistoire\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $auteur;

    /**
     * @var string
     *
     * @ORM\Column(name="corps", type="string", length=255)
     */
    private $corps;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
    
    /**
     * @ORM\ManyToOne(targetEntity="ContinuezLHistoire\ForumBundle\Entity\Discussion")
     * @ORM\JoinColumn(nullable=false)
     */
    private $discussion;

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
     * Set auteur
     *
     * @param string $auteur
     * @return Message
     */
    public function setAuteur($auteur)
    {
        $this->auteur = $auteur;
    
        return $this;
    }

    /**
     * Get auteur
     *
     * @return string 
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * Set corps
     *
     * @param string $corps
     * @return Message
     */
    public function setCorps($corps)
    {
        $this->corps = $corps;
    
        return $this;
    }

    /**
     * Get corps
     *
     * @return string 
     */
    public function getCorps()
    {
        return $this->corps;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Message
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
     * Set discussion
     *
     * @param \ContinuezLHistoire\ForumBundle\Entity\Discussion $discussion
     * @return Message
     */
    public function setDiscussion(\ContinuezLHistoire\ForumBundle\Entity\Discussion $discussion)
    {
        $this->discussion = $discussion;
    
        return $this;
    }

    /**
     * Get discussion
     *
     * @return \ContinuezLHistoire\ForumBundle\Entity\Discussion 
     */
    public function getDiscussion()
    {
        return $this->discussion;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     * @return Message
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
