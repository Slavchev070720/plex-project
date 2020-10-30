# plex-project
- `15.04.2019 - 11.05.2019` - This is an on board training project by UptetiX/Scalefocus. The assignment was to create a website similar to https://plex.tv which must analyse a big SQLite dump file with movies and tv series information. After it takes the important information then must hit 4 different APIs to get some missing information like year, actors, language, banner etc. and then present it in the browser. The analysis must be done as background process.
- `29.10.2020 - 30.10.2020` - The project was return to me without Git and the devops template (Vagrant, Ansible) after I quit the company. I made a new repository and used docker compose to run the project.
- Note: The SQLite dumb file is in `external_db` directory. First unzip it and then you can upload it. After it is uploaded in the form at the homepage then click `Analyse` to start the background process (15-20min). The logs of the process are found in `/var/log/analyse-command-log.log`. 

## Used Technologies
- PHP 7.3-fpm
- Symphony 4.2
- PDO
- CURL
- Background process
- Nginx 1.18
- MySQL 5.7
- Bootstrap 4.3.1
- Composer 1.10
- Git 2.25
- Docker 19.03
- Docker-compose 1.27

## Setup Prerequisites
You must have the following tools installed:
- Git - https://git-scm.com/downloads
- Docker - https://docs.docker.com/install/linux/docker-ce/ubuntu/
- Docker Compose - https://docs.docker.com/compose/install/
- You must add the proper virtual host record to your /etc/hosts file: `127.0.0.1	plex-project`
  
## Setup Configuration
- Configuration is in .env(will be created for you based on .env-dist) and there you can tweak database config and some Docker params.
- In case your uid and gid are not 1000 but say 1001, you must change the USER_ID and GROUP_ID vars in .env file. Type the `id` command in your terminal in order to find out.
- Nginx logs are accessible in ./volumes/nginx/logs
- MySQL data is persisted via a Docker volume.
- Composer cache is persisted via a Docker volume.
- You can write code by loading your project in your favourite IDE, but in order to use Composer you must work in the PHP container.

## Start the Docker ecosystem for a first time
- `mkdir plex-project` - create a new project dir
- `cd plex-project` - get into it
- `git clone https://github.com/Slavchev070720/plex-project.git .` - clone code from repo
- `cp .env-dist .env` - create the .env file
- Now you would want to run `id` command and set USER_ID and GROUP_ID env vars in .env file as per your needs.
- `docker-compose build` - build Docker images and volumes
- `docker-compose run --rm php-dev composer install` - install Composer packages
- `docker-compose up -d` - start the whole ecosystem (wait few seconds for mysql service to start)
- `docker-compose ps` - verify all containers are up and running
- `docker exec -it plex-php-dev /bin/bash` - ssh into plex-php-dev container to run database migration
- `php bin/console doctrine:migrations:migrate --no-interaction` - command for database migration
- Open your favorite browser and go to `http://plex-project` to see plex-project homepage

### Useful commands
- `docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' container` - gets container's IP
- `docker-compose exec plex-php-dev /bin/bash` - enter the php container
- `docker kill -s HUP container` - can be used to reload Nginx configuration dynamically