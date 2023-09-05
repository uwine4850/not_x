import {LazyLoader, ReverseLazyLoader} from "./lazy_loading";
import {postDeletePopUpBoard, postMenuPopUpBoard} from "./pop_up_board";
import {like_btn_click_style} from "./utils";
import {run_ajax_like_form} from "./ajax_form";
import {run_chat_ws, scrollToLastMsg} from "./ws/chat";
import {run_notification_ws} from "./ws/notification";
import {ws_create_new_chat} from "./ws/config";
import {SocketDataTransfer} from "./ws/sockets";

let s = run_notification_ws();

export function handle_server_url(){
    $.ajax({
        url: '/server-data',
        method: 'GET',
        data: {},
        dataType: 'json',
        success: (response) => {
            handle(response);
            if (response.hasOwnProperty(TRIGGER_JS.TRIGGER)){
                trigger_js(response[TRIGGER_JS.TRIGGER]);
            }
        }
    });
}

function handle(resp){
    switch (resp['curr_url_pattern']){
        case "/":
            let loadHome = new LazyLoader('last-post', ['last_post_id', 'user_id'],
                '/post-load-home', 'content', true);
            loadHome.start(function (){
                postMenuPopUpBoard();
                like_btn_click_style();
                run_ajax_like_form();
                postDeletePopUpBoard();
            });
            break;
        case "/profile/{username}":
            let loadProfile = new LazyLoader('last-post', ['last_post_id', 'user_id'],
                '/post-load', 'content', true);
            loadProfile.start(function (){
                postMenuPopUpBoard();
                like_btn_click_style();
                run_ajax_like_form();
                postDeletePopUpBoard();
            });
            break;
        case "/chat-room/{room_id}":
            // Running websocket and lazy loading of messages.
            let loadMessages = new ReverseLazyLoader('chat-last-msg', ['msg_id', 'chat_room_id'],
                '/load-msg', 'chat-messages', true);
            loadMessages.start(function (){});
            run_chat_ws(resp['room_id'], s, function (){
                loadMessages.close_observer();
                let loadMessages1 = new ReverseLazyLoader('chat-last-msg', ['msg_id', 'chat_room_id'],
                    '/load-msg', 'chat-messages', true);
                loadMessages1.start(function (){});
            });
            scrollToLastMsg();
            break;
    }
}

/**
 * Runs the trigger for the js method.
 */
function trigger_js(trigger_data){
    for (const t in trigger_data) {
        switch (t){
            case TRIGGER_JS.CREATE_NEW_CHAT:
                ws_create_new_chat.from_user_id = trigger_data[t]['from_user_id'];
                ws_create_new_chat.to_user_id = trigger_data[t]['to_user_id'];
                ws_create_new_chat.new_room_id = trigger_data[t]['new_room_id'];
                let sock = new SocketDataTransfer(s, ws_create_new_chat.action, ws_create_new_chat);
                sock.send()
        }
    }
}

const TRIGGER_JS = {
    TRIGGER: 'TRIGGER_JS',
    CREATE_NEW_CHAT: 'CREATE_NEW_CHAT',
}
