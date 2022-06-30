<?php

namespace app\core;

class Response
{
    public array $summierteMessages = [];

    public function setStatusCode(int $statusCode)
    {
        return http_response_code($statusCode);
    }

    public function toJson(array $data)
    {
        return json_encode($data, JSON_PRETTY_PRINT); // JSON_PRETTY_PRINT mach die Ausgabe wie wenn man den <pre>-Tag verwendet
    }

    public function message(string $message)
    {
        return $this->toJson(["message" => $message]);
    }

    public function messageSum(string $message)
    {
        $this->summierteMessages[] = $message;
    }

    public function messageSendOut()
    {
        $endMessage = "";
        if (!empty($this->summierteMessages)) {
            foreach ($this->summierteMessages as &$value) {
                $endMessage .= $value . ";";
            }
            return $this->message($endMessage);
        }
    }
}
