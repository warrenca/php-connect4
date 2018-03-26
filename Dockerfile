FROM php:7.1.15-cli-jessie

COPY . /usr/src/connect4

WORKDIR /usr/src/connect4

RUN apt-get update && \
    apt-get install -y --no-install-recommends git zip

RUN curl --silent --show-error https://getcomposer.org/installer | php

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV IN_DOCKER 1

RUN php composer.phar install && php composer.phar test

CMD [ "php", "./main.php" ]