<?php

namespace App\apps\Foo;

use PDO;

class Test
{
    private PDO $mysql;
    private array $resultList = ['normal', 'illegal', 'failed', 'success'];
    public function __construct()
    {
        $this->mysql = new PDO('mysql:host=172.24.0.3;port=3306;dbname=mysql', 'root', 'root');
    }
    private function fill() : void
    {
        $query = "INSERT INTO `tests` (script_name, start_time, end_time, result) VALUES (:script_name, :start_time, :end_time, :result)";
        $params = [
            ':script_name' => str_repeat("f", random_int(10, 25)),
            ':start_time' => time(),
            ':end_time' => time() + random_int(1000, 10000),
            ':result' => $this->resultList[array_rand($this->resultList)]
        ];
        $stmt = $this->mysql->prepare($query);
        $stmt->execute($params);
    }
    public function get() : array|false
    {
        $stmt = $this->mysql->query("SELECT * FROM `tests` WHERE result IN ('normal', 'success')");
        return $stmt->fetchAll();
    }
}