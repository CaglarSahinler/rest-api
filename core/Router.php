<?php


namespace app\core;


class Router
{
    public array $routes = [];
    public Request $request;
    public Response $response;

    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
    }
    /*
        $methods = ['get','put','post','delete'];
        $method = z.b get
    */
    
    public function add(string $route, array $methods, $callback)
    {
        foreach ($methods as $method) {      // die klasse und die Methode dieaufgerufen werden soll
            // get      /category   [Category::class,"category"]
            $this->routes[$method][$route] = $callback;
        }
    }

    public function get(string $route, $callback)
    {
        $this->routes["get"][$route] = $callback;
    }
    public function post(string $route, $callback)
    {
        $this->routes["post"][$route] = $callback;
    }
    public function put(string $route, $callback)
    {
        $this->routes["put"][$route] = $callback;
    }
    public function delete(string $route, $callback)
    {
        $this->routes["delete"][$route] = $callback;
    }

    public function resolve()
    {
        $path = $this->request->getPath(); // /category
        $methode = $this->request->getMethod(); // methode => get
        // get      category       index:      0             1
        $callback = $this->routes[$methode][$path] ?? false; // [Category::class,"category"]
        if ($callback == false) {
            $this->response->setStatusCode(400);
            return $this->response->message("This Route is not valid."); // Die Methode message() ist in Response.php
        }
        if (is_array($callback)) {
            // Category::class == new Category();
            $callback[0] = new $callback[0]();
            // $callback = [Object.Category(),"category"]
        }
        //var_dump($callback);
        //$callback[0]->category(); nimmt das Object und führt die Methode die im $callback[1] steht aus
        return call_user_func($callback, $this->response, $this->request, $this->request->pathVariables);
    }
}
