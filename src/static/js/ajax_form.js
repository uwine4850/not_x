/**
 * A class to run ajax form processing.
 */
class AjaxForm{
    #form_class;
    #url;
    #on_submit = function (resp){};
    constructor(form_class, url = '') {
        this.#form_class = form_class;
        this.#url = url;
    }

    run(){
        this.init_form();
    }

    on_submit(func){
        this.#on_submit = func;
    }

    init_form(){
        let this_ = this;
        $('.' + this.#form_class).on('submit', function(e) {
            e.preventDefault();
            let data = $(this).serialize();
            data += '&is_ajax=1';
            $.ajax({
                url: this_.#url,
                method: 'POST',
                data: data,
                dataType: 'json',
                success: function (resp) {
                    this_.#on_submit(resp);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });
        });
    }
}

let sub_form = new AjaxForm('subscription_form');
sub_form.on_submit(function (resp){
    if (resp.error){
        $('#form-error').html(resp.error);
    }
    let sub_btn = $('#profile-left-btn-sub');
    sub_btn.toggleClass('plb-gray');
    if (sub_btn.hasClass('plb-gray')){
        sub_btn.html('Subscribed')
    } else {
        sub_btn.html('Subscribe')
    }
});

// Like form
sub_form.run();

let like_form = new AjaxForm('post-like-form', 'http://localhost:8000/post-like');
like_form.on_submit(function (resp){
    console.log(resp);
});
like_form.run();

// Like button.
$('.post-like-btn').on('click', function (){
    let like_btn_path = $(this).children('.like-btn-svg').children('.like-btn-path');
    let like_val = $(this).children('.value').html();
    if (like_btn_path.hasClass('is-like')){
        $(this).children('.value').html(parseInt(like_val)-1);
    } else {
        $(this).children('.value').html(parseInt(like_val)+1);

    }
    like_btn_path.toggleClass('is-like');
});
