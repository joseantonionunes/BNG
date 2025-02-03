<?php

namespace bng\Models;

use bng\System\Database;

abstract class BaseModel
{
    public $db;
    // ===========================================================
    public function db_connect()
    {
        $options = [
            'host' => MYSQL_HOST,
            'database' => MYSQL_DATABASE,
            'username' => MYSQL_USERNAME,
            'password' => MYSQL_PASSWORD
        ];
        $this->db = new Database($options);
    }

    // ===========================================================
    public function query($sql = "", $params = [])
    {
        return $this->db->execute_query($sql, $params);
    }

    // ===========================================================
    public function non_query($sql = "", $params = [])
    {
        return $this->db->execute_non_query($sql, $params);
    }

    // ===========================================================
    public function set_user_last_login($id){
        // updates the user's last login
        $params = [
            ':id' => $id
        ];
        $this->db_connect();
        $results = $this->non_query(
            "UPDATE agents SET " .
            "last_login = NON() " .
            "WHERE id = :id" , $params
        );
        return $results;
    }
}