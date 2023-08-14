/**
 * A class to run ajax form processing.
 */
class AjaxForm{
    #form_id;
    #resp;
    #on_submit = function (resp){};
    constructor(form_id) {
        this.#form_id = form_id;
    }

    run(){
        this.init_form();
    }

    on_submit(func){
        this.#on_submit = func;
    }

    init_form(){
        if (document.getElementById(this.#form_id)){
            let this_ = this;
            $('#' + this.#form_id).on('submit', function(e) {
                e.preventDefault();
                let data = $(this).serialize();
                data += '&is_ajax=1';
                $.ajax({
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
})
sub_form.run();