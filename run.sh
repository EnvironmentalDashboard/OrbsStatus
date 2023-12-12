#!/bin/bash

docker stop ORBS_STATUS
docker rm ORBS_STATUS

docker run -dit -p 80:80 --restart unless-stopped \
  -v $(pwd)/:/var/www/html/ \
  -v /var/secret/:/var/secret/ \
  --name ORBS_STATUS orbs-status

docker exec ORBS_STATUS cron