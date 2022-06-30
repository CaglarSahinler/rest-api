<?php


namespace app\core;

class Model
{
    public \PDO $db;
    public string $dbName = "library-system";
    public string $dbUser = "phpmyadmin";
    public string $dbPassword = "1164";

    public function __construct()
    {
        try {
            $this->db = new \PDO("mysql:host=localhost;dbname=" . $this->dbName, $this->dbUser, $this->dbPassword);
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return json_encode(["message" => "Could not connect to database."]);
        }
    }

    public function executeSql(array $param = [], array $paramKeys = [])
    {
        $this->result = $this->execute($this->sql, $paramKeys, $param);
    }

    public function execute(string $sql, array $keys = [], array $values = [])
    {
        // $sql = "SELECT * FROM category WHERE categoryid=:categoryid AND name=:name";
        try {
            $stmt = $this->db->prepare($sql);

            if ($keys == []) {
                $stmt->execute();
                return $stmt;
            } // wenn $keys nicht leer ist dann wird kein return ausgeführt bedeute die Methode läuft weiter

            $arr = [];

            foreach ($values as $key => $value) {
                // $value = "1" $key = "categoryid"
                $arr[$key] = $value;
            }
            // $arr == assoc Array
            /*
                $arr = [
                    "categoryid" => 1,
                    "name" => "Comics"
                ]
             */

            if($stmt->execute($arr)){
                return $stmt;
            }
            
            return ["message" => "Something went wrong."];
        } catch (\PDOException $e) {
            $this->response->setStatusCode(400);
            return ["message" => $e->getMessage()];
        }
    }
}
