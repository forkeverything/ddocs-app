#!/bin/bash
now="$(date +'%d/%m/%Y/%r')";
gulp --production;
git add .;
git commit -m "DEPLOY LIVE AT: $now";
git push origin master;
echo "pushed";
curl https://forge.laravel.com/servers/106072/sites/248444/deploy/http?token=snqtSTzbivoxsjHxUentFgD3F4sE6UyNE1bg1FjI

