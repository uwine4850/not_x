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


class Notification implements MessageComponentInterface {
    use ConnectToAllTables;

    protected \SplObjectStorage $clients;
    private array $ids = array();
    private Database $db;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->db = new Database();
        $this->connect_to_all_tables($this->db);
    }

    public function __destruct()
    {
        $this->db->close();
    }

    public function onOpen(\Ratchet\ConnectionInterface $conn):void {
        $this->clients->attach($conn);
    }

    public function onMessage(\Ratchet\ConnectionInterface $from, $msg):void {
        $data = json_decode($msg, true);
        if ($data['action'] == \config\WS_ACTIONS_NOTIFICATION::JOIN->value){
            $this->ids[$data['join_uid']] = $from;
        }

        if ($data['action'] == \config\WS_ACTIONS_NOTIFICATION::NOTIFICATION->value){
            $d = $this->processing_notification($data);
            $send_id = $d['recipient_id'];
            if (isset($this->ids[$send_id])){
                $client = $this->ids[$send_id];
                $client->send(json_encode($d));
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
            case \config\WS_ACTIONS_NOTIFICATION_TYPE::NEW_MESSAGE->value:
                if ($this->chat_new_chat_room_mgs($data['room_id'])){
                    $data['new_chat_room_msg'] = true;
                }
                $this->create_message_notification($data['recipient_id'], $data['from_user'], $data['room_id']);
                return $data;
            default:
                break;
        }
        return $data;
    }

    private function chat_new_chat_room_mgs(int $chat_room_id): bool{
        $notification = $this->db_chat_messages_notification->all_where("room_id=$chat_room_id");
        if (empty($notification)){
            return true;
        }
        return false;
    }

    /**
     * Creates a record in the database of a new message for a user in a particular chat.
     * @param int $uid The ID of the user who sent the messages.
     * @param int $from_user_id User ID, receives the message.
     * @param int $room_id Chat ID.
     * @return void
     */
    private function create_message_notification(int $uid, int $from_user_id, int $room_id): void{
        $user = $this->db_chat_messages_notification->all_where("user=$uid AND from_user=$from_user_id AND room_id=$room_id");
        if (empty($user)){
            $this->db_chat_messages_notification->insert(array('user' => $uid, 'from_user' => $from_user_id, 'room_id' => $room_id,
                'count' => 1));
            return;
        }
        $user_msg_count = $user[0]['count'];
        $user_msg_count++;
        $this->db_chat_messages_notification->update($user[0]['id'], array('count' => $user_msg_count));
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