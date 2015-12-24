<?php
/*
 * EpargneBundle/Entity/Compte.php;
 *
 * Copyright 2015 GILLARDEAU Thibaut (aka Thibautg16)
 *
 * Authors :
 *  - Gillardeau Thibaut (aka Thibautg16)
 */
 
namespace EpargneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Compte
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="EpargneBundle\Entity\EpargneCompteRepository")
 */
class EpargneCompte
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
     * @ORM\Column(name="numero", type="string", length=255)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="compagnie", type="string", length=255)
     */
    private $compagnie;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;
    
    /**
    * @ORM\OneToMany(targetEntity="EpargneLigneCompte", mappedBy="compte", cascade={"remove", "persist"})
    */
    protected $lignes;  
    
    /**
	* @ORM\ManyToMany(targetEntity="Thibautg16\UtilisateurBundle\Entity\Utilisateur" , inversedBy="comptes")
	* @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id", nullable=false)
	*/
	protected $utilisateurs; 

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->lignes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->utilisateurs = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set numero
     *
     * @param string $numero
     *
     * @return Compte
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set compagnie
     *
     * @param string $compagnie
     *
     * @return Compte
     */
    public function setCompagnie($compagnie)
    {
        $this->compagnie = $compagnie;

        return $this;
    }

    /**
     * Get compagnie
     *
     * @return string
     */
    public function getCompagnie()
    {
        return $this->compagnie;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Compte
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Compte
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }
    


    /**
     * Add ligne
     *
     * @param \EpargneBundle\Entity\EpargneLigneCompte $ligne
     *
     * @return EpargneCompte
     */
    public function addLigne(\EpargneBundle\Entity\EpargneLigneCompte $ligne)
    {
        $this->lignes[] = $ligne;

        return $this;
    }

    /**
     * Remove ligne
     *
     * @param \EpargneBundle\Entity\EpargneLigneCompte $ligne
     */
    public function removeLigne(\EpargneBundle\Entity\EpargneLigneCompte $ligne)
    {
        $this->lignes->removeElement($ligne);
    }
    
  /**
     * Get lignes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLignes()
    {
        return $this->lignes;
    }

    /**
     * Add utilisateur
     *
     * @param \Thibautg16\UtilisateurBundle\Entity\Utilisateur $utilisateur
     *
     * @return EpargneCompte
     */
    public function addUtilisateur(\Thibautg16\UtilisateurBundle\Entity\Utilisateur $utilisateur)
    {
        $this->utilisateurs[] = $utilisateur;

        return $this;
    }

    /**
     * Remove utilisateur
     *
     * @param \Thibautg16\UtilisateurBundle\Entity\Utilisateur $utilisateur
     */
    public function removeUtilisateur(\Thibautg16\UtilisateurBundle\Entity\Utilisateur $utilisateur)
    {
        $this->utilisateurs->removeElement($utilisateur);
    }

    /**
     * Get utilisateurs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUtilisateurs()
    {
        return $this->utilisateurs;
    }  
}