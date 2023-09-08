<?php
require('dbConfig.php');
class database extends DBConfig{
    
    protected $table = '';
    protected $statement = null;
    protected $limit = 50;
    protected $offset = 0;
    protected $condition = [];
    protected $operators = ['>','<','<=','>=', '!='];
    protected $tblJoin = '';

    public function __construct($config){
        parent::__construct($config);
    }
    
    public function table($tableName){
        $this->table = $tableName;
        return $this;
    }


    public function resetQuery(){
        $this->table = '';
        $this->limit = 20;
        $this->offset = 1;
    }


    public function LIMIT($limit){
        $this->limit = $limit;
        return $this;
    }


    public function OFFSET($offset){
        $this->offset = $offset;
        return $this;
    }

    public function join($tableName){
        $this->tblJoin = $tableName;
        return $this;
    }

    public function exeJoin(){
        $column1 = [];
        $sql1 = "SHOW COLUMNS FROM $this->table";
        $result1 = $this->connection->query($sql1);
        while($row = $result1->fetch_array()){
            $column1[] = $row['Field'];
        }

        $column2 = [];
        $sql2 = "SHOW COLUMNS FROM $this->tblJoin";
        $result2 = $this->connection->query($sql2);
        while($row = $result2->fetch_array()){
            $column2[] = $row['Field'];
        }
        $columnJoin = implode('', array_intersect($column1, $column2));

        $sql = "INNER JOIN $this->tblJoin ON $this->table.$columnJoin = $this->tblJoin.$columnJoin";

        return $sql;
    }

    // Hàm get điều kiện
    public function where($data = []){
        $this->condition = $data;
        return $this;
    }



    // Hàm xử lý điều kiện
    public function condition(){
        $keysValue = [];

        foreach($this->condition as $key => $value){
            if(is_array($value)){
                $keysValue[] = $this->table . '.' . $key . $value[0] . '?';
            }else{
                $keysValue[] = $this->table . '.' . $key . '=?';
            }
        }

        $setFields = implode(' AND ', $keysValue);

        $value = array_values($this->condition);

        $valueFields = [];
        foreach($value as $vl){
            if(is_array($vl)){
                $valueFields[] = $vl[1];
            }else{
                $valueFields[] = $vl;
            }
        }

        $cdtFields = [$setFields, $valueFields];
        // print_r($cdtFields);
        return $cdtFields;
    }



    public function get($data = []){
        // Lấy điều kiện
        $condition = $this->condition();
        list($keyCdt, $valueCdt) = $condition;
        //
        $join = $this->exeJoin();

        $ValueField = [];
        foreach($data as $value){
            if($value == '*'){
                $ValueField[] = '*';
            }else{
                $ValueField[] = $this->table . '.' . $value;
            }
        }
        $columnTb = implode(', ', $ValueField);

        if(empty($condition[0])){
            $where = null;
        }else{
            $where = "WHERE $keyCdt";
        }
        
        $sql = "SELECT $columnTb FROM $this->table $join $where LIMIT ? OFFSET ? ";
        
        // echo $sql;
        $this->statement = $this->connection->prepare($sql);

        $dataType = str_repeat('s', count($valueCdt)) . 'ii';
        
        array_push($valueCdt, $this->limit, $this->offset);
        
        $this->statement->bind_param($dataType, ...$valueCdt);

        $this->statement->execute();
        $this->resetQuery();

        $result = $this->statement->get_result();

        $returnObject = [];
        while($row = $result->fetch_object()){
            $returnObject[] = $row;
        }

        return $returnObject;
    }




    public function insert($data = []){
        $columnTb = implode(', ', array_keys($data));
        $fieldArr = implode(', ', array_fill(0, count($data), '?'));
        $values = array_values($data);

        $sql = "INSERT INTO $this->table($columnTb) VALUES($fieldArr)";
       
        $this->statement = $this->connection->prepare($sql);

        $this->statement->bind_param(str_repeat('s', count($data)), ...$values);

        $this->statement->execute();
        $this->resetQuery();

        return $this->statement->affected_rows;
    }



    public function update($data = []){
        // Lấy điều kiện
        $condition = $this->condition();
        list($keyCdt, $valueCdt) = $condition;
        //
        $keysValue = [];
        foreach($data as $key => $value){
            $keysValue[] = $key . '=?';
        }

        $setFields = implode(', ', $keysValue);
        
        $values = array_values($data);

        foreach($valueCdt as $vlCdt){
            $values[] = $vlCdt;
        }

        
        $sql = "UPDATE $this->table SET $setFields WHERE $keyCdt";

        $this->statement = $this->connection->prepare($sql);

        $dataType = str_repeat('s', count($data) + count($valueCdt));

        $this->statement->bind_param($dataType, ...$values);
        
        $this->statement->execute();
        $this->resetQuery();

        return $this->statement->affected_rows;
    }




    public function delete(){
        // Lấy điều kiện
        $condition = $this->condition();
        list($keyCdt, $valueCdt) = $condition;

        
        if(is_array($valueCdt)){
            $count = count($valueCdt);
        }else{
            $count = 1;
        }
        
        if(empty($condition[0])){
            $where = null;
        }else{
            $where = "WHERE $keyCdt";
        }
        
        $sql = "DELETE FROM $this->table $where";
        echo $sql;
        print_r($valueCdt);
        $this->statement = $this->connection->prepare($sql);
        $this->statement->bind_param(str_repeat('s', $count), ...$valueCdt);
        
        $this->statement->execute();
        $this->resetQuery();


        return $this->statement->affected_rows;
    }
}