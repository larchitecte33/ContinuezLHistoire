<?php

namespace ContinuezLHistoire\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MessageContact
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="ContinuezLHistoire\SiteBundle\Entity\MessageContactRepository")
 */
class MessageContact
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
     * @var string
     *
     * @ORM\Column(name="corps", type="string", length=255)
     */
    private $corps;

    /**
     * @ORM\ManyToOne(targetEntity="ContinuezLHistoire\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $auteur;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

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
     * @return MessageContact
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
     * Set corps
     *
     * @param string $corps
     * @return MessageContact
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
     * Set auteur
     *
     * @param \ContinuezLHistoire\UserBundle\Entity\User $auteur
     * @return MessageContact
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
     * Set date
     *
     * @param \DateTime $date
     * @return MessageContact
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
}
