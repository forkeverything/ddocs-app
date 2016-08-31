#!/bin/bash
now="$(date +'%d/%m/%Y/%r')"
gulp --production
git add .
git commit -m "DEPLOY LIVE AT: $now"
git push origin master

curl https://forge.laravel.com/servers/95640/sites/218391/deploy/http?token=xjsheQDswocGPSN4YDnczazkcOJNhPGMSkvHpXiB
