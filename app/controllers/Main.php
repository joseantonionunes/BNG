<?php

namespace bng\Controllers;

use bng\Controllers\BaseController;
use bng\Models\Agents;

ini_set('memory_limit', '712M');

class Main extends BaseController
{
    public function index()
    {
        // check if there is no active user in session
        if (!check_session()) {
            $this->login_frm();
            return;
        }

        $data['user'] = $_SESSION['user'];

        $this->view('layouts/html_header');
        $this->view('navbar', $data);
        $this->view('homepage', $data);
        $this->view('footer');
        $this->view('layouts/html_footer');
    }

    // ===========================================================
    // LOGIN
    public function login_frm()
    {

        // check if there is already a user in the session
        static $call_count = 0;
        $call_count++;

        // check if there is already a user in the session
        if (!check_session() && $call_count < 5) { // Adjust the limit as needed
            $this->index();
            return;
        }

        //check if there are arrars (after login_submit)
        $data = [];
        if (!empty($_SESSION['validation_errars'])) {

            $data['validation_errors'] = $_SESSION['validation_errors'];
            unset($_SESSION['validation_errors']);
        }

        // check if there was an invalid login
        if (!empty($_SESSION['server_errar'])) {

            $data['server_error'] = $_SESSION['server_error'];
            unset($_SESSION['server_error']);
        }

        // display login form
        $this->view('layouts/html_header');
        $this->view('login_frm', $data);
        $this->view('layouts/html_footer');
    }

    // ===========================================================
    public function login_submit()
    {

        // check if there is aiready an active session
        if (check_session()) {
            $this->index();
            return;
        }

        // check if there was a post request
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->index();
            return;
        }

        // form validation
        $validation_errors = [];
        if (empty($_POST['text_username']) || empty($_POST['text_password'])) {
            $validation_errors[] = "Username e password são obrigatorios.";
        }

        // get form data
        $username = $_POST['text_username'];
        $password = $_POST['text_password'];

        // check if username is valid email and between 5 and 50 chars
        if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $validation_errors[] = 'O username tem que ser um email valido';
        }

        // check if username is between 5 and 50 chars
        if (strlen($username) < 5 || strlen($username) > 50) {

            $validation_errors[] = 'O username deve ter entre 5 e 50 caracteres';
        }

        // check if password is valid
        if (strlen($password) < 6 || strlen($password) > 12) {

            $validation_errors[] = 'A password deve ter entre 6 e 12 caracteres';
        }


        // check if there are validation errors
        if (!empty($validation_errors)) {
            $_SESSION['validation_errors'] = $validation_errors;
            $this->login_frm();
            return;
        }



        $model = new Agents();
        $results = $model->check_login($username, $password);
        if (!$results['status']) {

            // Logger
            logger("$username - Login inválido", 'error');

            // invalid login
            $_SESSION['server_error'] = 'Login invalido';
            $this->login_frm();
            return;
        }

        // logger
        logger("$username - Login com sucesso");

        // load user information to the session
        $results = $model->get_user_data($username);

        // add user to session
        $_SESSION['user'] = $results['data'];

        echo 'OK';

        // update the last login
        $results = $model->set_user_last_login($_SESSION['user']->id);

        // go to main page

    }

    // ===========================================================
    public function logout()
    {

        // disable direct access to logout
        if (!check_session()) {
            $this->index();
            return;
        }

        // Logger
        logger($_SESSION['user']->name . ' - fez logout');

        // clear user from session
        unset($_SESSION['user']);

        // go to index (login form)
        $this->index();
    }

    // ===========================================================
    public function change_password_frm() {
        if(!check_session()){
            $this->index();
            return;
        }

        $data['user'] = $_SESSION['user'];

        // check for validation errors
        if(!empty($_SESSION['validation_errors'])){
            $data['validation_errors'] = $_SESSION['validation_errors'];
            unset($_SESSION['validation_errors']);
        }

        // check for server errors
        if (!empty($_SESSION['server_errors'])){
            $data['server_errors'] = $_SESSION['server_errors'];
            unset($_SESSION['server_errors']);
        }

        $this->view('layouts/html_header');
        $this->view('navbar', $data);
        $this->view('profile_change_password_frm', $data);
        $this->view('footer');
        $this->view('layouts/html_footer');

    }

    // ===========================================================
    public function change_password_submit() {
        if(!check_session()){
            $this->index();
            return;
        }

        // check if there was a post request
        if($_SERVER['REQUEST_METHOD'] != 'POST'){
            $this->index();
            return;
        }

        // validation errors
        $validation_errors = [];

        // check if the input fields are filled
        if(empty($_POST['text_current_password'])){
            $validation_errors[] = "password atual é de preenchimento obrigatório.";
            $_SESSION['validation_errors'] = $validation_errors;
            $this->change_password_frm();
            return;
        }

        if(empty($_POST['text_new_password'])){
            $validation_errors[] = "A repetição da nova password é de preenchimento obrigatório.";
            $_SESSION['validation_errors'] = $validation_errors;
            $this->change_password_frm();
            return;
        }

        // get the input values
        $current_password = $_POST['text_current_password'];
        $new_password = $_POST['text_new_password'];
        $repeat_new_password = $_POST['text_repeat_new_password'];

        // check if all passwords have more than 6 and less than 12 characters
        if(strlen($current_password < 6 || strlen($current_password) > 12)){
            $_SESSION['validation_errors'] = $validation_errors;
            $this->change_password_frm();
            return;
        }

        if(strlen($new_password < 6 || strlen($new_password) > 12)){
            $validation_errors[] = "A nova password deve ter entre 6 e 12 caracteres.";
            $_SESSION['validation_errors'] = $validation_errors;
            $this->change_password_frm();
            return;
        }

        if(strlen($repeat_new_password < 6 || strlen($new_password) > 12)){
            $validation_errors[] = "A nova password deve ter entre 6 e 12 caracteres.";
            $_SESSION['validation_errors'] = $validation_errors;
            $this->change_password_frm();
            return;
        }

        if(strlen($repeat_new_password < 6 || strlen($repeat_new_password) > 12)){
            $validation_errors[] = "A repetição da nova password deve ter entre 6 e 12 caracteres.";
            $_SESSION['validation_errors'] = $validation_errors;
            $this->change_password_frm();
            return;
        }

        //check if all password heve, at least one upper, one lower and one digit

        // use positive look ahead
        if(!preg_match("/(?=.*[a-z])(?=.*[A-Z])/", $current_password)){
            $validation_errors[] = "A password atual deve ter, pelo menos, uma maiúscula, uma minúscula e um digito.";
            $_SESSION['validation_errors'] = $validation_errors;
            $this->change_password_frm();
            return;
        }

        if(!preg_match("/(?=.*[a-z])(?=.*[A-Z])/", $new_password)) {
            $validation_errors[] = "A nova password deve ter, pelo menos, uma maiúscula, uma minúscula e um digito.";
            $_SESSION['validation_errors'] = $validation_errors;
            $this->change_password_frm();
            return;
        }
        if(!preg_match("/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])/", $repeat_new_password)){
            $validation_errors[] = "A nova password deve ter, pelo menos, uma maiúscula, uma minúscula e um digito.";
            $_SESSION['validation_errors'] = $validation_errors;
            $this->change_password_frm();
            return;
        }

        // check if the new password and repeat new password are equal values
        if($new_password != $repeat_new_password){
            $validation_errors[] = "A nova password e a sua repetição não são iguais.";
            $_SESSION['validation_errors'] = $validation_errors;
            $this->change_password_frm();
            return;
        }

        // check if the current password is equal to the database
        $model = new Agents();
        $results = $model->check_current_password($current_password);

        // check if the current password is correct
        if(!$results['status']) {
            // current password does not match the one existing in the database
            $server_errors[] = "A password atual não está correta.";
            $_SESSION['server_errors'] = $server_errors;
            $this->change_password_frm();
            return;
        }

        // form data is ok. Updates the password in the database
        $model->update_agent_password($new_password);

        // logger
        $username = $_SESSION['user']->name;
        logger("$username - password alternada com sucesso no perfil de utilizador.");

        // show view with success information
        $data['user'] = $_SESSION['user'];
        $this->view(['layouts/html_header']);
        $this->view('navbar', $data);
        $this->view('profile_chenge_password_success');
        $this->view('footer');
        $this->view('layouts/html_footer');

    }

}
