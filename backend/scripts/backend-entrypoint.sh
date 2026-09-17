#!/usr/bin/env bash
set -e

until php -r '
	try{
		new PDO(
			"mysql:host=urbanaut_mariadb;dbname=urbanaut",
			"root",
			"root",
			[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
		);
		exit(0);
	}catch(PDOException $e){
		exit(1);
	}
'; do
    echo "Waiting for database..."
    sleep 2
done

cd /var/www/html 
php -d include_path="/var/www/html/handlers" scripts/seed_admin.php
exec apache2-foreground
