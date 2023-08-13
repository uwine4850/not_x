import './utils';
import {getCssValueById} from "./utils";

// An object to control any popup window.
export class PopUpBoard{
    #on_display_func;
    #on_click_func;
    #openElements = [];
    currentClickElement;

    #enable_blur = false;
    #enable_block_scroll = false;

    /**
     * @param boardsClass (string) board element class.
     * @param openElementsClass (array) id of element to open the board by click method.
     */
    constructor(boardsClass, openElementsClass) {
        this.boardsClass = boardsClass;
        this.openElementsClass = openElementsClass;
        this.#getOpenElements();
    }

    #getOpenElements(){
        for (let i = 0; i < this.openElementsClass.length; i++) {
            this.#openElements.push(document.getElementsByClassName(this.openElementsClass[i]));
        }
    }

    /**
     * Returns the HTML element of the board.
     * @return HTMLElement
     */
    getBoards(){
        return document.getElementsByClassName(this.boardsClass);
    }

    enableBlur(){
        this.#enable_blur = true;
        return this;
    }

    enableBlockScroll(){
        this.#enable_block_scroll = true;
        return this;
    }

    /**
     * Turns the background blur element on and off.
     */
    #useBlur(hide){
        let blur = document.getElementById('blur-all');
        if (hide){
            blur.classList.add('blur-hidden');
        } else {
            blur.classList.remove('blur-hidden');
        }
    }

    /**
     * Main method. Runs the board's open and close handlers.
     * It also launches custom methods during this method's try to open and close the board.
     */
    display(){
        let boards = this.getBoards();
        // run custom method
        this.#runFunc(this.#on_display_func);

        for (const openElementClass of this.#openElements) {
            for (let i = 0; i < openElementClass.length; i++) {
                openElementClass[i].onclick = (e) => {
                    // Double trigger disable
                    if (e.target.classList.length > 1){
                        for (const targetClass of e.target.classList) {
                            if (this.openElementsClass.includes(targetClass)){
                                e.stopPropagation();
                            }
                        }
                    }
                    if (this.openElementsClass.includes(e.target.className)){
                        e.stopPropagation();
                    }

                    // enable/disable scroll
                    if (this.#enable_block_scroll){
                        if (this.#getBodyOverflow() === 'hidden'){
                            document.body.style.overflow = "auto";
                        } else {
                            document.body.style.overflow = "hidden";
                        }
                    }
                    if (this.#enable_blur){
                        this.#useBlur(false);
                    }
                    boards[i].classList.toggle('pop-up-board-hidden');
                    this.currentClickElement = boards[i];
                    // run custom method
                    this.#runFunc(this.#on_click_func);
                }
            }
        }

        for (const board of boards) {
            document.addEventListener('click', (event) => {
                if (event.target.classList.length > 1){
                    for (const targetClass of event.target.classList) {
                        if (this.openElementsClass.includes(targetClass)){
                            return;
                        }
                    }
                }
                if (event.target !== board && !this.openElementsClass.includes(event.target.className)) {
                    board.classList.add('pop-up-board-hidden');
                    if (this.#enable_block_scroll){
                        document.body.style.overflow = "auto";
                    }
                    //check blur
                    if (this.#enable_blur){
                        this.#useBlur(true);
                    }
                }
            });
        }
    }
    #getBodyOverflow(){
        return getComputedStyle(document.body).overflow;
    }
    #runFunc(func){
        if (func){
            func(this);
        }
    }
    onDisplay(on_display_func){
        this.#on_display_func = on_display_func;
    }
    onClick(on_click_func){
        this.#on_click_func = on_click_func;
    }
}

/**
 * Centers the favorite board provided relative to the content.
 */
function centerBoardRelativeToContent(currBoard){
    let content = document.getElementById('content');
    let left = content.offsetWidth/2 - currBoard.offsetWidth/2;
    currBoard.style.left = left + 'px';
}

/**
 * Popup board logs with full user information.
 */
let profile_description_board = new PopUpBoard("descr-board", ['profile-description-span']);
export function profileDescriptionPopUpBoard(){
    if (document.getElementsByClassName("profile-description")){
        profile_description_board.enableBlockScroll().enableBlur();
        profile_description_board.onClick(function (b){
            centerBoardRelativeToContent(b.currentClickElement);
        })
        profile_description_board.display();
    }
}

/**
 * Centers the pop-up panel of the full profile description.
 */
export function centerProfileDescriptionBoard(){
    if (profile_description_board.currentClickElement){
        centerBoardRelativeToContent(profile_description_board.currentClickElement);
    }
}

/**
 * Menu for post management.
 */
export function postMenuPopUpBoard(){
    if (document.getElementsByClassName('post-menu-btn')){
        let board = new PopUpBoard('post-menu-board', ['post-menu-btn', 'post-menu-btn-img']);
        board.display()
    }
}

let logOutBoard = new PopUpBoard(['log-out-board'], ['log-out-menu-btn',
    'log-out-menu-btn-img', 'log-out-menu-btn-text'])
export function logOutPopUpBoard(){
    if (document.getElementsByClassName('log-out-menu-btn')){
        logOutBoard.enableBlur().enableBlockScroll();
        logOutBoard.onClick(function (b){
            centerBoardRelativeToContent(b.currentClickElement);
        })
        logOutBoard.display();
    }
}

export function centerLogOutBoard(){
    if (logOutBoard.currentClickElement){
        centerBoardRelativeToContent(logOutBoard.currentClickElement);
    }
}
