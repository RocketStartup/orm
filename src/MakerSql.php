<?php

namespace RocketStartup\Orm;
use RocketStartup\Orm\ConnectPDO;

/*
	OBS: Ao definir o setTable, todos os valores setados na última consulta serão limpos.

	#### EXEMPLO DE INSERT
		$sql->setTable('nome_da_tabela');
		$sql->setSet(array('nome'=>'Bob','sobrenome'=>'Marley'));
		$sql->Insert();

	#### EXEMPLO DE UPDATE
		$sql->setTable('nome_da_tabela');
		$sql->setWhere(array('id'=>123));
		$sql->setSet(array('nome'=>'Bob','sobrenome'=>'Marley'));
		$sql->Update();

	#### EXEMPLO DE DELETE
		$sql->setTable('nome_da_tabela');
		$sql->setWhere(array('id'=>123));
		$sql->Delete();

	#### EXEMPLO DE SELECT
		$sql->setTable('nome_da_tabela');
		$sql->setFields('id,nome,sobrenome');
		$sql->setWhere(array('id'=>123));
		$sql->Select();

		while($row=$sql->getRow()){
			echo $row['id'];
		}
	#### EXIBINDO ULTIMO SQL
		$sql->getLastQuery();

*/

class MakerSql extends ConnectPDO{
   
    private $insertId;
    private $table;
    private $fields;
    private $set;
    private $limit;
    private $where;
    private $orderBy;
    private $groupBy;
    private $records;
    private $lastQuery;
    private $query;
    private $test;
    private $lastError;
    private $error;
    private $allError;
    private $debugger=false;
    private $consult;
        


    public function __construct(){
        parent::__construct();        
    }


    //EXECUTA
    public function executeSQL($sql){
        //se for teste, exibe SQL e não executa;
        if($this->getError()!=''){ echo "Em:".$sql.'<br/><br/>'.utf8_decode($this->getError()); exit; }
        //salva última SQL realizada
        $this->setlastQuery($sql);
        //se for teste, exibe SQL e não executa;
        if($this->getTest()==true){ echo $this->getlastQuery(); exit; }
                
        $this->consult = \PDO::prepare($sql);
        try{
            $microtime=microtime(true);
            $this->consult->execute();
            //\Performace::getInstance('Timer')->register('SQL',$microtime,$sql.': total '.$this->consult->rowCount());
            //\Performace::getInstance('Timer')->register('SQL',microtime(true));
		    

            $this->setRecords($this->consult->rowCount());
            //salva na variavel query o objeto retornado do ADODB
            $this->setQuery($this->consult->queryString);

            return $this;
        } catch (\Exception $e) {
            $this->setLastError($e->getMessage());
            //armazenda erro na lista de erro $allError do tipo ARRAY
            if($this->getDebugger()==true){
                //exit();
            }
        }
    }

    // SELECT
    public function Select($executar=true){
        $q='SELECT ';
        $q.=($this->getFields()!=''?$this->getFields():'*');
        $q.=' FROM ';
        if($this->getTable()!=''){
            $q.=$this->getTable();
        }else{
            $this->setError('É necessário atribuir um valor ao método <i><b>getTable</b></i>');
        }
        //insere WHERE
        if($this->getWhere()!=''){
            $q.=' WHERE '.$this->getWhere();
        }

        //insere WHERE
        if($this->getGroupBy()!=''){
            $q.=' GROUP BY '.$this->getGroupBy();
        }

        //insere ORDER BY
        if($this->getOrderBy()!=''){
            $q.=' ORDER BY '.$this->getOrderBy();
        }
        //insere LIMIT
        if($this->getLimit()!=''){
            $q.=' LIMIT '.$this->getLimit();
        }
        if($executar==false){
            return $q;
        }else{
            return $this->executeSQL($q);
        }
    }

    // INSERIR
    public function Insert(){
        $q='INSERT INTO ';
        if($this->getTable()!=''){
            $q.=$this->getTable();
        }else{
            $this->setError('É necessário atribuir um valor ao método <i><b>getTable</b></i>');
        }
        //insere SET
        if($this->getSet()!=''){
            $q.=' SET ';
            $q.=$this->getSet();
        }else{
            $this->setError('É necessário atribuir um valor ao método <i><b>getSet</b></i>');
        }
        //insere WHERE
        if($this->getWhere()!=''){
            $q.=' WHERE '.$this->getWhere();
        }
        $insert=$this->executeSQL($q);
        //armazena ultimo id inserido no banco
        if($this->getLastError()==''){
            $inserted=$GLOBALS['CONN']->Insert_ID();
            $this->setInsertId($inserted);
        }

    }

    // INSERIR
    public function Update($executar=true){
        $q='UPDATE ';
        if($this->getTable()!=''){
            $q.=$this->getTable();
        }else{
            $this->setError('É necessário atribuir um valor ao método <i><b>getTable</b></i>');
        }
        $q.=' SET ';
        //insere SET
        if($this->getSet()!=''){
            $q.=$this->getSet();
        }else{
            $this->setError('É necessário atribuir um valor ao método <i><b>getSet</b></i>');
        }
        //insere WHERE
        if($this->getWhere()!=''){
            $q.=' WHERE '.$this->getWhere();
        }else{
            $this->setError('É necessário atribuir um valor ao <i><b>getWhere</b></i>');
        }
        //insere LIMIT
        if($this->getLimit()!=''){
            $q.=' LIMIT '.$this->getLimit();
        }

        $q=str_replace(array('"NULL"',"'NULL'"), 'NULL', $q);

        if($executar==false){
            return $q;
        }else{
            return $this->executeSQL($q);
        }
    }

    // INSERIR
    public function Delete(){
        $q='DELETE FROM ';
        if($this->getTable()!=''){
            $q.=$this->getTable();
        }else{
            $this->setError('É necessário atribuir um valor ao método <i><b>getTable</b></i>');
        }
        //insere WHERE
        if($this->getWhere()!=''){
            $q.=' WHERE '.$this->getWhere();
        }
        //insere LIMIT
        if($this->getLimit()!=''){
            $q.=' LIMIT '.$this->getLimit();
        }
        return $this->executeSQL($q);
    }



    
    //CONTROLE DE TABLE
    public function getRow(){
        return $this->consult->fetchAll();
    }
    //CONTROLE DE TABLE
    public function setInsertId($v=''){
        $this->insertId=$v;
    }
    //CONTROLE DE TABLE
    public function getInsertId(){
        return $this->insertId;
    }

    //CONTROLE DE TABLE
    public function setTable($v=''){
        $this->table=$v;
        return $this;
    }
    public function getTable(){
        return $this->table;
    }
    //CONTROLE DE COLUNAS
    public function setFields($v=''){
        $this->fields=$v;
        return $this;
    }
    public function getFields(){
        return $this->fields;
    }
    //CONTROLE DE SET
    public function setSet($v=''){
        if(is_array($v)){
            $q='';
            if(count($v)>0){
                foreach($v as $key=>$value){
                    $q.= "`{$key}` = '{$value}', ";
                }
                $q=substr($q, 0, -2);
            }
            $this->set=$q;
        }else{
            $this->set=$v;
        }
        return $this;
    }
    public function getSet(){
        return $this->set;
    }
    //CONTROLE DE LIMIT
    public function setLimit($v=''){
        $this->limit=$v;
        return $this;
    }
    public function getLimit(){
        return $this->limit;
    }
    //CONTROLE DE WHERE
    public function setWhere($v=''){
        if(is_array($v)){
            $q='';
            if(count($v)>0){
                foreach($v as $key=>$value){
                    $q.= " {$key}= '{$value}' AND";
                }
                $q=substr($q, 0, -3);
            }
            $this->where=$q;
        }else{
            $this->where=$v;
        }
        return $this;
    }
    public function getWhere(){
        return $this->where;
    }
    //CONTROLE DE ORDER
    public function setOrderBy($v=''){
        $this->orderBy=$v;
    }
    public function getOrderBy(){
        return $this->orderBy;
    }
    //CONTROLE DE ORDER
    public function setGroupBy($v=''){
        $this->groupBy=$v;
    }
    public function getGroupBy(){
        return $this->groupBy;
    }
    //CONTROLE DE QUERY
    public function setRecords($v=''){
        $this->records=$v;
    }
    public function getRecords(){
        return $this->records;
    }
    //CONTROLE DE QUERY
    public function setlastQuery($v=''){
        $this->lastQuery=$v;
    }
    public function getlastQuery(){
        return $this->lastQuery;
    }
    //CONTROLE DE QUERY
    public function setQuery($v=''){
        $this->query=$v;
    }
    public function getQuery(){
        return $this->query;
    }
    //CONTROLE DE QUERY
    public function setTest($v=false){
        $this->test=$v;
    }
    public function getTest(){
        return $this->test;
    }

    //CONTROLE DE LAST ERROR
    public function setLastError($v=''){
        $this->lastError=$v;
    }
    public function getLastError(){
        return $this->lastError;
    }
    //CONTROLE DE LAST ERROR
    public function setDebugger($v=''){
        $this->debugger=$v;
    }
    public function getDebugger(){
        return $this->debugger;
    }
    //CONTROLE DE ALL ERROR
    public function setError($v=''){
        $tmp=array();
        $tmp=$this->getError('array');
        $tmp[]=$v;
        $this->error=$tmp;
    }
    public function getError($t='text'){
        $tmp='';
        if(isset($this->error) && count($this->error)>0){
            for ($i=0; $i < count($this->error); $i++) {
                $tmp.='Atenção: '.$this->error[$i].'<br/>';
            }
        }
        if($t=='array'){
            return $this->error;
        }else{
            return $tmp;
        }
    }



}