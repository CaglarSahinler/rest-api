<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);

header("Access-Control-Allow-Methods: POST,GET,PUT,DELETE,PATCH"); //Alle Methoden werden akzeptiert
header('Access-Control-Allow-Origin: *'); // Man kann auch extern auf die Datei zugreifen
header('Content-Type: application/json; charset=utf-8'); // Alle Ausgaben werden in JSON formatiert

require_once(__DIR__ . "/vendor/autoload.php");
// test kommentar
// noch ein test
use app\controllers\Category;
// Da die Category in einem namespace definiert wurde, müssen wir den namespace Bezeichnung angeben
// also weil unter Category.php -> namespace app\controllers; ist
use app\controllers\Author;
use app\controllers\Book;

use app\core\Api;

$api = new Api();




$api->router->get("/", function () { //unter API.php ist die Klasse class Api. Da werden die Objekte z.B. $router instanziiert. Worauf wir hier zugreifen(aufrufen)
    return json_encode(["message" => "Nothing to find here!"]);
});

// mit add werden die zu wählenden Optionen definiert. und mit run() wird der Befehl aufgerufen.
// add fügt "hier category" in ein assoziatives Array. danach wird die methode ausgewählt -> siehe unter Router.php -> public resolve()
$api->router->add("/category", ['get', 'put', 'post', 'patch', 'delete'], [Category::class, "category"]);
//                  $api->router->routes["get"]["/category"] = [Category::class,"category"];

/* Category::class ist so wie wenn dieser Code übergeben wird ==> 

namespace app\controllers;

class Category{
    public function category(){
        echo "Category";
    }
}
*/
$api->router->add("/author", ['get', 'put', 'post', 'patch'], [Author::class, "author"]);
$api->router->add("/book", ['get', 'put', 'post', 'patch'], [Book::class, "book"]);


//var_dump($api->router->routes);

$api->run();
