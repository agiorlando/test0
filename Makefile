build:
	@echo 'Building all containers for development...'
	@rm -rf ./var/cache/* vendor/* ./var/sessions/* ./var/logs/* .php_cs_cache
	@docker-compose -f docker-compose-dev.yml build
	@echo 'Done'

up:
	@echo 'Pulling latest versions of all images...'
	@docker pull rknol/test0-mysql:latest
	@docker pull rknol/nginx-symfony:latest
	@docker pull rknol/test0:latest
	@echo 'Starting all containers...'
	@docker-compose up -d
	@echo 'Done'

dev:
	@echo 'Deleting any previously created application caches..'
	@rm -rf ./var/cache/* ./var/sessions/* ./var/logs/* .php_cs_cache
	@echo 'Pulling latest versions of all images...'
	@docker pull rknol/test0-mysql:latest
	@docker pull rknol/nginx-symfony:latest
	@echo 'Starting all containers for development...'
	@docker-compose -f docker-compose-dev.yml up -d
	@docker-compose -f docker-compose-dev.yml exec -T php-fpm composer install
	@echo 'Done'

stop-dev:
	@echo 'Stopping all containers...'
	@docker-compose -f docker-compose-dev.yml down
	@echo 'Done'

status:
	@docker-compose ps

status-dev:
	@docker-compose -f docker-compose-dev.yml ps

stop:
	@echo 'Stopping all containers...'
	@docker-compose stop
	@echo 'Done'

down: stop

destroy:
	@echo 'Destroying all containers...'
	@docker-compose down
	@echo 'Done'

shell:
	@docker-compose -f docker-compose-dev.yml exec php-fpm bash

style:
	@docker-compose -f docker-compose-dev.yml exec -T php vendor/bin/php-cs-fixer fix --dry-run --diff