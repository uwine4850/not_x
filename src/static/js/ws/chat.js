// Resizes the message space to fit the size of the window.
import {send_notification} from "./notification";
import {getCookie, getCurrentDateTime} from "../utils";
import {
    ACTIONS_CHAT,
    ACTIONS_NOTIFICATION_TYPES,
    ws_decrement_chat_room_msg_count,
    ws_generate_chat_id,
    ws_join_chat_room,
    ws_send_msg
} from "./config";
import {SocketDataTransfer} from "./sockets";

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
    let profile_user_id = parseInt(getCookie('UID'));
    const socket = new WebSocket(`ws://localhost:50099`);
    let isMyMessage = true;
    let last_msg = ws_send_msg;

    // User registration and user id.
    socket.onopen = function (){
        ws_join_chat_room.room_id = room_id;
        ws_join_chat_room.auth_uid = profile_user_id;
        let s1 = new SocketDataTransfer(socket, ws_join_chat_room.action, ws_join_chat_room)
        s1.send();

        ws_generate_chat_id.room_id = room_id;
        ws_generate_chat_id.chat_user_id = profile_user_id;
        let s2 = new SocketDataTransfer(socket, ws_generate_chat_id.action, ws_generate_chat_id)
        s2.send();
    }

    // A method that is executed when the server sends a message.
    socket.onmessage = function(event) {
        const message = JSON.parse(event.data);
        switch (message.action){
            case ACTIONS_CHAT.SEND_MSG:
                last_msg = message;
                const chat_messages = document.getElementById('chat-messages');
                isMyMessage = message.chat_user_id === profile_user_id;
                let msg_time = getCurrentDateTime();
                if (isMyMessage){
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
                break;
            case ACTIONS_CHAT.DECREMENT_CHAT_ROOM_MSG_COUNT:
                const data = ws_decrement_chat_room_msg_count;
                Object.assign(data, JSON.parse(event.data));
                if (data.decrement){
                    decrement_msg_chat_room_count();
                }
                break;
            // A message is sent to the conversation partner if he/she is not currently in the chat and the message is "mine".
            case ACTIONS_CHAT.MSG_NOTIFICATION:
                if (isMyMessage){
                    send_notification(s, last_msg.interlocutor_id, parseInt(getCookie('UID')), room_id,
                        ACTIONS_NOTIFICATION_TYPES.NEW_MESSAGE, last_msg.username, last_msg.msg);
                }
        }
    };

    // Sending a message to the server.
    document.getElementById('send_btn').onclick = function (){
        const messageInput = document.getElementById('chat-input-text');
        const message = messageInput.value;
        if (!message){
            return;
        }
        ws_send_msg.room_id = room_id;
        ws_send_msg.chat_user_id = profile_user_id;
        ws_send_msg.profile_user_id = profile_user_id;
        ws_send_msg.msg = message;
        let sm = new SocketDataTransfer(socket, ws_send_msg.action, ws_send_msg);
        sm.send();
        messageInput.value = '';
    }
}

// A visual reduction in the number of message chats.
export function decrement_msg_chat_room_count(){
    let messages_count = document.getElementById('messages-count');
    let count = parseInt(messages_count.innerHTML);
    count--;
    if (count === 0){
        messages_count.classList.add('messages-count-hidden');
    }
    messages_count.innerHTML = count;
}
