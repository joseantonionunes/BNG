<?php

namespace bng\Controllers;

use bng\Controllers\BaseController;
use bng\Models\Agents;

class Main extends BaseController
{
    public function index()
    {
        // check if there is no active user in session
        if(!check_session()){
            $this->login_frm();
            return;
        }

        $this->view('layouts/html_header');
        echo '<h3 class="text-white text-center">Olá Mundol</h3>';
        $this->view('layouts/html_footer');
        
    }

    // LOGIN
    public function login_frm(){

        // check if there is already a user in the session
        if(!check_session()){

            $this->index();
            return;
        }

        //check if there are arrars (after login_submit)
        $data = [];
        if(!empty($_SESSION['validation_errars'])){

            $data['validation_errors'] = $_SESSION['validation_errors'];
            unset($_SESSION['validation_errors']);
        }

        // display login form
        $this->view('layouts/html_header');
        $this->view('login_frm', $data);
        $this->view('layouts/html_footer');
    }

    public function login_submit(){

        // check if there is aiready an active session
        if(check_session()){
            $this->index();
            return;
        }

        // check if there was a post request
        if($_SESSION['REQUEST_METHOD'] != 'POST'){

            $this->index();
            return;
        }

        // form validation
        $validation_errors = [];
        if(empty($_POST['text_username']) || empty($_POST['text_password'])){
            $validation_errors[] = "Username e password são obrigatorios.";
        }

        // get form data
        $username = $_POST['text_username'];
        $password = $_POST['text_password'];

        // check if username is valid email and between 5 and 50 chars
        if(!filter_var($username, FILTER_VALIDATE_EMAIL)){

            $validation_errors[] = 'O username tem que ser um email valido';
        }

        // check if username is between 5 and 50 chars
        if(strlen($username) < 5 || strlen($username) > 50){

            $validation_errors[] = 'O username deve ter entre 5 e 50 caracteres';
        }

        // check if password is valid
        if(strlen($password) < 6 || strlen($password) > 12){

            $validation_errors[] = 'A password deve ter entre 6 e 12 caracteres';
        }


        // check if there are validation errors
        if(!empty($validation_errors)){
            $_SESSION['validation_errors'] = $validation_errors;
            $this->login_frm();
            return;
        }

        

        $model = new Agents();
        $results = $model->check_login($username, $password);
        if($results['status']){
            echo 'OK';
        } else {
            echo 'NOK';
        }
        
    }
}