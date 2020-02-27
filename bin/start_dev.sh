#!/bin/sh



curdir=$( dirname "$(readlink -f "$0")" )

cd ${curdir}/../www

php -S localhost:8000 ../bin/start_dev-php_router.php


