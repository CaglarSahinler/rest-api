<?php


namespace app\controllers;

use app\core\Controller;
use app\core\Response;
use app\core\Request;


//Autor
class Author extends Controller
{

    public string $table = "author";
    public Response $response;
    public Request $request;
    public array $pathVariables = [];
    public string $primaryKey = "authorid";

    public function author(Response $response, Request $request, array $pathVariables)
    {
        $this->response = $response;
        $this->request = $request;
        $this->pathVariables = $pathVariables;


        if ($this->request->isGet()) { // wenn auf die Seite mit der Methode Get zugegrifen wird ==> Request.php isGet()
            $this->getData(); // da wir die Klasse Controller extenden haben wir auf die Mtehode Controller::getData() zugriff
            if (is_array($this->result)) {
                echo $this->response->toJson($this->result);
                exit;
            }
            echo $this->response->toJson($this->result->fetchAll()); // das selbe gilt für $this->result
            exit;
        }

        if ($this->request->isPost()) {
            $this->postData();
            if (is_array($this->result)) {
                echo $this->response->toJson($this->result);
                exit;
            }
            echo $this->response->message("success");
            exit;
        }
    }
}
