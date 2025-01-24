<?php

namespace bng\Controllers;

abstract class BaseController{

    public function view(){

        require_once("../app/views/layouts/html_header.php");
        require_once("../app/views/layouts/html_footer.php");
    }
}