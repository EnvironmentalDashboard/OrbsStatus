@echo off

docker "stop" "ORBS_STATUS"
docker "rm" "ORBS_STATUS"
docker "run" "-dit" "-p" "80:80" "--restart" "unless-stopped" "--name" "ORBS_STATUS" "orbs-status"