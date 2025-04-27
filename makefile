PROJECT_NAME=otomoto-olx-bot

up:
	docker-compose up -d

down:
	docker-compose down --remove-orphans --volumes

build:
	docker-compose build

restart:
	docker-compose down && docker-compose up -d

logs:
	docker-compose logs -f

bash-app:
	docker exec -it $(PROJECT_NAME)_app bash

bash-crawler:
	docker exec -it $(PROJECT_NAME)_crawler bash

migrate:
	docker exec -it $(PROJECT_NAME)_app php artisan migrate

composer-install:
	docker exec -it $(PROJECT_NAME)_app bash composer install

artisan:
	docker exec -it $(PROJECT_NAME)_app php artisan

email:
	docker exec -it $(PROJECT_NAME)_app php artisan offers:send-emails

crawl:
	docker-compose run crawler python crawler.py