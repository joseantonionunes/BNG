<?php 

namespace bng\Controllers;

class Main {

    public function index($id = null){
        
        echo "Estou dentro do controlador Main - index<br>";
        $nomes = [];
    }

    public function teste(){

        die('aqui no teste!');
    }
}