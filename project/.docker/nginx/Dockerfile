FROM nginx:alpine

RUN apk add --no-cache openssl

RUN mkdir -p /etc/nginx/ssl \
    && openssl req -x509 -nodes -days 3650 -newkey rsa:2048 \
        -keyout /etc/nginx/ssl/server.key \
        -out /etc/nginx/ssl/server.crt \
        -subj "/C=BR/ST=State/L=City/O=SelfSigned/CN=localhost"

COPY .docker/nginx/default.conf /etc/nginx/conf.d/
