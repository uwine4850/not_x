(()=>{"use strict";class e{#e;#t;#n=[];currentClickElement;#l=!1;#s=!1;constructor(e,t){this.boardsClass=e,this.openElementsClass=t,this.#o()}#o(){for(let e=0;e<this.openElementsClass.length;e++)this.#n.push(document.getElementsByClassName(this.openElementsClass[e]))}getBoards(){return document.getElementsByClassName(this.boardsClass)}enableBlur(){return this.#l=!0,this}enableBlockScroll(){return this.#s=!0,this}#c(e){let t=document.getElementById("blur-all");e?t.classList.add("blur-hidden"):t.classList.remove("blur-hidden")}display(){let e=this.getBoards();this.#i(this.#e);for(const t of this.#n)for(let n=0;n<t.length;n++)t[n].onclick=t=>{if(t.target.classList.length>1)for(const e of t.target.classList)this.openElementsClass.includes(e)&&t.stopPropagation();this.openElementsClass.includes(t.target.className)&&t.stopPropagation(),this.#s&&("hidden"===this.#r()?document.body.style.overflow="auto":document.body.style.overflow="hidden"),this.#l&&this.#c(!1),e[n].classList.toggle("pop-up-board-hidden"),this.currentClickElement=e[n],this.#i(this.#t)};for(const t of e)document.addEventListener("click",(e=>{if(e.target.classList.length>1)for(const t of e.target.classList)if(this.openElementsClass.includes(t))return;e.target===t||this.openElementsClass.includes(e.target.className)||(t.classList.add("pop-up-board-hidden"),this.#s&&(document.body.style.overflow="auto"),this.#l&&this.#c(!0))}))}#r(){return getComputedStyle(document.body).overflow}#i(e){e&&e(this)}onDisplay(e){this.#e=e}onClick(e){this.#t=e}}function t(e){let t=document.getElementById("content").offsetWidth/2-e.offsetWidth/2;e.style.left=t+"px"}let n=new e("descr-board",["profile-description-span"]),l=new e(["log-out-board"],["log-out-menu-btn","log-out-menu-btn-img","log-out-menu-btn-text"]);function s(){document.getElementById("auth-content")&&(document.getElementById("auth-content").style.left=window.innerWidth/2-parseInt(function(e,t){const n=document.getElementById("auth-content");return getComputedStyle(n).getPropertyValue("width")}())/2+"px")}s(),document.getElementsByClassName("profile-description")&&(n.enableBlockScroll().enableBlur(),n.onClick((function(e){t(e.currentClickElement)})),n.display()),document.getElementsByClassName("post-menu-btn")&&new e("post-menu-board",["post-menu-btn","post-menu-btn-img"]).display(),document.getElementsByClassName("log-out-menu-btn")&&(l.enableBlur().enableBlockScroll(),l.onClick((function(e){t(e.currentClickElement)})),l.display()),window.onresize=function(){n.currentClickElement&&t(n.currentClickElement),s(),l.currentClickElement&&t(l.currentClickElement)}})();