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

class Chat implements MessageComponentInterface {
    protected \SplObjectStorage $clients;
    protected array $rooms;
    private array $roomUsers = array();
    private Database $db_chat_messages;
    private Database $db_chat_rooms;
    private Database $db_chat_mc;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->rooms = [];
        $this->db_chat_messages = new Database('chat_messages');
        $this->db_chat_rooms = new Database('chat_rooms');
        $this->db_chat_mc = new Database('chat_messages_notification');
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
     * Saves the message in the database.
     * @param array $values Client data about the message.
     * @return void
     */
    private function save_message(array $values): void{
        try {
            date_default_timezone_set('Europe/Kyiv');
            $values['time'] = date("Y-m-d H:i:s");
            $insert_values = array_to_db_assoc_array($values, array(
                FormDbField::make('room_id', 'parent_chat'),
                FormDbField::make('profile_user_id', 'user'),
                FormDbField::make('msg', 'text'),
                FormDbField::make('time', 'time'),
            ));
            $this->db_chat_messages->insert($insert_values);
        } catch (ArrayValueIsEmpty $e) {
        }
    }

    /**
     * Deletes a record of the number of messages in the current chat.
     * @param $room_id Chat ID.
     * @return void
     */
    private function delete_msgs_count($room_id): void{
        $id = $this->db_chat_mc->all_where("room_id=$room_id");
        if (!empty($id)){
            $this->db_chat_mc->delete($id[0]['id']);
        }
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
        if ($data['action'] === 'join_room') {
            $room_id = $data['room_id'];
            $this->rooms[$room_id][$from->resourceId] = $from;
            $this->delete_msgs_count($data['room_id']);
        }

        $id_ok = true;

        // Checking the uniqueness of the user id.
        if ($data['action'] === 'generate_id'){
            $uid = $data['uid'];
            $room_id = $data['room_id'];
            if (!empty($this->roomUsers[$room_id]) && in_array($uid, $this->roomUsers[$room_id])){
                $data['action'] = 'regenerate_id';
                $id_ok = false;
                foreach ($this->rooms[$room_id] as $client) {
                    $client->send(json_encode($data));
                }
            }
            $this->roomUsers[$room_id][] = $uid;
        }

        // Sending a message to the client.
        if ($id_ok && $data['action'] === 'send_msg'){
            $room_id = $data['room_id'];
            $this->save_message($data);
            $data['interlocutor_id'] = $this->get_interlocutor_id($room_id, $data['send_msg_uid']);
            foreach ($this->rooms[$room_id] as $client) {
                $client->send(json_encode($data));
            }
        }
    }

    public function onClose(\Ratchet\ConnectionInterface $conn): void {
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