<?php
require_once "utils/middleware.php";
require_once "utils/database.php";

class NotificationMddl extends Middleware{

    public function run(): void{
        if ($_GET['url_pattern'] == '/login' || $_GET['url_pattern'] == '/register'){
            return;
        }
        $db = new Database('chat_messages_notification');
        $uid = $_GET['user_g']['id'];
        $msgn = $db->count("user=$uid");
        if (!empty($msgn)){
            $_GET['msgn'] = $msgn[0];
        } else{
            $_GET['msgn'] = '';
        }
        $db->close();
    }
}
