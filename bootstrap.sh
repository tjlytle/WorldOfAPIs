#!/usr/bin/env bash
export DEBIAN_FRONTEND=noninteractive

apt-get update
apt-get install -y php5 php5-dev php5-cli apache2 php5-curl php-pear php5-xdebug php5-intl make

# Dev dependencies
apt-get install -y git-core curl
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

rm -rf /var/www/html
ln -fs /vagrant/public /var/www/html

cp /vagrant/000-default.conf /etc/apache2/sites-available/000-default.conf

a2enmod rewrite
service apache2 restart
