<?php

function get_user_interlocutor(array $chat_room){
    if ($chat_room['user1'] == $_GET['user_g']['id']){
        return get_user_by_id($chat_room['user2']);
    }
    if ($chat_room['user2'] == $_GET['user_g']['id']){
        return get_user_by_id($chat_room['user1']);
    }
    return array();
}

function get_chat_room(int $room_id): array{
    $db_chat_rooms = new Database('chat_rooms');
    return $db_chat_rooms->all_where("id=$room_id")[0];
}

function get_user_chat_rooms(int $uid): array{
    $db_chat_rooms = new Database('chat_rooms');
    return $db_chat_rooms->all_where("user1=$uid OR user2=$uid");
}

function get_room_messages(int $room_id, int $msg_id=0): array{
    $db_chat_messages = new Database('chat_messages');
    $id_where = '';
    if ($msg_id){
        $id_where = "AND id < $msg_id";
    }
    $res = $db_chat_messages->query("SELECT * FROM (
                    SELECT * FROM `chat_messages` WHERE parent_chat=$room_id $id_where ORDER BY id DESC LIMIT 20 ) AS msg ORDER BY id;");
    return $res->fetch_all(MYSQLI_ASSOC);
}
