<?php
require_once "utils/middleware.php";
require_once "utils/database.php";

class ChatPermissionsMddl extends Middleware{

    public function run(): void    {
        if ($_GET["url_pattern"] == '/chat-room/{room_id}'){
            $db = new Database('chat_rooms');
            $uid = $_GET['user_g']['id'];
            $room_id = $_GET['room_id'];
            if (empty($db->all_where("id=$room_id AND (user1=$uid OR user2=$uid)"))){
                require_once 'utils/router.php';
                render_403();
                exit();
            }
        }
    }
}