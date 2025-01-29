<?php

namespace bng\Models;

use bng\Models\BaseModel;

class Agents extends BaseModel{

    function get_total_agents() {

        $this->db_connect();
        return $this->query("SELECT COUNT(*) total FROM agents");
    
    }
}