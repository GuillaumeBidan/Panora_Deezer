Deezer Favorit Track List
========================


Structure:
--------------
  * .ebextensions: fichiers de configuration pour deploiement elastic beanstalk AWS
  * assets: fichiers css/js
  * config: fichiers de configuration symfony
  * public: fichiers accessibles par les serveurs webs
  * src: code source
  * templates: vues
  * var: cache/log
  * vendor: librairie externe


Paramètres d'envionnement
--------------

Symfony:
  * APP_ENV => prod ou DEV

Deezer API:
  * app_id
  * app_secret
  * my_url


Technologies utilisés:
--------------
  * Symfony 4 (>PHP 7.1.3)
  * ReactJs
  * Préparé pour AWS
