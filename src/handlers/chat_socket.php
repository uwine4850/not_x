<?php
session_start();
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\MessageComponentInterface;

require_once 'utils/database.php';
require_once 'handlers/profile/profile_utils.php';
require_once 'vendor/autoload.php';
require_once 'utils/utils.php';
require_once 'config.php';
require_once 'handlers/chat/chat_utils.php';

class Chat implements MessageComponentInterface {
    use ConnectToAllTables;

    protected \SplObjectStorage $clients;
    private array $clients_uid;
    protected array $rooms;
    private array $roomUsers = array();
    private Database $db;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->rooms = [];
        $this->db = new Database();
        $this->connect_to_all_tables($this->db);
    }

    public function __destruct()
    {
        $this->db->close();
    }

    /**
     * Returns the ID of the conversation partner in the current chat.
     * @param int $room_id Chat ID.
     * @param int $send_msg_uid The ID of the user from whom the message came.
     * @return int
     */
    private function get_interlocutor_id(int $room_id, int $send_msg_uid): int{
        $u2 = $this->db_chat_rooms->all_where("id=$room_id AND user1=$send_msg_uid");
        if (!empty($u2)){
            return $u2[0]['user2'];
        }
        $u1 = $this->db_chat_rooms->all_where("id=$room_id AND user2=$send_msg_uid");
        if (!empty($u1)){
            return $u1[0]['user1'];
        }
        return 0;
    }

    /**
     * Deletes a record of the number of messages in the current chat.
     * @param int $room_id Chat ID.
     * @return bool
     */
    private function delete_msgs_count(int $room_id, int $auth_uid): bool{
        $id = $this->db_chat_messages_notification->all_where("room_id=$room_id AND user=$auth_uid");
        if (!empty($id)){
            $this->db_chat_messages_notification->delete($id[0]['id']);
            return true;
        }
        return false;
    }

    public function onOpen(\Ratchet\ConnectionInterface $conn):void {
        $this->clients->attach($conn);
    }

    public function onMessage(\Ratchet\ConnectionInterface $from, $msg):void {
        $data = json_decode($msg, true);
        if (!$data){
            return;
        }

        // Registering the user in the room.
        if ($data['action'] === \config\WS_ACTIONS_CHAT::JOIN_CHAT_ROOM->value) {
            $join_data = array();
            $room_id = $data['room_id'];
            $this->rooms[$room_id][$from->resourceId] = $from;
            $this->clients_uid[$from->resourceId] = $data['auth_uid'];
            $is_del = $this->delete_msgs_count($data['room_id'], $data['auth_uid']);

            // decrement messages count
            if ($is_del){
                $join_data['action'] = \config\WS_ACTIONS_CHAT::DECREMENT_CHAT_ROOM_MSG_COUNT->value;
                $join_data['decrement'] = true;
            }
            $from->send(json_encode($join_data));
        }

        // Checking the uniqueness of the user id.
        if ($data['action'] === \config\WS_ACTIONS_CHAT::GENERATE_CHAT_ID->value){
            $chat_user_id = $data['chat_user_id'];
            $room_id = $data['room_id'];
            $this->roomUsers[$chat_user_id] = $room_id;
        }

        // Sending a message to the client.
        if ($data['action'] === \config\WS_ACTIONS_CHAT::SEND_MSG->value){
            $room_id = $data['room_id'];
            save_message($data, $this->db_chat_messages);
            $profile_user_id = $data['profile_user_id'];
            $interlocutor_id = $this->get_interlocutor_id($room_id, $profile_user_id);
            $data['interlocutor_id'] = $interlocutor_id;
            $data['username'] = $this->db_users->all_where("id=$profile_user_id")[0]['username'];
            foreach ($this->rooms[$room_id] as $client) {
                $client->send(json_encode($data));
            }
            $this->send_message_notification($room_id, $profile_user_id);
        }
    }

    /**
     * Deletes the given user in the chat when he/she leaves.
     * @param int $resourceId
     * @return void
     */
    private function leave_client(int $resourceId): void{
        $room_id = $this->roomUsers[$this->clients_uid[$resourceId]];
        unset($this->rooms[$room_id][$resourceId]);
        unset($this->roomUsers[$this->clients_uid[$resourceId]]);
    }

    /**
     * The conversation partner is notified of a new message if he/she is not currently in the chat.
     * If the conversation partner is in the chat room, no notification is sent.
     * @param int $room_id Chat ID.
     * @param int $profile_user_id The ID of the user who is sending the message.
     * @return void
     */
    private function send_message_notification(int $room_id, int $profile_user_id): void{
        $chat = $this->db_chat_rooms->all_where("id=$room_id");
        $interlocutor_id = 0;
        if (!empty($chat)){
            if ($chat[0]['user1'] == $profile_user_id){
                $interlocutor_id = $chat[0]['user2'];
            }
            if ($chat[0]['user2'] == $profile_user_id){
                $interlocutor_id = $chat[0]['user1'];
            }
        }
        if ($interlocutor_id != 0){
            if ((int)$this->roomUsers[$interlocutor_id] != $room_id){
                foreach ($this->rooms[$room_id] as $client) {
                    $client->send(json_encode(array(
                        'action' => \config\WS_ACTIONS_CHAT::MSG_NOTIFICATION->value,
                    )));
                }
            }
        }
    }

    public function onClose(\Ratchet\ConnectionInterface $conn): void {
        $this->leave_client($conn->resourceId);
        unset($this->clients_uid[$conn->resourceId]);
        $this->clients->detach($conn);
    }

    public function onError(\Ratchet\ConnectionInterface $conn, \Exception $e):void {
        $conn->close();
    }
}

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    50099
);
$server->run();