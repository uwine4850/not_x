import {LazyLoader} from "./lazy_loading";
import {postDeletePopUpBoard, postMenuPopUpBoard} from "./pop_up_board";
import {like_btn_click_style} from "./utils";
import {run_ajax_like_form} from "./ajax_form";

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
            let loadHome = new LazyLoader('last-post', ['last_post_id', 'user_id'], '/post-load-home', 'content', true)
            loadHome.start(function (){
                postMenuPopUpBoard();
                like_btn_click_style();
                run_ajax_like_form();
                postDeletePopUpBoard();
            });
            break;
        case "/profile/{username}":
            let loadProfile = new LazyLoader('last-post', ['last_post_id', 'user_id'], '/post-load', 'content', true)
            loadProfile.start(function (){
                postMenuPopUpBoard();
                like_btn_click_style();
                run_ajax_like_form();
                postDeletePopUpBoard();
            });
            break;
    }
}
