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
            let messages_count = document.getElementById('messages-count');
            if (!messages_count.innerHTML){
                messages_count.classList.remove('messages-count-hidden');
                messages_count.innerHTML = 1;
                return;
            }
            messages_count.innerHTML = parseInt(messages_count.innerHTML) + 1;
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
 */
export function send_notification(notificationSocket, recipient_id, from_user, room_id, type){
    let ws = notificationSocket;
    ws_notification.recipient_id = recipient_id;
    ws_notification.type = type;
    ws_notification.room_id = room_id;
    ws_notification.from_user = from_user;
    let s = new SocketDataTransfer(ws, ws_notification.action, ws_notification);
    s.send();
}

