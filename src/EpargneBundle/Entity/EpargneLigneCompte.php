<?php

namespace EpargneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EpargneLigneCompte
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="EpargneBundle\Entity\EpargneLigneCompteRepository")
 */
class EpargneLigneCompte
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
     * @var \DateTime
     *
     * @ORM\Column(name="date_operation", type="date")
     */
    private $dateOperation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_valeur", type="date")
     */
    private $dateValeur;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=255)
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="designation", type="string", length=255)
     */
    private $designation;

    /**
     * @var string
     *
     * @ORM\Column(name="ref_banque", type="string", length=255)
     */
    private $refBanque;

    /**
     * @var string
     *
     * @ORM\Column(name="montant", type="decimal", precision=12, scale=2)
     */
    private $montant;
    
     /**
     * @var string $solde
     *
     * @ORM\Column(name="solde", type="decimal", precision=12, scale=2, nullable=TRUE)
     */
    private $solde;   
      
    /**
    * @ORM\ManyToOne(targetEntity="EpargneCompte", inversedBy="lignes", cascade={"remove"})
    * @ORM\JoinColumn(name="compte_id", referencedColumnName="id")
    */    
    private $compte;
    
    /**
    * @var boolean $valider
    *
    * @ORM\Column(name="valider", type="boolean")
    */
    private $valider;

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
     * Set dateOperation
     *
     * @param \DateTime $dateOperation
     *
     * @return EpargneLigneCompte
     */
    public function setDateOperation($dateOperation)
    {
        $this->dateOperation = $dateOperation;

        return $this;
    }

    /**
     * Get dateOperation
     *
     * @return \DateTime
     */
    public function getDateOperation()
    {
        return $this->dateOperation;
    }

    /**
     * Set dateValeur
     *
     * @param \DateTime $dateValeur
     *
     * @return EpargneLigneCompte
     */
    public function setDateValeur($dateValeur)
    {
        $this->dateValeur = $dateValeur;

        return $this;
    }

    /**
     * Get dateValeur
     *
     * @return \DateTime
     */
    public function getDateValeur()
    {
        return $this->dateValeur;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return EpargneLigneCompte
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return EpargneLigneCompte
     */
    public function setType($type){
        $oType = "Autres";
        $lstType = array("CB" => array("CB", "carte"), 
                         "PRE" => array("PRE", "PRL", "Cotisation", "Ech. prêt immobilier"), 
                         "VER" => array("VER", "Int cr"),
                         "VIR" => array("VIR"),
                         "CHQ" => array("CHQ", "cheq"),
        );
                                                
        if(!array_key_exists($type, $lstType)){
            foreach($lstType as $key => $t){
                foreach($t as $u){
                    if(stripos(utf8_encode($type), $u) !== FALSE){
                        $oType = $key; 
                        break 2;
                    }        
                }
            }
        }
        else{
            $oType = $type;    
        }
        
        $this->type = $oType;

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
     * Set designation
     *
     * @param string $designation
     *
     * @return EpargneLigneCompte
     */
    public function setDesignation($designation){
        $oDesignation = "Divers";
        $lstDesignation = array("ASS. VIE" => array("ASS. VIE", "GAN VIE"),
                         "ADSL SFR" => array("ADSL SFR"), 
                         "Assurance" => array("Assurance", "Prl de GROUPAM"), 
                         "BC-745-MW" => array("BC-745-MW"),
                         "Bricolage" => array("Bricolage"),
                         "Bus" => array("Bus"),
                         "BY-477-FP" => array("BY-477-FP"), 
                         "Courses" => array("Courses"),
                         "CS-384-DN" => array("CS-384-DN"), 
                         "Divers" => array(""), 
                         "EDF" => array("EDF"),
                         "Electroménager" => array("Electromenager"), 
                         "Essence" => array("Essence"),
                         "Eaux" => array("Eaux", "Eaux du Nor", "DOMEO"), 
                         "Habillement" => array("Habillement"),
                         "High-Tech" => array("High-Tech", "RUE DU COMMERCE", "TOP ACHAT"),  
                         "Famille" => array("Famille"), 
                         "Impôts" => array("Impôts", "DRFIP"),
                         "Info. / Hifi" => array("Info. / Hifi"),
                         "Laposte" => array("Laposte","COLISSIMO", "COLIPOSTE", "La poste"),
                         "Livret A" => array("Livret A"),
                         "Loyer" => array("Loyer"),
                         "Meuble" => array("Meuble"),
                         "Salaire" => array(""),
                         "Salaire Thibaut" => array("Salaire", "GFK", "VIR DE OVH"),
                         "Salaire Emilie" => array("Salaire Emilie"),
                         "Vir Thibaut" => array("Vir à M GILLARDEAU THIBAUT"),
                         "Vir Emilie" => array("Vir à ROGER EMILIE"),
                         "OVH" => array("OVH", "BOUDIN", "LAUDIN", "PIERRE GUILLAUD"),
                         "PEL" => array("PEL", "plan épargne log"),
                         "Pole Emploi" => array("Pole Emploi"),
                         "Portable" => array("Portable", "Orange"),
                         "Presse" => array("Presse"),      
                         "Prêt Immo" => array("Ech. prêt immobilier"),                 
                         "Santé" => array("Santé"),                         
        );
        
        if(!array_key_exists($designation, $lstDesignation)){                                        
            foreach($lstDesignation as $key => $t){
                foreach($t as $u){
                    if(stripos(utf8_encode($designation), $u) !== FALSE){
                        $oDesignation = $key; 
                        break 2;
                    }        
                }
            }
        }
        else{
            $oDesignation = $designation;
        }
                            
        $this->designation = $oDesignation;

        return $this;
    }

    /**
     * Get designation
     *
     * @return string
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * Set refBanque
     *
     * @param string $refBanque
     *
     * @return EpargneLigneCompte
     */
    public function setRefBanque($refBanque)
    {
        $this->refBanque = $refBanque;

        return $this;
    }

    /**
     * Get refBanque
     *
     * @return string
     */
    public function getRefBanque()
    {
        return $this->refBanque;
    }

    /**
     * Set montant
     *
     * @param string $montant
     *
     * @return EpargneLigneCompte
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get montant
     *
     * @return string
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * Set compte
     *
     * @param \EpargneBundle\Entity\EpargneCompte $compte
     *
     * @return EpargneLigneCompte
     */
    public function setCompte(\EpargneBundle\Entity\EpargneCompte $compte = null)
    {
        $this->compte = $compte;

        return $this;
    }

    /**
     * Get compte
     *
     * @return \EpargneBundle\Entity\EpargneCompte
     */
    public function getCompte()
    {
        return $this->compte;
    }
    
    /**
     * Set valider
     *
     * @param boolean $valider
     *
     * @return EpargneLigneCompte
     */
    public function setValider($valider)
    {
        $this->valider = $valider;

        return $this;
    }

    /**
     * Get valider
     *
     * @return boolean
     */
    public function getValider()
    {
        return $this->valider;
    }
    
    /**
     * Set solde
     *
     * @param string $solde
     *
     * @return EpargneLigneCompte
     */
    public function setSolde($solde)
    {
        $this->solde = $solde;

        return $this;
    }

    /**
     * Get solde
     *
     * @return string
     */
    public function getSolde()
    {
        return $this->solde;
    }
}