FROM nginx

COPY ./docker/nginx/nginx.conf /etc/nginx/
COPY ./docker/nginx/symfony.conf /etc/nginx/conf.d/
COPY ./. /var/www/project/

RUN echo "upstream php-upstream { server myq_php:9000; }" > /etc/nginx/conf.d/upstream.conf

