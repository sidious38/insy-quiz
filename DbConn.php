<?php

require_once 'Config.php';

class DbConn
{
    private PDO $pdo;

    public function __construct()
    {
        try {
            $this->pdo = new PDO("mysql:host=". Config::$dbHost .";dbname=". Config::$dbName .";charset=utf8",
                Config::$dbUser, Config::$dbPassword);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            /*
            Als Fehlermeldung ist lt. HRD nur eine allgemeine Meldung auszugeben,
            damit man keine Rückschlüsse auf die DB-Struktur ziehen kann.
            */
            die("<p><strong>Error:</strong> Fehler bei der DB-Verbindung aufgetreten!</p>");
        }
    }

    public function getPdo() :PDO
    {
        return $this->pdo;
    }

    public function executeQuery($query, $params=null) : ?PDOStatement {
        try {
            $stmt = $this->getPdo()->prepare($query);
            if (is_null($params)) {
                $stmt->execute();
            } else {
                $stmt->execute($params);
            }
            return $stmt;
        } catch (PDOException $e) {
            /*
            Als Fehlermeldung ist lt. HRD nur eine allgemeine Meldung auszugeben,
            damit man keine Rückschlüsse auf die DB-Struktur ziehen kann.
            */
            if ($e->getCode() == 23000) {
                echo "<p class='more-margin-bottom'><strong>Error:</strong> Eintrag bereits vorhanden oder fehlerhaft!</p>";
                return null;
            } else {
                die("<p class='more-margin-bottom'><strong>Error:</strong> Fehler bei der DB-Query aufgetreten!</p>");
            }
        }
    }
}