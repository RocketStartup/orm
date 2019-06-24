<?php

namespace Astronphp\Orm;

class Handler{
    public  $return;
    private $parms      = array();
    private $commands   = array(
                                "orm:create-entity"         =>      'php '.PATH_ROOT.'vendor/astronphp/orm/src/schema/generate.php'
                          );
    
    public function __construct($p=array()){
        $this->parms = $p;
        $this->executeShell();
        return $this;
    }

    private function executeShell(){
        copy(PATH_ROOT.'vendor/astronphp/orm/src/schema/cli-config.php', PATH_ROOT.'cli-config.php');
        if(in_array($this->parms[1],array_flip($this->commands))==true){
            $this->return = shell_exec($this->commands[$this->parms[1]]);
        }else{
            array_shift($this->parms);
            $this->return = system('vendor/bin/doctrine '.implode(' ',$this->parms));
        }
        if(file_exists(PATH_ROOT."cli-config.php")) { unlink(PATH_ROOT."cli-config.php"); }
        
    }
}