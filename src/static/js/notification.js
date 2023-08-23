import {getCookie} from "./utils";

export const NEW_MESSAGE = 1;

/**
 * Starts a websocket to listen to notifications.
 */
export function run_notification_ws(){
    const notificationSocket = new WebSocket('ws://localhost:50100');
    notificationSocket.onopen = function (){
        const d = {
            action: 'join',
            join_uid: getCookie('UID'),
        }
        notificationSocket.send(JSON.stringify(d));
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
        case NEW_MESSAGE:
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
 * @param uid (int) The ID of the user for whom the notification is to be sent.
 * @param from_user (int) The ID of the user who sent the message in the chat.
 * @param room_id (int) Chat ID.
 * @param type (int) Notification Type.
 * @param text (string) Alert text. Optional.
 */
export function send_notification(notificationSocket, uid, from_user, room_id, type, text=''){
    const Data = {
        action: 'notification',
        uid: uid,
        room_id: room_id,
        from_user: from_user,
        type: type,
        text: text,
    }
    notificationSocket.send(JSON.stringify(Data));
}

