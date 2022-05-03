#!/bin/bash 

bin/bash --login
NVM_DIR=/usr/local/nvm/.nvm
export NVM_DIR=/usr/local/nvm/.nvm
nvm install 15.14.0
nvm use 15
PATH="/usr/local/nvm/.nvm/versions/node/v15.14.0/bin/:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"
npm install
npm run prod
chmod -R 777 storage
chmod -R 777 public/images
php artisan passport:install
