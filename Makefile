include .env
export

init-dev: docker-pull docker-build docker-up composer-install npm-install
run-dev: docker-up

docker-up:
	COMPOSE_PROJECT_NAME=$(APP_NAME) docker compose up -d --build
docker-down:
	COMPOSE_PROJECT_NAME=$(APP_NAME) docker compose down --remove-orphans
docker-pull:
	COMPOSE_PROJECT_NAME=$(APP_NAME) docker compose pull
docker-build:
	COMPOSE_PROJECT_NAME=$(APP_NAME) docker compose build

clear: cache-clear cache-warmup

cache-clear:
	composer dump-autoload -o  && php bin/console cache:clear
cache-warmup:
	php bin/console cache:warmup
composer-install:
	composer install

npm-install:
	npm i

npm-build:
	npm run build

stan:
	php vendor/bin/phpstan analyse src tests

fix:
	vendor/bin/php-cs-fixer fix src && vendor/bin/php-cs-fixer fix tests

clean: fix stan cache-clear

entity:
	php bin/console m:e

migration:
	php bin/console make:migration

migration-migrate:
	php bin/console doctrine:migrations:migrate

jwt:
	php bin/console lexik:jwt:generate-keypair --skip-if-exists

#запуск в hyper-v для доступа с локальной машины
serve:
	symfony server:start --port=8001 --allow-all-ip -d

debug-dotenv:
	php bin/console debug:dotenv
debug-vars:
	symfony console debug:container --env-vars