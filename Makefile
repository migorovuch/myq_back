container:
	docker-compose exec php bash

install-dependencies:
	docker-compose exec php composer install

update-dependencies:
	docker-compose exec php composer update

php-container:
	docker-compose exec php sh

db-create: ## DB Schema create
	docker-compose exec php bin/console doctrine:schema:create

db-update: ## DB Schema update
	docker-compose exec php bin/console doctrine:schema:update --force --dump-sql

cc: ## Clear cache
	docker-compose exec php bin/console c:c

router: ## Routes list
	docker-compose exec php bin/console debug:router

cs: ## Fix code styles
	docker-compose exec php php-cs-fixer fix

load-fixtures: ## Build the db, control the schema validity, load fixtures and check the migration status
	docker-compose exec php bin/console doctrine:cache:clear-metadata --env=test
	docker-compose exec php bin/console doctrine:database:drop --force
	docker-compose exec php bin/console doctrine:database:create --if-not-exists
	docker-compose exec php bin/console doctrine:schema:drop --force
	docker-compose exec php bin/console doctrine:schema:create
	docker-compose exec php bin/console doctrine:schema:validate
	docker-compose exec php bin/console doctrine:fixtures:load -n
	docker-compose exec php bin/console doctrine:migration:status

run-tests: ## run tests
	docker-compose exec php bin/phpunit -v
