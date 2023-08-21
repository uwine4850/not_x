<?php
require_once 'utils/handler.php';
require_once 'utils/database.php';

class LoadMsg extends BaseHandler{
    private Database $db_chat_messages;
    private array $messages;

    public function __construct()
    {
        parent::__construct();
        $this->db_chat_messages = new Database('chat_messages');
    }

    private function get(): void{
        if ($_SERVER['REQUEST_METHOD'] != 'GET'){
            return;
        }
        $msg_id = $_GET['msg_id'];
        $chat_room_id = $_GET['chat_room_id'];
        $this->messages = $this->db_chat_messages->all_where("parent_chat=$chat_room_id AND id > $msg_id", 5);
    }

    public function handle(): void
    {
        $this->get();
        $this->render('includes/chat_messages.html', array(
            'messages' => $this->messages,
        ));
    }
}