<?php

namespace ContinuezLHistoire\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SousHistoire
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="ContinuezLHistoire\SiteBundle\Entity\SousHistoireRepository")
 */
class SousHistoire
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
     * @ORM\ManyToOne(targetEntity="ContinuezLHistoire\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $auteur;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="string", length=255)
     */
    private $contenu;

    /**
     * @ORM\ManyToOne(targetEntity="ContinuezLHistoire\SiteBundle\Entity\Histoire")
     * @ORM\JoinColumn(nullable=false)
     */
    private $histoire;

    /**
     * @var integer
     *
     * @ORM\Column(name="place", type="integer")
     */
    private $place;
    
    /**
     * @var boolean
     * 
     * @ORM\Column(name="actif", type="boolean", nullable=false)
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
     * Set auteur
     *
     * @param integer $auteur
     * @return SousHistoire
     */
    public function setAuteur($auteur)
    {
        $this->auteur = $auteur;
    
        return $this;
    }

    /**
     * Get auteur
     *
     * @return integer 
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return SousHistoire
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
     * Set contenu
     *
     * @param string $contenu
     * @return SousHistoire
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;
    
        return $this;
    }

    /**
     * Get contenu
     *
     * @return string 
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set histoire
     *
     * @param integer $histoire
     * @return SousHistoire
     */
    public function setHistoire($histoire)
    {
        $this->histoire = $histoire;
    
        return $this;
    }

    /**
     * Get histoire
     *
     * @return integer 
     */
    public function getHistoire()
    {
        return $this->histoire;
    }

    /**
     * Set place
     *
     * @param integer $place
     * @return SousHistoire
     */
    public function setPlace($place)
    {
        $this->place = $place;
    
        return $this;
    }

    /**
     * Get place
     *
     * @return integer 
     */
    public function getPlace()
    {
        return $this->place;
    }
    
    public function integrerSautsDeLigne()
    {
        for ( $i = 0 ; $i < strlen($this->contenu) ; $i++ ) {
            if ( $this->contenu == "\n" )
                echo 'Fin de paragraphe';
            echo $this->contenu[$i];
        }
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     * @return SousHistoire
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
