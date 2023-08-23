// Resizes the message space to fit the size of the window.
import {NEW_MESSAGE, send_notification} from "./notification";
import {getCookie, getCurrentDateTime} from "./utils";

export function resize_msg_place(){
    let chat_messages = document.getElementById('chat-messages');
    if (!chat_messages) {
        return;
    }
    let content = document.getElementById('content');
    let chat_header = document.getElementById('chat-header');
    let chat_input = document.getElementById('chat-input');
    let messages_height = content.offsetHeight - (chat_header.offsetHeight + chat_input.offsetHeight);
    chat_messages.style.height = messages_height + 'px';
}
resize_msg_place();
window.addEventListener("resize", resize_msg_place);

// Scrolls down all messages.
export function scrollToLastMsg(){
    let messages = $("#chat-messages");
    if (!messages){
        return;
    }
    let last_msg = messages.children().last();
    messages.scrollTop(last_msg.offset().top - messages.offset().top + messages.scrollTop());
}

// Launches a websocket to listen in on the chat.
// Each chat is always isolated by a room with its own id.
// Each user has a unique id in the room. If this id is already in the room, it will be generated again. This id is also
// used to define "my" message.
export function run_chat_ws(room_id, s, on_user_msg){
    let currentUserId = generateUniqueUserId();
    let profile_user_id = parseInt(getCookie('UID'));
    const socket = new WebSocket(`ws://localhost:50099`); // Замените порт, если нужно

    // User registration and user id.
    socket.onopen = function (){
        const roomData = {
            action: 'join_room',
            room_id: room_id,
            auth_uid: getCookie('UID'),
        };
        socket.send(JSON.stringify(roomData));

        const rData = {
            action: 'generate_id',
            room_id: room_id,
            uid: currentUserId,
        };
        socket.send(JSON.stringify(rData));
    }

    // A method that is executed when the server sends a message.
    socket.onmessage = function(event) {
        const message = JSON.parse(event.data);
        switch (message.action){
            // Re-generating user id.
            case "regenerate_id":
                currentUserId = generateUniqueUserId();
                const rData = {
                    action: 'generate_id',
                    room_id: room_id,
                    uid: currentUserId,
                };
                socket.send(JSON.stringify(rData));
                break;
            // Sending a message to the chat room. This message is saved in the database before it is sent.
            case "send_msg":
                const chat_messages = document.getElementById('chat-messages');
                const isMyMessage = message.uid === currentUserId;
                let msg_time = getCurrentDateTime();
                if (isMyMessage){
                    send_notification(s, message.interlocutor_id, parseInt(getCookie('UID')), room_id,
                        NEW_MESSAGE, '');
                    chat_messages.innerHTML += `<div class="chat-message my-msg">${message.msg}
                                                <div class="msg-time my-msg-time">${msg_time}</div>
                                                </div>`;
                } else {
                    chat_messages.innerHTML += `<div class="chat-message">${message.msg}
                                                <div class="msg-time">${msg_time}</div>
                                                </div>`;
                }
                scrollToLastMsg();
                on_user_msg();
        }
    };

    // Sending a message to the server.
    document.getElementById('send_btn').onclick = function (){
        const messageInput = document.getElementById('chat-input-text');
        const message = messageInput.value;
        if (!message){
            return;
        }
        const roomData = {
            action: 'send_msg',
            room_id: room_id,
            uid: currentUserId,
            send_msg_uid: getCookie('UID'),
            profile_user_id: profile_user_id,
            msg: message
        };
        socket.send(JSON.stringify(roomData));
        messageInput.value = '';
    }
}

function generateUniqueUserId() {
    return 'user_' + Math.floor(Math.random() * 100000);
}
