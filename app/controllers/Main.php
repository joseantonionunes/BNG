<?php 

namespace bng\Controllers;
use bng\Controllers\BaseController;

class Main extends BaseController {

    public function index()
    {
        $data['nome'] = "joão";
        $data['apelido'] = "Ribeiro";
        $this->view('lauots/html_header.php');
        $this->view('home', $data);
        $this->view('layouts/html_footer');
    }

}