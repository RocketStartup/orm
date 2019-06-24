<?php
// replace with file to your own project bootstrap

if(!defined('PATH_ROOT')){
	define('PATH_ROOT', dirname(__FILE__).  '/' );
}
if(file_exists(PATH_ROOT."vendor/astronphp/orm/src/schema/bootstrap.php")){
	require_once PATH_ROOT."vendor/astronphp/orm/src/schema/bootstrap.php";
}else{
	echo "error on cli-config.php"; exit();
}

use Doctrine\ORM\Tools\Console\ConsoleRunner;
return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);