<?php
session_start();
require_once 'utils/handler.php';
require_once 'utils/database.php';
require_once 'config.php';

class ServerDataHandler extends BaseHandler{

    public function handle(): void
    {
        $s = $_SESSION;
        echo json_encode($s);
        $this->clear_trigger_js();
    }

    private function clear_trigger_js(): void{
        utils_start_session();
        unset($_SESSION[config\TRIGGER_JS::TRIGGER->value]);
    }
}

/**
 * Sends the given trigger js.
 * @param array $data
 * @return void
 */
function send_data(array $data): void{
    utils_start_session();
    $_SESSION[config\TRIGGER_JS::TRIGGER->value] = $data;
}
