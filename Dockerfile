FROM php:8.2-cli
COPY . /php-project-48
WORKDIR /php-project-48
RUN apt-get update && \
    apt-get install -y git unzip curl && \
    rm -rf /var/lib/apt/lists/*
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer
CMD ["php", "-S", "0.0.0.0:8000", "-t", "/php-project-48"]