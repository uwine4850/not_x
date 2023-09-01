<?php
require_once 'utils/handler.php';
require_once 'utils/database.php';
require_once 'handlers/profile/profile_utils.php';
require_once 'handlers/twig_functions.php';
require_once 'handlers/chat/chat_utils.php';

class ChatRoomHandler extends BaseHandler{
    use HandlerUtils;
    use \TwigFunc\GlobalFunc;
    use ConnectToAllTables;

    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->set_current_url_pattern();
        $this->db = new Database();
        $this->connect_to_all_tables($this->db);
        // Sending chat data to the client.
        $this->set_custom_js_data(array('room_id' => $_GET['room_id'], 'uid' => $_GET['user_g']['id']));
    }

    public function __destruct()
    {
        $this->db->close();
    }

    public function handle(): void
    {
        $this->enable_global_func($this->twig);
        $messages = get_room_messages($_GET['room_id'], $this->db_chat_messages, 0);
        $chat_room = get_chat_room($_GET['room_id'], $this->db_chat_rooms);
        $user_interlocutor = get_user_interlocutor($chat_room, $this->db_users);
        $this->render('chat/chat_room.html', array(
            'messages' => $messages,
            'user_interlocutor' => $user_interlocutor,
        ));
    }
}