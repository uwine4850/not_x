if (document.getElementById('message_user')){
    let msg_btn = document.getElementById('message_user');
    let create_chat_bg = document.getElementById('create-chat-bg');
    let create_chat_form = document.getElementById('create-chat-form');
    msg_btn.onclick = function (){
        create_chat_bg.classList.remove('new-chat-hidden');
        create_chat_form.style.display = 'flex';
    }
    create_chat_bg.onclick = function (){
        create_chat_bg.classList.add('new-chat-hidden');
        create_chat_form.style.display = 'none';
    }
}