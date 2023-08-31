import {getCookie} from "../utils";
import {SocketDataTransfer} from "./sockets";
import {
    ACTIONS_NOTIFICATION_TYPES, ws_notification, ws_notification_join,
} from "./config";

/**
 * Starts a websocket to listen to notifications.
 */
export function run_notification_ws(){
    const notificationSocket = new WebSocket('ws://localhost:50100');
    notificationSocket.onopen = function (){
        ws_notification_join.join_uid = getCookie('UID');
        let s = new SocketDataTransfer(notificationSocket, ws_notification_join.action, ws_notification_join);
        s.send();
    }

    notificationSocket.onmessage = function(event) {
        const message = JSON.parse(event.data);
        processing_notification(message);
    }
    return notificationSocket
}

/**
 * Processing a message from the server.
 * @param message (object) Alert text. Optional.
 */
function processing_notification(message){
    switch (message.type){
        // Increment the number of messages that are displayed to the user.
        case ACTIONS_NOTIFICATION_TYPES.NEW_MESSAGE:
            chat_list_update_msg(message);
            if (message.new_chat_room_msg){
                let messages_count = document.getElementById('messages-count');
                if (!messages_count.innerHTML){
                    messages_count.classList.remove('messages-count-hidden');
                    messages_count.innerHTML = 1;
                } else {
                    messages_count.innerHTML = parseInt(messages_count.innerHTML) + 1;
                }
            }
            break;
    }
}

/**
 * Sending a message to the notification socket.
 * @param notificationSocket (socket) An instance of the notification socket
 * @param recipient_id (int) The ID of the user for whom the notification is to be sent.
 * @param from_user (int) The ID of the user who sent the message in the chat.
 * @param room_id (int) Chat ID.
 * @param type (int) Notification Type.
 * @param username
 * @param text (string)
 */
export function send_notification(notificationSocket, recipient_id, from_user, room_id, type, username, text){
    let ws = notificationSocket;
    ws_notification.recipient_id = recipient_id;
    ws_notification.type = type;
    ws_notification.room_id = room_id;
    ws_notification.from_user = from_user;
    ws_notification.username = username;
    ws_notification.text = text;
    let s = new SocketDataTransfer(ws, ws_notification.action, ws_notification);
    s.send();
}

/**
 * Sending a message to the notification socket.
 * @param _ws_notification (object) The object of message notification.
 */
function chat_list_update_msg(_ws_notification) {
    $('.chat-list-item').each(function (i){
        let room_id = $(this).data('room_id');
        if (parseInt(room_id) === parseInt(_ws_notification.room_id)){
            let last_msg = $('.chat-list-item .chat-info .last-msg')[i];
            last_msg.innerHTML = `${_ws_notification.username}: ${_ws_notification.text}`;
            // update msg count
            let msg_count = $(this).find('.msg-count')[0];
            if (!msg_count){
                $(this).append(`<div class="msg-count">
                               1
                               </div>`);
            } else {
                let count = parseInt(msg_count.innerHTML);
                count++;
                msg_count.innerHTML = count;
            }
        }
    });
}

