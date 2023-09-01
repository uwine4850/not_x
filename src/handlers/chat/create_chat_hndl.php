<?php
require_once 'utils/handler.php';
require_once 'utils/database.php';

class CreateChatHandler extends BaseHandler{
    private Database $db_chat_rooms;

    public function __construct()
    {
        parent::__construct();
        $this->db_chat_rooms = new Database('chat_rooms');
    }

    public function __destruct()
    {
        $this->db_chat_rooms->close();
    }

    private function post(): void{
        if ($_SERVER['REQUEST_METHOD'] != 'POST'){
            return;
        }
        $post_data = array();
        try {
            $post_data = validate_post_data(['chat_user2_id']);
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

        // If a chat room exists, open it. If it does not exist create and open it.
        $chat_room = $this->chat_exist($insert_data['user1'], $insert_data['user2']);
        if (!$chat_room){
            $new_chat = $this->db_chat_rooms->insert($insert_data);
            header("Location: /chat-room/$new_chat");
        } else{
            header("Location: /chat-room/$chat_room");
        }
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
