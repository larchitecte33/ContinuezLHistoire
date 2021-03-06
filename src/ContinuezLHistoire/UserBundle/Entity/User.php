<?php

namespace ContinuezLHistoire\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="continuezlhistoire_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var integer $codeActivation
     * @ORM\Column(name="codeActivation", type="integer", nullable=true)
     */
    private $codeActivation;
    
    /**
     * @ORM\OneToOne(targetEntity="ContinuezLHistoire\SiteBundle\Entity\Image", cascade={"persist"})
     */
    private $imageProfil;
    
    /**
     * @var string $quiSuisJe
     * @ORM\Column(name="quiSuisJe", type="string", length=255, nullable=true)
     */
    private $quiSuisJe;
    
    /**
     * @var integer $nbPoints
     * @ORM\Column(name="nbPoints", type="integer", nullable=true)
     */
    private $nbPoints;
    
    /**
     * @var boolean $actif
     * @ORM\Column(name="actif", type="string", length=1, nullable=false)
     */
    private $actif = 'O';
    
    /**
     * @var string $droitAuteurAttribue
     * @ORM\Column(name="droitAuteurAttribue", type="string", length=1, nullable=false)
     */
    private $droitAuteurAttribue = 'N';

    public function getRolesNames(){
      return array(
          "ROLE_ADMIN"             => "Administrateur",
          "ROLE_AUTEUR"            => "Auteur",
          "ROLE_SUPER_ADMIN"       => "Super Administrateur",
          "ROLE_ALLOWED_TO_SWITCH" => "Autorisé à switcher"
      );
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
     * Set codeActivation
     *
     * @param integer $codeActivation
     * @return User
     */
    public function setCodeActivation($codeActivation)
    {
        $this->codeActivation = $codeActivation;

        return $this;
    }

    /**
     * Get codeActivation
     *
     * @return integer 
     */
    public function getCodeActivation()
    {
        return $this->codeActivation;
    }

    /**
     * Set imageProfil
     *
     * @param \ContinuezLHistoire\SiteBundle\Entity\Image $imageProfil
     * @return User
     */
    public function setImageProfil(\ContinuezLHistoire\SiteBundle\Entity\Image $imageProfil = null)
    {
        $this->imageProfil = $imageProfil;
    
        return $this;
    }

    /**
     * Get imageProfil
     *
     * @return \ContinuezLHistoire\SiteBundle\Entity\Image 
     */
    public function getImageProfil()
    {
        return $this->imageProfil;
    }

    /**
     * Set quiSuisJe
     *
     * @param string $quiSuisJe
     * @return User
     */
    public function setQuiSuisJe($quiSuisJe)
    {
        $this->quiSuisJe = $quiSuisJe;

        return $this;
    }

    /**
     * Get quiSuisJe
     *
     * @return string 
     */
    public function getQuiSuisJe()
    {
        return $this->quiSuisJe;
    }

    /**
     * Set nbPoints
     *
     * @param integer $nbPoints
     * @return User
     */
    public function setNbPoints($nbPoints)
    {
        $this->nbPoints = $nbPoints;

        return $this;
    }

    /**
     * Get nbPoints
     *
     * @return integer 
     */
    public function getNbPoints()
    {
        return $this->nbPoints;
    }

    /**
     * Set actif
     *
     * @param string $actif
     * @return User
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Get actif
     *
     * @return string 
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * Set droitAuteurAttribue
     *
     * @param string $droitAuteurAttribue
     * @return User
     */
    public function setDroitAuteurAttribue($droitAuteurAttribue)
    {
        $this->droitAuteurAttribue = $droitAuteurAttribue;

        return $this;
    }

    /**
     * Get droitAuteurAttribue
     *
     * @return string 
     */
    public function getDroitAuteurAttribue()
    {
        return $this->droitAuteurAttribue;
    }
}
