<?php

namespace scr\model;
use \PDO;
use \PDOException;

class Database {

    /**
     * HOST de conexão com o banco de dados
     * @var String
     */
    const HOST = 'localhost';

    /**
     * Nome do usuário do banco de dados
     * @var String
     */
    const USER = 'root';

    /**
     * Senha de acesso ao banco de dados
     * @var String
     */
    const PASS = '1234';

    /**
     * Nome do banco de dados
     * @var String
     */
    const DBNAME = 'urlcutter';


    /**
     * Nome da tabela a ser manipulada
     * @var String 
     */
    private String $tableName;

    /**
     * Instancia de conexão com o banco de dados
     * @var PDO 
     */    
    protected PDO $conn;

    /**
     * Cria a instancia da conexão com o banco de dados
     * @param $table
     */
    function __construct(String $table = null)
    {
        $this->tableName = $table;        
        $this->setConnection();
    }

    /**
     * Retona a instancia de conexão com o banco de dados
     * @return PDO
     */
    public function getConn() 
    {
        return $this->conn;
    }

    /**
     * Método responsável por criar a conexão com o banco de dados
     */
    private function setConnection() 
    {
        try {
            $this->conn = new PDO("mysql:host=".self::HOST.";dbname=".self::DBNAME,self::USER, self::PASS);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//Configura o PDO para lançar uma exception caso ocorra um erro
        } catch(PDOException $ex) {
            die("ERROR: Ocorreu um problema interno! Tente novamente mais tarde.");
        }
    }


    /**
     * Método responsável por executar queries dentro do banco de dados
     * @param String $query
     * @param array $params
     * @return PDOStatement
     */
    public function execute($query, $params = []) 
    {
        try {
            $statement = $this->conn->prepare($query);            
            $statement->execute($params);
            return $statement;
        } catch(PDOException $ex) {
            echo $ex;
            die("ERROR: Ocorreu um problema interno! Tente novamente mais tarde.");
        }
    }

    /**
     * Método responsavel por inserir dados no banco de dados
     * @param array $values [field -> value]
     * @param integer id inserido   
     */
    public function insert($values) 
    {

        $fields = array_keys($values);
        $binds = array_pad([], count($fields), "?"); 

        $query = 'INSERT INTO '.$this->tableName.' ('.implode(',', $fields).') VALUES ('.implode(',', $binds).')';
        $this->execute($query, array_values($values));

        return $this->conn->lastInsertId(); //retorna o id inserido
    }

    /**
     * Retona uma consultado no banco de dados
     * @param String $field     
     * @param String $value
     * @param array $fields
     * @return PDOStatement
     */
    public function selectWhere($field, $value, $fields = []) {        
        $bindValue = array();        
        array_push($bindValue, $value);
        
        $query = 'SELECT '.implode(',', $fields).' FROM '.$this->tableName.' WHERE '.$field.' LIKE ?';    
        return $this->execute($query, array_values($bindValue));
    }
    
}