FROM nginx

COPY ./docker/nginx/nginx.conf /etc/nginx/
COPY ./docker/nginx/symfony.conf /etc/nginx/conf.d/

RUN echo "upstream php-upstream { server php:9000; }" > /etc/nginx/conf.d/upstream.conf
