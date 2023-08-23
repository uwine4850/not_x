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

const NEW_MESSAGE = 1;

class Notification implements MessageComponentInterface {
    protected \SplObjectStorage $clients;
    private array $ids = array();
    private Database $db_chat_mn;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->db_chat_mn = new Database('chat_messages_notification');
    }

    public function onOpen(\Ratchet\ConnectionInterface $conn):void {
        $this->clients->attach($conn);
    }

    public function onMessage(\Ratchet\ConnectionInterface $from, $msg):void {
        $data = json_decode($msg, true);
        if ($data['action'] == 'join'){
            $this->ids[$data['join_uid']] = $from;
        }

        if ($data['action'] == 'notification'){
            $d = $this->processing_notification($data);
            $send_id = $d['uid'];
            if (isset($this->ids[$send_id])){
                $client = $this->ids[$send_id];
                $client->send(json_encode($data));
            }
        }

    }

    /**
     * Processing of a specific notification.
     * @param array $data
     * @return array
     */
    private function processing_notification(array $data): array{
        switch ($data['type']){
            case NEW_MESSAGE:
                $this->create_message_notification($data['uid'], $data['from_user'], $data['room_id']);
                return $data;
            default:
                break;
        }
        return $data;
    }

    /**
     * Creates a record in the database of a new message for a user in a particular chat.
     * @param int $uid The ID of the user who sent the messages.
     * @param int $from_user_id User ID, receives the message.
     * @param int $room_id Chat ID.
     * @return void
     */
    private function create_message_notification(int $uid, int $from_user_id, int $room_id): void{
        $user = $this->db_chat_mn->all_where("user=$uid AND from_user=$from_user_id AND room_id=$room_id");
        if (empty($user)){
            $this->db_chat_mn->insert(array('user' => $uid, 'from_user' => $from_user_id, 'room_id' => $room_id,
                'count' => 1));
            return;
        }
        $user_msg_count = $user[0]['count'];
        $user_msg_count++;
        $this->db_chat_mn->update($user[0]['id'], array('count' => $user_msg_count));
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
            new Notification()
        )
    ),
    50100
);
$server->run();