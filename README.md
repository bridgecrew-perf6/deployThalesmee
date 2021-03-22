# Installation de thalesmee via Docker

## Installation de Docker

``` 
$ sudo apt-get update
$ sudo apt-get install \
    apt-transport-https \
    ca-certificates \
    curl \
    gnupg-agent \
    software-properties-common
$ curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -
$ sudo apt-key fingerprint 0EBFCD88
$ sudo add-apt-repository \
   "deb [arch=amd64] https://download.docker.com/linux/ubuntu \
   $(lsb_release -cs) \
   stable"
$ sudo apt-get update
$ sudo apt-get install docker-ce docker-ce-cli containerd.io
$ sudo docker run hello-world
```

## Installation de docker compose

```
$ sudo curl -L "https://github.com/docker/compose/releases/download/1.27.4/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
$ sudo chmod +x /usr/local/bin/docker-compose
$ sudo ln -s /usr/local/bin/docker-compose /usr/bin/docker-compose
$ docker-compose --version
```

## Contenu du répertoire

- /www : code source
- /php :
	- php.ini : fichier de configuration de php
	- Dockerfile : fichier d'installation
	- /font : polices d'ecriture
- /apache : configuration d'apache

## MySql : étapes nécessaire

1. Création d'un nouvel utilisateur
	- user : mee
	- password : pipo
2. Import de la base de donnée depuis le fichier /db/bdmee_prod.sql

## Code source : modification 

1. /www/graph/jpgraph/jp-config.inc.php : Ajouter à la fin du fichier

```
define('ANTIALIASING', false);
 
if(!ANTIALIASING){
    function imageantialias($image, $enabled){
        return true;
    }
}
```

2. /www/conf/enregistrerDoc_param.php : Modifier le chemin

```
$chemin="/var/www/html/doc/";
```

3. /www/conf/connexion_param.php : Modifier l'adresse du serveur

```
$add = "db";
```

4. /www/conf/connexionPDO_param.php : Modifier l'adresse du serveur

```
$hostname = "db";
```

## Ajouter les droits sur le dossier /www

```
$ cd /www
$ sudo chmod 777 -R ./*
```

## Déploiement

``` $ docker-compose up ``` 