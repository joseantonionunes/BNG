<?php

namespace bng\Controllers;

use bng\Controllers\BaseController;
use bng\Models\AdminModel;
use bng\Models\BaseModel;
use Monolog\Handler\PushoverHandler;

class Admin extends BaseController{

    // ===========================================================
    public function all_clients(){
        // check if session has a user with admin profile
        if(!check_session() || $_SESSION['nome']->profile != 'admin') {
            header('Location: index.php?ct=main&mt=index');
        }

        // get all clients from all agents
        $model = new AdminModel();
        $results = $model->get_all_clients();

        $data['user'] = $_SESSION['user'];
        $data['clients'] = $results->results;

        $this->view('layouts/html_header');
        $this->view('navbar', $data);
        $this->view('global_clients', $data);
        $this->view('footer');
        $this->view('layouts/html_footer');

    }
}