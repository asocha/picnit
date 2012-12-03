picnit
======

Photo hosting website. Tell your friends!


Created by PhotoDolo:
Keifer Davis
Collin Dobmeyer
Ben Dupree
Rowdy Howell
Calvin Owens
Andrew Socha

Install instructions:
1) Unzip project into the /var/www/
	1a) The resulting root should be /var/www/picnit/index.php
2) Change directories to the /var/www/picnit/mysql folder
3) Run the ./perform_database_clean.sh script
	3a) This will reset the database and clear all images in the directory
4) Run the ./create_user.mysql mysql file in mysql
5) Website should be fully operational!
