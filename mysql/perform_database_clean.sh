#!/bin/bash

rm -rf /var/www/picnit/images/user/*
mysql -u root -p < create_database.mysql
