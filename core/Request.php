<?php


namespace app\core;

class Request
{
    public array $pathVariables = [];

    public function findSlashes(string $path)
    {
        $pathExploded = explode('/', $path);
        foreach ($pathExploded as $key => $value) {
            if ($value == "") {
                unset($pathExploded[$key]);
            } else {
                $this->pathVariables[] = $value;
            }
        }
        // dadurch dass die Slashes bei explode(/) entfert werden, muss man das / wieder 
        // beim return wert wieder hinzufügen
        if (isset($pathExploded[1])) {
            return "/" . $pathExploded[1];
        }
        return "/";
    }

    public function getPath()
    {
        $path = $_SERVER["REQUEST_URI"] ?? '/';
        $postition = strpos($path, '?');
        if ($postition == false) {
            $path = $this->findSlashes($path);
            return $path;
        }
        $path = substr($path, 0, $postition);
        $path = $this->findSlashes($path);
        return $path;
    }

    public function isGet()
    {
        return $this->getMethod() == 'get' ?? false;
    }

    public function isPost()
    {
        return $this->getMethod() == 'post' ?? false;
    }

    public function isPut()
    {
        return $this->getMethod() == 'put' ?? false;
    }

    public function isDelete()
    {
        return $this->getMethod() == 'delete' ?? false;
    }

    public function isPatch()
    {
        return $this->getMethod() == 'patch' ?? false;
    }

    public static function getMethod()
    {
        return strtolower($_SERVER["REQUEST_METHOD"]);
    }
}
