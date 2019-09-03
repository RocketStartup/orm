<?php
// bootstrap.php
require_once PATH_ROOT."vendor/autoload.php";

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once "db-config.php";

$config = Setup::createAnnotationMetadataConfiguration(array($dir), $isDevMode);
$entityManager = EntityManager::create($dbParams, $config);

try {
    $entityManager->getConnection()->connect();
} catch (\Exception $e) {
    echo 'Not connected Database '.$dbParams['host'].':'.$dbParams['port'].'.'.$dbParams['dbname'].PHP_EOL; exit;
}