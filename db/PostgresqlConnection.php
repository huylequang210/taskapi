<?php

namespace Db;

use \PDO;
use Db\SqlConnection;

class PostgresqlConnection implements SqlConnection {
    protected $db;

    public function connectDB(){
        extract(parse_url($_ENV['DATABASE_URL']));
        $path = ltrim($path, "/");
        $dsn = "pgsql:host=$host;port=$port;dbname=$path;user=$user;password=$pass";

        if($this->db === null) {
            try {
                $this->db = new PDO($dsn);
                $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch(\PDOException $e) {
                throw new \PDOException($e->getMessage(), (int)$e->getCode());
            }
        }
        return $this->db;
    }
}