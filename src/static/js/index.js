import '../scss/style.scss';
import './pop_up_board';
import {
    centerProfileDescriptionBoard,
    postMenuPopUpBoard,
    profileDescriptionPopUpBoard
} from "./pop_up_board";
import {getCssValueById} from "./utils";

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

window.onresize = function (){
    centerProfileDescriptionBoard();
    centerAuthContent();
}

