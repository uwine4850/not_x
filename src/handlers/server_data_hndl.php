<?php
session_start();
require_once 'utils/handler.php';
require_once 'utils/database.php';

class ServerDataHandler extends BaseHandler{

    public function handle(): void
    {
        echo json_encode($_SESSION);
    }
}
