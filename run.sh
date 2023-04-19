#!/bin/bash
docker stop orbs-status
docker rm orbs-status

docker run -d --name orbs-status -p 8001:80 \
-v $(pwd):/var/www/html \
-v $(pwd)/php/php.ini-development:/usr/local/etc/php/php.ini-development \
-v $(pwd)/php/php.ini-production:/usr/local/etc/php/php.ini-production \
orb-status

