<?php

	define('PATH_ROOT', explode('vendor/astronphp', str_replace('\\','/',__DIR__))[0]);
	// bootstrap.php
	require_once PATH_ROOT."/vendor/autoload.php";

	use Doctrine\ORM\Tools\Setup;
	use Doctrine\ORM\EntityManager;

	require_once "db-config.php";

	if (is_dir($dir)) {
	    $iterator = new \FilesystemIterator($dir);
	    if ($iterator->valid()) {
	        $di = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
	        $ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
	        foreach ( $ri as $file ) {
	            $file->isDir() ?  rmdir($file) : unlink($file);
	        }
	    }
	}
	//setando as configurações definidas anteriormente
	$config = Setup::createAnnotationMetadataConfiguration(array($dir), $isDevMode);
	//criando o Entity Manager com base nas configurações de dev e banco de dados
	$em = EntityManager::create($dbParams, $config);
	$em->getConfiguration()->setMetadataDriverImpl(
	    new \Doctrine\ORM\Mapping\Driver\DatabaseDriver(
	        $em->getConnection()->getSchemaManager()
	    )
	);
	$cmf = new \Doctrine\ORM\Tools\DisconnectedClassMetadataFactory();
	$cmf->setEntityManager($em);
	$metadata = $cmf->getAllMetadata();

	$generator = new \Doctrine\ORM\Tools\EntityGenerator();
	$generator->setUpdateEntityIfExists(true);
	$generator->setGenerateStubMethods(true);
	$generator->setGenerateAnnotations(true);
	$generator->setNumSpaces(5);
	$generator->generate($metadata, $dir);

	$types = array('php');
	$path = new DirectoryIterator($dir);

	$contador=0;
	foreach ($path as $fileInfo) {
		if( in_array( strtolower( $fileInfo->getExtension() ), $types ) ){
			$contador++;
		}
	}
	if($contador>0){
		echo "\e[0;30;42mGenerated ".($contador)." new classes in '".$dir."'\e[0m\n";
	}else{
		echo "No generated classes\n";
	}
