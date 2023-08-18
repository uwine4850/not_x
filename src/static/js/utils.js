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
