<?php

use bng\System\Router;

require_once('../vendor/autoload.php');

Router::dispatch();

$nomes = ['joao', 'ana', 'carlos'];
$nome = "joão ribeiro";

printData($nomes);