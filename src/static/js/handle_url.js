import {LazyLoader, ReverseLazyLoader} from "./lazy_loading";
import {postDeletePopUpBoard, postMenuPopUpBoard} from "./pop_up_board";
import {like_btn_click_style} from "./utils";
import {run_ajax_like_form} from "./ajax_form";
import {run_chat_ws, scrollToLastMsg} from "./ws/chat";
import {run_notification_ws} from "./ws/notification";

let s = run_notification_ws();

export function handle_server_url(){
    $.ajax({
        url: '/server-data',
        method: 'GET',
        data: {},
        dataType: 'json',
        success: (response) => {
            handle(response);
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
