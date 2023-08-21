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
