#!/usr/bin/env php
<?php


chdir ( dirname(__FILE__) );

// admin module
print `php -f ./db-generatemodel.php admin admin insights__`;

// others
print `php -f ./db-generatemodel.php default base base__`;
print `php -f ./db-generatemodel.php default base customer__`;
print `php -f ./db-generatemodel.php default invoice article__`;
print `php -f ./db-generatemodel.php default invoice invoice__`;
print `php -f ./db-generatemodel.php default rental rental__`;
print `php -f ./db-generatemodel.php default calendar cal__`;
print `php -f ./db-generatemodel.php default webmail mailing__`;
print `php -f ./db-generatemodel.php default webmail webmail__`;
print `php -f ./db-generatemodel.php default filesync filesync__`;
print `php -f ./db-generatemodel.php default signrequest signrequest__`;
print `php -f ./db-generatemodel.php default project project__`;
print `php -f ./db-generatemodel.php default support support__`;


