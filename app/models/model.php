<?php

abstract class Model {
    private static ?PDO $pdo = null;

    protected function db(): PDO {
        if (self::$pdo === null) {
            $c = require __DIR__ . '/../config/database.php';
            $dsn = "mysql:host{$c['host']};dbname={$c['dbname']};charset={$c['charset']}";
            self::$pdo = new PDO($dsn, $c['user'], $c['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        return self::$pdo;
    }
}