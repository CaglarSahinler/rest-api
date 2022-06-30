<?php


namespace app\core;

class Api
{
    public Router $router;
    public Request $request;
    public Response $response;

    public function __construct()
    {
        $this->router = new Router();
        $this->request = new Request();
        $this->response = new Response();
    }

    public function run()
    {
        echo $this->router->resolve();
    }
}
