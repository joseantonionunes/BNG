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

    // =======================================================
    public function export_clients_XLSX(){
        // check if session has a user with admin profile
        if (!check_session() || $_SESSION['user']->profile != 'admin'){
            header('Location: index.php');
        }

        // get all clients from all agents
        $model = new AdminModel();
        $results = $model->get_all_clients();
        $results = $results->results;

        // add header to collection
        $data[] = ['name', 'gender', 'brithdate', 'email', 'phone', 'interests', 'agent', 'created_at'];

        // place all clients in the $data collection
        foreach ($results as $client) {
            // remove the first property (id)
            unset($client->id);

            // add data as array (original $client is a stdClass object)
            $data[] = (array)$client;
        }

        // store the data into the XSLX file
        $filename = 'output_' . time() . '.xlsx';
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);
        $worksheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'dados');
        $spreadsheet->addSheet($worksheet);
        $worksheet->fromArray($data);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($filename) . '"');
        $writer->save('php://output');

        // logger
        logger(get_active_user_name() . " - fez download da lista de clientes para o ficheiro: " . $filename . " | total: " . count($data) - 1 . " registos.");
    }
}