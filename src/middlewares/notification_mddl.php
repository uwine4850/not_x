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
        $msgn = $db->all_where("user=$uid");
        if (!empty($msgn)){
            $c = 0;
            foreach ($msgn as $i){
                $c += $i['count'];
            }
            $_GET['msgn'] = $c;
        } else{
            $_GET['msgn'] = '';
        }
    }
}
