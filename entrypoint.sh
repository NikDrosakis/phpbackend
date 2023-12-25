#!/bin/bash
# sed -i: "s/DOMAIN/localhost/g" /etc/nginx/sites-available/default
# sed -i: "s/DOMAINIP/localhost/g" /etc/nginx/sites-available/default
# sed -i: "s/ssl_certificate/#ssl_certificate/g" /etc/nginx/sites-available/default
# sed -i: "s/ssl_certificate_key/#ssl_certificate_key/g" /etc/nginx/sites-available/default
nginx -t
service nginx restart
service php8.1-fpm enable
service php8.1-fpm restart
docker-php-entrypoint php
# ENTRYPOINT ["nginx","-g","daemon off;"]

