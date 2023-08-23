export function getCssValue(className, attributeName) {
    const element = document.getElementsByClassName(className);
    const computedStyle = getComputedStyle(element);
    return computedStyle.getPropertyValue(attributeName);
}

export function getCssValueById(id, attributeName) {
    const element = document.getElementById(id);
    const computedStyle = getComputedStyle(element);
    return computedStyle.getPropertyValue(attributeName);
}

export function like_btn_click_style(){
    let post_like_btns = $('.post-like-btn');
    post_like_btns.off('click');
    post_like_btns.on('click', function (){
        let like_btn_path = $(this).children('.like-btn-svg').children('.like-btn-path');
        let like_val = $(this).children('.value').html();
        if (like_btn_path.hasClass('is-like')){
            $(this).children('.value').html(parseInt(like_val)-1);
        } else {
            $(this).children('.value').html(parseInt(like_val)+1);

        }
        like_btn_path.toggleClass('is-like');
    });
}

export function getCookie(name){
    let cookies = document.cookie.split(';');
    for (let i = 0; i < cookies.length; i++) {
        let cookie = cookies[i].trim();

        if (cookie.indexOf(name + '=') === 0) {
            return cookie.substring(name.length + 1);
        }
    }
    return null;
}

export function getCurrentDateTime() {
    const currentDate = new Date();
    const year = currentDate.getFullYear();
    const month = String(currentDate.getMonth() + 1).padStart(2, '0');
    const day = String(currentDate.getDate()).padStart(2, '0');
    const hours = String(currentDate.getHours()).padStart(2, '0');
    const minutes = String(currentDate.getMinutes()).padStart(2, '0');
    const seconds = String(currentDate.getSeconds()).padStart(2, '0');

    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}
