#!/usr/bin/env make

export COMPOSE_HTTP_TIMEOUT=120
export DOCKER_CLIENT_TIMEOUT=120


default:
	@echo "make needs target:"
	@egrep -e '^\S+' ./Makefile | grep -v default | sed -r 's/://' | sed -r 's/^/ - /'
%:
	@: # silence

restart: down up

init:
	docker-compose down --remove-orphans
	rm -rf ./var/cache/ || echo "delete cache failure"
	docker-compose build
	docker-compose run --rm php-cli composer install
	docker-compose run --rm php-cli php bin/console doctrine:migrations:migrate --no-interaction
    docker-compose run --rm php-cli chown -R www-data:www-data /var/www/html/var/
	docker-compose up -d

up:
	docker-compose up -d

down:
	docker-compose down --remove-orphans

down-clear:
	docker-compose down -v --remove-orphans

# work with composer
composer-install:
	docker-compose run --rm php-cli composer install
composer-update:
	docker-compose run --rm php-cli composer update
# make composer install
# make composer "install --no-dev"
composer:
	docker-compose run --rm php-cli composer $(filter-out $@,$(MAKECMDGOALS))

dev-dump-cache:
	composer dumpautoload
	php bin/console cache:clear

dev-cli-bash:
	docker-compose run --rm php-cli sh $(filter-out $@,$(MAKECMDGOALS))

dev-fpm-bash:
	docker-compose run --rm php-fpm sh $(filter-out $@,$(MAKECMDGOALS))

dev-nginx-bash:
	docker-compose run --rm nginx sh $(filter-out $@,$(MAKECMDGOALS))


# schema management
app-test-validate-orm-schema:
	docker-compose run --rm php-cli php bin/console doctrine:schema:validate --verbose

dev-migrations-make:
	docker-compose run --rm php-cli php bin/console make:migration --no-interaction

dev-migrations-run:
	docker-compose run --rm php-cli php bin/console doctrine:migrations:migrate --no-interaction

# tests
app-test-run-phpstan-analyse:
	docker-compose run --rm php-cli vendor/bin/phpstan analyse --memory-limit 1G

app-test-run-unit-tests:
	docker-compose run --rm php-cli php bin/phpunit --colors=always --testsuite=unit --testdox

app-test-run-functional-tests:
	docker-compose run --rm php-cli php bin/console --env=test doctrine:database:drop --if-exists --force
	docker-compose run --rm php-cli php bin/console --env=test doctrine:database:create --if-not-exists
	docker-compose run --rm php-cli php bin/console --env=test doctrine:migrations:migrate --no-interaction
	docker-compose run --rm php-cli php bin/phpunit --colors=always --testsuite=functional --testdox

app-test-run-integration-tests:
	docker-compose run --rm php-cli php bin/console --env=test doctrine:database:drop --if-exists --force
	docker-compose run --rm php-cli php bin/console --env=test doctrine:database:create --if-not-exists
	docker-compose run --rm php-cli php bin/console --env=test doctrine:migrations:migrate --no-interaction
	docker-compose run --rm php-cli php bin/phpunit --colors=always --testsuite=integration --testdox