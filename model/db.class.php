<?php

class DB {

    private static $db = null;

    private function __construct() {

    }

    private function __clone() {

    }

    public static function getConnection() {
        if (DB::$db === null) {
            try {

                $user = 'root';
                $pass = '';

                DB::$db = new PDO("mysql: host=localhost; dbname=rooms; charset=utf8", $user, $pass);
                DB::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                exit('PDO Error: ' . $e->getMessage());
            }
        }
        return DB::$db;
    }

}

?>
