#!/bin/bash

docker run -d --name orbs-status -p 8000:80 -v $(pwd):/var/www/html orb-status

