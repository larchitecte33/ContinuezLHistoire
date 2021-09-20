<?php

namespace ContinuezLHistoire\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Client
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="ContinuezLHistoire\SiteBundle\Entity\ClientRepository")
 */
class Client
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
     * @ORM\Column(name="addresseClient", type="string", length=15)
     */
    private $addresseClient;

    /**
     * @var boolean
     *
     * @ORM\Column(name="vientDeSInscrire", type="boolean")
     */
    private $vientDeSInscrire;


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
     * Set addresseClient
     *
     * @param string $addresseClient
     * @return Client
     */
    public function setAddresseClient($addresseClient)
    {
        $this->addresseClient = $addresseClient;
    
        return $this;
    }

    /**
     * Get addresseClient
     *
     * @return string 
     */
    public function getAddresseClient()
    {
        return $this->addresseClient;
    }

    /**
     * Set vientDeSInscrire
     *
     * @param boolean $vientDeSInscrire
     * @return Client
     */
    public function setVientDeSInscrire($vientDeSInscrire)
    {
        $this->vientDeSInscrire = $vientDeSInscrire;
    
        return $this;
    }

    /**
     * Get vientDeSInscrire
     *
     * @return boolean 
     */
    public function getVientDeSInscrire()
    {
        return $this->vientDeSInscrire;
    }
}
