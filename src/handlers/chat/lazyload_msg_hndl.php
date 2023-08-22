<?php
require_once 'utils/handler.php';
require_once 'utils/database.php';
require_once 'handlers/chat/chat_utils.php';

class LoadMsg extends BaseHandler{
    private array $messages;

    public function __construct()
    {
        parent::__construct();
    }

    private function get(): void{
        if ($_SERVER['REQUEST_METHOD'] != 'GET'){
            return;
        }
        $msg_id = $_GET['msg_id'];
        $chat_room_id = $_GET['chat_room_id'];
        $this->messages = get_room_messages($chat_room_id, $msg_id);
    }

    public function handle(): void
    {
        $this->get();
        $this->render('includes/chat_messages.html', array(
            'messages' => $this->messages,
        ));
    }
}