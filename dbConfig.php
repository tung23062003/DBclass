<?php
class DBConfig{
    protected $host = '';
    protected $user = '';
    protected $pass = '';
    protected $name = '';
    protected $connection = null;
    public function __construct($config){
        $this->host = $config['host'];
        $this->user = $config['user'];
        $this->pass = $config['pass'];
        $this->name = $config['name'];

        $this->connect();
    }


    protected function connect(){
        $this->connection = new mysqli(
            $this->host,
            $this->user,
            $this->pass,
            $this->name
        );

        if($this->connection->connect_error){
            die('Connection error ' . $this->connection->connect_error);
        }
    }
}