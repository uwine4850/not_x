<?php

function get_user_interlocutor(array $chat_room, Database $db_users_instance){
    if ($chat_room['user1'] == $_GET['user_g']['id']){
        return get_user_by_id($chat_room['user2'], $db_users_instance);
    }
    if ($chat_room['user2'] == $_GET['user_g']['id']){
        return get_user_by_id($chat_room['user1'], $db_users_instance);
    }
    return array();
}

function get_chat_room(int $room_id, Database $db_chat_rooms_instance): array{
    return $db_chat_rooms_instance->all_where("id=$room_id")[0];
}

function get_user_chat_rooms(int $uid, Database $db_chat_rooms_instance): array{
    return $db_chat_rooms_instance->all_where("user1=$uid OR user2=$uid");
}

function get_room_messages(int $room_id, Database $db_chat_messages_instance, int $msg_id=0): array{
    $id_where = '';
    if ($msg_id){
        $id_where = "AND id < $msg_id";
    }
    $res = $db_chat_messages_instance->query("SELECT * FROM (
                    SELECT * FROM `chat_messages` WHERE parent_chat=$room_id $id_where ORDER BY id DESC LIMIT 20 ) AS msg ORDER BY id;");
    return $res->fetch_all(MYSQLI_ASSOC);
}
