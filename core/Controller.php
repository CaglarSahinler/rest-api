<?php


namespace app\core;

class Controller extends Model
{

    public array $getVariablen = []; // ["categoryid" => 1,"name" => "Comics"]
    public array $getVariablenKeys = []; // ["categoryid","name"]
    public array $postVariablen = [];
    public array $postVariablenKeys = [];
    public string $sql = "";
    public $result = [];

    public string $limitClause = "";
    public string $orderByClause = "";
    public string $getColumsClause = "*";
    public string $joinClause = "";

    public function getData()
    {
        $this->getParams();

        $whereClause = $this->prepareWhere();
        // z.B. WHERE categoryid=:categoryid AND name=:name
        $this->setSql("SELECT $this->getColumsClause FROM $this->table $this->joinClause $whereClause $this->orderByClause $this->limitClause");
        $this->executeSql($this->getVariablen, $this->getVariablenKeys); // Model::executeSql ist in der Klasse Model drin
    }

    public function postData()
    {
        $this->getParams();
        $sqlColumns = implode(',', $this->postVariablenKeys);// name,status
        $sqlValues = implode(",", array_map(fn ($key) => ":$key", $this->postVariablenKeys));// :name,:status
        $this->setSql("INSERT INTO $this->table($sqlColumns) VALUES($sqlValues)");
        $this->executeSql($this->postVariablen, $this->postVariablenKeys);
    }

  
    public function loadGetData()
    {
        foreach ($_GET as $key => &$value) {
            $smallKey = strtolower($key);
            switch ($smallKey) {
                case "limit":
                    $this->addLimitToSql($value);
                    break;
                case "orderby":
                    $this->addOrderByToSql(["column" => $value]);
                    break;
                case "params":
                    $this->handleParamRequest($value);
                    break;
                default:
                    $this->getVariablen[$key] = $value;
                    $this->getVariablenKeys[] = $key;
            }
        }
    }

    public function addOrderByToSql($value)
    {
        if (is_array($value) && isset($value['column'])) {
            @$sortparameter =  $value['sortparameter'] ?? " ASC ";
            $this->orderByClause = " ORDER BY " . $value["column"] . " " . $sortparameter;
        } else {
            $this->response->setStatusCode(400);
            echo $this->response->message("OrderBy Anfrage wurde falsch gestellt!");
        }
    }

    public function setGetColumsClause(&$value)
    {
        if (is_array($value) && !empty($value)) {
            $this->getColumsClause = implode(",", $value);
        } else {
            $this->response->setStatusCode(400);
            echo $this->response->message("Get Anfrage wurde falsch gestellt!");
        }
    }

    public function addJoinToSql(&$joins, $joinType)
    {
        if (is_array($joins) && !empty($joins)) {
            foreach ($joins as $key => $value) {
                $this->joinClause .= " " . $joinType . " $key ON " . $value . " ";
                // LEFT JOIN author a ON a.authorid = book.authorid  LEFT JOIN category c ON c.categoryid = book.categoryid
            }
        } else {
            $this->response->setStatusCode(400);
            echo $this->response->message("$joinType Anfrage wurde falsch gestellt!");
        }
    }

    public function handleParamRequest($params)
    {
        if (is_array($params)) {
            foreach ($params as $key => &$value) {
                $key = strtolower($key);
                switch ($key) {
                    case 'limit':
                        $this->addLimitToSql($value);
                        break;
                    case 'orderby':
                        $this->addOrderByToSql($value);
                        break;
                    case 'get':
                        $this->setGetColumsClause($value);
                        break;
                    case 'leftjoin':
                        $this->addJoinToSql($value, "LEFT JOIN");
                        break;
                    case 'rightjoin':
                        $this->addJoinToSql($value, "RIGHT JOIN ");
                        break;
                    case 'innerjoin':
                        $this->addJoinToSql($value, "INNER JOIN ");
                        break;
                    case 'outerjoin':
                        $this->addJoinToSql($value, "OUTER JOIN ");
                        break;
                    default:
                        $this->response->setStatusCode(400);
                        $this->response->messageSum("Der Key: $key existiert nicht.");
                        break;
                }
            }
            echo $this->response->messageSendOut();
        } else {
            $this->response->setStatusCode(400);
            echo $this->response->message("Your given Params must be in an array!");
            exit;
        }
    }

    public function addLimitToSql(int &$value)
    {
        if (is_numeric($value)) {
            $value = intval($value);
            $this->limitClause = " LIMIT $value";
        } else {
            $this->limitClause = "";
        }
    }

    public function loadPostData()
    {
        foreach ($_POST as $key => $value) {
            $this->postVariablen[$key] = $value;
            $this->postVariablenKeys[] = $key;
        }
    }

    public function prepareWhere() // wandelt die Werte die wir in $this->getVariablenKeys haben zu einer validen SQL-Syntax um
    {
        $sql = [];
        //$sql = array_map(fn ($key) => "$key=:$key", $this->getVariablenKeys);

        foreach ($this->getVariablenKeys as $key) {
            $sql[] = "$this->table.$key=:$key";
        }

        $sql = implode(" AND ", $sql); // ["test","test2"] implode "," ==> test,test2 (array wird zu string)
        // ["test"] implode "," ==> test   (wenn nur ein wert in dem array ist wird kein zeichen hinzugefügt)
        if ($sql != "") {
            $sql = " WHERE " . $sql;
        }
        return $sql;
    }

    public function getParams()
    {
        if (sizeof($this->pathVariables) > 2) {
            $this->response->setStatusCode(400);
            echo $this->response->message("Too much values.");
            exit;
        } else if (sizeof($this->pathVariables) == 2) { // nach /category/* darf nur eine Zahl stehen, wenn nicht dann wird eine Fehlermeldung ausgegeben.
            if (!is_numeric($this->pathVariables[1])) {
                echo $this->response->message("Es dürfen nur Zahlen eigegeben werden");
                exit;
            }
            $_GET[$this->primaryKey] = $this->pathVariables[1];
            // $_GET["categoryid"] = 1
        }

        if (!empty($_GET)) {
            //$_GET["name"] = "Comic";
            $this->loadGetData();
            // /category?name=Comic
            // wenn auf die $_GET nicht leer ist dann wird der Inhalt von $_GET in die jeweiligen getVariablen
            // der Klasse hinzugefüht 
            // $_GET["name"] = "Comic"; ===> $this->getVariablen["name"] = "Comic"; etc
        }
        if (!empty($_POST)) {
            $this->loadPostData();
        }
    }

    public function setSql($sql)
    {
        $this->sql = $sql;
    }
}
