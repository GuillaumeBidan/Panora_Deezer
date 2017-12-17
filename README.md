Deezer Favorit Track List
========================

Cette application liste les musiques favorites à partir de l'Api Deezer. Elle permet d'ajouter et de supprimer des musiques.
Il faut avoir un compte deezer pour tester l'application.

Structure:
--------------
  * .ebextensions: fichiers de configuration pour deploiement elastic beanstalk AWS
  * assets: fichiers css/js
  * config: fichiers de configuration symfony
  * public: fichiers accessibles par les serveurs webs
  * src: code source
  * templates: vues
  * var: cache/log
  * vendor: librairies externes


Paramètres d'envionnement
--------------

Symfony:
  * APP_ENV => prod ou DEV

Deezer API:
  * app_id
  * app_secret


Technologies utilisées:
--------------
  * Symfony 4 (>PHP 7.1.3)
  * ReactJs
  * Préparé pour AWS
