(()=>{var t={291:()=>{if(document.getElementById("message_user")){let t=document.getElementById("message_user"),e=document.getElementById("create-chat-bg"),n=document.getElementById("create-chat-form");t.onclick=function(){e.classList.remove("new-chat-hidden"),n.style.display="flex"},e.onclick=function(){e.classList.add("new-chat-hidden"),n.style.display="none"}}}},e={};function n(s){var o=e[s];if(void 0!==o)return o.exports;var i=e[s]={exports:{}};return t[s](i,i.exports,n),i.exports}(()=>{"use strict";class t{#t;#e;#n=[];currentClickElement;#s=!1;#o=!1;constructor(t,e){this.boardsClass=t,this.openElementsClass=e,this.#i()}#i(){for(let t=0;t<this.openElementsClass.length;t++)this.#n.push(document.getElementsByClassName(this.openElementsClass[t]))}getBoards(){return document.getElementsByClassName(this.boardsClass)}enableBlur(){return this.#s=!0,this}enableBlockScroll(){return this.#o=!0,this}#l(t){let e=document.getElementById("blur-all");t?e.classList.add("blur-hidden"):e.classList.remove("blur-hidden")}display(){let t=this.getBoards();this.#a(this.#t);for(const e of this.#n)for(let n=0;n<e.length;n++)e[n].onclick=e=>{if(e.target.classList.length>1)for(const t of e.target.classList)this.openElementsClass.includes(t)&&e.stopPropagation();this.openElementsClass.includes(e.target.className)&&e.stopPropagation(),this.#o&&("hidden"===this.#r()?document.body.style.overflow="auto":document.body.style.overflow="hidden"),this.#s&&this.#l(!1),t[n].classList.toggle("pop-up-board-hidden"),this.currentClickElement=t[n],this.#a(this.#e)};for(const e of t)document.addEventListener("click",(t=>{if(t.target.classList.length>1)for(const e of t.target.classList)this.openElementsClass.includes(e)&&t.stopPropagation();for(const n of t.target.classList)n!==this.boardsClass&&(e.classList.add("pop-up-board-hidden"),this.#o&&(document.body.style.overflow="auto"),this.#s&&this.#l(!0));t.target!==e||this.openElementsClass.includes(t.target.className)||(e.classList.add("pop-up-board-hidden"),this.#o&&(document.body.style.overflow="auto"),this.#s&&this.#l(!0))}))}#r(){return getComputedStyle(document.body).overflow}#a(t){t&&t(this)}onDisplay(t){this.#t=t}onClick(t){this.#e=t}}function e(t){let e=document.getElementById("content").offsetWidth/2-t.offsetWidth/2;t.style.left=e+"px"}let s=new t("descr-board",["profile-description-span"]);function o(){document.getElementsByClassName("post-menu-btn")&&new t("post-menu-board",["post-menu-btn","post-menu-btn-img"]).display()}let i=new t("log-out-board",["log-out-menu-btn","log-out-menu-btn-img","log-out-menu-btn-text"]);function l(){let n=new t("post-delete-pop-up",["post-delete-btn"]);n.enableBlur().enableBlockScroll(),n.onClick((function(t){e(t.currentClickElement);let n=window.pageYOffset||document.documentElement.scrollTop;t.currentClickElement.style.top=n+200+"px"})),n.display()}function a(){let t=$(".post-like-btn");t.off("click"),t.on("click",(function(){let t=$(this).children(".like-btn-svg").children(".like-btn-path"),e=$(this).children(".value").html();t.hasClass("is-like")?$(this).children(".value").html(parseInt(e)-1):$(this).children(".value").html(parseInt(e)+1),t.toggleClass("is-like")}))}function r(t){let e=document.cookie.split(";");for(let n=0;n<e.length;n++){let s=e[n].trim();if(0===s.indexOf(t+"="))return s.substring(t.length+1)}return null}class c{#c;#d;#u=function(t){};constructor(t,e=""){this.#c=t,this.#d=e}run(){this.init_form()}on_submit(t){this.#u=t}init_form(){let t=this,e=$("."+this.#c);e.off("submit"),e.on("submit",(function(e){e.preventDefault();let n=$(this).serialize();n+="&is_ajax=1",$.ajax({url:t.#d,method:"POST",data:n,dataType:"json",success:function(e){t.#u(e)},error:function(t,e,n){console.log(n)}})}))}}let d=new c("subscription_form");function u(){let t=new c("post-like-form","http://localhost:8000/post-like");t.on_submit((function(t){console.log(t)})),t.run()}d.on_submit((function(t){t.error&&$("#form-error").html(t.error);let e=$("#profile-left-btn-sub");e.toggleClass("plb-gray"),e.hasClass("plb-gray")?e.html("Subscribed"):e.html("Subscribe")})),d.run();class m{#m;#_;target_element_id;data_fields;remove_target_id;insert_element_id;url;constructor(t,e,n,s,o=!1){this.target_element_id=t,this.data_fields=e,this.remove_target_id=o,this.url=n,this.insert_element_id=s,this.#h()}observer_start(t){let e=document.getElementById(this.target_element_id);e&&(this.#m&&this.#m.disconnect(),this.#m=new IntersectionObserver((e=>{e.forEach((e=>{e.isIntersecting&&t()}))})),this.#m.observe(e))}#g(){let t={};for(let e=0;e<this.data_fields.length;e++)t[this.data_fields[e]]=this.#_.data(this.data_fields[e]);return t}#f(t,e){$.ajax({url:t,method:"GET",data:this.#g(),success:t=>{e(t)}})}#h(){this.#_=$(`#${this.target_element_id}`)}start(t){this.observer_start((()=>{this.#f(this.url,(e=>{this.display_response(e),this.#m.disconnect(),t(),this.remove_target_id&&this.#_.removeAttr("id"),this.#h(),this.start(t)}))}))}display_response(t){$(`#${this.insert_element_id}`).append(t)}close_observer(){this.#m.disconnect()}}class _ extends m{constructor(t,e,n,s,o=!1){super(t,e,n,s,o)}display_response(t){$(`#${this.insert_element_id}`).prepend(t)}}class h{constructor(t,e,n){this.ws=t,this.action=e,this.data=n}#p(){return this.data.action=this.action,JSON.stringify(this.data)}send(){let t=this.#p();this.ws.send(t)}}const g="SEND_MSG",f="DECREMENT_CHAT_ROOM_MSG_COUNT",p="MSG_NOTIFICATION",b="CREATE_NEW_CHAT",y="NEW_MESSAGE",E={action:"JOIN",join_uid:null},v={action:"NOTIFICATION",recipient_id:null,room_id:null,from_user:null,type:null,username:null,new_chat_room_msg:!1,text:null},w={action:"JOIN_CHAT_ROOM",room_id:null,auth_uid:null},k={action:"GENERATE_CHAT_ID",room_id:null,chat_user_id:null},C={action:g,room_id:null,interlocutor_id:null,chat_user_id:null,profile_user_id:null,username:null,msg:null},I={action:f,decrement:!1},B={action:b,from_user_id:null,to_user_id:null,new_room_id:null,first_message:null,user_data:null};function S(t,e,n,s,o,i,l){let a=t;v.recipient_id=e,v.type=o,v.room_id=s,v.from_user=n,v.username=i,v.text=l,new h(a,v.action,v).send()}function T(){let t=document.getElementById("chat-messages");if(!t)return;let e=document.getElementById("content"),n=document.getElementById("chat-header"),s=document.getElementById("chat-input"),o=e.offsetHeight-(n.offsetHeight+s.offsetHeight);t.style.height=o+"px"}function O(){let t=$("#chat-messages");if(!t)return;let e=t.children().last();"chat-message"===e.className&&t.scrollTop(e.offset().top-t.offset().top+t.scrollTop())}T(),window.addEventListener("resize",T);let L=function(){const t=new WebSocket("ws://localhost:50100");return t.onopen=function(){E.join_uid=r("UID"),new h(t,E.action,E).send()},t.onmessage=function(e){const n=JSON.parse(e.data);!function(t){if(t.type===y&&(e=t,$(".chat-list-item").each((function(t){let n=$(this).data("room_id");if(parseInt(n)===parseInt(e.room_id)){$(".chat-list-item .chat-info .last-msg")[t].innerHTML=`${e.username}: ${e.text}`;let n=$(this).find(".msg-count")[0];if(n){let t=parseInt(n.innerHTML);t++,n.innerHTML=t}else $(this).append('<div class="msg-count">\n                               1\n                               </div>')}})),t.new_chat_room_msg)){let t=document.getElementById("messages-count");t.innerHTML?t.innerHTML=parseInt(t.innerHTML)+1:(t.classList.remove("messages-count-hidden"),t.innerHTML=1)}var e}(n),function(t,e){switch(t.action){case b:if("http://localhost:8000/chat-list"!==window.location.toString())break;!function(t){let e=t.user_data;$("#chat-list").append(`<a href="/chat-room/${t.new_room_id}" class="chat-list-item" data-room_id="${t.new_room_id}">\n        <div class="chat-list-item-img">\n            <img src="${function(t){if(!t)return"/static/img/default.jpeg";const e=t,n=e.indexOf("/media");return-1!==n?e.substring(n):""}(e.path_to_user_image)}" alt="chat-img">\n        </div>\n        <div class="chat-info">\n            <div class="chat-user">\n                @${e.username}\n            </div>\n            <div class="last-msg">\n            </div>\n        </div>\n    </a>`)}(t),S(e,t.to_user_id,t.from_user_id,t.new_room_id,y,t.user_data.username,t.first_message)}}(n,t)},t}();const N="TRIGGER_JS",x="CREATE_NEW_CHAT";function H(){document.getElementById("auth-content")&&(document.getElementById("auth-content").style.left=window.innerWidth/2-parseInt(function(t,e){const n=document.getElementById("auth-content");return getComputedStyle(n).getPropertyValue("width")}())/2+"px")}n(291),$.ajax({url:"/server-data",method:"GET",data:{},dataType:"json",success:t=>{!function(t){switch(t.curr_url_pattern){case"/":new m("last-post",["last_post_id","user_id"],"/post-load-home","content",!0).start((function(){o(),a(),u(),l()}));break;case"/profile/{username}":new m("last-post",["last_post_id","user_id"],"/post-load","content",!0).start((function(){o(),a(),u(),l()}));break;case"/chat-room/{room_id}":let e=new _("chat-last-msg",["msg_id","chat_room_id"],"/load-msg","chat-messages",!0);e.start((function(){})),function(t,e,n){let s=parseInt(r("UID"));const o=new WebSocket("ws://localhost:50099");let i=!0,l=C;o.onopen=function(){w.room_id=t,w.auth_uid=s,new h(o,w.action,w).send(),k.room_id=t,k.chat_user_id=s,new h(o,k.action,k).send()},o.onmessage=function(o){const a=JSON.parse(o.data);switch(a.action){case g:l=a;const c=document.getElementById("chat-messages");i=a.chat_user_id===s;let d=function(){const t=new Date;return`${t.getFullYear()}-${String(t.getMonth()+1).padStart(2,"0")}-${String(t.getDate()).padStart(2,"0")} ${String(t.getHours()).padStart(2,"0")}:${String(t.getMinutes()).padStart(2,"0")}:${String(t.getSeconds()).padStart(2,"0")}`}();c.innerHTML+=i?`<div class="chat-message my-msg">${a.msg}\n                                                <div class="msg-time my-msg-time">${d}</div>\n                                                </div>`:`<div class="chat-message">${a.msg}\n                                                <div class="msg-time">${d}</div>\n                                                </div>`,O(),n();break;case f:const u=I;Object.assign(u,JSON.parse(o.data)),u.decrement&&function(){let t=document.getElementById("messages-count"),e=parseInt(t.innerHTML);e--,0===e&&t.classList.add("messages-count-hidden"),t.innerHTML=e}();break;case p:i&&S(e,l.interlocutor_id,parseInt(r("UID")),t,y,l.username,l.msg)}},document.getElementById("send_btn").onclick=function(){const e=document.getElementById("chat-input-text"),n=e.value;n&&(C.room_id=t,C.chat_user_id=s,C.profile_user_id=s,C.msg=n,new h(o,C.action,C).send(),e.value="")}}(t.room_id,L,(function(){e.close_observer(),new _("chat-last-msg",["msg_id","chat_room_id"],"/load-msg","chat-messages",!0).start((function(){}))})),O()}}(t),t.hasOwnProperty(N)&&function(t){for(const e in t)e===x&&(B.from_user_id=t[e].from_user_id,B.to_user_id=t[e].to_user_id,B.new_room_id=t[e].new_room_id,B.first_message=t[e].first_message,new h(L,B.action,B).send())}(t[N])}}),H(),document.getElementsByClassName("profile-description")&&(s.enableBlockScroll().enableBlur(),s.onClick((function(t){e(t.currentClickElement)})),s.display()),o(),document.getElementsByClassName("log-out-menu-btn")&&(i.enableBlur().enableBlockScroll(),i.onClick((function(t){e(t.currentClickElement);let n=window.pageYOffset||document.documentElement.scrollTop;t.currentClickElement.style.top=n+200+"px"})),i.display()),l(),window.onresize=function(){s.currentClickElement&&e(s.currentClickElement),H(),i.currentClickElement&&e(i.currentClickElement)},$(".comment-answer-btn").on("click",(function(){$("#answer_id").val($(this).data("comment-id")),$(".answer-name").html("@"+$(this).data("comment-username")),$(".answer-name").css("display","block")})),$(".answer-name").on("click",(function(){$("#answer_id").val(""),$(this).css("display","none")})),u(),a()})()})();