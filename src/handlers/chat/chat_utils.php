<?php
require_once 'config.php';

/**
 * Returns the conversation partner data of the selected chat.
 * @param array $chat_room Chat room data.
 * @param Database $db_users_instance An instance of the users table connection database.
 * @return array
 */
function get_user_interlocutor(array $chat_room, Database $db_users_instance): array{
    if ($chat_room['user1'] == $_GET['user_g']['id']){
        return get_user_by_id($chat_room['user2'], $db_users_instance);
    }
    if ($chat_room['user2'] == $_GET['user_g']['id']){
        return get_user_by_id($chat_room['user1'], $db_users_instance);
    }
    return array();
}

/**
 * Returns chat data by ID.
 * @param int $room_id Chat ID.
 * @param Database $db_chat_rooms_instance An instance of the chat_rooms table connection database.
 * @return array
 */
function get_chat_room(int $room_id, Database $db_chat_rooms_instance): array{
    return $db_chat_rooms_instance->all_where("id=$room_id")[0];
}

/**
 * Returns chat data by user ID.
 * @param int $uid User ID.
 * @param Database $db_chat_rooms_instance An instance of the chat_rooms table connection database.
 * @return array
 */
function get_user_chat_rooms(int $uid, Database $db_chat_rooms_instance): array{
    return $db_chat_rooms_instance->all_where("user1=$uid OR user2=$uid");
}

/**
 * Returns a certain number of messages starting with the oldest ones.
 * If there is a $msg_id parameter, the output starts with the ID of this message.
 * @param int $room_id Chat ID.
 * @param Database $db_chat_messages_instance An instance of the chat_messages table connection database.
 * @param int $msg_id Message ID.
 * @return array
 */
function get_room_messages(int $room_id, Database $db_chat_messages_instance, int $msg_id=0): array{
    $id_where = '';
    if ($msg_id){
        $id_where = "AND id < $msg_id";
    }
    $c = config\LOAD_MSG_COUNT;
    $res = $db_chat_messages_instance->query("SELECT * FROM (
                    SELECT * FROM `chat_messages` WHERE parent_chat=$room_id $id_where ORDER BY id DESC LIMIT $c ) AS msg ORDER BY id;");
    return $res->fetch_all(MYSQLI_ASSOC);
}

/**
 * Saves the message in the database.
 * @param array $values Client data about the message.
 * @param Database $db_chat_messages
 * @return void
 */
function save_message(array $values, Database $db_chat_messages): void{
    try {
        date_default_timezone_set('Europe/Kyiv');
        $values['time'] = date("Y-m-d H:i:s");
        $insert_values = array_to_db_assoc_array($values, array(
            FormDbField::make('room_id', 'parent_chat'),
            FormDbField::make('profile_user_id', 'user'),
            FormDbField::make('msg', 'text'),
            FormDbField::make('time', 'time'),
        ));
        $db_chat_messages->insert($insert_values);
    } catch (ArrayValueIsEmpty $e) {
    }
}