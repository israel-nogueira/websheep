var functions, include_css, include_js, out, trace, Dinheiro;
var _global = window;
var _root	= document;


//#######################################################################
//#######################################################################
//####################################################################### PASSO A PASSO (TOUR)
//#######################################################################
//#######################################################################
(function(C,p){"object"===typeof exports?p(exports):"function"===typeof define&&define.amd?define(["exports"],p):p(C)})(this,function(C){function p(a){this._targetElement=a;this._introItems=[];this._options={nextLabel:"Next &rarr;",prevLabel:"&larr; Back",skipLabel:"Skip",doneLabel:"Done",tooltipPosition:"bottom",tooltipClass:"",highlightClass:"",exitOnEsc:!0,exitOnOverlayClick:!0,showStepNumbers:!0,keyboardNavigation:!0,showButtons:!0,showBullets:!0,showProgress:!1,scrollToElement:!0,overlayOpacity:0.8,
positionPrecedence:["bottom","top","right","left"],disableInteraction:!1,hintPosition:"top-middle",hintButtonLabel:"Got it"}}function P(a){var b=[],c=this;if(this._options.steps)for(var d=0,f=this._options.steps.length;d<f;d++){var e=y(this._options.steps[d]);e.step=b.length+1;"string"===typeof e.element&&(e.element=document.querySelector(e.element));if("undefined"===typeof e.element||null==e.element){var g=document.querySelector(".introjsFloatingElement");null==g&&(g=document.createElement("div"),
g.className="introjsFloatingElement",document.body.appendChild(g));e.element=g;e.position="floating"}null!=e.element&&b.push(e)}else{f=a.querySelectorAll("*[data-intro]");if(1>f.length)return!1;d=0;for(e=f.length;d<e;d++){var g=f[d],k=parseInt(g.getAttribute("data-step"),10);0<k&&(b[k-1]={element:g,intro:g.getAttribute("data-intro"),step:parseInt(g.getAttribute("data-step"),10),tooltipClass:g.getAttribute("data-tooltipClass"),highlightClass:g.getAttribute("data-highlightClass"),position:g.getAttribute("data-position")||
this._options.tooltipPosition})}d=k=0;for(e=f.length;d<e;d++)if(g=f[d],null==g.getAttribute("data-step")){for(;"undefined"!=typeof b[k];)k++;b[k]={element:g,intro:g.getAttribute("data-intro"),step:k+1,tooltipClass:g.getAttribute("data-tooltipClass"),highlightClass:g.getAttribute("data-highlightClass"),position:g.getAttribute("data-position")||this._options.tooltipPosition}}}d=[];for(f=0;f<b.length;f++)b[f]&&d.push(b[f]);b=d;b.sort(function(a,c){return a.step-c.step});c._introItems=b;Q.call(c,a)&&
(z.call(c),a.querySelector(".introjs-skipbutton"),a.querySelector(".introjs-nextbutton"),c._onKeyDown=function(b){if(27===b.keyCode&&!0==c._options.exitOnEsc)void 0!=c._introExitCallback&&c._introExitCallback.call(c),A.call(c,a);else if(37===b.keyCode)D.call(c);else if(39===b.keyCode)z.call(c);else if(13===b.keyCode){var d=b.target||b.srcElement;d&&0<d.className.indexOf("introjs-prevbutton")?D.call(c):d&&0<d.className.indexOf("introjs-skipbutton")?(c._introItems.length-1==c._currentStep&&"function"===
typeof c._introCompleteCallback&&c._introCompleteCallback.call(c),void 0!=c._introExitCallback&&c._introExitCallback.call(c),A.call(c,a)):z.call(c);b.preventDefault?b.preventDefault():b.returnValue=!1}},c._onResize=function(a){r.call(c,document.querySelector(".introjs-helperLayer"));r.call(c,document.querySelector(".introjs-tooltipReferenceLayer"))},window.addEventListener?(this._options.keyboardNavigation&&window.addEventListener("keydown",c._onKeyDown,!0),window.addEventListener("resize",c._onResize,
!0)):document.attachEvent&&(this._options.keyboardNavigation&&document.attachEvent("onkeydown",c._onKeyDown),document.attachEvent("onresize",c._onResize)));return!1}function y(a){if(null==a||"object"!=typeof a||"undefined"!=typeof a.nodeType)return a;var b={},c;for(c in a)b[c]="undefined"!=typeof jQuery&&a[c]instanceof jQuery?a[c]:y(a[c]);return b}function z(){this._direction="forward";"undefined"===typeof this._currentStep?this._currentStep=0:++this._currentStep;if(this._introItems.length<=this._currentStep)"function"===
typeof this._introCompleteCallback&&this._introCompleteCallback.call(this),A.call(this,this._targetElement);else{var a=this._introItems[this._currentStep];"undefined"!==typeof this._introBeforeChangeCallback&&this._introBeforeChangeCallback.call(this,a.element);K.call(this,a)}}function D(){this._direction="backward";if(0===this._currentStep)return!1;var a=this._introItems[--this._currentStep];"undefined"!==typeof this._introBeforeChangeCallback&&this._introBeforeChangeCallback.call(this,a.element);
K.call(this,a)}function A(a){var b=a.querySelector(".introjs-overlay");if(null!=b){b.style.opacity=0;setTimeout(function(){b.parentNode&&b.parentNode.removeChild(b)},500);var c=a.querySelector(".introjs-helperLayer");c&&c.parentNode.removeChild(c);(c=a.querySelector(".introjs-tooltipReferenceLayer"))&&c.parentNode.removeChild(c);(a=a.querySelector(".introjs-disableInteraction"))&&a.parentNode.removeChild(a);(a=document.querySelector(".introjsFloatingElement"))&&a.parentNode.removeChild(a);if(a=document.querySelector(".introjs-showElement"))a.className=
a.className.replace(/introjs-[a-zA-Z]+/g,"").replace(/^\s+|\s+$/g,"");if((a=document.querySelectorAll(".introjs-fixParent"))&&0<a.length)for(c=a.length-1;0<=c;c--)a[c].className=a[c].className.replace(/introjs-fixParent/g,"").replace(/^\s+|\s+$/g,"");window.removeEventListener?window.removeEventListener("keydown",this._onKeyDown,!0):document.detachEvent&&document.detachEvent("onkeydown",this._onKeyDown);this._currentStep=void 0}}function F(a,b,c,d,f){var e="",g,k;f=f||!1;b.style.top=null;b.style.right=
null;b.style.bottom=null;b.style.left=null;b.style.marginLeft=null;b.style.marginTop=null;c.style.display="inherit";"undefined"!=typeof d&&null!=d&&(d.style.top=null,d.style.left=null);if(this._introItems[this._currentStep]){e=this._introItems[this._currentStep];e="string"===typeof e.tooltipClass?e.tooltipClass:this._options.tooltipClass;b.className=("introjs-tooltip "+e).replace(/^\s+|\s+$/g,"");k=this._introItems[this._currentStep].position;if(("auto"==k||"auto"==this._options.tooltipPosition)&&
"floating"!=k){e=k;g=this._options.positionPrecedence.slice();k=G();var x=t(b).height+10,q=t(b).width+20,l=t(a),m="floating";l.left+q>k.width||0>l.left+l.width/2-q?(s(g,"bottom"),s(g,"top")):(l.height+l.top+x>k.height&&s(g,"bottom"),0>l.top-x&&s(g,"top"));l.width+l.left+q>k.width&&s(g,"right");0>l.left-q&&s(g,"left");0<g.length&&(m=g[0]);e&&"auto"!=e&&-1<g.indexOf(e)&&(m=e);k=m}e=t(a);a=t(b);g=G();switch(k){case "top":c.className="introjs-arrow bottom";H(e,f?0:15,a,g,b);b.style.bottom=e.height+20+
"px";break;case "right":b.style.left=e.width+20+"px";e.top+a.height>g.height?(c.className="introjs-arrow left-bottom",b.style.top="-"+(a.height-e.height-20)+"px"):c.className="introjs-arrow left";break;case "left":f||!0!=this._options.showStepNumbers||(b.style.top="15px");e.top+a.height>g.height?(b.style.top="-"+(a.height-e.height-20)+"px",c.className="introjs-arrow right-bottom"):c.className="introjs-arrow right";b.style.right=e.width+20+"px";break;case "floating":c.style.display="none";b.style.left=
"50%";b.style.top="50%";b.style.marginLeft="-"+a.width/2+"px";b.style.marginTop="-"+a.height/2+"px";"undefined"!=typeof d&&null!=d&&(d.style.left="-"+(a.width/2+18)+"px",d.style.top="-"+(a.height/2+18)+"px");break;case "bottom-right-aligned":c.className="introjs-arrow top-right";L(e,0,a,b);b.style.top=e.height+20+"px";break;case "bottom-middle-aligned":c.className="introjs-arrow top-middle";c=e.width/2-a.width/2;f&&(c+=5);L(e,c,a,b)&&(b.style.right=null,H(e,c,a,g,b));b.style.top=e.height+20+"px";
break;default:c.className="introjs-arrow top",H(e,0,a,g,b),b.style.top=e.height+20+"px"}}}function H(a,b,c,d,f){if(a.left+b+c.width>d.width)return f.style.left=d.width-c.width-a.left+"px",!1;f.style.left=b+"px";return!0}function L(a,b,c,d){if(0>a.left+a.width-b-c.width)return d.style.left=-a.left+"px",!1;d.style.right=b+"px";return!0}function s(a,b){-1<a.indexOf(b)&&a.splice(a.indexOf(b),1)}function r(a){if(a&&this._introItems[this._currentStep]){var b=this._introItems[this._currentStep],c=t(b.element),
d=10;I(b.element)&&(a.className+=" introjs-fixedTooltip");"floating"==b.position&&(d=0);a.setAttribute("style","width: "+(c.width+d)+"px; height:"+(c.height+d)+"px; top:"+(c.top-5)+"px;left: "+(c.left-5)+"px;")}}function R(){var a=document.querySelector(".introjs-disableInteraction");null===a&&(a=document.createElement("div"),a.className="introjs-disableInteraction",this._targetElement.appendChild(a));r.call(this,a)}function K(a){"undefined"!==typeof this._introChangeCallback&&this._introChangeCallback.call(this,
a.element);var b=this,c=document.querySelector(".introjs-helperLayer"),d=document.querySelector(".introjs-tooltipReferenceLayer"),f="introjs-helperLayer";t(a.element);"string"===typeof a.highlightClass&&(f+=" "+a.highlightClass);"string"===typeof this._options.highlightClass&&(f+=" "+this._options.highlightClass);if(null!=c){var e=d.querySelector(".introjs-helperNumberLayer"),g=d.querySelector(".introjs-tooltiptext"),k=d.querySelector(".introjs-arrow"),x=d.querySelector(".introjs-tooltip"),q=d.querySelector(".introjs-skipbutton"),
l=d.querySelector(".introjs-prevbutton"),m=d.querySelector(".introjs-nextbutton");c.className=f;x.style.opacity=0;x.style.display="none";if(null!=e){var h=this._introItems[0<=a.step-2?a.step-2:0];if(null!=h&&"forward"==this._direction&&"floating"==h.position||"backward"==this._direction&&"floating"==a.position)e.style.opacity=0}r.call(b,c);r.call(b,d);if((h=document.querySelectorAll(".introjs-fixParent"))&&0<h.length)for(f=h.length-1;0<=f;f--)h[f].className=h[f].className.replace(/introjs-fixParent/g,
"").replace(/^\s+|\s+$/g,"");h=document.querySelector(".introjs-showElement");h.className=h.className.replace(/introjs-[a-zA-Z]+/g,"").replace(/^\s+|\s+$/g,"");b._lastShowElementTimer&&clearTimeout(b._lastShowElementTimer);b._lastShowElementTimer=setTimeout(function(){null!=e&&(e.innerHTML=a.step);g.innerHTML=a.intro;x.style.display="block";F.call(b,a.element,x,k,e);d.querySelector(".introjs-bullets li > a.active").className="";d.querySelector('.introjs-bullets li > a[data-stepnumber="'+a.step+'"]').className=
"active";d.querySelector(".introjs-progress .introjs-progressbar").setAttribute("style","width:"+M.call(b)+"%;");x.style.opacity=1;e&&(e.style.opacity=1);-1===m.tabIndex?q.focus():m.focus()},350)}else{var p=document.createElement("div"),l=document.createElement("div"),c=document.createElement("div"),n=document.createElement("div"),s=document.createElement("div"),w=document.createElement("div"),E=document.createElement("div"),u=document.createElement("div");p.className=f;l.className="introjs-tooltipReferenceLayer";
r.call(b,p);r.call(b,l);this._targetElement.appendChild(p);this._targetElement.appendChild(l);c.className="introjs-arrow";s.className="introjs-tooltiptext";s.innerHTML=a.intro;w.className="introjs-bullets";!1===this._options.showBullets&&(w.style.display="none");for(var p=document.createElement("ul"),f=0,C=this._introItems.length;f<C;f++){var y=document.createElement("li"),B=document.createElement("a");B.onclick=function(){b.goToStep(this.getAttribute("data-stepnumber"))};f===a.step-1&&(B.className=
"active");B.href="javascript:void(0);";B.innerHTML="&nbsp;";B.setAttribute("data-stepnumber",this._introItems[f].step);y.appendChild(B);p.appendChild(y)}w.appendChild(p);E.className="introjs-progress";!1===this._options.showProgress&&(E.style.display="none");f=document.createElement("div");f.className="introjs-progressbar";f.setAttribute("style","width:"+M.call(this)+"%;");E.appendChild(f);u.className="introjs-tooltipbuttons";!1===this._options.showButtons&&(u.style.display="none");n.className="introjs-tooltip";
n.appendChild(s);n.appendChild(w);n.appendChild(E);!0==this._options.showStepNumbers&&(h=document.createElement("span"),h.className="introjs-helperNumberLayer",h.innerHTML=a.step,l.appendChild(h));n.appendChild(c);l.appendChild(n);m=document.createElement("a");m.onclick=function(){b._introItems.length-1!=b._currentStep&&z.call(b)};m.href="javascript:void(0);";m.innerHTML=this._options.nextLabel;l=document.createElement("a");l.onclick=function(){0!=b._currentStep&&D.call(b)};l.href="javascript:void(0);";
l.innerHTML=this._options.prevLabel;q=document.createElement("a");q.className="introjs-button introjs-skipbutton";q.href="javascript:void(0);";q.innerHTML=this._options.skipLabel;q.onclick=function(){b._introItems.length-1==b._currentStep&&"function"===typeof b._introCompleteCallback&&b._introCompleteCallback.call(b);b._introItems.length-1!=b._currentStep&&"function"===typeof b._introExitCallback&&b._introExitCallback.call(b);A.call(b,b._targetElement)};u.appendChild(q);1<this._introItems.length&&
(u.appendChild(l),u.appendChild(m));n.appendChild(u);F.call(b,a.element,n,c,h)}!0===this._options.disableInteraction&&R.call(b);l.removeAttribute("tabIndex");m.removeAttribute("tabIndex");0==this._currentStep&&1<this._introItems.length?(l.className="introjs-button introjs-prevbutton introjs-disabled",l.tabIndex="-1",m.className="introjs-button introjs-nextbutton",q.innerHTML=this._options.skipLabel):this._introItems.length-1==this._currentStep||1==this._introItems.length?(q.innerHTML=this._options.doneLabel,
l.className="introjs-button introjs-prevbutton",m.className="introjs-button introjs-nextbutton introjs-disabled",m.tabIndex="-1"):(l.className="introjs-button introjs-prevbutton",m.className="introjs-button introjs-nextbutton",q.innerHTML=this._options.skipLabel);m.focus();a.element.className+=" introjs-showElement";h=v(a.element,"position");"absolute"!==h&&"relative"!==h&&(a.element.className+=" introjs-relativePosition");for(h=a.element.parentNode;null!=h&&"body"!==h.tagName.toLowerCase();){c=v(h,
"z-index");n=parseFloat(v(h,"opacity"));u=v(h,"transform")||v(h,"-webkit-transform")||v(h,"-moz-transform")||v(h,"-ms-transform")||v(h,"-o-transform");if(/[0-9]+/.test(c)||1>n||"none"!==u&&void 0!==u)h.className+=" introjs-fixParent";h=h.parentNode}S(a.element)||!0!==this._options.scrollToElement||(n=a.element.getBoundingClientRect(),h=G().height,c=n.bottom-(n.bottom-n.top),n=n.bottom-h,0>c||a.element.clientHeight>h?window.scrollBy(0,c-30):window.scrollBy(0,n+100));"undefined"!==typeof this._introAfterChangeCallback&&
this._introAfterChangeCallback.call(this,a.element)}function v(a,b){var c="";a.currentStyle?c=a.currentStyle[b]:document.defaultView&&document.defaultView.getComputedStyle&&(c=document.defaultView.getComputedStyle(a,null).getPropertyValue(b));return c&&c.toLowerCase?c.toLowerCase():c}function I(a){var b=a.parentNode;return"HTML"===b.nodeName?!1:"fixed"==v(a,"position")?!0:I(b)}function G(){if(void 0!=window.innerWidth)return{width:window.innerWidth,height:window.innerHeight};var a=document.documentElement;
return{width:a.clientWidth,height:a.clientHeight}}function S(a){a=a.getBoundingClientRect();return 0<=a.top&&0<=a.left&&a.bottom+80<=window.innerHeight&&a.right<=window.innerWidth}function Q(a){var b=document.createElement("div"),c="",d=this;b.className="introjs-overlay";if("body"===a.tagName.toLowerCase())c+="top: 0;bottom: 0; left: 0;right: 0;position: fixed;",b.setAttribute("style",c);else{var f=t(a);f&&(c+="width: "+f.width+"px; height:"+f.height+"px; top:"+f.top+"px;left: "+f.left+"px;",b.setAttribute("style",
c))}a.appendChild(b);b.onclick=function(){!0==d._options.exitOnOverlayClick&&(void 0!=d._introExitCallback&&d._introExitCallback.call(d),A.call(d,a))};setTimeout(function(){c+="opacity: "+d._options.overlayOpacity.toString()+";";b.setAttribute("style",c)},10);return!0}function w(){var a=this._targetElement.querySelector(".introjs-hintReference");if(a){var b=a.getAttribute("data-step");a.parentNode.removeChild(a);return b}}function N(){for(var a=0,b=this._introItems.length;a<b;a++){var c=this._introItems[a];
O.call(this,c.hintPosition,c.element,c.targetElement)}}function T(a){w.call(this);var b=this._targetElement.querySelector('.introjs-hint[data-step="'+a+'"]');b&&(b.className+=" introjs-hidehint");"undefined"!==typeof this._hintCloseCallback&&this._hintCloseCallback.call(this,a)}function U(){var a=this,b=document.querySelector(".introjs-hints");null==b&&(b=document.createElement("div"),b.className="introjs-hints");for(var c=0,d=this._introItems.length;c<d;c++){var f=this._introItems[c];if(!document.querySelector('.introjs-hint[data-step="'+
c+'"]')){var e=document.createElement("a");e.href="javascript:void(0);";(function(b,c,d){b.onclick=function(e){e=e?e:window.event;e.stopPropagation&&e.stopPropagation();null!=e.cancelBubble&&(e.cancelBubble=!0);V.call(a,b,c,d)}})(e,f,c);e.className="introjs-hint";I(f.element)&&(e.className+=" introjs-fixedhint");var g=document.createElement("div");g.className="introjs-hint-dot";var k=document.createElement("div");k.className="introjs-hint-pulse";e.appendChild(g);e.appendChild(k);e.setAttribute("data-step",
c);f.targetElement=f.element;f.element=e;O.call(this,f.hintPosition,e,f.targetElement);b.appendChild(e)}}document.body.appendChild(b);"undefined"!==typeof this._hintsAddedCallback&&this._hintsAddedCallback.call(this)}function O(a,b,c){c=t.call(this,c);switch(a){default:case "top-left":b.style.left=c.left+"px";b.style.top=c.top+"px";break;case "top-right":b.style.left=c.left+c.width+"px";b.style.top=c.top+"px";break;case "bottom-left":b.style.left=c.left+"px";b.style.top=c.top+c.height+"px";break;
case "bottom-right":b.style.left=c.left+c.width+"px";b.style.top=c.top+c.height+"px";break;case "bottom-middle":b.style.left=c.left+c.width/2+"px";b.style.top=c.top+c.height+"px";break;case "top-middle":b.style.left=c.left+c.width/2+"px",b.style.top=c.top+"px"}}function V(a,b,c){"undefined"!==typeof this._hintClickCallback&&this._hintClickCallback.call(this,a,b,c);var d=w.call(this);if(parseInt(d,10)!=c){var d=document.createElement("div"),f=document.createElement("div"),e=document.createElement("div"),
g=document.createElement("div");d.className="introjs-tooltip";d.onclick=function(a){a.stopPropagation?a.stopPropagation():a.cancelBubble=!0};f.className="introjs-tooltiptext";var k=document.createElement("p");k.innerHTML=b.hint;b=document.createElement("a");b.className="introjs-button";b.innerHTML=this._options.hintButtonLabel;b.onclick=T.bind(this,c);f.appendChild(k);f.appendChild(b);e.className="introjs-arrow";d.appendChild(e);d.appendChild(f);this._currentStep=a.getAttribute("data-step");g.className=
"introjs-tooltipReferenceLayer introjs-hintReference";g.setAttribute("data-step",a.getAttribute("data-step"));r.call(this,g);g.appendChild(d);document.body.appendChild(g);F.call(this,a,d,e,null,!0)}}function t(a){var b={};b.width=a.offsetWidth;b.height=a.offsetHeight;for(var c=0,d=0;a&&!isNaN(a.offsetLeft)&&!isNaN(a.offsetTop);)c+=a.offsetLeft,d+=a.offsetTop,a=a.offsetParent;b.top=d;b.left=c;return b}function M(){return 100*(parseInt(this._currentStep+1,10)/this._introItems.length)}var J=function(a){if("object"===
typeof a)return new p(a);if("string"===typeof a){if(a=document.querySelector(a))return new p(a);throw Error("There is no element with given selector.");}return new p(document.body)};J.version="2.0";J.fn=p.prototype={clone:function(){return new p(this)},setOption:function(a,b){this._options[a]=b;return this},setOptions:function(a){var b=this._options,c={},d;for(d in b)c[d]=b[d];for(d in a)c[d]=a[d];this._options=c;return this},start:function(){P.call(this,this._targetElement);return this},goToStep:function(a){this._currentStep=
a-2;"undefined"!==typeof this._introItems&&z.call(this);return this},nextStep:function(){z.call(this);return this},previousStep:function(){D.call(this);return this},exit:function(){A.call(this,this._targetElement);return this},refresh:function(){r.call(this,document.querySelector(".introjs-helperLayer"));r.call(this,document.querySelector(".introjs-tooltipReferenceLayer"));return this},onbeforechange:function(a){if("function"===typeof a)this._introBeforeChangeCallback=a;else throw Error("Provided callback for onbeforechange was not a function");
return this},onchange:function(a){if("function"===typeof a)this._introChangeCallback=a;else throw Error("Provided callback for onchange was not a function.");return this},onafterchange:function(a){if("function"===typeof a)this._introAfterChangeCallback=a;else throw Error("Provided callback for onafterchange was not a function");return this},oncomplete:function(a){if("function"===typeof a)this._introCompleteCallback=a;else throw Error("Provided callback for oncomplete was not a function.");return this},
onhintsadded:function(a){if("function"===typeof a)this._hintsAddedCallback=a;else throw Error("Provided callback for onhintsadded was not a function.");return this},onhintclick:function(a){if("function"===typeof a)this._hintClickCallback=a;else throw Error("Provided callback for onhintclick was not a function.");return this},onhintclose:function(a){if("function"===typeof a)this._hintCloseCallback=a;else throw Error("Provided callback for onhintclose was not a function.");return this},onexit:function(a){if("function"===
typeof a)this._introExitCallback=a;else throw Error("Provided callback for onexit was not a function.");return this},addHints:function(){a:{var a=this._targetElement;this._introItems=[];if(this._options.hints)for(var a=0,b=this._options.hints.length;a<b;a++){var c=y(this._options.hints[a]);"string"===typeof c.element&&(c.element=document.querySelector(c.element));c.hintPosition=c.hintPosition||"top-middle";null!=c.element&&this._introItems.push(c)}else{c=a.querySelectorAll("*[data-hint]");if(1>c.length)break a;
a=0;for(b=c.length;a<b;a++){var d=c[a];this._introItems.push({element:d,hint:d.getAttribute("data-hint"),hintPosition:d.getAttribute("data-hintPosition")||this._options.hintPosition,tooltipClass:d.getAttribute("data-tooltipClass"),position:d.getAttribute("data-position")||this._options.tooltipPosition})}}U.call(this);document.addEventListener?(document.addEventListener("click",w.bind(this),!1),window.addEventListener("resize",N.bind(this),!0)):document.attachEvent&&(document.attachEvent("onclick",
w.bind(this)),document.attachEvent("onresize",N.bind(this)))}return this}};return C.introJs=J});


function addslashes(str) {
    str = str.replace(/\\/g, '\\\\');
    str = str.replace(/\'/g, '\\\'');
    str = str.replace(/\"/g, '\\"');
    str = str.replace(/\0/g, '\\0');
    return str;
}
 
function stripslashes(str) {
    str = str.replace(/\\'/g, '\'');
    str = str.replace(/\\"/g, '"');
    str = str.replace(/\\0/g, '\0');
    str = str.replace(/\\\\/g, '\\');
    return str;
}

$(function(e) {
	e.fn.selectText = function(){
	    var doc = document;
	    var element = this[0];
	    console.log(this, element);
	    if (doc.body.createTextRange) {
	        var range = document.body.createTextRange();
	        range.moveToElementText(element);
	        range.select();
	    } else if (window.getSelection) {
	        var selection = window.getSelection();        
	        var range = document.createRange();
	        range.selectNodeContents(element);
	        selection.removeAllRanges();
	        selection.addRange(range);
	    }
	};
})

//#######################################################################
//#######################################################################
//#######################################################################
//#######################################################################
//#######################################################################
//#######################################################################
function utf8_encode(e) {
	e = e.replace(/\r\n/g, "\n");
	var t = "";
	for(var n = 0; n < e.length; n++) {
		var r = e.charCodeAt(n);
		if(r < 128) {
			t += String.fromCharCode(r)
		} else if(r > 127 && r < 2048) {
			t += String.fromCharCode(r >> 6 | 192);
			t += String.fromCharCode(r & 63 | 128)
		} else {
			t += String.fromCharCode(r >> 12 | 224);
			t += String.fromCharCode(r >> 6 & 63 | 128);
			t += String.fromCharCode(r & 63 | 128)
		}
	}
	return t
}
function utf8_decode(e) {
	var t = "";
	var n = 0;
	var r = c1 = c2 = 0;
	while(n < e.length) {
		r = e.charCodeAt(n);
		if(r < 128) {
			t += String.fromCharCode(r);
			n++
		} else if(r > 191 && r < 224) {
			c2 = e.charCodeAt(n + 1);
			t += String.fromCharCode((r & 31) << 6 | c2 & 63);
			n += 2
		} else {
			c2 = e.charCodeAt(n + 1);
			c3 = e.charCodeAt(n + 2);
			t += String.fromCharCode((r & 15) << 12 | (c2 & 63) << 6 | c3 & 63);
			n += 3
		}
	}
	return t;
}
function base64_encode(e) {
	var _keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
	var t = "";
	var n, r, i, s, o, u, a;
	var f = 0;
	e = utf8_encode(e);
	while(f < e.length) {
		n = e.charCodeAt(f++);
		r = e.charCodeAt(f++);
		i = e.charCodeAt(f++);
		s = n >> 2;
		o = (n & 3) << 4 | r >> 4;
		u = (r & 15) << 2 | i >> 6;
		a = i & 63;
		if(isNaN(r)) {
			u = a = 64
		} else if(isNaN(i)) {
			a = 64
		}
		t = t + _keyStr.charAt(s) + _keyStr.charAt(o) + _keyStr.charAt(u) + _keyStr.charAt(a)
	}
	return t;
}
function base64_decode(e) {
	var _keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
	var t = "";
	var n, r, i;
	var s, o, u, a;
	var f = 0;
	e = e.replace(/[^A-Za-z0-9\+\/\=]/g, "");
	while(f < e.length) {
		s = _keyStr.indexOf(e.charAt(f++));
		o = _keyStr.indexOf(e.charAt(f++));
		u = _keyStr.indexOf(e.charAt(f++));
		a = _keyStr.indexOf(e.charAt(f++));
		n = s << 2 | o >> 4;
		r = (o & 15) << 4 | u >> 2;
		i = (u & 3) << 6 | a;
		t = t + String.fromCharCode(n);
		if(u != 64) {
			t = t + String.fromCharCode(r)
		}
		if(a != 64) {
			t = t + String.fromCharCode(i)
		}
	}
	t = utf8_decode(t);
	return t
}

 
window.jsonParse=function(){var r="(?:-?\\b(?:0|[1-9][0-9]*)(?:\\.[0-9]+)?(?:[eE][+-]?[0-9]+)?\\b)",k='(?:[^\\0-\\x08\\x0a-\\x1f"\\\\]|\\\\(?:["/\\\\bfnrt]|u[0-9A-Fa-f]{4}))';k='(?:"'+k+'*")';var s=new RegExp("(?:false|true|null|[\\{\\}\\[\\]]|"+r+"|"+k+")","g"),t=new RegExp("\\\\(?:([^u])|u(.{4}))","g"),u={'"':'"',"/":"/","\\":"\\",b:"\u0008",f:"\u000c",n:"\n",r:"\r",t:"\t"};function v(h,j,e){return j?u[j]:String.fromCharCode(parseInt(e,16))}var w=new String(""),x=Object.hasOwnProperty;return function(h,
j){h=h.match(s);var e,c=h[0],l=false;if("{"===c)e={};else if("["===c)e=[];else{e=[];l=true}for(var b,d=[e],m=1-l,y=h.length;m<y;++m){c=h[m];var a;switch(c.charCodeAt(0)){default:a=d[0];a[b||a.length]=+c;b=void 0;break;case 34:c=c.substring(1,c.length-1);if(c.indexOf("\\")!==-1)c=c.replace(t,v);a=d[0];if(!b)if(a instanceof Array)b=a.length;else{b=c||w;break}a[b]=c;b=void 0;break;case 91:a=d[0];d.unshift(a[b||a.length]=[]);b=void 0;break;case 93:d.shift();break;case 102:a=d[0];a[b||a.length]=false;
b=void 0;break;case 110:a=d[0];a[b||a.length]=null;b=void 0;break;case 116:a=d[0];a[b||a.length]=true;b=void 0;break;case 123:a=d[0];d.unshift(a[b||a.length]={});b=void 0;break;case 125:d.shift();break}}if(l){if(d.length!==1)throw new Error;e=e[0]}else if(d.length)throw new Error;if(j){var p=function(n,o){var f=n[o];if(f&&typeof f==="object"){var i=null;for(var g in f)if(x.call(f,g)&&f!==n){var q=p(f,g);if(q!==void 0)f[g]=q;else{i||(i=[]);i.push(g)}}if(i)for(g=i.length;--g>=0;)delete f[i[g]]}return j.call(n,
o,f)};e=p({"":e},"")}return e}}();

function popup (url, w, h) {
	var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
	var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;
	var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
	var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;
	var left = ((width / 2) - (w / 2)) + dualScreenLeft;
	var top = ((height / 2) - (h / 2)) + dualScreenTop;
	var newWindow = window.open(url,null, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
}

function getUrlVars(url) {
	url = url.replace(/&amp;/g,"ws_amp");
	var vars = {};
	var parts = url.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {vars[key]=value.replace(/ws_amp/g,"&amp;");});
	return vars;
}

function autoSave(seconds){
		if(window.autoSaveInterval){clearInterval(window.autoSaveInterval)};
		window.autoSaveInterval=setInterval(function(){
			if($("#bt_SalvarItem").length && !$("#ws_confirm").length){
				$("#bt_SalvarItem").click(); 
			}
		},(seconds*1000)*60);
}
//#######################################################################
//#######################################################################
//####################################################################### LOAD PLUGIN FILE
//#######################################################################
//#######################################################################
function loadPluginFile(opcoes){
	var options = $.extend({
		filename	:null, 
		pathname	:null, 
		mensagem	:"Preparando ambiente",
		type		:"inner",
		dataW		:500,
		dataH		:500
	}, opcoes);
	jQuery.ajax({
		type: "POST",
		sync: true,
		beforeSend:function(){confirma({width:"auto",conteudo:options.mensagem+"<div class=\'preloaderupdate\' style=\'left: 50%;margin-left: -15px; position: absolute;width: 30px;height: 18px;top: 53px;background-image:url(\""+ws.rootPath+"admin/app/templates/img/websheep/loader_thumb.gif\");background-repeat:no-repeat;background-position: top center;\'></div>",drag:false,bot1:0,bot2:0}); },
		url: "./app/ws-modules/ws-tools/functions.php",
		data: {"function":"returnFileInnerPlugin","page":options.filename}
	}).done(function(e) {
		if(options.type=='inner'){
			$( "#conteudo" ).html('<div class="ws-plugin"><div>');
			$( "#conteudo .ws-plugin" ).html(e)
			$("#ws_confirm").remove();
			$("*").removeClass("scrollhidden").removeClass("blur");
			$('#container').perfectScrollbar('destroy');
		}else if(options.type=='modal'){
			confirma({
				width:dataW,
				height:dataH,
				conteudo:'<div class="ws-plugin">'+e+'<div>',
				drag:false,
				posFn:function(){$("#ws_confirm #body").css({"padding-bottom":"0px","padding-top":"0px"}); },
				bot1:0,
				bot2:0,
				botclose:1
			})
		}else if(options.type=='iframe'){
				$('#conteudo').html('<iframe src="'+options.filename+'" style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;margin:0;"></iframe>');
				$( "#conteudo .ws-plugin" ).html(e)
				$("#ws_confirm").remove();
				$("*").removeClass("scrollhidden").removeClass("blur");
				$('#container').perfectScrollbar('destroy');
		}
				window.CloseMenu();
	})
}

//#######################################################################
//#######################################################################
//####################################################################### notificações
//#######################################################################
//#######################################################################

function downloadFile(opcoes){
		var options = $.extend({
			typeSend:"GET",
			file:null,
			newfile:null,
			abort:function(e){},
			error:function(e){},
			load:function(e){},
			finish:function(e){},
			progress:function(e){}
		}, opcoes);
		if(options.file==null){
			alert("Por favor, dê um nome ao arquivo...");return false
		}
		jQuery.ajax({
		  xhr: function() {
				var xhr = new window.XMLHttpRequest();
				xhr.responseType = "arraybuffer";
				xhr.addEventListener("abort", function() {options.abort()})
				xhr.addEventListener("error", function() {options.error()})
				xhr.addEventListener("loadend", function() {options.finish()})
				xhr.addEventListener("load", function() {
					console.log('load')
					//############################################################################################
					//##################################################################### capta data-type e nome
					//############################################################################################
					var file_type = xhr.getResponseHeader('Content-Type');
					var disposition = xhr.getResponseHeader('Content-Disposition');
					if (disposition && disposition.indexOf('attachment') !== -1) {
						var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
						var matches = filenameRegex.exec(disposition);
						if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
					}else{
						filename = options.file.replace(/^.*[\\\/]/, '')
					}
					window.BlobBuilder = window.BlobBuilder || window.WebKitBlobBuilder || window.MozBlobBuilder || window.MSBlobBuilder;
					window.URL = window.URL || window.webkitURL;
					var arrayBufferView = new Uint8Array( this.response );
					var blob = new Blob( [ arrayBufferView ], { type: file_type } );
					var urlCreator = window.URL || window.BlobBuilder;
					var imageUrl = urlCreator.createObjectURL(blob);
					var a = document.createElement("a");
					document.body.appendChild(a);
					a.href = imageUrl;
					if(options.newfile!=null){
						a.download = options.newfile;
					}else{
						a.download = filename;
					}
					a.click(); 
					options.load()

				}, false);
				xhr.addEventListener("progress", function(evt){
					if (evt.lengthComputable) {
						var percentComplete = Math.floor((evt.loaded / evt.total)*100);
						console.log(percentComplete)
						options.progress(percentComplete)
					}
				}, false);
				return xhr;
			},
			type: options.typeSend,
			url: options.file
		});
}
//#######################################################################
//#######################################################################
//#######################################################################  INCLUDES
//#######################################################################
//#######################################################################
function getScript(script, data, textStatus, jqxhr){
	$.getScript( script, function( data, textStatus, jqxhr ) {
	  console.log( data ); // Data returned
	  console.log( textStatus ); // Success
	  console.log( jqxhr.status ); // 200
	  console.log( "Load was performed." );
	});
}



function include_css(documento, id, Media) {
	//console.log("Incluindo arquivo .CSS:  " + documento);
	if ($('#' + id).length) {
		$('#' + id).remove()
	}
	var script = document.createElement('link');
	script.id = id;
	script.rel = 'stylesheet';
	script.type = 'text/css';
	script.media = Media;
	script.href = documento;
	var s = document.getElementsByTagName('link')[0];
	s.parentNode.insertBefore(script, s);
}

function include_js(documento,id,reload) {
	if(!reload){reload=false;}
	if(!$('script#' + id).length){
		var script = document.createElement('script');
		script.id = id;
		script.type = 'text/javascript';
		script.src = documento;
		var s = document.getElementsByTagName('script')[0];
		s.parentNode.insertBefore(script, s);
	}else if($('script#' + id).length && reload==true){
		out("Substituindo arquivo .JS: \n  #" +id+" : "+documento)
		$('script#' + id).remove();
		var script = document.createElement('script');
		script.id = id;
		script.type = 'text/javascript';
		script.src = documento;
		var s = document.getElementsByTagName('script')[0];
		s.parentNode.insertBefore(script, s);
	}
}
function out(msn) {console.log(msn)}
function trace(msn) {console.log(msn)}

window.sanfona = function (opcoes){
	if ( typeof opcoes === 'string' ) { 
		var options = $.extend({
			cabecalho:opcoes,
			initOpen:function(e){},
			initClose:function(e){},
			finishOpen:function(e){},
			finishClose:function(e){},
		},null);
	}else{
		var options = $.extend({
			cabecalho:"",
			initOpen:function(e){},
			initClose:function(e){},
			finishOpen:function(e){},
			finishClose:function(e){},
		}, opcoes);
	}
	$(options.cabecalho).next().slideUp("slow");
	$(options.cabecalho).unbind("click tap press").bind("click tap press", function(){
		
		if($(this).next().hasClass('SanfonaOpen')){
			$(this).removeClass('FolderOpen');
			options.initClose($(this));
			$(this).next().slideUp("slow",function(){
				options.finishClose($(this))
			}).removeClass('SanfonaOpen');
		}else{
			options.initOpen($(this))
			$(this).addClass('FolderOpen');
			$(this).next().slideDown("slow",function(){
			options.finishOpen($(this))
			}).addClass('SanfonaOpen');
		};
	});
}

//#######################################################################
//#######################################################################
//#######################################################################
//#######################################################################  MASK MONETARIA:   http://jquerypriceformat.com/#download
//#######################################################################  $('#example2').Dinheiro({prefix: 'R$ ', centsSeparator: ',', thousandsSeparator: '.'});
//#######################################################################
//#######################################################################
$(function(e) {
	e.fn.Dinheiro = function(t) {
		var n = {
			prefix: "US$ ",
			suffix: "",
			centsSeparator: ".",
			thousandsSeparator: ",",
			limit: false,
			centsLimit: 2,
			clearPrefix: false,
			clearSufix: false,
			allowNegative: false,
			insertPlusSign: false,
			clearOnEmpty: false
		}
		var t = e.extend(n, t);
		return this.each(function() {
			function m(e) {
				if (n.is("input")) n.val(e);
				else n.html(e)
			};

			function g() {
				if (n.is("input")) r = n.val();
				else r = n.html();
				return r
			}

			function y(e) {
				var t = "";
				for (var n = 0; n < e.length; n++) {
					char_ = e.charAt(n);
					if (t.length == 0 && char_ == 0) char_ = false;
					if (char_ && char_.match(i)) {
						if (f) {
							if (t.length < f) t = t + char_
						} else {
							t = t + char_;
						}
					}
				}
				return t
			}

			function b(e) {
				while (e.length < l + 1) e = "0" + e;
				return e
			}

			function w(t, n) {
				if (!n && (t === "" || t == w("0", true)) && v) return "";
				var r = b(y(t));
				var i = "";
				var f = 0;
				if (l == 0) {
					u = "";
					c = ""
				};
				var c = r.substr(r.length - l, l);
				var h = r.substr(0, r.length - l);
				r = l == 0 ? h : h + u + c;
				if (a || e.trim(a) != "") {
					for (var m = h.length; m > 0; m--) {
						char_ = h.substr(m - 1, 1);
						f++;
						if (f % 3 == 0) char_ = a + char_;
						i = char_ + i
					};
					if (i.substr(0, 1) == a) i = i.substring(1, i.length);
					r = l == 0 ? i : i + u + c
				}
				if (p && (h != 0 || c != 0)) {
					if (t.indexOf("-") != -1 && t.indexOf("+") < t.indexOf("-")) {
						r = "-" + r
					} else {
						if (!d) r = "" + r;
						else r = "+" + r
					}
				}
				if (s) r = s + r;
				if (o) r = r + o;
				return r
			};

			function E(e) {
				var t = e.keyCode ? e.keyCode : e.which;
				var n = String.fromCharCode(t);
				var i = false;
				var s = r;
				var o = w(s + n);
				if (t >= 48 && t <= 57 || t >= 96 && t <= 105) i = true;
				if (t == 8) i = true;
				if (t == 9) i = true;
				if (t == 13) i = true;
				if (t == 46) i = true;
				if (t == 37) i = true;
				if (t == 39) i = true;
				if (p && (t == 189 || t == 109 || t == 173)) i = true;
				if (d && (t == 187 || t == 107 || t == 61)) i = true;
				if (!i) {
					e.preventDefault();
					e.stopPropagation();
					if (s != o) m(o)
				}
			}

			function S() {
				var e = g();
				var t = w(e);
				if (e != t) m(t);
				if (parseFloat(e) == 0 && v) m("")
			}

			function x() {
				n.val(s + g())
			}

			function T() {
				n.val(g() + o)
			}

			function N() {
				if (e.trim(s) != "" && c) {
					var t = g().split(s);
					m(t[1])
				}
			}

			function C() {
				if (e.trim(o) != "" && h) {
					var t = g().split(o);
					m(t[0])
				}
			}
			var n = e(this);
			var r = "";
			var i = /[0-9]/;
			if (n.is("input")) r = n.val();
			else r = n.html();
			var s = t.prefix;
			var o = t.suffix;
			var u = t.centsSeparator;
			var a = t.thousandsSeparator;
			var f = t.limit;
			var l = t.centsLimit;
			var c = t.clearPrefix;
			var h = t.clearSuffix;
			var p = t.allowNegative;
			var d = t.insertPlusSign;
			var v = t.clearOnEmpty;
			if (d) p = true;
			n.bind("keydown.price_format", E);
			n.bind("keyup.price_format", S);
			n.bind("focusout.price_format", S);
			if (c) {
				n.bind("focusout.price_format", function() {
					N()
				});
				n.bind("focusin.price_format", function() {
					x()
				})
			}
			if (h) {
				n.bind("focusout.price_format", function() {
					C()
				});
				n.bind("focusin.price_format", function() {
					T()
				})
			}
			if (g().length > 0) {
				S();
				N();
				C()
			}
		})
	}
	e.fn.unDinheiro = function() {
		return e(this).unbind(".price_format")
	};
	e.fn.unmask = function() {
		var t;
		var n = "";
		if (e(this).is("input")) t = e(this).val();
		else t = e(this).html();
		for (var r in t) {
			if (!isNaN(t[r]) || t[r] == "-") n += t[r]
		}
		return n
	}})
//#######################################################################
//#######################################################################
//#######################################################################
//#######################################################################  CONFIRMA
//#######################################################################
//#######################################################################
//#######################################################################


function confirma(opcoes) {
	var options = $.extend({
		conteudo: "Mensagem padrão",
		width: 500,
		height: 'auto',
		mleft: 0,
		mtop: 0,
		posFn: function() {},
		Init: function() {},
		posClose: function() {},
		bots:[
			// {
			// 	label:"Aceitar",
			// 	class:"Ok",
			//	ErrorCheck	: function() {},
			//	Check 		: function() {return true}
			// 	style:"Ok",
			// 	action:function(){console.log("1111111")}
			// }
		],
		bot1		: "Ok",
		bot2		: "Cancelar",
		idModal	: "ws_confirm",
		divScroll	: "body",
		divBlur		: "body #container",
		drag		: true,
		botclose	: false,
		newFun		: function() {},
		onCancel	: function() {},
		onClose		: function() {},
		Callback	: function() {},
		ErrorCheck	: function() {},
		Check 		: function() {return true}
	}, opcoes)
	options.Init();
	var ArryBotoes = "";
	//console.log(options.bots.length)
	var largBot 		= (100/options.bots.length);
	var marBots 		= (options.bots.length * 5);
	var index_highest 	= 1000;
	$(".ws_popup_confirm").each(function() {
	    var index_current = parseInt($(this).css("zIndex"),10);
	    if (index_current >= index_highest) {
	        index_highest = index_current+1;
	    }
	});


	// MONTA OS BOTÕES DO ALERTA
	$.each(options.bots, function( index, value ) {
		var id 	= "botConfirma_"+index+"_"+index_highest;
		ArryBotoes +="<div id='"+id+"' class='botao "+value.class+"' style='width:calc("+largBot+"% - 6px);margin: 0 2px;float: left;position: relative;padding: 10px 0;"+value.style+"'>" + value.label + "</div>\n";
	});
	// SE TIVER BOTÕES:
	if(options.bots.length>0){
		options.bot1 = false;
		options.bot2 = false;
		var Botoes = "<div id='bottons' class='bottons'>" + ArryBotoes + "</div>";
	}else{
		if (options.bot1 == false) {
			var botao1 = ""
		} else {
			var botao1 = "<div id='aceitar' class='botao aceitar'>" + options.bot1+"</div>"
		}
		if (options.bot2 == false) {
			var botao2 = ""
		} else {
			var botao2 = "<div id='recusar' class='recusar'>" + options.bot2+"</div>"
		}
		if (options.botclose == false) {
			var BotClose = ""
		} else {
			var BotClose = "<div id='close' class='botao close' >x</div>"
		}
		if (options.bot1 == false && options.bot2 == false) {
			var Botoes = "";
		} else {
			var Botoes = "<div id='bottons' class='bottons'>" + botao1 + botao2 + "</div>";
		}
	}

	if($.type(options.idModal) === "string"){
		if(options.idModal.indexOf("#")==0){
			options.idModal = options.idModal.slice(1);
		}else{
			options.idModal = options.idModal;
		}
	}else{return false;}

	$("#"+options.idModal).remove();
	$('body').prepend("<div id='"+options.idModal+"' class='ws_popup_confirm' style='opacity:1;width:100%;height:100%;z-index:"+index_highest+"!important'><div class='body'>" + BotClose + "<div class='ws_confirm_conteudo w1'>" + options.conteudo + "</div>" + Botoes+ "</div></div>");	$("#"+options.idModal+" .body").css({"width": options.width,"height": options.height});

	if (options.bot2 == false) {$("#"+options.idModal+" .aceitar").css({"left": '50%',"transform":"translateX(-50%)"});}
	$(options.divScroll).addClass("scrollhidden");
	$("#"+options.idModal).fadeIn('fast',function(){});
	var closed = false
	options.posFn();
	$(options.divBlur).addClass("blur");
	$("#"+options.idModal+" .body").css({"cursor": 'default'})
	function closeAlert() {
		closed = true;
		$(options.divScroll).removeClass("scrollhidden");
		$(options.divBlur).removeClass("blur");
		$("#"+options.idModal).animate({
			opacity: 0
		}, 200, 'linear', function() {
			$("#"+options.idModal).remove()
		});
		options.posClose();
	}

	if(options.bots.length>0){
		$.each(options.bots, function( index, value ) {
			$("#botConfirma_"+index+"_"+index_highest).click(function(){ 
			if(typeof(value.check) !== "undefined" && value.check != null && typeof(value.check)=='function'){
					if (options.Check() == true) {
						value.action(); 
						closeAlert();
					}else {
						options.ErrorCheck();
					}
				}else{
					value.action(); 
					closeAlert();
				}
			});		
		});
	}

	$("#"+options.idModal+" .recusar").click(function() {
		options.onCancel();
		closeAlert();
	});
	$("#"+options.idModal+" .close").click(function() {
		options.onClose();
		closeAlert();
	});
	$("#"+options.idModal+" .aceitar").click(function() {
		if (options.Check() == true) {
			options.newFun();
			options.Callback();
			closeAlert();
		}else {
			options.ErrorCheck();
		}
	});
}



//#######################################################################
//#######################################################################
//#######################################################################
//#######################################################################  CARREGANDO TELAS
//#######################################################################
//#######################################################################
//#######################################################################

function loadModule(opcoes) {
	options = $.extend({
		preload: true,
		loadMsn: "loading...",
		file: 	 null
	});
	if(options.file==null){TopAlert({mensagem: "Ops, faltou o caminho do arquivo",type: 2});return false;}
	if(options.preload==true){confirma({conteudo: options.loadMsn, width: 'auto', height: 'auto'});}
	$("#conteudo").load(options.file,function(){
		if(options.preload==true){
			$("#ws_confirm").remove();
			$("#body").removeClass("scrollhidden");
			$("*").removeClass("blur");
			setTimeout(function(){
				$('#container').perfectScrollbar();
				$('#container').perfectScrollbar('update');
			},1000);
		}
	});
}


//#######################################################################
//#######################################################################
//#######################################################################
//#######################################################################  --------------------------------------------------FUNÇÃO GLOBAL
//#######################################################################
//#######################################################################
//#######################################################################
function functions(opcoes) {
	options = $.extend({
		patch: "",
		funcao: "",
		vars: "",
		metodo: "post",
		Init: function(e) {},
		beforeSend: function(e) {},
		Progress: function(e) {},
		ajaxSend: function(e) {},
		Loading: function(e) {},
		Sucess: function(e) {},
		Error: function(e) {},
		complete: function(e) {},
		posFn: function(e) {},
		done: function(e) {},
		Callback: function(e) {},
		ErrorCheck: function() {},
		Check: function(e) {return true}
	}, opcoes);


	if (options.funcao == '') {alert('Por favor, determine uma função')}
	if (options.patch == '') {alert('Determine um patch a Função:  "' + options.funcao + '"')}
	options.Init();

	if (options.Check() == true) {
		jQuery.ajax({
			type: "POST",
			url: window.location + options.patch + "/functions.php",
			data: "function=" + options.funcao + "&" + options.vars,
			async: true,
			beforeSend: function(data) {
				options.beforeSend(data)
			}, //1
			ajaxSend: function(data) {
				options.ajaxSend(data.responseText)
			},
			success: function(data) {
				options.Sucess(data)
			}, //2 ----- traz retorno do php
			error: function(data) {
				options.Error(data)
			},
			complete: function(data) {
				options.complete(data.responseText)
			} //4 ---  esse traz o retorno
		}).done(function(data) {
			options.done(data.responseText)
		}); //3
	} else {
		options.ErrorCheck();
	}
}


//#######################################################################
//#######################################################################
//#######################################################################  POSIÇÃO DO MOUSE
//#######################################################################
//#######################################################################

$(document).bind("mousemove", function(event) {
	document.mouse_x = event.pageX;
	document.mouse_y = event.pageY;
});
// inclui css's corretivos

//if (BrowserDetect.browser == "Firefox") {
//	include_css("http://" + document.location.hostname + '/css/firefox.css', 'cssFf', 'All');
//}
//if (BrowserDetect.browser == "Opera") {
//	include_css("http://" + document.location.hostname + '/css/opera.css', 'cssOp', 'All');
//}
//#######################################################################
//#######################################################################  Touch iPad iPhone
//#######################################################################
//(function(b){b.support.touch="ontouchend" in document;if(!b.support.touch){return;}var c=b.ui.mouse.prototype,e=c._mouseInit,a;function d(g,h){if(g.originalEvent.touches.length>1){return;}g.preventDefault();var i=g.originalEvent.changedTouches[0],f=document.createEvent("MouseEvents");f.initMouseEvent(h,true,true,window,1,i.screenX,i.screenY,i.clientX,i.clientY,false,false,false,false,0,null);g.target.dispatchEvent(f);}c._touchStart=function(g){var f=this;if(a||!f._mouseCapture(g.originalEvent.changedTouches[0])){return;}a=true;f._touchMoved=false;d(g,"mouseover");d(g,"mousemove");d(g,"mousedown");}c._touchMove=function(f){if(!a){return;}this._touchMoved=true;d(f,"mousemove");}c._touchEnd=function(f){if(!a){return;}d(f,"mouseup");d(f,"mouseout");if(!this._touchMoved){d(f,"click");}a=false;}c._mouseInit=function(){var f=this;f.element.bind("touchstart",b.proxy(f,"_touchStart")).bind("touchmove",b.proxy(f,"_touchMove")).bind("touchend",b.proxy(f,"_touchEnd"));e.call(f);}})(jQuery);


function TopAlert(opcoes) {
	var options = $.extend({
		mensagem: "Mensagem padrão",
		clickclose: true,
		height: 20,
		botClose:null,
		onClose:function(){},
		posFn:function(){},
		timeoutFn:function(){},
		timer: 3000,
		type: null,
		color: "#E04E1F",
		background: "#F3DB7A",
		bottomColor:"#F5C814",
	}, opcoes)
	if(options.clickclose==true){
		$('#avisoTopo').unbind("click").click(function() {
			$(this).animate({height: 0,"padding": 0}, 200, 'linear');
			options.onClose();
		})
	}
	clearTimeout(window.recolheTopAlert);
	$('#avisoTopo').animate({
		height: 0,
		'padding': 0
	}, 200, 'linear', function() {

		if (options.type == 1) {
			options.color 		= "#E04E1F";
			options.background 	= "#F3DB7A";
			options.bottomColor = "#F5C814";
		}
		if (options.type == 2) {
			options.color 		= "#FFF";
			options.background 	= "#D4250D";
			options.bottomColor = "#990600";
		}
		if (options.type == 3) {
			options.color 		= "#FFF";
			options.background 	= "#85BE47";
			options.bottomColor = "#439900";
		}
		if (options.type == 4) {
			options.color 		= "#FFF";
			options.background 	= "#61A8D8";
			options.bottomColor = "#003D99";
		}
		$('#avisoTopo').css({"background":options.background,"color":options.color,"border-bottom-color":options.bottomColor});
		$('#avisoTopo').html(options.mensagem);
		if(options.botClose != null){
			$(options.botClose).unbind("click").click(function() {
				$("#avisoTopo").animate({height: 0,"padding": 0}, 200, 'linear');
				options.onClose()
			})
		}
		options.posFn();
		$('#avisoTopo').animate({height: options.height,"padding": 10}, 200, 'linear');
		window.recolheTopAlert = setTimeout(function() {
			$('#avisoTopo').animate({height: 0,"padding": 0}, 200, 'linear');
			options.timeoutFn();
		}, options.timer);
	});
}

//#######################################################################
//#######################################################################
//#######################################################################
//#######################################################################  ALERTA
//#######################################################################
//#######################################################################
//#######################################################################
function Alert(opcoes) {
	var options = $.extend({
		conteudo: "Mensagem padrão",
		width: 500,
		height: 'auto',
		divScroll: "body",
		divBlur: "body #container",
		drag: false,
		mleft: 0,
		mtop: 0,
		Callback: function(e) {}
	}, opcoes)
	$('body').prepend("<div id='ws_alert'><div id='body' class='bg02'><div id='conteudo'>" + options.conteudo + "</div><div id='bottons'><div id='ok' class='botao'>OK</div></div></div></div>");
	$("#ws_alert #body").on("mouseout", function() {});
	$("#ws_alert #body").css({
		"width": options.width,
		"height": 'auto'
	});
	var meiow = -(($("#ws_alert #body").width() / 2) - options.mleft)
	var meioh = -(($("#ws_alert #body").height() / 2) + 50 - options.mtop)
	var closed = false
	$("#ws_alert #body").css({
		"margin-left": meiow,
		"margin-top": meioh,
		"padding-top": 20
	});
	$(options.divScroll).addClass("scrollhidden");
	$(options.divBlur).addClass("blur");
	$('#ws_alert').animate({
		opacity: 0
	}, 0);
	$('#ws_alert').animate({
		opacity: 1
	}, 200);

	if (options.drag == false) {
		$("#ws_alert #body").css({
			"cursor": 'default'
		})
	} else {
		$("#ws_alert #body").draggable();
		$("#ws_alert #body").css({
			"cursor": 'move'
		})
	}



	var e = $.Event("click", function() {});
	$("#ws_alert #body").trigger(e);

	function closeAlert() {
		closed = true;
		$(options.divScroll).removeClass("scrollhidden");
		$(options.divBlur).removeClass("blur");
		$('#ws_alert').animate({
			opacity: 0
		}, 200, 'linear', function() {
			$('#ws_alert').remove()
		});

	}
	$("#ok").click(function() {
		var funct = options.Callback;
		funct();
		closeAlert()
	});
}


//#######################################################################
//#######################################################################
//#######################################################################  CONTEXT MENU
//#######################################################################
//####################################################################### ;
/*
var contextmenu = "<div id='ContextMenu' class='bg02'>";
contextmenu += "<div id='logomarca'>";
contextmenu += "<div id='beta'>°Beta</div>";
contextmenu += "<spam id='h1'>Web</spam><spam id='h2'>Sheep</spam>";
contextmenu += "<div id='SubTit'>Content Management Systems</div>";
contextmenu += "<div id='Copyrigth'>WebSheep Tecnologia Integrada Ltda  <br>  © Copyrigth 2013 - Direitos Protegidos</div>";
contextmenu += "</div>";
contextmenu += "</div>";

$(document).bind("contextmenu", function(event) {
	if ($("#ContextMenu").length) {
		$("#ContextMenu").hide();
	} else {
		$('body').prepend(contextmenu);
		$("#ContextMenu").hide();
	}

	if (event.pageX >= (self.innerWidth - ($("#ContextMenu").width() + 24))) {
		$("#ContextMenu").css({
			"top": event.pageY,
			"left": (event.pageX - ($("#ContextMenu").width() + 24))
		})
	} else {
		$("#ContextMenu").css({
			"top": event.pageY,
			"left": (event.pageX)
		})
	}

	if (event.pageY >= (self.innerHeight - ($("#ContextMenu").height()))) {
		$("#ContextMenu").css({
			"top": (event.pageY - ($("#ContextMenu").height()))
		})
	} else {
		$("#ContextMenu").css({
			"top": event.pageY
		})
	}
	$("#ContextMenu").show();
	return false;
});;
$(document).bind("click", function() {
	$("#ContextMenu").fadeOut(200, '', function() {
		$("#ContextMenu").remove();
	});
});
*/

//#######################################################################
//#######################################################################
//#######################################################################  balãosinho de legenda
//#######################################################################
//####################################################################### 
jQuery.fn.LegendaOver = function(msn) {
	return this.each(function() {
			$(this).mouseover(function() {
			$("#Balao_ToolType").remove();
			$('body').prepend('<div id="Balao_ToolType" class="bg_01">' + $(this).attr('legenda') + '<div id="pointer"  class="pontab"></div></div>');
			$("#Balao_ToolType").bind("click",function(){$(this).remove();}).hide().fadeIn('fast');
			
			var altura = $("#Balao_ToolType").height() + 15;
			var largura = $("#Balao_ToolType").width() + 20;
			var paddLeft = $(this).css('padding-left') + "str"
			paddLeft = paddLeft.toString();
			paddLeft = paddLeft.substr(0, paddLeft.length - 2);
			paddLeft = parseFloat(paddLeft);
			var paddRight = $(this).css('padding-right') + "str"
			paddRight = paddRight.toString();
			paddRight = paddRight.substr(0, paddRight.length - 2);
			paddRight = parseFloat(paddRight);
			$("#Balao_ToolType").css({
				"left": $(this).offset().left + (paddLeft + ((paddRight - paddLeft) / 2)) + ($(this).width() / 2) - (largura / 2)
			});
			if ($(this).offset().top < altura) {
				$("#Balao_ToolType  #pointer").removeClass('pontab')
				$("#Balao_ToolType  #pointer").addClass('pontat')
				$("#Balao_ToolType").css({
					"top": $(this).offset().top + $(this).height() + 15
				})
			} else {
				$("#Balao_ToolType  #pointer").removeClass('pontat')
				$("#Balao_ToolType  #pointer").addClass('pontab')
				$("#Balao_ToolType #pointer").css({
					"top": altura - 6
				})
				$("#Balao_ToolType").css({
					"top": $(this).offset().top - altura
				})
			}
		});
		$(this).mouseout(function() {
			$("#Balao_ToolType").remove()
		});
	});
}
//#######################################################################
//#######################################################################
//#######################################################################
//#######################################################################  ORDENA UL LI
//#######################################################################
//#######################################################################
//#######################################################################
function _orderlist(op) {
	options = $.extend({
		ul: "",
		filtro: "#div",
		order: 'asc',
		reload: function() {}
	}, op)
	ul = $(options.ul);
	lilength = $(options.ul + " li").length;
	lis = $(options.ul + " li");
	var items = [];
	var Result = "";
	var i = 0;
	$(options.ul + " li").each(function() {
		var filtro = $(this).find(options.filtro).text();
		var id = $(this).attr('id');
		var classe = $(this).attr('class');
		var conteudo = $(this).html();
		items[i] = [];
		items[i][0] = filtro;
		items[i][1] = id;
		items[i][2] = classe;
		items[i][3] = conteudo;
		i++
	});
	items.sort();
	if (options.order == "asc") {
		items.reverse();
	}

	for (var i = 0; i < lilength; i++) {
		Result += "<li class='" + items[i][2] + "' id='" + items[i][1] + "'>" + items[i][3] + "</li>"
	}
	$(options.ul).html(Result);
	options.reload()
}
//#######################################################################
//#######################################################################
//#######################################################################  	printObj JSON
//#######################################################################		printObj( "objetooo" )
//#######################################################################
var printObj = typeof JSON !== "undefined" ? JSON.stringify : function(obj) {
		var arr = [];
		$.each(obj, function(key, val) {
			var next = key + ": ";
			next += $.isPlainObject(val) ? printObj(val) : val;
			arr.push(next);
		});
		return "{ " + arr.join(", ") + " }";
	}
	//#######################################################################
	//#######################################################################
	//#######################################################################  	printObj JSON
	//#######################################################################		getObjects(TestObj, 'id', 'A'); 
	//#######################################################################;

function getObjects(obj, key, val) {
	var objects = [];
	for (var i in obj) {
		if (!obj.hasOwnProperty(i)) continue;
		if (typeof obj[i] == 'object') {
			objects = objects.concat(getObjects(obj[i], key, val));
		} else if (i == key && obj[key] == val) {
			objects.push(obj);
		}
	}
	return objects;
}


jQuery.fn.json_convert = function() {
  var arrayData, objectData;
  arrayData = this.serializeArray();
  objectData = {};

  $.each(arrayData, function() {
	var value;

	if (this.value != null) {
	  value = this.value;
	} else {
	  value = '';
	}

	if (objectData[this.name] != null) {
	  if (!objectData[this.name].push) {
		objectData[this.name] = [objectData[this.name]];
	  }

	  objectData[this.name].push(value);
	} else {
	  objectData[this.name] = value;
	}
  });

  return objectData;
};


	$(window).keydown(function(a) {
	  if(a.keyCode=="83" && a.ctrlKey==true){
		a.preventDefault();
		if($("#bt_SalvarItem").length>0){
			$("#bt_SalvarItem").click();
		}else if($("#aceitar").length>0){
			$("#aceitar").click();
		};
		return false;
	  }
	})

function abreBiblioteca(opcoes) {
	var options = $.extend({
		admin:0,
		multiple:1,
		type:'img',
		posFn:function(){},
		onSelect:function(){}
	}, opcoes)
		   window.imgSelectedBiblioteca = Array();
		  jQuery.ajax({
			type: "POST",
			url: "./app/ws-modules/ws-tools/functions.php",
			beforeSend:function(){
				confirma({
					width:"auto",
					conteudo:"  carregando...<div class=\'preloaderupdate\' style=\'left: 50%;margin-left: -15px; position: absolute;width: 30px;height: 18px;top: 53px;background-image:url(\""+ws.rootPath+"admin/app/templates/img/websheep/loader_thumb.gif\");background-repeat:no-repeat;background-position: top center;\'></div>",
					idModal:"ws_biblioteca",
					drag:false,
					bot1:0,
					bot2:0
				})
			},
			cache: false,
			data: {"function":"galeriaGeralImagens","admin":options.admin,"type":options.type,"multiple":options.multiple},
		  }).done(function(e) {
					if(options.admin==1){
						bot1 = "ok";
						bot2 = false;
					}else{
						bot1 = "selecionar";
						bot2 = "cancelar";
					}
					confirma({
						conteudo:e,
						width: '100%',
						height: 'calc(100% - 75px)',
						bot1:bot1,
						bot2:bot2,
						idModal:"ws_biblioteca",
						drag:false,
						botclose:true,
						Check:function(){if(window.imgSelectedBiblioteca.length==0 && options.admin==0){TopAlert({mensagem: "Por favor, selecione um pelo menos 1 ítem!",type: 2}); return false; }else{return true;} },
						posFn:function(){
								$('.galeria_img_textarea .imagem, .galeria_file_textarea tr.item').unbind('click tap press').bind("click tap press",function(){
										if(options.multiple==1){
												if($(this).hasClass("active")){$(this).removeClass("active"); }else{$(this).addClass("active"); }
												window.imgSelectedBiblioteca = Array();
												$( ".galeria_img_textarea .active" ).each(function( index ) { window.imgSelectedBiblioteca.push($(this).data('img'));})
												$( ".galeria_file_textarea .active" ).each(function( index ) { window.imgSelectedBiblioteca.push($(this).data('img'));})
										}else{
												$('.galeria_img_textarea .imagem,.galeria_file_textarea tr.item').removeClass("active");
												$(this).addClass("active");
												window.imgSelectedBiblioteca = Array();
												$( ".galeria_img_textarea .active,.galeria_file_textarea tr.active" ).each(function( index ) {
													window.imgSelectedBiblioteca.push($(this).data('img'));
												})
										}
								})
							options.posFn()
						},
						newFun:function(){
							options.onSelect(window.imgSelectedBiblioteca)
						}
					})
		  })
}

//#######################################################################
//#######################################################################
//#######################################################################
//#######################################################################  MASCARA DE FORMULARIO
//#######################################################################
//####################################################################### http://jsfiddle.net/muratoner/VrUJ3/
//####################################################################### $("#div").mask("99/99/9999");
/* Masked Input plugin for jQuery Copyright (c) 2007-2013 Josh Bush (digitalbush.com) Licensed under the MIT license (http://digitalbush.com/projects/masked-input-plugin/#license) Version: 1.3.1 */ 

(function(e){function t(){var e=document.createElement("input"),t="onpaste";e.setAttribute(t,"");return typeof e[t]==="function"?"paste":"input"}var n=t()+".mask",r=navigator.userAgent,i=/iphone/i.test(r),s=/chrome/i.test(r),o=/android/i.test(r),u;e.mask={definitions:{9:"[0-9]",a:"[A-Za-z]","*":"[A-Za-z0-9]"},autoclear:true,dataName:"rawMaskFn",placeholder:"_"};e.fn.extend({caret:function(e,t){var n;if(this.length===0||this.is(":hidden")){return}if(typeof e=="number"){t=typeof t==="number"?t:e;return this.each(function(){if(this.setSelectionRange){this.setSelectionRange(e,t)}else if(this.createTextRange){n=this.createTextRange();n.collapse(true);n.moveEnd("character",t);n.moveStart("character",e);n.select()}})}else{if(this[0].setSelectionRange){e=this[0].selectionStart;t=this[0].selectionEnd}else if(document.selection&&document.selection.createRange){n=document.selection.createRange();e=0-n.duplicate().moveStart("character",-1e5);t=e+n.text.length}return{begin:e,end:t}}},unmask:function(){return this.trigger("unmask")},mask:function(t,r){var a,f,l,c,h,p;if(!t&&this.length>0){a=e(this[0]);return a.data(e.mask.dataName)()}r=e.extend({autoclear:e.mask.autoclear,placeholder:e.mask.placeholder,completed:null},r);f=e.mask.definitions;l=[];c=p=t.length;h=null;e.each(t.split(""),function(e,t){if(t=="?"){p--;c=e}else if(f[t]){l.push(new RegExp(f[t]));if(h===null){h=l.length-1}}else{l.push(null)}});return this.trigger("unmask").each(function(){function g(e){while(++e<p&&!l[e]);return e}function y(e){while(--e>=0&&!l[e]);return e}function b(e,t){var n,i;if(e<0){return}for(n=e,i=g(t);n<p;n++){if(l[n]){if(i<p&&l[n].test(d[i])){d[n]=d[i];d[i]=r.placeholder}else{break}i=g(i)}}N();a.caret(Math.max(h,e))}function w(e){var t,n,i,s;for(t=e,n=r.placeholder;t<p;t++){if(l[t]){i=g(t);s=d[t];d[t]=n;if(i<p&&l[i].test(s)){n=s}else{break}}}}function E(e){C();if(a.val()!=m)a.change()}function S(e){var t=e.which,n,r,s;if(t===8||t===46||i&&t===127){n=a.caret();r=n.begin;s=n.end;if(s-r===0){r=t!==46?y(r):s=g(r-1);s=t===46?g(s):s}T(r,s);b(r,s-1);e.preventDefault()}else if(t===13){E.call(this,e)}else if(t===27){a.val(m);a.caret(0,C());e.preventDefault()}}function x(t){var n=t.which,i=a.caret(),s,u,f;if(n==0){if(i.begin>=p){a.val(a.val().substr(0,p));t.preventDefault();return false}if(i.begin==i.end){n=a.val().charCodeAt(i.begin-1);i.begin--;i.end--}}if(t.ctrlKey||t.altKey||t.metaKey||n<32){return}else if(n&&n!==13){if(i.end-i.begin!==0){T(i.begin,i.end);b(i.begin,i.end-1)}s=g(i.begin-1);if(s<p){u=String.fromCharCode(n);if(l[s].test(u)){w(s);d[s]=u;N();f=g(s);if(o){var c=function(){e.proxy(e.fn.caret,a,f)()};setTimeout(c,0)}else{a.caret(f)}if(r.completed&&f>=p){r.completed.call(a)}}}t.preventDefault()}}function T(e,t){var n;for(n=e;n<t&&n<p;n++){if(l[n]){d[n]=r.placeholder}}}function N(){a.val(d.join(""))}function C(e){var t=a.val(),n=-1,i,s,o;for(i=0,o=0;i<p;i++){if(l[i]){d[i]=r.placeholder;while(o++<t.length){s=t.charAt(o-1);if(l[i].test(s)){d[i]=s;n=i;break}}if(o>t.length){break}}else if(d[i]===t.charAt(o)&&i!==c){o++;n=i}}if(e){N()}else if(n+1<c){if(r.autoclear||d.join("")===v){if(a.val())a.val("");T(0,p)}else{N()}}else{N();a.val(a.val().substring(0,n+1))}return c?i:h}var a=e(this),d=e.map(t.split(""),function(e,t){if(e!="?"){return f[e]?r.placeholder:e}}),v=d.join(""),m=a.val();a.data(e.mask.dataName,function(){return e.map(d,function(e,t){return l[t]&&e!=r.placeholder?e:null}).join("")});if(!a.attr("readonly"))a.one("unmask",function(){a.off(".mask").removeData(e.mask.dataName)}).on("focus.mask",function(){clearTimeout(u);var e;m=a.val();e=C();u=setTimeout(function(){N();if(e==t.replace("?","").length){a.caret(0,e)}else{a.caret(e)}},10)}).on("blur.mask",E).on("keydown.mask",S).on("keypress.mask",x).on(n,function(){setTimeout(function(){var e=C(true);a.caret(e);if(r.completed&&e==a.val().length)r.completed.call(a)},0)});if(s&&o){a.on("keyup.mask",x)}C()})}})})(jQuery);
