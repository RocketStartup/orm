<?php

namespace RocketStartup\Orm;
use RocketStartup\Orm\MakerSql;

class ConnectPDO extends \PDO{
    private $dns            = '';
    // ...
    private $engine         = 'mysql';
    // mysql
    private $host           = '';
    // localhost
    private $port           = '3306';
    // 3006
    private $database         = '';
    // databasenome
    private $username       = '';
    // root
    private $password       = '';
    // 123456
    
    public function __construct(){

        $this->engine        =  \Orm::getInstance('Orm')->engine     ??  $this->engine;
        $this->host          =  \Orm::getInstance('Orm')->host       ??  $this->host;
        $this->port          =  \Orm::getInstance('Orm')->port       ??  $this->port;
        $this->database      =  \Orm::getInstance('Orm')->database   ??  $this->database;
        $this->username      =  \Orm::getInstance('Orm')->username   ??  $this->username;
        $this->password      =  \Orm::getInstance('Orm')->password   ??  $this->password;
        
        $this->connect();
    }

    public function connect(){
        $this->generateDns();
        try {
            parent::__construct(
                $this->dns,
                $this->username,
                $this->password
            );
            parent::setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return parent::class;
        } catch(\PDOException $e) {
            throw new \Exception('Unable to connect to database.');
        }
    }
    
    public function generateDns(){
        $this->dns  = $this->engine;
        $this->dns .= ':host='.$this->host;
        $this->dns .= ((!empty($this->port)) ? (';port=' . $this->port) : '');
        $this->dns .= ';dbname=' . $this->database;
    }

    public function __set($name,$value){
		if(method_exists($this,'set'.$name)){
			$n = 'set'.$name;
			$this->$n($value);
			return $this;
		}else if(property_exists($this,lcfirst($name))){
			$name=lcfirst($name);
			$this->$name=$value;
			return $this;
		}
	}
}