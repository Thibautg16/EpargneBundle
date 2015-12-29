# Thibautg16EpargneBundle

**//!\\ Attention : ce module est en cours de développement, il n'est actuellement pas complètement fonctionnel //!\\**

### Prérequis
- php 5.3.9
- Symfony 2.8.*
- ObHighchartsBundle
- Thibautg16UtilisateurBundle
- Thibautg16SqueletteBundle

## Installation EpargneBundle
### Installation à l'aide de composer

1. Ajouter ``thibautg16/epargne-bundle`` comme dépendance de votre projet dans le fichier ``composer.json`` :

        {
          "require": {
            "thibautg16/epargne-bundle": "dev-master"
          }
        }

3. Installer vos dépendances :

        php composer.phar update

4. Ajouter le Bundle dans votre kernel :

        <?php
        // app/AppKernel.php
        
        public function registerBundles(){
            $bundles = array(
              // ...
              new EpargneBundle\EpargneBundle(),
            );
        }

5. Ajouter les routes du bundle à votre projet en ajoutant dans votre fichier app/config/routing.yml :

        EpargneBundle:
            resource: "@EpargneBundle/Resources/config/routing.yml"
            prefix:   /

6. Ajouter la relation entre le bundle Utilisateur et le bundle Epargne

        6.1 Ajouter les lignes suivantes dans le fichier :
        - vendor/thibautg16/utilisateur-bundle/src/Thibautg16/UtilisateurBundle/Entity/Utilisateur.php
                /**
                * @ORM\ManyToMany(targetEntity="EpargneBundle\Entity\EpargneCompte" , mappedBy="utilisateurs")
                */
                protected $comptes;
        
        6.2 Mettre à jour les entites du bundle Utilisateur
                # php app/console doctrine:generate:entities Thibautg16UtilisateurBundle