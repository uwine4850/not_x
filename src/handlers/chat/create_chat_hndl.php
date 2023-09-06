<?php
require_once 'utils/handler.php';
require_once 'utils/database.php';
require_once 'handlers/server_data_hndl.php';
require_once 'handlers/chat/chat_utils.php';
require_once 'handlers/profile/profile_utils.php';

class CreateChatHandler extends BaseHandler{
    use ConnectToAllTables;

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

    private function post(): void{
        if ($_SERVER['REQUEST_METHOD'] != 'POST'){
            return;
        }
        $post_data = array();
        try {
            $post_data = validate_post_data(['chat_user2_id', 'first_message']);
        } catch (FormFieldNotExist $e) {
            return;
        }
        $post_data['user1'] = $_GET['user_g']['id'];
        $insert_data = array();
        try {
            $insert_data = array_to_db_assoc_array($post_data, array(
                FormDbField::make('user1', 'user1'),
                FormDbField::make('chat_user2_id', 'user2'),
            ));
        } catch (ArrayValueIsEmpty $e) {
            return;
        }

        // You can't create a chat room by yourself.
        if ($insert_data['user1'] == $insert_data['user2']){
            return;
        }

        if ($post_data['first_message'] == ''){
            $uid = $post_data['chat_user2_id'];
            $username = get_user_by_id($uid, $this->db_users)['username'];
            header("Location: /profile/$username");
            exit();
        }
        // If a chat room exists, open it. If it does not exist create and open it.
        $chat_room = $this->chat_exist($insert_data['user1'], $insert_data['user2']);
        if (!$chat_room){
            $new_chat = $this->db_chat_rooms->insert($insert_data);
            send_data(array(\config\TRIGGER_JS::CREATE_NEW_CHAT->value => array(
                'from_user_id' => $insert_data['user1'],
                'to_user_id' => $insert_data['user2'],
                'new_room_id' => $new_chat,
                'first_message' => $post_data['first_message'],
            )));
            $msg_data = $insert_data + $post_data;
            $msg_data['new_room_id'] = $new_chat;
            $this->save_first_message($msg_data);
            header("Location: /chat-room/$new_chat");
        } else{
            header("Location: /chat-room/$chat_room");
        }
    }

    /**
     * Saves the first message in a new chat.
     * @param array $data
     * @return void
     */
    private function save_first_message(array $data): void{
        $values = array();
        $values['room_id'] = $data['new_room_id'];
        $values['profile_user_id'] = $data['user1'];
        $values['msg'] = $data['first_message'];
        save_message($values, $this->db_chat_messages);
    }

    /**
     * Checking the existence of a chat room.
     * @param int $user1
     * @param int $user2
     * @return int
     */
    private function chat_exist(int $user1, int $user2): int{
        $room = $this->db_chat_rooms->all_where("user1=$user1 AND user2=$user2");
        if (empty($room)){
            return 0;
        }
        return $room[0]['id'];
    }

    public function handle(): void{
        $this->post();
    }
}
