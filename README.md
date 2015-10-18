# Thibautg16EpargneBundle

**//!\\ Attention : ce module est en cours de développement, il n'est actuellement pas complètement fonctionnel //!\\**

### Prérequis
- php 5.3.3
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
