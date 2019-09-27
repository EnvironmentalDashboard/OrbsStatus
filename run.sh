#!/bin/bash

docker run -d --name orb-update -p 8000:80 -v $(pwd):/var/www/html orb-status

