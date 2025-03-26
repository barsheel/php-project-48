gendiff:
	bin/gendiff

install:
	composer install
	composer dump-autoload
	composer validate

lint:
	composer exec --verbose phpcbf -- --standard=PSR12 src
	composer exec --verbose phpcs -- --standard=PSR12 src
	composer exec phpstan -- analyze -c phpstan.neon
