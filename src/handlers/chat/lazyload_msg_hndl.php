<?php
require_once 'utils/handler.php';
require_once 'utils/database.php';
require_once 'handlers/chat/chat_utils.php';

class LoadMsg extends BaseHandler{
    use ConnectToAllTables;

    private array $messages;
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = new Database();
        $this->connect_to_all_tables($this->db);
    }

    public function __destruct()
    {
        $this->db->close();
    }

    private function get(): void{
        if ($_SERVER['REQUEST_METHOD'] != 'GET'){
            return;
        }
        $msg_id = $_GET['msg_id'];
        $chat_room_id = $_GET['chat_room_id'];
        $this->messages = get_room_messages($chat_room_id, $this->db_chat_messages, $msg_id);
    }

    public function handle(): void
    {
        $this->get();
        $this->render('includes/chat_messages.html', array(
            'messages' => $this->messages,
        ));
    }
}