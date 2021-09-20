<?php

namespace ContinuezLHistoire\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Histoire
 *
 * @ORM\Table()
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="ContinuezLHistoire\SiteBundle\Entity\HistoireRepository")
 */
class Histoire
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
     * @ORM\Column(name="titre", type="string", length=255)
     */
    private $titre;

    /**
     * @ORM\OneToOne(targetEntity="ContinuezLHistoire\SiteBundle\Entity\Image", cascade={"persist"})
     */
    private $image;

    /**
     * @var boolean
     *
     * @ORM\Column(name="edite", type="boolean")
     */
    private $edite;
    
    /**
     * @var datetime
     * 
     * @ORM\Column(name="debutEdition", type="datetime")
     */
    private $debutEdition;
    
    /**
     * @ORM\ManyToOne(targetEntity="ContinuezLHistoire\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $editeur;
    
    /**
     * @ORM\ManyToOne(targetEntity="ContinuezLHistoire\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $premierAuteur;

    /**
     * @ORM\ManyToOne(targetEntity="ContinuezLHistoire\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $dernierAuteur;

    /**
     * @var datetime
     * 
     * @ORM\Column(name="dateCloture", type="datetime", nullable=true)
     */
    private $dateCloture;
    
    /**
     * @var datetime
     * 
     * @ORM\Column(name="dateClotureEffective", type="datetime", nullable=true)
     */
    private $dateClotureEffective;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="estEnInstanceDeCloture", type="boolean", nullable=true)
     */
    private $estEnInstanceDeCloture;
    
    /**
     * @var boolean
     * 
     * @ORM\Column(name="estClos", type="boolean", nullable=true)
     */
    private $estClos = false;
    
    /**
     * @var boolean
     * 
     * @ORM\Column(name="actif", type="boolean", nullable=false)
     */
    private $actif;

    public function __constuct()
    {
        
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
     * Set titre
     *
     * @param string $titre
     * @return Histoire
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;
    
        return $this;
    }

    /**
     * Get titre
     *
     * @return string 
     */
    public function getTitre()
    {
        return $this->titre;
    }
    
    /**
     * Set image
     *
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }
 
    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }
    
    /**
     * Set edite
     *
     * @param boolean $edite
     * @return Histoire
     */
    public function setEdite($edite)
    {
        $this->edite = $edite;
    
        return $this;
    }

    /**
     * Get edite
     *
     * @return string 
     */
    public function getEdite()
    {
        return $this->edite;
    }

    /**
     * Set debutEdition
     *
     * @param \DateTime $debutEdition
     * @return Histoire
     */
    public function setDebutEdition($debutEdition)
    {
        $this->debutEdition = $debutEdition;
    
        return $this;
    }

    /**
     * Get debutEdition
     *
     * @return \DateTime 
     */
    public function getDebutEdition()
    {
        return $this->debutEdition;
    }

    /**
     * Set editeur
     *
     * @param string $editeur
     */
    public function setEditeur($editeur)
    {
        $this->editeur = $editeur;
    }

    /**
     * Get editeur
     *
     * @return string
     */
    public function getEditeur()
    {
        return $this->editeur;
    }

    /**
     * Set dernierAuteur
     *
     * @param \ContinuezLHistoire\UserBundle\Entity\User $dernierAuteur
     * @return Histoire
     */
    public function setDernierAuteur(\ContinuezLHistoire\UserBundle\Entity\User $dernierAuteur)
    {
        $this->dernierAuteur = $dernierAuteur;
    
        return $this;
    }

    /**
     * Get dernierAuteur
     *
     * @return \ContinuezLHistoire\UserBundle\Entity\User 
     */
    public function getDernierAuteur()
    {
        return $this->dernierAuteur;
    }

    /**
     * Set dateCloture
     *
     * @param \DateTime $dateCloture
     * @return Histoire
     */
    public function setDateCloture($dateCloture)
    {
        $this->dateCloture = $dateCloture;
    
        return $this;
    }

    /**
     * Get dateCloture
     *
     * @return \DateTime 
     */
    public function getDateCloture()
    {
        return $this->dateCloture;
    }

    /**
     * Set dateClotureEffective
     *
     * @param \DateTime $dateClotureEffective
     * @return Histoire
     */
    public function setDateClotureEffective($dateClotureEffective)
    {
        $this->dateClotureEffective = $dateClotureEffective;
    
        return $this;
    }

    /**
     * Get dateClotureEffective
     *
     * @return \DateTime 
     */
    public function getDateClotureEffective()
    {
        return $this->dateClotureEffective;
    }

    /**
     * Set estEnInstanceDeCloture
     *
     * @param boolean $estEnInstanceDeCloture
     * @return Histoire
     */
    public function setEstEnInstanceDeCloture($estEnInstanceDeCloture)
    {
        $this->estEnInstanceDeCloture = $estEnInstanceDeCloture;
    
        return $this;
    }

    /**
     * Get estEnInstanceDeCloture
     *
     * @return boolean 
     */
    public function getEstEnInstanceDeCloture()
    {
        return $this->estEnInstanceDeCloture;
    }

    /**
     * Set estClos
     *
     * @param boolean $estClos
     * @return Histoire
     */
    public function setEstClos($estClos)
    {
        $this->estClos = $estClos;
    
        return $this;
    }

    /**
     * Get estClos
     *
     * @return boolean 
     */
    public function getEstClos()
    {
        return $this->estClos;
    }

    /**
     * Set premierAuteur
     *
     * @param \ContinuezLHistoire\UserBundle\Entity\User $premierAuteur
     * @return Histoire
     */
    public function setPremierAuteur(\ContinuezLHistoire\UserBundle\Entity\User $premierAuteur)
    {
        $this->premierAuteur = $premierAuteur;

        return $this;
    }

    /**
     * Get premierAuteur
     *
     * @return \ContinuezLHistoire\UserBundle\Entity\User 
     */
    public function getPremierAuteur()
    {
        return $this->premierAuteur;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     * @return Histoire
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
