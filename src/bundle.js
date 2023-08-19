(()=>{"use strict";class t{#t;#e;#s=[];currentClickElement;#n=!1;#l=!1;constructor(t,e){this.boardsClass=t,this.openElementsClass=e,this.#o()}#o(){for(let t=0;t<this.openElementsClass.length;t++)this.#s.push(document.getElementsByClassName(this.openElementsClass[t]))}getBoards(){return document.getElementsByClassName(this.boardsClass)}enableBlur(){return this.#n=!0,this}enableBlockScroll(){return this.#l=!0,this}#i(t){let e=document.getElementById("blur-all");t?e.classList.add("blur-hidden"):e.classList.remove("blur-hidden")}display(){let t=this.getBoards();this.#r(this.#t);for(const e of this.#s)for(let s=0;s<e.length;s++)e[s].onclick=e=>{if(e.target.classList.length>1)for(const t of e.target.classList)this.openElementsClass.includes(t)&&e.stopPropagation();this.openElementsClass.includes(e.target.className)&&e.stopPropagation(),this.#l&&("hidden"===this.#a()?document.body.style.overflow="auto":document.body.style.overflow="hidden"),this.#n&&this.#i(!1),t[s].classList.toggle("pop-up-board-hidden"),this.currentClickElement=t[s],this.#r(this.#e)};for(const e of t)document.addEventListener("click",(t=>{if(t.target.classList.length>1)for(const e of t.target.classList)this.openElementsClass.includes(e)&&t.stopPropagation();for(const s of t.target.classList)s!==this.boardsClass&&(e.classList.add("pop-up-board-hidden"),this.#l&&(document.body.style.overflow="auto"),this.#n&&this.#i(!0));t.target!==e||this.openElementsClass.includes(t.target.className)||(e.classList.add("pop-up-board-hidden"),this.#l&&(document.body.style.overflow="auto"),this.#n&&this.#i(!0))}))}#a(){return getComputedStyle(document.body).overflow}#r(t){t&&t(this)}onDisplay(t){this.#t=t}onClick(t){this.#e=t}}function e(t){let e=document.getElementById("content").offsetWidth/2-t.offsetWidth/2;t.style.left=e+"px"}let s=new t("descr-board",["profile-description-span"]);function n(){document.getElementsByClassName("post-menu-btn")&&new t("post-menu-board",["post-menu-btn","post-menu-btn-img"]).display()}let l=new t("log-out-board",["log-out-menu-btn","log-out-menu-btn-img","log-out-menu-btn-text"]);function o(){let s=new t("post-delete-pop-up",["post-delete-btn"]);s.enableBlur().enableBlockScroll(),s.onClick((function(t){e(t.currentClickElement);let s=window.pageYOffset||document.documentElement.scrollTop;t.currentClickElement.style.top=s+200+"px"})),s.display()}function i(){let t=$(".post-like-btn");t.off("click"),t.on("click",(function(){let t=$(this).children(".like-btn-svg").children(".like-btn-path"),e=$(this).children(".value").html();t.hasClass("is-like")?$(this).children(".value").html(parseInt(e)-1):$(this).children(".value").html(parseInt(e)+1),t.toggleClass("is-like")}))}class r{#c;#u;#d=function(t){};constructor(t,e=""){this.#c=t,this.#u=e}run(){this.init_form()}on_submit(t){this.#d=t}init_form(){let t=this,e=$("."+this.#c);e.off("submit"),e.on("submit",(function(e){e.preventDefault();let s=$(this).serialize();s+="&is_ajax=1",$.ajax({url:t.#u,method:"POST",data:s,dataType:"json",success:function(e){t.#d(e)},error:function(t,e,s){console.log(s)}})}))}}let a=new r("subscription_form");function c(){let t=new r("post-like-form","http://localhost:8000/post-like");t.on_submit((function(t){console.log(t)})),t.run()}a.on_submit((function(t){t.error&&$("#form-error").html(t.error);let e=$("#profile-left-btn-sub");e.toggleClass("plb-gray"),e.hasClass("plb-gray")?e.html("Subscribed"):e.html("Subscribe")})),a.run();class u{#m;#h;target_element_id;data_fields;remove_target_id;insert_element_id;url;constructor(t,e,s,n,l=!1){this.target_element_id=t,this.data_fields=e,this.remove_target_id=l,this.url=s,this.insert_element_id=n,this.#_()}#p(t){let e=document.getElementById(this.target_element_id);e&&(this.#m&&this.#m.disconnect(),this.#m=new IntersectionObserver((e=>{e.forEach((e=>{e.isIntersecting&&t()}))})),this.#m.observe(e))}#b(){let t={};for(let e=0;e<this.data_fields.length;e++)t[this.data_fields[e]]=this.#h.data(this.data_fields[e]);return t}#f(t,e){$.ajax({url:t,method:"GET",data:this.#b(),success:t=>{e(t)}})}#_(){this.#h=$(`#${this.target_element_id}`)}start(t){this.#p((()=>{this.#f(this.url,(e=>{$(`#${this.insert_element_id}`).append(e),this.#m.disconnect(),t(),this.#_(),this.start(t)}))})),this.remove_target_id&&this.#h.removeAttr("id")}}function d(){document.getElementById("auth-content")&&(document.getElementById("auth-content").style.left=window.innerWidth/2-parseInt(function(t,e){const s=document.getElementById("auth-content");return getComputedStyle(s).getPropertyValue("width")}())/2+"px")}$.ajax({url:"/server-data",method:"GET",data:{},dataType:"json",success:t=>{!function(t){switch(t.curr_url_pattern){case"/":new u("last-post",["last_post_id","user_id"],"/post-load-home","content",!0).start((function(){n(),i(),c(),o()}));break;case"/profile/{username}":new u("last-post",["last_post_id","user_id"],"/post-load","content",!0).start((function(){n(),i(),c(),o()}))}}(t)}}),d(),document.getElementsByClassName("profile-description")&&(s.enableBlockScroll().enableBlur(),s.onClick((function(t){e(t.currentClickElement)})),s.display()),n(),document.getElementsByClassName("log-out-menu-btn")&&(l.enableBlur().enableBlockScroll(),l.onClick((function(t){e(t.currentClickElement);let s=window.pageYOffset||document.documentElement.scrollTop;t.currentClickElement.style.top=s+200+"px"})),l.display()),o(),window.onresize=function(){s.currentClickElement&&e(s.currentClickElement),d(),l.currentClickElement&&e(l.currentClickElement)},$(".comment-answer-btn").on("click",(function(){$("#answer_id").val($(this).data("comment-id")),$(".answer-name").html("@"+$(this).data("comment-username")),$(".answer-name").css("display","block")})),$(".answer-name").on("click",(function(){$("#answer_id").val(""),$(this).css("display","none")})),c(),i()})();