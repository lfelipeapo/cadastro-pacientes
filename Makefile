TAG=$(shell git log -1 --format=%h)

build:
	docker build -t php-nginx-web ./docker/
login:
	docker login
tag: login
	docker tag php-nginx-web lfelipeapo/php-nginx-web:$(TAG)
push: tag
	docker push lfelipeapo/php-nginx-web:$(TAG)
