<?php

namespace Astronphp\Orm;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class ConnectDoctrine{
    // ...
    private $connect        = null;
    // mysql
    private $engine         = 'mysql';
    // localhost
    private $host           = '';
    // 3006
    private $port           = '3306';
    // databasenome
    private $database         = '';
    // root
    private $username       = '';
    // 123456
    private $password       = '';
    // true | false
    private $isDevMode      = false;
    // true | false
    private $dirEntity      = '';

    public function __construct($autoConnect=true){

        $this->engine           =  \Orm::getInstance('Orm')->engine     ??  $this->engine;
        $this->host             =  \Orm::getInstance('Orm')->host       ??  $this->host;
        $this->port             =  \Orm::getInstance('Orm')->port       ??  $this->port;
        $this->database         =  \Orm::getInstance('Orm')->database   ??  $this->database;
        $this->username         =  \Orm::getInstance('Orm')->username   ??  $this->username;
        $this->password         =  \Orm::getInstance('Orm')->password   ??  $this->password;
        $this->dirEntity        =  \Orm::getInstance('Orm')->dirEntity   ?? null;
        $this->isDevMode        =  \Orm::getInstance('Orm')->isDevMode   ?? false;
        $this->entityNamespace  =  \Orm::getInstance('Orm')->entityNamespace   ?? null;

        $this->connect          = $this->startConnect();
    }

    public function startConnect(){
        if(
            !empty($this->engine) &&
            !empty($this->host) &&
            !empty($this->port) &&
            !empty($this->database) &&
            !empty($this->username) &&
            !empty($this->password) &&
            file_exists($this->dirEntity)
        ){
            $config = Setup::createAnnotationMetadataConfiguration(
                array($this->dirEntity),
                $this->isDevMode
            );
            $config->addEntityNamespace($this->entityNamespace, 'Entity\''.$this->entityNamespace);
            // the connection configuration
            return EntityManager::create(
                    array(
                        'driver'   => $this->engine,
                        'host'     => $this->host,
                        'port'     => $this->port,
                        'user'     => $this->username,
                        'password' => $this->password,
                        'dbname'   => $this->database
                    ),
                    $config
                );
        }else{
            return null;
        }
    }

    public function connect() {
        return $this->connect;
    }
}