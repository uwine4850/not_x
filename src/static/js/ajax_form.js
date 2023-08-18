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
        let form_classes = $('.' + this.#form_class);
        form_classes.off('submit');
        form_classes.on('submit', function(e) {
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
sub_form.run();

export function run_ajax_like_form(){
    let like_form = new AjaxForm('post-like-form', 'http://localhost:8000/post-like');
    like_form.on_submit(function (resp){
        console.log(resp);
    });
    like_form.run();
}
