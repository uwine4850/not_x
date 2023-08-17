import '../scss/style.scss';
import './pop_up_board';
import {
    centerLogOutBoard,
    centerProfileDescriptionBoard, logOutPopUpBoard,
    postMenuPopUpBoard,
    profileDescriptionPopUpBoard
} from "./pop_up_board";
import {getCssValueById} from "./utils";
import './ajax_form';

/**
 * Centers the authentication content relative to the size of the browser window.
 */
function centerAuthContent(){
    if (document.getElementById('auth-content')){
        let content = document.getElementById('auth-content');
        content.style.left = window.innerWidth/2 - (parseInt(getCssValueById('auth-content', 'width'))/2) + 'px';
    }
}

centerAuthContent();

profileDescriptionPopUpBoard();
postMenuPopUpBoard();
logOutPopUpBoard();

window.onresize = function (){
    centerProfileDescriptionBoard();
    centerAuthContent();
    centerLogOutBoard();
}


// Answer comment click
$('.comment-answer-btn').on('click', function (){
    $('#answer_id').val($(this).data('comment-id'));
    $('.answer-name').html('@' + $(this).data('comment-username'));
    $('.answer-name').css('display', 'block');
});

$('.answer-name').on('click', function (){
    $('#answer_id').val('');
    $(this).css('display', 'none');
});
