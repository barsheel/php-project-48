gendiff:
	bin/gendiff

install:
	composer install
	composer validate

autoload:
	composer dump-autoload

lint:
	composer exec --verbose phpcbf -- --standard=PSR12 src tests bin
	composer exec --verbose phpcs -- --standard=PSR12 src tests bin
	composer exec phpstan -- analyze -c phpstan.neon

test:
	composer exec --verbose phpunit tests