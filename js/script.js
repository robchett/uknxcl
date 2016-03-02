/*! jQuery v2.0.3 | (c) 2005, 2013 jQuery Foundation, Inc. | jquery.org/license
 //@ sourceMappingURL=jquery-2.0.3.min.map
 */
(function(e,undefined){var t,n,r=typeof undefined,i=e.location,o=e.document,s=o.documentElement,a=e.jQuery,u=e.$,l={},c=[],p="2.0.3",f=c.concat,h=c.push,d=c.slice,g=c.indexOf,m=l.toString,y=l.hasOwnProperty,v=p.trim,x=function(e,n){return new x.fn.init(e,n,t)},b=/[+-]?(?:\d*\.|)\d+(?:[eE][+-]?\d+|)/.source,w=/\S+/g,T=/^(?:\s*(<[\w\W]+>)[^>]*|#([\w-]*))$/,C=/^<(\w+)\s*\/?>(?:<\/\1>|)$/,k=/^-ms-/,N=/-([\da-z])/gi,E=function(e,t){return t.toUpperCase()},S=function(){o.removeEventListener("DOMContentLoaded",S,!1),e.removeEventListener("load",S,!1),x.ready()};x.fn=x.prototype={jquery:p,constructor:x,init:function(e,t,n){var r,i;if(!e)return this;if("string"==typeof e){if(r="<"===e.charAt(0)&&">"===e.charAt(e.length-1)&&e.length>=3?[null,e,null]:T.exec(e),!r||!r[1]&&t)return!t||t.jquery?(t||n).find(e):this.constructor(t).find(e);if(r[1]){if(t=t instanceof x?t[0]:t,x.merge(this,x.parseHTML(r[1],t&&t.nodeType?t.ownerDocument||t:o,!0)),C.test(r[1])&&x.isPlainObject(t))for(r in t)x.isFunction(this[r])?this[r](t[r]):this.attr(r,t[r]);return this}return i=o.getElementById(r[2]),i&&i.parentNode&&(this.length=1,this[0]=i),this.context=o,this.selector=e,this}return e.nodeType?(this.context=this[0]=e,this.length=1,this):x.isFunction(e)?n.ready(e):(e.selector!==undefined&&(this.selector=e.selector,this.context=e.context),x.makeArray(e,this))},selector:"",length:0,toArray:function(){return d.call(this)},get:function(e){return null==e?this.toArray():0>e?this[this.length+e]:this[e]},pushStack:function(e){var t=x.merge(this.constructor(),e);return t.prevObject=this,t.context=this.context,t},each:function(e,t){return x.each(this,e,t)},ready:function(e){return x.ready.promise().done(e),this},slice:function(){return this.pushStack(d.apply(this,arguments))},first:function(){return this.eq(0)},last:function(){return this.eq(-1)},eq:function(e){var t=this.length,n=+e+(0>e?t:0);return this.pushStack(n>=0&&t>n?[this[n]]:[])},map:function(e){return this.pushStack(x.map(this,function(t,n){return e.call(t,n,t)}))},end:function(){return this.prevObject||this.constructor(null)},push:h,sort:[].sort,splice:[].splice},x.fn.init.prototype=x.fn,x.extend=x.fn.extend=function(){var e,t,n,r,i,o,s=arguments[0]||{},a=1,u=arguments.length,l=!1;for("boolean"==typeof s&&(l=s,s=arguments[1]||{},a=2),"object"==typeof s||x.isFunction(s)||(s={}),u===a&&(s=this,--a);u>a;a++)if(null!=(e=arguments[a]))for(t in e)n=s[t],r=e[t],s!==r&&(l&&r&&(x.isPlainObject(r)||(i=x.isArray(r)))?(i?(i=!1,o=n&&x.isArray(n)?n:[]):o=n&&x.isPlainObject(n)?n:{},s[t]=x.extend(l,o,r)):r!==undefined&&(s[t]=r));return s},x.extend({expando:"jQuery"+(p+Math.random()).replace(/\D/g,""),noConflict:function(t){return e.$===x&&(e.$=u),t&&e.jQuery===x&&(e.jQuery=a),x},isReady:!1,readyWait:1,holdReady:function(e){e?x.readyWait++:x.ready(!0)},ready:function(e){(e===!0?--x.readyWait:x.isReady)||(x.isReady=!0,e!==!0&&--x.readyWait>0||(n.resolveWith(o,[x]),x.fn.trigger&&x(o).trigger("ready").off("ready")))},isFunction:function(e){return"function"===x.type(e)},isArray:Array.isArray,isWindow:function(e){return null!=e&&e===e.window},isNumeric:function(e){return!isNaN(parseFloat(e))&&isFinite(e)},type:function(e){return null==e?e+"":"object"==typeof e||"function"==typeof e?l[m.call(e)]||"object":typeof e},isPlainObject:function(e){if("object"!==x.type(e)||e.nodeType||x.isWindow(e))return!1;try{if(e.constructor&&!y.call(e.constructor.prototype,"isPrototypeOf"))return!1}catch(t){return!1}return!0},isEmptyObject:function(e){var t;for(t in e)return!1;return!0},error:function(e){throw Error(e)},parseHTML:function(e,t,n){if(!e||"string"!=typeof e)return null;"boolean"==typeof t&&(n=t,t=!1),t=t||o;var r=C.exec(e),i=!n&&[];return r?[t.createElement(r[1])]:(r=x.buildFragment([e],t,i),i&&x(i).remove(),x.merge([],r.childNodes))},parseJSON:JSON.parse,parseXML:function(e){var t,n;if(!e||"string"!=typeof e)return null;try{n=new DOMParser,t=n.parseFromString(e,"text/xml")}catch(r){t=undefined}return(!t||t.getElementsByTagName("parsererror").length)&&x.error("Invalid XML: "+e),t},noop:function(){},globalEval:function(e){var t,n=eval;e=x.trim(e),e&&(1===e.indexOf("use strict")?(t=o.createElement("script"),t.text=e,o.head.appendChild(t).parentNode.removeChild(t)):n(e))},camelCase:function(e){return e.replace(k,"ms-").replace(N,E)},nodeName:function(e,t){return e.nodeName&&e.nodeName.toLowerCase()===t.toLowerCase()},each:function(e,t,n){var r,i=0,o=e.length,s=j(e);if(n){if(s){for(;o>i;i++)if(r=t.apply(e[i],n),r===!1)break}else for(i in e)if(r=t.apply(e[i],n),r===!1)break}else if(s){for(;o>i;i++)if(r=t.call(e[i],i,e[i]),r===!1)break}else for(i in e)if(r=t.call(e[i],i,e[i]),r===!1)break;return e},trim:function(e){return null==e?"":v.call(e)},makeArray:function(e,t){var n=t||[];return null!=e&&(j(Object(e))?x.merge(n,"string"==typeof e?[e]:e):h.call(n,e)),n},inArray:function(e,t,n){return null==t?-1:g.call(t,e,n)},merge:function(e,t){var n=t.length,r=e.length,i=0;if("number"==typeof n)for(;n>i;i++)e[r++]=t[i];else while(t[i]!==undefined)e[r++]=t[i++];return e.length=r,e},grep:function(e,t,n){var r,i=[],o=0,s=e.length;for(n=!!n;s>o;o++)r=!!t(e[o],o),n!==r&&i.push(e[o]);return i},map:function(e,t,n){var r,i=0,o=e.length,s=j(e),a=[];if(s)for(;o>i;i++)r=t(e[i],i,n),null!=r&&(a[a.length]=r);else for(i in e)r=t(e[i],i,n),null!=r&&(a[a.length]=r);return f.apply([],a)},guid:1,proxy:function(e,t){var n,r,i;return"string"==typeof t&&(n=e[t],t=e,e=n),x.isFunction(e)?(r=d.call(arguments,2),i=function(){return e.apply(t||this,r.concat(d.call(arguments)))},i.guid=e.guid=e.guid||x.guid++,i):undefined},access:function(e,t,n,r,i,o,s){var a=0,u=e.length,l=null==n;if("object"===x.type(n)){i=!0;for(a in n)x.access(e,t,a,n[a],!0,o,s)}else if(r!==undefined&&(i=!0,x.isFunction(r)||(s=!0),l&&(s?(t.call(e,r),t=null):(l=t,t=function(e,t,n){return l.call(x(e),n)})),t))for(;u>a;a++)t(e[a],n,s?r:r.call(e[a],a,t(e[a],n)));return i?e:l?t.call(e):u?t(e[0],n):o},now:Date.now,swap:function(e,t,n,r){var i,o,s={};for(o in t)s[o]=e.style[o],e.style[o]=t[o];i=n.apply(e,r||[]);for(o in t)e.style[o]=s[o];return i}}),x.ready.promise=function(t){return n||(n=x.Deferred(),"complete"===o.readyState?setTimeout(x.ready):(o.addEventListener("DOMContentLoaded",S,!1),e.addEventListener("load",S,!1))),n.promise(t)},x.each("Boolean Number String Function Array Date RegExp Object Error".split(" "),function(e,t){l["[object "+t+"]"]=t.toLowerCase()});function j(e){var t=e.length,n=x.type(e);return x.isWindow(e)?!1:1===e.nodeType&&t?!0:"array"===n||"function"!==n&&(0===t||"number"==typeof t&&t>0&&t-1 in e)}t=x(o),function(e,undefined){var t,n,r,i,o,s,a,u,l,c,p,f,h,d,g,m,y,v="sizzle"+-new Date,b=e.document,w=0,T=0,C=st(),k=st(),N=st(),E=!1,S=function(e,t){return e===t?(E=!0,0):0},j=typeof undefined,D=1<<31,A={}.hasOwnProperty,L=[],q=L.pop,H=L.push,O=L.push,F=L.slice,P=L.indexOf||function(e){var t=0,n=this.length;for(;n>t;t++)if(this[t]===e)return t;return-1},R="checked|selected|async|autofocus|autoplay|controls|defer|disabled|hidden|ismap|loop|multiple|open|readonly|required|scoped",M="[\\x20\\t\\r\\n\\f]",W="(?:\\\\.|[\\w-]|[^\\x00-\\xa0])+",$=W.replace("w","w#"),B="\\["+M+"*("+W+")"+M+"*(?:([*^$|!~]?=)"+M+"*(?:(['\"])((?:\\\\.|[^\\\\])*?)\\3|("+$+")|)|)"+M+"*\\]",I=":("+W+")(?:\\(((['\"])((?:\\\\.|[^\\\\])*?)\\3|((?:\\\\.|[^\\\\()[\\]]|"+B.replace(3,8)+")*)|.*)\\)|)",z=RegExp("^"+M+"+|((?:^|[^\\\\])(?:\\\\.)*)"+M+"+$","g"),_=RegExp("^"+M+"*,"+M+"*"),X=RegExp("^"+M+"*([>+~]|"+M+")"+M+"*"),U=RegExp(M+"*[+~]"),Y=RegExp("="+M+"*([^\\]'\"]*)"+M+"*\\]","g"),V=RegExp(I),G=RegExp("^"+$+"$"),J={ID:RegExp("^#("+W+")"),CLASS:RegExp("^\\.("+W+")"),TAG:RegExp("^("+W.replace("w","w*")+")"),ATTR:RegExp("^"+B),PSEUDO:RegExp("^"+I),CHILD:RegExp("^:(only|first|last|nth|nth-last)-(child|of-type)(?:\\("+M+"*(even|odd|(([+-]|)(\\d*)n|)"+M+"*(?:([+-]|)"+M+"*(\\d+)|))"+M+"*\\)|)","i"),bool:RegExp("^(?:"+R+")$","i"),needsContext:RegExp("^"+M+"*[>+~]|:(even|odd|eq|gt|lt|nth|first|last)(?:\\("+M+"*((?:-\\d)?\\d*)"+M+"*\\)|)(?=[^-]|$)","i")},Q=/^[^{]+\{\s*\[native \w/,K=/^(?:#([\w-]+)|(\w+)|\.([\w-]+))$/,Z=/^(?:input|select|textarea|button)$/i,et=/^h\d$/i,tt=/'|\\/g,nt=RegExp("\\\\([\\da-f]{1,6}"+M+"?|("+M+")|.)","ig"),rt=function(e,t,n){var r="0x"+t-65536;return r!==r||n?t:0>r?String.fromCharCode(r+65536):String.fromCharCode(55296|r>>10,56320|1023&r)};try{O.apply(L=F.call(b.childNodes),b.childNodes),L[b.childNodes.length].nodeType}catch(it){O={apply:L.length?function(e,t){H.apply(e,F.call(t))}:function(e,t){var n=e.length,r=0;while(e[n++]=t[r++]);e.length=n-1}}}function ot(e,t,r,i){var o,s,a,u,l,f,g,m,x,w;if((t?t.ownerDocument||t:b)!==p&&c(t),t=t||p,r=r||[],!e||"string"!=typeof e)return r;if(1!==(u=t.nodeType)&&9!==u)return[];if(h&&!i){if(o=K.exec(e))if(a=o[1]){if(9===u){if(s=t.getElementById(a),!s||!s.parentNode)return r;if(s.id===a)return r.push(s),r}else if(t.ownerDocument&&(s=t.ownerDocument.getElementById(a))&&y(t,s)&&s.id===a)return r.push(s),r}else{if(o[2])return O.apply(r,t.getElementsByTagName(e)),r;if((a=o[3])&&n.getElementsByClassName&&t.getElementsByClassName)return O.apply(r,t.getElementsByClassName(a)),r}if(n.qsa&&(!d||!d.test(e))){if(m=g=v,x=t,w=9===u&&e,1===u&&"object"!==t.nodeName.toLowerCase()){f=gt(e),(g=t.getAttribute("id"))?m=g.replace(tt,"\\$&"):t.setAttribute("id",m),m="[id='"+m+"'] ",l=f.length;while(l--)f[l]=m+mt(f[l]);x=U.test(e)&&t.parentNode||t,w=f.join(",")}if(w)try{return O.apply(r,x.querySelectorAll(w)),r}catch(T){}finally{g||t.removeAttribute("id")}}}return kt(e.replace(z,"$1"),t,r,i)}function st(){var e=[];function t(n,r){return e.push(n+=" ")>i.cacheLength&&delete t[e.shift()],t[n]=r}return t}function at(e){return e[v]=!0,e}function ut(e){var t=p.createElement("div");try{return!!e(t)}catch(n){return!1}finally{t.parentNode&&t.parentNode.removeChild(t),t=null}}function lt(e,t){var n=e.split("|"),r=e.length;while(r--)i.attrHandle[n[r]]=t}function ct(e,t){var n=t&&e,r=n&&1===e.nodeType&&1===t.nodeType&&(~t.sourceIndex||D)-(~e.sourceIndex||D);if(r)return r;if(n)while(n=n.nextSibling)if(n===t)return-1;return e?1:-1}function pt(e){return function(t){var n=t.nodeName.toLowerCase();return"input"===n&&t.type===e}}function ft(e){return function(t){var n=t.nodeName.toLowerCase();return("input"===n||"button"===n)&&t.type===e}}function ht(e){return at(function(t){return t=+t,at(function(n,r){var i,o=e([],n.length,t),s=o.length;while(s--)n[i=o[s]]&&(n[i]=!(r[i]=n[i]))})})}s=ot.isXML=function(e){var t=e&&(e.ownerDocument||e).documentElement;return t?"HTML"!==t.nodeName:!1},n=ot.support={},c=ot.setDocument=function(e){var t=e?e.ownerDocument||e:b,r=t.defaultView;return t!==p&&9===t.nodeType&&t.documentElement?(p=t,f=t.documentElement,h=!s(t),r&&r.attachEvent&&r!==r.top&&r.attachEvent("onbeforeunload",function(){c()}),n.attributes=ut(function(e){return e.className="i",!e.getAttribute("className")}),n.getElementsByTagName=ut(function(e){return e.appendChild(t.createComment("")),!e.getElementsByTagName("*").length}),n.getElementsByClassName=ut(function(e){return e.innerHTML="<div class='a'></div><div class='a i'></div>",e.firstChild.className="i",2===e.getElementsByClassName("i").length}),n.getById=ut(function(e){return f.appendChild(e).id=v,!t.getElementsByName||!t.getElementsByName(v).length}),n.getById?(i.find.ID=function(e,t){if(typeof t.getElementById!==j&&h){var n=t.getElementById(e);return n&&n.parentNode?[n]:[]}},i.filter.ID=function(e){var t=e.replace(nt,rt);return function(e){return e.getAttribute("id")===t}}):(delete i.find.ID,i.filter.ID=function(e){var t=e.replace(nt,rt);return function(e){var n=typeof e.getAttributeNode!==j&&e.getAttributeNode("id");return n&&n.value===t}}),i.find.TAG=n.getElementsByTagName?function(e,t){return typeof t.getElementsByTagName!==j?t.getElementsByTagName(e):undefined}:function(e,t){var n,r=[],i=0,o=t.getElementsByTagName(e);if("*"===e){while(n=o[i++])1===n.nodeType&&r.push(n);return r}return o},i.find.CLASS=n.getElementsByClassName&&function(e,t){return typeof t.getElementsByClassName!==j&&h?t.getElementsByClassName(e):undefined},g=[],d=[],(n.qsa=Q.test(t.querySelectorAll))&&(ut(function(e){e.innerHTML="<select><option selected=''></option></select>",e.querySelectorAll("[selected]").length||d.push("\\["+M+"*(?:value|"+R+")"),e.querySelectorAll(":checked").length||d.push(":checked")}),ut(function(e){var n=t.createElement("input");n.setAttribute("type","hidden"),e.appendChild(n).setAttribute("t",""),e.querySelectorAll("[t^='']").length&&d.push("[*^$]="+M+"*(?:''|\"\")"),e.querySelectorAll(":enabled").length||d.push(":enabled",":disabled"),e.querySelectorAll("*,:x"),d.push(",.*:")})),(n.matchesSelector=Q.test(m=f.webkitMatchesSelector||f.mozMatchesSelector||f.oMatchesSelector||f.msMatchesSelector))&&ut(function(e){n.disconnectedMatch=m.call(e,"div"),m.call(e,"[s!='']:x"),g.push("!=",I)}),d=d.length&&RegExp(d.join("|")),g=g.length&&RegExp(g.join("|")),y=Q.test(f.contains)||f.compareDocumentPosition?function(e,t){var n=9===e.nodeType?e.documentElement:e,r=t&&t.parentNode;return e===r||!(!r||1!==r.nodeType||!(n.contains?n.contains(r):e.compareDocumentPosition&&16&e.compareDocumentPosition(r)))}:function(e,t){if(t)while(t=t.parentNode)if(t===e)return!0;return!1},S=f.compareDocumentPosition?function(e,r){if(e===r)return E=!0,0;var i=r.compareDocumentPosition&&e.compareDocumentPosition&&e.compareDocumentPosition(r);return i?1&i||!n.sortDetached&&r.compareDocumentPosition(e)===i?e===t||y(b,e)?-1:r===t||y(b,r)?1:l?P.call(l,e)-P.call(l,r):0:4&i?-1:1:e.compareDocumentPosition?-1:1}:function(e,n){var r,i=0,o=e.parentNode,s=n.parentNode,a=[e],u=[n];if(e===n)return E=!0,0;if(!o||!s)return e===t?-1:n===t?1:o?-1:s?1:l?P.call(l,e)-P.call(l,n):0;if(o===s)return ct(e,n);r=e;while(r=r.parentNode)a.unshift(r);r=n;while(r=r.parentNode)u.unshift(r);while(a[i]===u[i])i++;return i?ct(a[i],u[i]):a[i]===b?-1:u[i]===b?1:0},t):p},ot.matches=function(e,t){return ot(e,null,null,t)},ot.matchesSelector=function(e,t){if((e.ownerDocument||e)!==p&&c(e),t=t.replace(Y,"='$1']"),!(!n.matchesSelector||!h||g&&g.test(t)||d&&d.test(t)))try{var r=m.call(e,t);if(r||n.disconnectedMatch||e.document&&11!==e.document.nodeType)return r}catch(i){}return ot(t,p,null,[e]).length>0},ot.contains=function(e,t){return(e.ownerDocument||e)!==p&&c(e),y(e,t)},ot.attr=function(e,t){(e.ownerDocument||e)!==p&&c(e);var r=i.attrHandle[t.toLowerCase()],o=r&&A.call(i.attrHandle,t.toLowerCase())?r(e,t,!h):undefined;return o===undefined?n.attributes||!h?e.getAttribute(t):(o=e.getAttributeNode(t))&&o.specified?o.value:null:o},ot.error=function(e){throw Error("Syntax error, unrecognized expression: "+e)},ot.uniqueSort=function(e){var t,r=[],i=0,o=0;if(E=!n.detectDuplicates,l=!n.sortStable&&e.slice(0),e.sort(S),E){while(t=e[o++])t===e[o]&&(i=r.push(o));while(i--)e.splice(r[i],1)}return e},o=ot.getText=function(e){var t,n="",r=0,i=e.nodeType;if(i){if(1===i||9===i||11===i){if("string"==typeof e.textContent)return e.textContent;for(e=e.firstChild;e;e=e.nextSibling)n+=o(e)}else if(3===i||4===i)return e.nodeValue}else for(;t=e[r];r++)n+=o(t);return n},i=ot.selectors={cacheLength:50,createPseudo:at,match:J,attrHandle:{},find:{},relative:{">":{dir:"parentNode",first:!0}," ":{dir:"parentNode"},"+":{dir:"previousSibling",first:!0},"~":{dir:"previousSibling"}},preFilter:{ATTR:function(e){return e[1]=e[1].replace(nt,rt),e[3]=(e[4]||e[5]||"").replace(nt,rt),"~="===e[2]&&(e[3]=" "+e[3]+" "),e.slice(0,4)},CHILD:function(e){return e[1]=e[1].toLowerCase(),"nth"===e[1].slice(0,3)?(e[3]||ot.error(e[0]),e[4]=+(e[4]?e[5]+(e[6]||1):2*("even"===e[3]||"odd"===e[3])),e[5]=+(e[7]+e[8]||"odd"===e[3])):e[3]&&ot.error(e[0]),e},PSEUDO:function(e){var t,n=!e[5]&&e[2];return J.CHILD.test(e[0])?null:(e[3]&&e[4]!==undefined?e[2]=e[4]:n&&V.test(n)&&(t=gt(n,!0))&&(t=n.indexOf(")",n.length-t)-n.length)&&(e[0]=e[0].slice(0,t),e[2]=n.slice(0,t)),e.slice(0,3))}},filter:{TAG:function(e){var t=e.replace(nt,rt).toLowerCase();return"*"===e?function(){return!0}:function(e){return e.nodeName&&e.nodeName.toLowerCase()===t}},CLASS:function(e){var t=C[e+" "];return t||(t=RegExp("(^|"+M+")"+e+"("+M+"|$)"))&&C(e,function(e){return t.test("string"==typeof e.className&&e.className||typeof e.getAttribute!==j&&e.getAttribute("class")||"")})},ATTR:function(e,t,n){return function(r){var i=ot.attr(r,e);return null==i?"!="===t:t?(i+="","="===t?i===n:"!="===t?i!==n:"^="===t?n&&0===i.indexOf(n):"*="===t?n&&i.indexOf(n)>-1:"$="===t?n&&i.slice(-n.length)===n:"~="===t?(" "+i+" ").indexOf(n)>-1:"|="===t?i===n||i.slice(0,n.length+1)===n+"-":!1):!0}},CHILD:function(e,t,n,r,i){var o="nth"!==e.slice(0,3),s="last"!==e.slice(-4),a="of-type"===t;return 1===r&&0===i?function(e){return!!e.parentNode}:function(t,n,u){var l,c,p,f,h,d,g=o!==s?"nextSibling":"previousSibling",m=t.parentNode,y=a&&t.nodeName.toLowerCase(),x=!u&&!a;if(m){if(o){while(g){p=t;while(p=p[g])if(a?p.nodeName.toLowerCase()===y:1===p.nodeType)return!1;d=g="only"===e&&!d&&"nextSibling"}return!0}if(d=[s?m.firstChild:m.lastChild],s&&x){c=m[v]||(m[v]={}),l=c[e]||[],h=l[0]===w&&l[1],f=l[0]===w&&l[2],p=h&&m.childNodes[h];while(p=++h&&p&&p[g]||(f=h=0)||d.pop())if(1===p.nodeType&&++f&&p===t){c[e]=[w,h,f];break}}else if(x&&(l=(t[v]||(t[v]={}))[e])&&l[0]===w)f=l[1];else while(p=++h&&p&&p[g]||(f=h=0)||d.pop())if((a?p.nodeName.toLowerCase()===y:1===p.nodeType)&&++f&&(x&&((p[v]||(p[v]={}))[e]=[w,f]),p===t))break;return f-=i,f===r||0===f%r&&f/r>=0}}},PSEUDO:function(e,t){var n,r=i.pseudos[e]||i.setFilters[e.toLowerCase()]||ot.error("unsupported pseudo: "+e);return r[v]?r(t):r.length>1?(n=[e,e,"",t],i.setFilters.hasOwnProperty(e.toLowerCase())?at(function(e,n){var i,o=r(e,t),s=o.length;while(s--)i=P.call(e,o[s]),e[i]=!(n[i]=o[s])}):function(e){return r(e,0,n)}):r}},pseudos:{not:at(function(e){var t=[],n=[],r=a(e.replace(z,"$1"));return r[v]?at(function(e,t,n,i){var o,s=r(e,null,i,[]),a=e.length;while(a--)(o=s[a])&&(e[a]=!(t[a]=o))}):function(e,i,o){return t[0]=e,r(t,null,o,n),!n.pop()}}),has:at(function(e){return function(t){return ot(e,t).length>0}}),contains:at(function(e){return function(t){return(t.textContent||t.innerText||o(t)).indexOf(e)>-1}}),lang:at(function(e){return G.test(e||"")||ot.error("unsupported lang: "+e),e=e.replace(nt,rt).toLowerCase(),function(t){var n;do if(n=h?t.lang:t.getAttribute("xml:lang")||t.getAttribute("lang"))return n=n.toLowerCase(),n===e||0===n.indexOf(e+"-");while((t=t.parentNode)&&1===t.nodeType);return!1}}),target:function(t){var n=e.location&&e.location.hash;return n&&n.slice(1)===t.id},root:function(e){return e===f},focus:function(e){return e===p.activeElement&&(!p.hasFocus||p.hasFocus())&&!!(e.type||e.href||~e.tabIndex)},enabled:function(e){return e.disabled===!1},disabled:function(e){return e.disabled===!0},checked:function(e){var t=e.nodeName.toLowerCase();return"input"===t&&!!e.checked||"option"===t&&!!e.selected},selected:function(e){return e.parentNode&&e.parentNode.selectedIndex,e.selected===!0},empty:function(e){for(e=e.firstChild;e;e=e.nextSibling)if(e.nodeName>"@"||3===e.nodeType||4===e.nodeType)return!1;return!0},parent:function(e){return!i.pseudos.empty(e)},header:function(e){return et.test(e.nodeName)},input:function(e){return Z.test(e.nodeName)},button:function(e){var t=e.nodeName.toLowerCase();return"input"===t&&"button"===e.type||"button"===t},text:function(e){var t;return"input"===e.nodeName.toLowerCase()&&"text"===e.type&&(null==(t=e.getAttribute("type"))||t.toLowerCase()===e.type)},first:ht(function(){return[0]}),last:ht(function(e,t){return[t-1]}),eq:ht(function(e,t,n){return[0>n?n+t:n]}),even:ht(function(e,t){var n=0;for(;t>n;n+=2)e.push(n);return e}),odd:ht(function(e,t){var n=1;for(;t>n;n+=2)e.push(n);return e}),lt:ht(function(e,t,n){var r=0>n?n+t:n;for(;--r>=0;)e.push(r);return e}),gt:ht(function(e,t,n){var r=0>n?n+t:n;for(;t>++r;)e.push(r);return e})}},i.pseudos.nth=i.pseudos.eq;for(t in{radio:!0,checkbox:!0,file:!0,password:!0,image:!0})i.pseudos[t]=pt(t);for(t in{submit:!0,reset:!0})i.pseudos[t]=ft(t);function dt(){}dt.prototype=i.filters=i.pseudos,i.setFilters=new dt;function gt(e,t){var n,r,o,s,a,u,l,c=k[e+" "];if(c)return t?0:c.slice(0);a=e,u=[],l=i.preFilter;while(a){(!n||(r=_.exec(a)))&&(r&&(a=a.slice(r[0].length)||a),u.push(o=[])),n=!1,(r=X.exec(a))&&(n=r.shift(),o.push({value:n,type:r[0].replace(z," ")}),a=a.slice(n.length));for(s in i.filter)!(r=J[s].exec(a))||l[s]&&!(r=l[s](r))||(n=r.shift(),o.push({value:n,type:s,matches:r}),a=a.slice(n.length));if(!n)break}return t?a.length:a?ot.error(e):k(e,u).slice(0)}function mt(e){var t=0,n=e.length,r="";for(;n>t;t++)r+=e[t].value;return r}function yt(e,t,n){var i=t.dir,o=n&&"parentNode"===i,s=T++;return t.first?function(t,n,r){while(t=t[i])if(1===t.nodeType||o)return e(t,n,r)}:function(t,n,a){var u,l,c,p=w+" "+s;if(a){while(t=t[i])if((1===t.nodeType||o)&&e(t,n,a))return!0}else while(t=t[i])if(1===t.nodeType||o)if(c=t[v]||(t[v]={}),(l=c[i])&&l[0]===p){if((u=l[1])===!0||u===r)return u===!0}else if(l=c[i]=[p],l[1]=e(t,n,a)||r,l[1]===!0)return!0}}function vt(e){return e.length>1?function(t,n,r){var i=e.length;while(i--)if(!e[i](t,n,r))return!1;return!0}:e[0]}function xt(e,t,n,r,i){var o,s=[],a=0,u=e.length,l=null!=t;for(;u>a;a++)(o=e[a])&&(!n||n(o,r,i))&&(s.push(o),l&&t.push(a));return s}function bt(e,t,n,r,i,o){return r&&!r[v]&&(r=bt(r)),i&&!i[v]&&(i=bt(i,o)),at(function(o,s,a,u){var l,c,p,f=[],h=[],d=s.length,g=o||Ct(t||"*",a.nodeType?[a]:a,[]),m=!e||!o&&t?g:xt(g,f,e,a,u),y=n?i||(o?e:d||r)?[]:s:m;if(n&&n(m,y,a,u),r){l=xt(y,h),r(l,[],a,u),c=l.length;while(c--)(p=l[c])&&(y[h[c]]=!(m[h[c]]=p))}if(o){if(i||e){if(i){l=[],c=y.length;while(c--)(p=y[c])&&l.push(m[c]=p);i(null,y=[],l,u)}c=y.length;while(c--)(p=y[c])&&(l=i?P.call(o,p):f[c])>-1&&(o[l]=!(s[l]=p))}}else y=xt(y===s?y.splice(d,y.length):y),i?i(null,s,y,u):O.apply(s,y)})}function wt(e){var t,n,r,o=e.length,s=i.relative[e[0].type],a=s||i.relative[" "],l=s?1:0,c=yt(function(e){return e===t},a,!0),p=yt(function(e){return P.call(t,e)>-1},a,!0),f=[function(e,n,r){return!s&&(r||n!==u)||((t=n).nodeType?c(e,n,r):p(e,n,r))}];for(;o>l;l++)if(n=i.relative[e[l].type])f=[yt(vt(f),n)];else{if(n=i.filter[e[l].type].apply(null,e[l].matches),n[v]){for(r=++l;o>r;r++)if(i.relative[e[r].type])break;return bt(l>1&&vt(f),l>1&&mt(e.slice(0,l-1).concat({value:" "===e[l-2].type?"*":""})).replace(z,"$1"),n,r>l&&wt(e.slice(l,r)),o>r&&wt(e=e.slice(r)),o>r&&mt(e))}f.push(n)}return vt(f)}function Tt(e,t){var n=0,o=t.length>0,s=e.length>0,a=function(a,l,c,f,h){var d,g,m,y=[],v=0,x="0",b=a&&[],T=null!=h,C=u,k=a||s&&i.find.TAG("*",h&&l.parentNode||l),N=w+=null==C?1:Math.random()||.1;for(T&&(u=l!==p&&l,r=n);null!=(d=k[x]);x++){if(s&&d){g=0;while(m=e[g++])if(m(d,l,c)){f.push(d);break}T&&(w=N,r=++n)}o&&((d=!m&&d)&&v--,a&&b.push(d))}if(v+=x,o&&x!==v){g=0;while(m=t[g++])m(b,y,l,c);if(a){if(v>0)while(x--)b[x]||y[x]||(y[x]=q.call(f));y=xt(y)}O.apply(f,y),T&&!a&&y.length>0&&v+t.length>1&&ot.uniqueSort(f)}return T&&(w=N,u=C),b};return o?at(a):a}a=ot.compile=function(e,t){var n,r=[],i=[],o=N[e+" "];if(!o){t||(t=gt(e)),n=t.length;while(n--)o=wt(t[n]),o[v]?r.push(o):i.push(o);o=N(e,Tt(i,r))}return o};function Ct(e,t,n){var r=0,i=t.length;for(;i>r;r++)ot(e,t[r],n);return n}function kt(e,t,r,o){var s,u,l,c,p,f=gt(e);if(!o&&1===f.length){if(u=f[0]=f[0].slice(0),u.length>2&&"ID"===(l=u[0]).type&&n.getById&&9===t.nodeType&&h&&i.relative[u[1].type]){if(t=(i.find.ID(l.matches[0].replace(nt,rt),t)||[])[0],!t)return r;e=e.slice(u.shift().value.length)}s=J.needsContext.test(e)?0:u.length;while(s--){if(l=u[s],i.relative[c=l.type])break;if((p=i.find[c])&&(o=p(l.matches[0].replace(nt,rt),U.test(u[0].type)&&t.parentNode||t))){if(u.splice(s,1),e=o.length&&mt(u),!e)return O.apply(r,o),r;break}}}return a(e,f)(o,t,!h,r,U.test(e)),r}n.sortStable=v.split("").sort(S).join("")===v,n.detectDuplicates=E,c(),n.sortDetached=ut(function(e){return 1&e.compareDocumentPosition(p.createElement("div"))}),ut(function(e){return e.innerHTML="<a href='#'></a>","#"===e.firstChild.getAttribute("href")})||lt("type|href|height|width",function(e,t,n){return n?undefined:e.getAttribute(t,"type"===t.toLowerCase()?1:2)}),n.attributes&&ut(function(e){return e.innerHTML="<input/>",e.firstChild.setAttribute("value",""),""===e.firstChild.getAttribute("value")})||lt("value",function(e,t,n){return n||"input"!==e.nodeName.toLowerCase()?undefined:e.defaultValue}),ut(function(e){return null==e.getAttribute("disabled")})||lt(R,function(e,t,n){var r;return n?undefined:(r=e.getAttributeNode(t))&&r.specified?r.value:e[t]===!0?t.toLowerCase():null}),x.find=ot,x.expr=ot.selectors,x.expr[":"]=x.expr.pseudos,x.unique=ot.uniqueSort,x.text=ot.getText,x.isXMLDoc=ot.isXML,x.contains=ot.contains}(e);var D={};function A(e){var t=D[e]={};return x.each(e.match(w)||[],function(e,n){t[n]=!0}),t}x.Callbacks=function(e){e="string"==typeof e?D[e]||A(e):x.extend({},e);var t,n,r,i,o,s,a=[],u=!e.once&&[],l=function(p){for(t=e.memory&&p,n=!0,s=i||0,i=0,o=a.length,r=!0;a&&o>s;s++)if(a[s].apply(p[0],p[1])===!1&&e.stopOnFalse){t=!1;break}r=!1,a&&(u?u.length&&l(u.shift()):t?a=[]:c.disable())},c={add:function(){if(a){var n=a.length;(function s(t){x.each(t,function(t,n){var r=x.type(n);"function"===r?e.unique&&c.has(n)||a.push(n):n&&n.length&&"string"!==r&&s(n)})})(arguments),r?o=a.length:t&&(i=n,l(t))}return this},remove:function(){return a&&x.each(arguments,function(e,t){var n;while((n=x.inArray(t,a,n))>-1)a.splice(n,1),r&&(o>=n&&o--,s>=n&&s--)}),this},has:function(e){return e?x.inArray(e,a)>-1:!(!a||!a.length)},empty:function(){return a=[],o=0,this},disable:function(){return a=u=t=undefined,this},disabled:function(){return!a},lock:function(){return u=undefined,t||c.disable(),this},locked:function(){return!u},fireWith:function(e,t){return!a||n&&!u||(t=t||[],t=[e,t.slice?t.slice():t],r?u.push(t):l(t)),this},fire:function(){return c.fireWith(this,arguments),this},fired:function(){return!!n}};return c},x.extend({Deferred:function(e){var t=[["resolve","done",x.Callbacks("once memory"),"resolved"],["reject","fail",x.Callbacks("once memory"),"rejected"],["notify","progress",x.Callbacks("memory")]],n="pending",r={state:function(){return n},always:function(){return i.done(arguments).fail(arguments),this},then:function(){var e=arguments;return x.Deferred(function(n){x.each(t,function(t,o){var s=o[0],a=x.isFunction(e[t])&&e[t];i[o[1]](function(){var e=a&&a.apply(this,arguments);e&&x.isFunction(e.promise)?e.promise().done(n.resolve).fail(n.reject).progress(n.notify):n[s+"With"](this===r?n.promise():this,a?[e]:arguments)})}),e=null}).promise()},promise:function(e){return null!=e?x.extend(e,r):r}},i={};return r.pipe=r.then,x.each(t,function(e,o){var s=o[2],a=o[3];r[o[1]]=s.add,a&&s.add(function(){n=a},t[1^e][2].disable,t[2][2].lock),i[o[0]]=function(){return i[o[0]+"With"](this===i?r:this,arguments),this},i[o[0]+"With"]=s.fireWith}),r.promise(i),e&&e.call(i,i),i},when:function(e){var t=0,n=d.call(arguments),r=n.length,i=1!==r||e&&x.isFunction(e.promise)?r:0,o=1===i?e:x.Deferred(),s=function(e,t,n){return function(r){t[e]=this,n[e]=arguments.length>1?d.call(arguments):r,n===a?o.notifyWith(t,n):--i||o.resolveWith(t,n)}},a,u,l;if(r>1)for(a=Array(r),u=Array(r),l=Array(r);r>t;t++)n[t]&&x.isFunction(n[t].promise)?n[t].promise().done(s(t,l,n)).fail(o.reject).progress(s(t,u,a)):--i;return i||o.resolveWith(l,n),o.promise()}}),x.support=function(t){var n=o.createElement("input"),r=o.createDocumentFragment(),i=o.createElement("div"),s=o.createElement("select"),a=s.appendChild(o.createElement("option"));return n.type?(n.type="checkbox",t.checkOn=""!==n.value,t.optSelected=a.selected,t.reliableMarginRight=!0,t.boxSizingReliable=!0,t.pixelPosition=!1,n.checked=!0,t.noCloneChecked=n.cloneNode(!0).checked,s.disabled=!0,t.optDisabled=!a.disabled,n=o.createElement("input"),n.value="t",n.type="radio",t.radioValue="t"===n.value,n.setAttribute("checked","t"),n.setAttribute("name","t"),r.appendChild(n),t.checkClone=r.cloneNode(!0).cloneNode(!0).lastChild.checked,t.focusinBubbles="onfocusin"in e,i.style.backgroundClip="content-box",i.cloneNode(!0).style.backgroundClip="",t.clearCloneStyle="content-box"===i.style.backgroundClip,x(function(){var n,r,s="padding:0;margin:0;border:0;display:block;-webkit-box-sizing:content-box;-moz-box-sizing:content-box;box-sizing:content-box",a=o.getElementsByTagName("body")[0];a&&(n=o.createElement("div"),n.style.cssText="border:0;width:0;height:0;position:absolute;top:0;left:-9999px;margin-top:1px",a.appendChild(n).appendChild(i),i.innerHTML="",i.style.cssText="-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;padding:1px;border:1px;display:block;width:4px;margin-top:1%;position:absolute;top:1%",x.swap(a,null!=a.style.zoom?{zoom:1}:{},function(){t.boxSizing=4===i.offsetWidth}),e.getComputedStyle&&(t.pixelPosition="1%"!==(e.getComputedStyle(i,null)||{}).top,t.boxSizingReliable="4px"===(e.getComputedStyle(i,null)||{width:"4px"}).width,r=i.appendChild(o.createElement("div")),r.style.cssText=i.style.cssText=s,r.style.marginRight=r.style.width="0",i.style.width="1px",t.reliableMarginRight=!parseFloat((e.getComputedStyle(r,null)||{}).marginRight)),a.removeChild(n))}),t):t}({});var L,q,H=/(?:\{[\s\S]*\}|\[[\s\S]*\])$/,O=/([A-Z])/g;function F(){Object.defineProperty(this.cache={},0,{get:function(){return{}}}),this.expando=x.expando+Math.random()}F.uid=1,F.accepts=function(e){return e.nodeType?1===e.nodeType||9===e.nodeType:!0},F.prototype={key:function(e){if(!F.accepts(e))return 0;var t={},n=e[this.expando];if(!n){n=F.uid++;try{t[this.expando]={value:n},Object.defineProperties(e,t)}catch(r){t[this.expando]=n,x.extend(e,t)}}return this.cache[n]||(this.cache[n]={}),n},set:function(e,t,n){var r,i=this.key(e),o=this.cache[i];if("string"==typeof t)o[t]=n;else if(x.isEmptyObject(o))x.extend(this.cache[i],t);else for(r in t)o[r]=t[r];return o},get:function(e,t){var n=this.cache[this.key(e)];return t===undefined?n:n[t]},access:function(e,t,n){var r;return t===undefined||t&&"string"==typeof t&&n===undefined?(r=this.get(e,t),r!==undefined?r:this.get(e,x.camelCase(t))):(this.set(e,t,n),n!==undefined?n:t)},remove:function(e,t){var n,r,i,o=this.key(e),s=this.cache[o];if(t===undefined)this.cache[o]={};else{x.isArray(t)?r=t.concat(t.map(x.camelCase)):(i=x.camelCase(t),t in s?r=[t,i]:(r=i,r=r in s?[r]:r.match(w)||[])),n=r.length;while(n--)delete s[r[n]]}},hasData:function(e){return!x.isEmptyObject(this.cache[e[this.expando]]||{})},discard:function(e){e[this.expando]&&delete this.cache[e[this.expando]]}},L=new F,q=new F,x.extend({acceptData:F.accepts,hasData:function(e){return L.hasData(e)||q.hasData(e)},data:function(e,t,n){return L.access(e,t,n)},removeData:function(e,t){L.remove(e,t)},_data:function(e,t,n){return q.access(e,t,n)},_removeData:function(e,t){q.remove(e,t)}}),x.fn.extend({data:function(e,t){var n,r,i=this[0],o=0,s=null;if(e===undefined){if(this.length&&(s=L.get(i),1===i.nodeType&&!q.get(i,"hasDataAttrs"))){for(n=i.attributes;n.length>o;o++)r=n[o].name,0===r.indexOf("data-")&&(r=x.camelCase(r.slice(5)),P(i,r,s[r]));q.set(i,"hasDataAttrs",!0)}return s}return"object"==typeof e?this.each(function(){L.set(this,e)}):x.access(this,function(t){var n,r=x.camelCase(e);if(i&&t===undefined){if(n=L.get(i,e),n!==undefined)return n;if(n=L.get(i,r),n!==undefined)return n;if(n=P(i,r,undefined),n!==undefined)return n}else this.each(function(){var n=L.get(this,r);L.set(this,r,t),-1!==e.indexOf("-")&&n!==undefined&&L.set(this,e,t)})},null,t,arguments.length>1,null,!0)},removeData:function(e){return this.each(function(){L.remove(this,e)})}});function P(e,t,n){var r;if(n===undefined&&1===e.nodeType)if(r="data-"+t.replace(O,"-$1").toLowerCase(),n=e.getAttribute(r),"string"==typeof n){try{n="true"===n?!0:"false"===n?!1:"null"===n?null:+n+""===n?+n:H.test(n)?JSON.parse(n):n}catch(i){}L.set(e,t,n)}else n=undefined;return n}x.extend({queue:function(e,t,n){var r;return e?(t=(t||"fx")+"queue",r=q.get(e,t),n&&(!r||x.isArray(n)?r=q.access(e,t,x.makeArray(n)):r.push(n)),r||[]):undefined},dequeue:function(e,t){t=t||"fx";var n=x.queue(e,t),r=n.length,i=n.shift(),o=x._queueHooks(e,t),s=function(){x.dequeue(e,t)
};"inprogress"===i&&(i=n.shift(),r--),i&&("fx"===t&&n.unshift("inprogress"),delete o.stop,i.call(e,s,o)),!r&&o&&o.empty.fire()},_queueHooks:function(e,t){var n=t+"queueHooks";return q.get(e,n)||q.access(e,n,{empty:x.Callbacks("once memory").add(function(){q.remove(e,[t+"queue",n])})})}}),x.fn.extend({queue:function(e,t){var n=2;return"string"!=typeof e&&(t=e,e="fx",n--),n>arguments.length?x.queue(this[0],e):t===undefined?this:this.each(function(){var n=x.queue(this,e,t);x._queueHooks(this,e),"fx"===e&&"inprogress"!==n[0]&&x.dequeue(this,e)})},dequeue:function(e){return this.each(function(){x.dequeue(this,e)})},delay:function(e,t){return e=x.fx?x.fx.speeds[e]||e:e,t=t||"fx",this.queue(t,function(t,n){var r=setTimeout(t,e);n.stop=function(){clearTimeout(r)}})},clearQueue:function(e){return this.queue(e||"fx",[])},promise:function(e,t){var n,r=1,i=x.Deferred(),o=this,s=this.length,a=function(){--r||i.resolveWith(o,[o])};"string"!=typeof e&&(t=e,e=undefined),e=e||"fx";while(s--)n=q.get(o[s],e+"queueHooks"),n&&n.empty&&(r++,n.empty.add(a));return a(),i.promise(t)}});var R,M,W=/[\t\r\n\f]/g,$=/\r/g,B=/^(?:input|select|textarea|button)$/i;x.fn.extend({attr:function(e,t){return x.access(this,x.attr,e,t,arguments.length>1)},removeAttr:function(e){return this.each(function(){x.removeAttr(this,e)})},prop:function(e,t){return x.access(this,x.prop,e,t,arguments.length>1)},removeProp:function(e){return this.each(function(){delete this[x.propFix[e]||e]})},addClass:function(e){var t,n,r,i,o,s=0,a=this.length,u="string"==typeof e&&e;if(x.isFunction(e))return this.each(function(t){x(this).addClass(e.call(this,t,this.className))});if(u)for(t=(e||"").match(w)||[];a>s;s++)if(n=this[s],r=1===n.nodeType&&(n.className?(" "+n.className+" ").replace(W," "):" ")){o=0;while(i=t[o++])0>r.indexOf(" "+i+" ")&&(r+=i+" ");n.className=x.trim(r)}return this},removeClass:function(e){var t,n,r,i,o,s=0,a=this.length,u=0===arguments.length||"string"==typeof e&&e;if(x.isFunction(e))return this.each(function(t){x(this).removeClass(e.call(this,t,this.className))});if(u)for(t=(e||"").match(w)||[];a>s;s++)if(n=this[s],r=1===n.nodeType&&(n.className?(" "+n.className+" ").replace(W," "):"")){o=0;while(i=t[o++])while(r.indexOf(" "+i+" ")>=0)r=r.replace(" "+i+" "," ");n.className=e?x.trim(r):""}return this},toggleClass:function(e,t){var n=typeof e;return"boolean"==typeof t&&"string"===n?t?this.addClass(e):this.removeClass(e):x.isFunction(e)?this.each(function(n){x(this).toggleClass(e.call(this,n,this.className,t),t)}):this.each(function(){if("string"===n){var t,i=0,o=x(this),s=e.match(w)||[];while(t=s[i++])o.hasClass(t)?o.removeClass(t):o.addClass(t)}else(n===r||"boolean"===n)&&(this.className&&q.set(this,"__className__",this.className),this.className=this.className||e===!1?"":q.get(this,"__className__")||"")})},hasClass:function(e){var t=" "+e+" ",n=0,r=this.length;for(;r>n;n++)if(1===this[n].nodeType&&(" "+this[n].className+" ").replace(W," ").indexOf(t)>=0)return!0;return!1},val:function(e){var t,n,r,i=this[0];{if(arguments.length)return r=x.isFunction(e),this.each(function(n){var i;1===this.nodeType&&(i=r?e.call(this,n,x(this).val()):e,null==i?i="":"number"==typeof i?i+="":x.isArray(i)&&(i=x.map(i,function(e){return null==e?"":e+""})),t=x.valHooks[this.type]||x.valHooks[this.nodeName.toLowerCase()],t&&"set"in t&&t.set(this,i,"value")!==undefined||(this.value=i))});if(i)return t=x.valHooks[i.type]||x.valHooks[i.nodeName.toLowerCase()],t&&"get"in t&&(n=t.get(i,"value"))!==undefined?n:(n=i.value,"string"==typeof n?n.replace($,""):null==n?"":n)}}}),x.extend({valHooks:{option:{get:function(e){var t=e.attributes.value;return!t||t.specified?e.value:e.text}},select:{get:function(e){var t,n,r=e.options,i=e.selectedIndex,o="select-one"===e.type||0>i,s=o?null:[],a=o?i+1:r.length,u=0>i?a:o?i:0;for(;a>u;u++)if(n=r[u],!(!n.selected&&u!==i||(x.support.optDisabled?n.disabled:null!==n.getAttribute("disabled"))||n.parentNode.disabled&&x.nodeName(n.parentNode,"optgroup"))){if(t=x(n).val(),o)return t;s.push(t)}return s},set:function(e,t){var n,r,i=e.options,o=x.makeArray(t),s=i.length;while(s--)r=i[s],(r.selected=x.inArray(x(r).val(),o)>=0)&&(n=!0);return n||(e.selectedIndex=-1),o}}},attr:function(e,t,n){var i,o,s=e.nodeType;if(e&&3!==s&&8!==s&&2!==s)return typeof e.getAttribute===r?x.prop(e,t,n):(1===s&&x.isXMLDoc(e)||(t=t.toLowerCase(),i=x.attrHooks[t]||(x.expr.match.bool.test(t)?M:R)),n===undefined?i&&"get"in i&&null!==(o=i.get(e,t))?o:(o=x.find.attr(e,t),null==o?undefined:o):null!==n?i&&"set"in i&&(o=i.set(e,n,t))!==undefined?o:(e.setAttribute(t,n+""),n):(x.removeAttr(e,t),undefined))},removeAttr:function(e,t){var n,r,i=0,o=t&&t.match(w);if(o&&1===e.nodeType)while(n=o[i++])r=x.propFix[n]||n,x.expr.match.bool.test(n)&&(e[r]=!1),e.removeAttribute(n)},attrHooks:{type:{set:function(e,t){if(!x.support.radioValue&&"radio"===t&&x.nodeName(e,"input")){var n=e.value;return e.setAttribute("type",t),n&&(e.value=n),t}}}},propFix:{"for":"htmlFor","class":"className"},prop:function(e,t,n){var r,i,o,s=e.nodeType;if(e&&3!==s&&8!==s&&2!==s)return o=1!==s||!x.isXMLDoc(e),o&&(t=x.propFix[t]||t,i=x.propHooks[t]),n!==undefined?i&&"set"in i&&(r=i.set(e,n,t))!==undefined?r:e[t]=n:i&&"get"in i&&null!==(r=i.get(e,t))?r:e[t]},propHooks:{tabIndex:{get:function(e){return e.hasAttribute("tabindex")||B.test(e.nodeName)||e.href?e.tabIndex:-1}}}}),M={set:function(e,t,n){return t===!1?x.removeAttr(e,n):e.setAttribute(n,n),n}},x.each(x.expr.match.bool.source.match(/\w+/g),function(e,t){var n=x.expr.attrHandle[t]||x.find.attr;x.expr.attrHandle[t]=function(e,t,r){var i=x.expr.attrHandle[t],o=r?undefined:(x.expr.attrHandle[t]=undefined)!=n(e,t,r)?t.toLowerCase():null;return x.expr.attrHandle[t]=i,o}}),x.support.optSelected||(x.propHooks.selected={get:function(e){var t=e.parentNode;return t&&t.parentNode&&t.parentNode.selectedIndex,null}}),x.each(["tabIndex","readOnly","maxLength","cellSpacing","cellPadding","rowSpan","colSpan","useMap","frameBorder","contentEditable"],function(){x.propFix[this.toLowerCase()]=this}),x.each(["radio","checkbox"],function(){x.valHooks[this]={set:function(e,t){return x.isArray(t)?e.checked=x.inArray(x(e).val(),t)>=0:undefined}},x.support.checkOn||(x.valHooks[this].get=function(e){return null===e.getAttribute("value")?"on":e.value})});var I=/^key/,z=/^(?:mouse|contextmenu)|click/,_=/^(?:focusinfocus|focusoutblur)$/,X=/^([^.]*)(?:\.(.+)|)$/;function U(){return!0}function Y(){return!1}function V(){try{return o.activeElement}catch(e){}}x.event={global:{},add:function(e,t,n,i,o){var s,a,u,l,c,p,f,h,d,g,m,y=q.get(e);if(y){n.handler&&(s=n,n=s.handler,o=s.selector),n.guid||(n.guid=x.guid++),(l=y.events)||(l=y.events={}),(a=y.handle)||(a=y.handle=function(e){return typeof x===r||e&&x.event.triggered===e.type?undefined:x.event.dispatch.apply(a.elem,arguments)},a.elem=e),t=(t||"").match(w)||[""],c=t.length;while(c--)u=X.exec(t[c])||[],d=m=u[1],g=(u[2]||"").split(".").sort(),d&&(f=x.event.special[d]||{},d=(o?f.delegateType:f.bindType)||d,f=x.event.special[d]||{},p=x.extend({type:d,origType:m,data:i,handler:n,guid:n.guid,selector:o,needsContext:o&&x.expr.match.needsContext.test(o),namespace:g.join(".")},s),(h=l[d])||(h=l[d]=[],h.delegateCount=0,f.setup&&f.setup.call(e,i,g,a)!==!1||e.addEventListener&&e.addEventListener(d,a,!1)),f.add&&(f.add.call(e,p),p.handler.guid||(p.handler.guid=n.guid)),o?h.splice(h.delegateCount++,0,p):h.push(p),x.event.global[d]=!0);e=null}},remove:function(e,t,n,r,i){var o,s,a,u,l,c,p,f,h,d,g,m=q.hasData(e)&&q.get(e);if(m&&(u=m.events)){t=(t||"").match(w)||[""],l=t.length;while(l--)if(a=X.exec(t[l])||[],h=g=a[1],d=(a[2]||"").split(".").sort(),h){p=x.event.special[h]||{},h=(r?p.delegateType:p.bindType)||h,f=u[h]||[],a=a[2]&&RegExp("(^|\\.)"+d.join("\\.(?:.*\\.|)")+"(\\.|$)"),s=o=f.length;while(o--)c=f[o],!i&&g!==c.origType||n&&n.guid!==c.guid||a&&!a.test(c.namespace)||r&&r!==c.selector&&("**"!==r||!c.selector)||(f.splice(o,1),c.selector&&f.delegateCount--,p.remove&&p.remove.call(e,c));s&&!f.length&&(p.teardown&&p.teardown.call(e,d,m.handle)!==!1||x.removeEvent(e,h,m.handle),delete u[h])}else for(h in u)x.event.remove(e,h+t[l],n,r,!0);x.isEmptyObject(u)&&(delete m.handle,q.remove(e,"events"))}},trigger:function(t,n,r,i){var s,a,u,l,c,p,f,h=[r||o],d=y.call(t,"type")?t.type:t,g=y.call(t,"namespace")?t.namespace.split("."):[];if(a=u=r=r||o,3!==r.nodeType&&8!==r.nodeType&&!_.test(d+x.event.triggered)&&(d.indexOf(".")>=0&&(g=d.split("."),d=g.shift(),g.sort()),c=0>d.indexOf(":")&&"on"+d,t=t[x.expando]?t:new x.Event(d,"object"==typeof t&&t),t.isTrigger=i?2:3,t.namespace=g.join("."),t.namespace_re=t.namespace?RegExp("(^|\\.)"+g.join("\\.(?:.*\\.|)")+"(\\.|$)"):null,t.result=undefined,t.target||(t.target=r),n=null==n?[t]:x.makeArray(n,[t]),f=x.event.special[d]||{},i||!f.trigger||f.trigger.apply(r,n)!==!1)){if(!i&&!f.noBubble&&!x.isWindow(r)){for(l=f.delegateType||d,_.test(l+d)||(a=a.parentNode);a;a=a.parentNode)h.push(a),u=a;u===(r.ownerDocument||o)&&h.push(u.defaultView||u.parentWindow||e)}s=0;while((a=h[s++])&&!t.isPropagationStopped())t.type=s>1?l:f.bindType||d,p=(q.get(a,"events")||{})[t.type]&&q.get(a,"handle"),p&&p.apply(a,n),p=c&&a[c],p&&x.acceptData(a)&&p.apply&&p.apply(a,n)===!1&&t.preventDefault();return t.type=d,i||t.isDefaultPrevented()||f._default&&f._default.apply(h.pop(),n)!==!1||!x.acceptData(r)||c&&x.isFunction(r[d])&&!x.isWindow(r)&&(u=r[c],u&&(r[c]=null),x.event.triggered=d,r[d](),x.event.triggered=undefined,u&&(r[c]=u)),t.result}},dispatch:function(e){e=x.event.fix(e);var t,n,r,i,o,s=[],a=d.call(arguments),u=(q.get(this,"events")||{})[e.type]||[],l=x.event.special[e.type]||{};if(a[0]=e,e.delegateTarget=this,!l.preDispatch||l.preDispatch.call(this,e)!==!1){s=x.event.handlers.call(this,e,u),t=0;while((i=s[t++])&&!e.isPropagationStopped()){e.currentTarget=i.elem,n=0;while((o=i.handlers[n++])&&!e.isImmediatePropagationStopped())(!e.namespace_re||e.namespace_re.test(o.namespace))&&(e.handleObj=o,e.data=o.data,r=((x.event.special[o.origType]||{}).handle||o.handler).apply(i.elem,a),r!==undefined&&(e.result=r)===!1&&(e.preventDefault(),e.stopPropagation()))}return l.postDispatch&&l.postDispatch.call(this,e),e.result}},handlers:function(e,t){var n,r,i,o,s=[],a=t.delegateCount,u=e.target;if(a&&u.nodeType&&(!e.button||"click"!==e.type))for(;u!==this;u=u.parentNode||this)if(u.disabled!==!0||"click"!==e.type){for(r=[],n=0;a>n;n++)o=t[n],i=o.selector+" ",r[i]===undefined&&(r[i]=o.needsContext?x(i,this).index(u)>=0:x.find(i,this,null,[u]).length),r[i]&&r.push(o);r.length&&s.push({elem:u,handlers:r})}return t.length>a&&s.push({elem:this,handlers:t.slice(a)}),s},props:"altKey bubbles cancelable ctrlKey currentTarget eventPhase metaKey relatedTarget shiftKey target timeStamp view which".split(" "),fixHooks:{},keyHooks:{props:"char charCode key keyCode".split(" "),filter:function(e,t){return null==e.which&&(e.which=null!=t.charCode?t.charCode:t.keyCode),e}},mouseHooks:{props:"button buttons clientX clientY offsetX offsetY pageX pageY screenX screenY toElement".split(" "),filter:function(e,t){var n,r,i,s=t.button;return null==e.pageX&&null!=t.clientX&&(n=e.target.ownerDocument||o,r=n.documentElement,i=n.body,e.pageX=t.clientX+(r&&r.scrollLeft||i&&i.scrollLeft||0)-(r&&r.clientLeft||i&&i.clientLeft||0),e.pageY=t.clientY+(r&&r.scrollTop||i&&i.scrollTop||0)-(r&&r.clientTop||i&&i.clientTop||0)),e.which||s===undefined||(e.which=1&s?1:2&s?3:4&s?2:0),e}},fix:function(e){if(e[x.expando])return e;var t,n,r,i=e.type,s=e,a=this.fixHooks[i];a||(this.fixHooks[i]=a=z.test(i)?this.mouseHooks:I.test(i)?this.keyHooks:{}),r=a.props?this.props.concat(a.props):this.props,e=new x.Event(s),t=r.length;while(t--)n=r[t],e[n]=s[n];return e.target||(e.target=o),3===e.target.nodeType&&(e.target=e.target.parentNode),a.filter?a.filter(e,s):e},special:{load:{noBubble:!0},focus:{trigger:function(){return this!==V()&&this.focus?(this.focus(),!1):undefined},delegateType:"focusin"},blur:{trigger:function(){return this===V()&&this.blur?(this.blur(),!1):undefined},delegateType:"focusout"},click:{trigger:function(){return"checkbox"===this.type&&this.click&&x.nodeName(this,"input")?(this.click(),!1):undefined},_default:function(e){return x.nodeName(e.target,"a")}},beforeunload:{postDispatch:function(e){e.result!==undefined&&(e.originalEvent.returnValue=e.result)}}},simulate:function(e,t,n,r){var i=x.extend(new x.Event,n,{type:e,isSimulated:!0,originalEvent:{}});r?x.event.trigger(i,null,t):x.event.dispatch.call(t,i),i.isDefaultPrevented()&&n.preventDefault()}},x.removeEvent=function(e,t,n){e.removeEventListener&&e.removeEventListener(t,n,!1)},x.Event=function(e,t){return this instanceof x.Event?(e&&e.type?(this.originalEvent=e,this.type=e.type,this.isDefaultPrevented=e.defaultPrevented||e.getPreventDefault&&e.getPreventDefault()?U:Y):this.type=e,t&&x.extend(this,t),this.timeStamp=e&&e.timeStamp||x.now(),this[x.expando]=!0,undefined):new x.Event(e,t)},x.Event.prototype={isDefaultPrevented:Y,isPropagationStopped:Y,isImmediatePropagationStopped:Y,preventDefault:function(){var e=this.originalEvent;this.isDefaultPrevented=U,e&&e.preventDefault&&e.preventDefault()},stopPropagation:function(){var e=this.originalEvent;this.isPropagationStopped=U,e&&e.stopPropagation&&e.stopPropagation()},stopImmediatePropagation:function(){this.isImmediatePropagationStopped=U,this.stopPropagation()}},x.each({mouseenter:"mouseover",mouseleave:"mouseout"},function(e,t){x.event.special[e]={delegateType:t,bindType:t,handle:function(e){var n,r=this,i=e.relatedTarget,o=e.handleObj;return(!i||i!==r&&!x.contains(r,i))&&(e.type=o.origType,n=o.handler.apply(this,arguments),e.type=t),n}}}),x.support.focusinBubbles||x.each({focus:"focusin",blur:"focusout"},function(e,t){var n=0,r=function(e){x.event.simulate(t,e.target,x.event.fix(e),!0)};x.event.special[t]={setup:function(){0===n++&&o.addEventListener(e,r,!0)},teardown:function(){0===--n&&o.removeEventListener(e,r,!0)}}}),x.fn.extend({on:function(e,t,n,r,i){var o,s;if("object"==typeof e){"string"!=typeof t&&(n=n||t,t=undefined);for(s in e)this.on(s,t,n,e[s],i);return this}if(null==n&&null==r?(r=t,n=t=undefined):null==r&&("string"==typeof t?(r=n,n=undefined):(r=n,n=t,t=undefined)),r===!1)r=Y;else if(!r)return this;return 1===i&&(o=r,r=function(e){return x().off(e),o.apply(this,arguments)},r.guid=o.guid||(o.guid=x.guid++)),this.each(function(){x.event.add(this,e,r,n,t)})},one:function(e,t,n,r){return this.on(e,t,n,r,1)},off:function(e,t,n){var r,i;if(e&&e.preventDefault&&e.handleObj)return r=e.handleObj,x(e.delegateTarget).off(r.namespace?r.origType+"."+r.namespace:r.origType,r.selector,r.handler),this;if("object"==typeof e){for(i in e)this.off(i,t,e[i]);return this}return(t===!1||"function"==typeof t)&&(n=t,t=undefined),n===!1&&(n=Y),this.each(function(){x.event.remove(this,e,n,t)})},trigger:function(e,t){return this.each(function(){x.event.trigger(e,t,this)})},triggerHandler:function(e,t){var n=this[0];return n?x.event.trigger(e,t,n,!0):undefined}});var G=/^.[^:#\[\.,]*$/,J=/^(?:parents|prev(?:Until|All))/,Q=x.expr.match.needsContext,K={children:!0,contents:!0,next:!0,prev:!0};x.fn.extend({find:function(e){var t,n=[],r=this,i=r.length;if("string"!=typeof e)return this.pushStack(x(e).filter(function(){for(t=0;i>t;t++)if(x.contains(r[t],this))return!0}));for(t=0;i>t;t++)x.find(e,r[t],n);return n=this.pushStack(i>1?x.unique(n):n),n.selector=this.selector?this.selector+" "+e:e,n},has:function(e){var t=x(e,this),n=t.length;return this.filter(function(){var e=0;for(;n>e;e++)if(x.contains(this,t[e]))return!0})},not:function(e){return this.pushStack(et(this,e||[],!0))},filter:function(e){return this.pushStack(et(this,e||[],!1))},is:function(e){return!!et(this,"string"==typeof e&&Q.test(e)?x(e):e||[],!1).length},closest:function(e,t){var n,r=0,i=this.length,o=[],s=Q.test(e)||"string"!=typeof e?x(e,t||this.context):0;for(;i>r;r++)for(n=this[r];n&&n!==t;n=n.parentNode)if(11>n.nodeType&&(s?s.index(n)>-1:1===n.nodeType&&x.find.matchesSelector(n,e))){n=o.push(n);break}return this.pushStack(o.length>1?x.unique(o):o)},index:function(e){return e?"string"==typeof e?g.call(x(e),this[0]):g.call(this,e.jquery?e[0]:e):this[0]&&this[0].parentNode?this.first().prevAll().length:-1},add:function(e,t){var n="string"==typeof e?x(e,t):x.makeArray(e&&e.nodeType?[e]:e),r=x.merge(this.get(),n);return this.pushStack(x.unique(r))},addBack:function(e){return this.add(null==e?this.prevObject:this.prevObject.filter(e))}});function Z(e,t){while((e=e[t])&&1!==e.nodeType);return e}x.each({parent:function(e){var t=e.parentNode;return t&&11!==t.nodeType?t:null},parents:function(e){return x.dir(e,"parentNode")},parentsUntil:function(e,t,n){return x.dir(e,"parentNode",n)},next:function(e){return Z(e,"nextSibling")},prev:function(e){return Z(e,"previousSibling")},nextAll:function(e){return x.dir(e,"nextSibling")},prevAll:function(e){return x.dir(e,"previousSibling")},nextUntil:function(e,t,n){return x.dir(e,"nextSibling",n)},prevUntil:function(e,t,n){return x.dir(e,"previousSibling",n)},siblings:function(e){return x.sibling((e.parentNode||{}).firstChild,e)},children:function(e){return x.sibling(e.firstChild)},contents:function(e){return e.contentDocument||x.merge([],e.childNodes)}},function(e,t){x.fn[e]=function(n,r){var i=x.map(this,t,n);return"Until"!==e.slice(-5)&&(r=n),r&&"string"==typeof r&&(i=x.filter(r,i)),this.length>1&&(K[e]||x.unique(i),J.test(e)&&i.reverse()),this.pushStack(i)}}),x.extend({filter:function(e,t,n){var r=t[0];return n&&(e=":not("+e+")"),1===t.length&&1===r.nodeType?x.find.matchesSelector(r,e)?[r]:[]:x.find.matches(e,x.grep(t,function(e){return 1===e.nodeType}))},dir:function(e,t,n){var r=[],i=n!==undefined;while((e=e[t])&&9!==e.nodeType)if(1===e.nodeType){if(i&&x(e).is(n))break;r.push(e)}return r},sibling:function(e,t){var n=[];for(;e;e=e.nextSibling)1===e.nodeType&&e!==t&&n.push(e);return n}});function et(e,t,n){if(x.isFunction(t))return x.grep(e,function(e,r){return!!t.call(e,r,e)!==n});if(t.nodeType)return x.grep(e,function(e){return e===t!==n});if("string"==typeof t){if(G.test(t))return x.filter(t,e,n);t=x.filter(t,e)}return x.grep(e,function(e){return g.call(t,e)>=0!==n})}var tt=/<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:]+)[^>]*)\/>/gi,nt=/<([\w:]+)/,rt=/<|&#?\w+;/,it=/<(?:script|style|link)/i,ot=/^(?:checkbox|radio)$/i,st=/checked\s*(?:[^=]|=\s*.checked.)/i,at=/^$|\/(?:java|ecma)script/i,ut=/^true\/(.*)/,lt=/^\s*<!(?:\[CDATA\[|--)|(?:\]\]|--)>\s*$/g,ct={option:[1,"<select multiple='multiple'>","</select>"],thead:[1,"<table>","</table>"],col:[2,"<table><colgroup>","</colgroup></table>"],tr:[2,"<table><tbody>","</tbody></table>"],td:[3,"<table><tbody><tr>","</tr></tbody></table>"],_default:[0,"",""]};ct.optgroup=ct.option,ct.tbody=ct.tfoot=ct.colgroup=ct.caption=ct.thead,ct.th=ct.td,x.fn.extend({text:function(e){return x.access(this,function(e){return e===undefined?x.text(this):this.empty().append((this[0]&&this[0].ownerDocument||o).createTextNode(e))},null,e,arguments.length)},append:function(){return this.domManip(arguments,function(e){if(1===this.nodeType||11===this.nodeType||9===this.nodeType){var t=pt(this,e);t.appendChild(e)}})},prepend:function(){return this.domManip(arguments,function(e){if(1===this.nodeType||11===this.nodeType||9===this.nodeType){var t=pt(this,e);t.insertBefore(e,t.firstChild)}})},before:function(){return this.domManip(arguments,function(e){this.parentNode&&this.parentNode.insertBefore(e,this)})},after:function(){return this.domManip(arguments,function(e){this.parentNode&&this.parentNode.insertBefore(e,this.nextSibling)})},remove:function(e,t){var n,r=e?x.filter(e,this):this,i=0;for(;null!=(n=r[i]);i++)t||1!==n.nodeType||x.cleanData(mt(n)),n.parentNode&&(t&&x.contains(n.ownerDocument,n)&&dt(mt(n,"script")),n.parentNode.removeChild(n));return this},empty:function(){var e,t=0;for(;null!=(e=this[t]);t++)1===e.nodeType&&(x.cleanData(mt(e,!1)),e.textContent="");return this},clone:function(e,t){return e=null==e?!1:e,t=null==t?e:t,this.map(function(){return x.clone(this,e,t)})},html:function(e){return x.access(this,function(e){var t=this[0]||{},n=0,r=this.length;if(e===undefined&&1===t.nodeType)return t.innerHTML;if("string"==typeof e&&!it.test(e)&&!ct[(nt.exec(e)||["",""])[1].toLowerCase()]){e=e.replace(tt,"<$1></$2>");try{for(;r>n;n++)t=this[n]||{},1===t.nodeType&&(x.cleanData(mt(t,!1)),t.innerHTML=e);t=0}catch(i){}}t&&this.empty().append(e)},null,e,arguments.length)},replaceWith:function(){var e=x.map(this,function(e){return[e.nextSibling,e.parentNode]}),t=0;return this.domManip(arguments,function(n){var r=e[t++],i=e[t++];i&&(r&&r.parentNode!==i&&(r=this.nextSibling),x(this).remove(),i.insertBefore(n,r))},!0),t?this:this.remove()},detach:function(e){return this.remove(e,!0)},domManip:function(e,t,n){e=f.apply([],e);var r,i,o,s,a,u,l=0,c=this.length,p=this,h=c-1,d=e[0],g=x.isFunction(d);if(g||!(1>=c||"string"!=typeof d||x.support.checkClone)&&st.test(d))return this.each(function(r){var i=p.eq(r);g&&(e[0]=d.call(this,r,i.html())),i.domManip(e,t,n)});if(c&&(r=x.buildFragment(e,this[0].ownerDocument,!1,!n&&this),i=r.firstChild,1===r.childNodes.length&&(r=i),i)){for(o=x.map(mt(r,"script"),ft),s=o.length;c>l;l++)a=r,l!==h&&(a=x.clone(a,!0,!0),s&&x.merge(o,mt(a,"script"))),t.call(this[l],a,l);if(s)for(u=o[o.length-1].ownerDocument,x.map(o,ht),l=0;s>l;l++)a=o[l],at.test(a.type||"")&&!q.access(a,"globalEval")&&x.contains(u,a)&&(a.src?x._evalUrl(a.src):x.globalEval(a.textContent.replace(lt,"")))}return this}}),x.each({appendTo:"append",prependTo:"prepend",insertBefore:"before",insertAfter:"after",replaceAll:"replaceWith"},function(e,t){x.fn[e]=function(e){var n,r=[],i=x(e),o=i.length-1,s=0;for(;o>=s;s++)n=s===o?this:this.clone(!0),x(i[s])[t](n),h.apply(r,n.get());return this.pushStack(r)}}),x.extend({clone:function(e,t,n){var r,i,o,s,a=e.cloneNode(!0),u=x.contains(e.ownerDocument,e);if(!(x.support.noCloneChecked||1!==e.nodeType&&11!==e.nodeType||x.isXMLDoc(e)))for(s=mt(a),o=mt(e),r=0,i=o.length;i>r;r++)yt(o[r],s[r]);if(t)if(n)for(o=o||mt(e),s=s||mt(a),r=0,i=o.length;i>r;r++)gt(o[r],s[r]);else gt(e,a);return s=mt(a,"script"),s.length>0&&dt(s,!u&&mt(e,"script")),a},buildFragment:function(e,t,n,r){var i,o,s,a,u,l,c=0,p=e.length,f=t.createDocumentFragment(),h=[];for(;p>c;c++)if(i=e[c],i||0===i)if("object"===x.type(i))x.merge(h,i.nodeType?[i]:i);else if(rt.test(i)){o=o||f.appendChild(t.createElement("div")),s=(nt.exec(i)||["",""])[1].toLowerCase(),a=ct[s]||ct._default,o.innerHTML=a[1]+i.replace(tt,"<$1></$2>")+a[2],l=a[0];while(l--)o=o.lastChild;x.merge(h,o.childNodes),o=f.firstChild,o.textContent=""}else h.push(t.createTextNode(i));f.textContent="",c=0;while(i=h[c++])if((!r||-1===x.inArray(i,r))&&(u=x.contains(i.ownerDocument,i),o=mt(f.appendChild(i),"script"),u&&dt(o),n)){l=0;while(i=o[l++])at.test(i.type||"")&&n.push(i)}return f},cleanData:function(e){var t,n,r,i,o,s,a=x.event.special,u=0;for(;(n=e[u])!==undefined;u++){if(F.accepts(n)&&(o=n[q.expando],o&&(t=q.cache[o]))){if(r=Object.keys(t.events||{}),r.length)for(s=0;(i=r[s])!==undefined;s++)a[i]?x.event.remove(n,i):x.removeEvent(n,i,t.handle);q.cache[o]&&delete q.cache[o]}delete L.cache[n[L.expando]]}},_evalUrl:function(e){return x.ajax({url:e,type:"GET",dataType:"script",async:!1,global:!1,"throws":!0})}});function pt(e,t){return x.nodeName(e,"table")&&x.nodeName(1===t.nodeType?t:t.firstChild,"tr")?e.getElementsByTagName("tbody")[0]||e.appendChild(e.ownerDocument.createElement("tbody")):e}function ft(e){return e.type=(null!==e.getAttribute("type"))+"/"+e.type,e}function ht(e){var t=ut.exec(e.type);return t?e.type=t[1]:e.removeAttribute("type"),e}function dt(e,t){var n=e.length,r=0;for(;n>r;r++)q.set(e[r],"globalEval",!t||q.get(t[r],"globalEval"))}function gt(e,t){var n,r,i,o,s,a,u,l;if(1===t.nodeType){if(q.hasData(e)&&(o=q.access(e),s=q.set(t,o),l=o.events)){delete s.handle,s.events={};for(i in l)for(n=0,r=l[i].length;r>n;n++)x.event.add(t,i,l[i][n])}L.hasData(e)&&(a=L.access(e),u=x.extend({},a),L.set(t,u))}}function mt(e,t){var n=e.getElementsByTagName?e.getElementsByTagName(t||"*"):e.querySelectorAll?e.querySelectorAll(t||"*"):[];return t===undefined||t&&x.nodeName(e,t)?x.merge([e],n):n}function yt(e,t){var n=t.nodeName.toLowerCase();"input"===n&&ot.test(e.type)?t.checked=e.checked:("input"===n||"textarea"===n)&&(t.defaultValue=e.defaultValue)}x.fn.extend({wrapAll:function(e){var t;return x.isFunction(e)?this.each(function(t){x(this).wrapAll(e.call(this,t))}):(this[0]&&(t=x(e,this[0].ownerDocument).eq(0).clone(!0),this[0].parentNode&&t.insertBefore(this[0]),t.map(function(){var e=this;while(e.firstElementChild)e=e.firstElementChild;return e}).append(this)),this)},wrapInner:function(e){return x.isFunction(e)?this.each(function(t){x(this).wrapInner(e.call(this,t))}):this.each(function(){var t=x(this),n=t.contents();n.length?n.wrapAll(e):t.append(e)})},wrap:function(e){var t=x.isFunction(e);return this.each(function(n){x(this).wrapAll(t?e.call(this,n):e)})},unwrap:function(){return this.parent().each(function(){x.nodeName(this,"body")||x(this).replaceWith(this.childNodes)}).end()}});var vt,xt,bt=/^(none|table(?!-c[ea]).+)/,wt=/^margin/,Tt=RegExp("^("+b+")(.*)$","i"),Ct=RegExp("^("+b+")(?!px)[a-z%]+$","i"),kt=RegExp("^([+-])=("+b+")","i"),Nt={BODY:"block"},Et={position:"absolute",visibility:"hidden",display:"block"},St={letterSpacing:0,fontWeight:400},jt=["Top","Right","Bottom","Left"],Dt=["Webkit","O","Moz","ms"];function At(e,t){if(t in e)return t;var n=t.charAt(0).toUpperCase()+t.slice(1),r=t,i=Dt.length;while(i--)if(t=Dt[i]+n,t in e)return t;return r}function Lt(e,t){return e=t||e,"none"===x.css(e,"display")||!x.contains(e.ownerDocument,e)}function qt(t){return e.getComputedStyle(t,null)}function Ht(e,t){var n,r,i,o=[],s=0,a=e.length;for(;a>s;s++)r=e[s],r.style&&(o[s]=q.get(r,"olddisplay"),n=r.style.display,t?(o[s]||"none"!==n||(r.style.display=""),""===r.style.display&&Lt(r)&&(o[s]=q.access(r,"olddisplay",Rt(r.nodeName)))):o[s]||(i=Lt(r),(n&&"none"!==n||!i)&&q.set(r,"olddisplay",i?n:x.css(r,"display"))));for(s=0;a>s;s++)r=e[s],r.style&&(t&&"none"!==r.style.display&&""!==r.style.display||(r.style.display=t?o[s]||"":"none"));return e}x.fn.extend({css:function(e,t){return x.access(this,function(e,t,n){var r,i,o={},s=0;if(x.isArray(t)){for(r=qt(e),i=t.length;i>s;s++)o[t[s]]=x.css(e,t[s],!1,r);return o}return n!==undefined?x.style(e,t,n):x.css(e,t)},e,t,arguments.length>1)},show:function(){return Ht(this,!0)},hide:function(){return Ht(this)},toggle:function(e){return"boolean"==typeof e?e?this.show():this.hide():this.each(function(){Lt(this)?x(this).show():x(this).hide()})}}),x.extend({cssHooks:{opacity:{get:function(e,t){if(t){var n=vt(e,"opacity");return""===n?"1":n}}}},cssNumber:{columnCount:!0,fillOpacity:!0,fontWeight:!0,lineHeight:!0,opacity:!0,order:!0,orphans:!0,widows:!0,zIndex:!0,zoom:!0},cssProps:{"float":"cssFloat"},style:function(e,t,n,r){if(e&&3!==e.nodeType&&8!==e.nodeType&&e.style){var i,o,s,a=x.camelCase(t),u=e.style;return t=x.cssProps[a]||(x.cssProps[a]=At(u,a)),s=x.cssHooks[t]||x.cssHooks[a],n===undefined?s&&"get"in s&&(i=s.get(e,!1,r))!==undefined?i:u[t]:(o=typeof n,"string"===o&&(i=kt.exec(n))&&(n=(i[1]+1)*i[2]+parseFloat(x.css(e,t)),o="number"),null==n||"number"===o&&isNaN(n)||("number"!==o||x.cssNumber[a]||(n+="px"),x.support.clearCloneStyle||""!==n||0!==t.indexOf("background")||(u[t]="inherit"),s&&"set"in s&&(n=s.set(e,n,r))===undefined||(u[t]=n)),undefined)}},css:function(e,t,n,r){var i,o,s,a=x.camelCase(t);return t=x.cssProps[a]||(x.cssProps[a]=At(e.style,a)),s=x.cssHooks[t]||x.cssHooks[a],s&&"get"in s&&(i=s.get(e,!0,n)),i===undefined&&(i=vt(e,t,r)),"normal"===i&&t in St&&(i=St[t]),""===n||n?(o=parseFloat(i),n===!0||x.isNumeric(o)?o||0:i):i}}),vt=function(e,t,n){var r,i,o,s=n||qt(e),a=s?s.getPropertyValue(t)||s[t]:undefined,u=e.style;return s&&(""!==a||x.contains(e.ownerDocument,e)||(a=x.style(e,t)),Ct.test(a)&&wt.test(t)&&(r=u.width,i=u.minWidth,o=u.maxWidth,u.minWidth=u.maxWidth=u.width=a,a=s.width,u.width=r,u.minWidth=i,u.maxWidth=o)),a};function Ot(e,t,n){var r=Tt.exec(t);return r?Math.max(0,r[1]-(n||0))+(r[2]||"px"):t}function Ft(e,t,n,r,i){var o=n===(r?"border":"content")?4:"width"===t?1:0,s=0;for(;4>o;o+=2)"margin"===n&&(s+=x.css(e,n+jt[o],!0,i)),r?("content"===n&&(s-=x.css(e,"padding"+jt[o],!0,i)),"margin"!==n&&(s-=x.css(e,"border"+jt[o]+"Width",!0,i))):(s+=x.css(e,"padding"+jt[o],!0,i),"padding"!==n&&(s+=x.css(e,"border"+jt[o]+"Width",!0,i)));return s}function Pt(e,t,n){var r=!0,i="width"===t?e.offsetWidth:e.offsetHeight,o=qt(e),s=x.support.boxSizing&&"border-box"===x.css(e,"boxSizing",!1,o);if(0>=i||null==i){if(i=vt(e,t,o),(0>i||null==i)&&(i=e.style[t]),Ct.test(i))return i;r=s&&(x.support.boxSizingReliable||i===e.style[t]),i=parseFloat(i)||0}return i+Ft(e,t,n||(s?"border":"content"),r,o)+"px"}function Rt(e){var t=o,n=Nt[e];return n||(n=Mt(e,t),"none"!==n&&n||(xt=(xt||x("<iframe frameborder='0' width='0' height='0'/>").css("cssText","display:block !important")).appendTo(t.documentElement),t=(xt[0].contentWindow||xt[0].contentDocument).document,t.write("<!doctype html><html><body>"),t.close(),n=Mt(e,t),xt.detach()),Nt[e]=n),n}function Mt(e,t){var n=x(t.createElement(e)).appendTo(t.body),r=x.css(n[0],"display");return n.remove(),r}x.each(["height","width"],function(e,t){x.cssHooks[t]={get:function(e,n,r){return n?0===e.offsetWidth&&bt.test(x.css(e,"display"))?x.swap(e,Et,function(){return Pt(e,t,r)}):Pt(e,t,r):undefined},set:function(e,n,r){var i=r&&qt(e);return Ot(e,n,r?Ft(e,t,r,x.support.boxSizing&&"border-box"===x.css(e,"boxSizing",!1,i),i):0)}}}),x(function(){x.support.reliableMarginRight||(x.cssHooks.marginRight={get:function(e,t){return t?x.swap(e,{display:"inline-block"},vt,[e,"marginRight"]):undefined}}),!x.support.pixelPosition&&x.fn.position&&x.each(["top","left"],function(e,t){x.cssHooks[t]={get:function(e,n){return n?(n=vt(e,t),Ct.test(n)?x(e).position()[t]+"px":n):undefined}}})}),x.expr&&x.expr.filters&&(x.expr.filters.hidden=function(e){return 0>=e.offsetWidth&&0>=e.offsetHeight},x.expr.filters.visible=function(e){return!x.expr.filters.hidden(e)}),x.each({margin:"",padding:"",border:"Width"},function(e,t){x.cssHooks[e+t]={expand:function(n){var r=0,i={},o="string"==typeof n?n.split(" "):[n];for(;4>r;r++)i[e+jt[r]+t]=o[r]||o[r-2]||o[0];return i}},wt.test(e)||(x.cssHooks[e+t].set=Ot)});var Wt=/%20/g,$t=/\[\]$/,Bt=/\r?\n/g,It=/^(?:submit|button|image|reset|file)$/i,zt=/^(?:input|select|textarea|keygen)/i;x.fn.extend({serialize:function(){return x.param(this.serializeArray())},serializeArray:function(){return this.map(function(){var e=x.prop(this,"elements");return e?x.makeArray(e):this}).filter(function(){var e=this.type;return this.name&&!x(this).is(":disabled")&&zt.test(this.nodeName)&&!It.test(e)&&(this.checked||!ot.test(e))}).map(function(e,t){var n=x(this).val();return null==n?null:x.isArray(n)?x.map(n,function(e){return{name:t.name,value:e.replace(Bt,"\r\n")}}):{name:t.name,value:n.replace(Bt,"\r\n")}}).get()}}),x.param=function(e,t){var n,r=[],i=function(e,t){t=x.isFunction(t)?t():null==t?"":t,r[r.length]=encodeURIComponent(e)+"="+encodeURIComponent(t)};if(t===undefined&&(t=x.ajaxSettings&&x.ajaxSettings.traditional),x.isArray(e)||e.jquery&&!x.isPlainObject(e))x.each(e,function(){i(this.name,this.value)});else for(n in e)_t(n,e[n],t,i);return r.join("&").replace(Wt,"+")};function _t(e,t,n,r){var i;if(x.isArray(t))x.each(t,function(t,i){n||$t.test(e)?r(e,i):_t(e+"["+("object"==typeof i?t:"")+"]",i,n,r)});else if(n||"object"!==x.type(t))r(e,t);else for(i in t)_t(e+"["+i+"]",t[i],n,r)}x.each("blur focus focusin focusout load resize scroll unload click dblclick mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave change select submit keydown keypress keyup error contextmenu".split(" "),function(e,t){x.fn[t]=function(e,n){return arguments.length>0?this.on(t,null,e,n):this.trigger(t)}}),x.fn.extend({hover:function(e,t){return this.mouseenter(e).mouseleave(t||e)},bind:function(e,t,n){return this.on(e,null,t,n)},unbind:function(e,t){return this.off(e,null,t)
},delegate:function(e,t,n,r){return this.on(t,e,n,r)},undelegate:function(e,t,n){return 1===arguments.length?this.off(e,"**"):this.off(t,e||"**",n)}});var Xt,Ut,Yt=x.now(),Vt=/\?/,Gt=/#.*$/,Jt=/([?&])_=[^&]*/,Qt=/^(.*?):[ \t]*([^\r\n]*)$/gm,Kt=/^(?:about|app|app-storage|.+-extension|file|res|widget):$/,Zt=/^(?:GET|HEAD)$/,en=/^\/\//,tn=/^([\w.+-]+:)(?:\/\/([^\/?#:]*)(?::(\d+)|)|)/,nn=x.fn.load,rn={},on={},sn="*/".concat("*");try{Ut=i.href}catch(an){Ut=o.createElement("a"),Ut.href="",Ut=Ut.href}Xt=tn.exec(Ut.toLowerCase())||[];function un(e){return function(t,n){"string"!=typeof t&&(n=t,t="*");var r,i=0,o=t.toLowerCase().match(w)||[];if(x.isFunction(n))while(r=o[i++])"+"===r[0]?(r=r.slice(1)||"*",(e[r]=e[r]||[]).unshift(n)):(e[r]=e[r]||[]).push(n)}}function ln(e,t,n,r){var i={},o=e===on;function s(a){var u;return i[a]=!0,x.each(e[a]||[],function(e,a){var l=a(t,n,r);return"string"!=typeof l||o||i[l]?o?!(u=l):undefined:(t.dataTypes.unshift(l),s(l),!1)}),u}return s(t.dataTypes[0])||!i["*"]&&s("*")}function cn(e,t){var n,r,i=x.ajaxSettings.flatOptions||{};for(n in t)t[n]!==undefined&&((i[n]?e:r||(r={}))[n]=t[n]);return r&&x.extend(!0,e,r),e}x.fn.load=function(e,t,n){if("string"!=typeof e&&nn)return nn.apply(this,arguments);var r,i,o,s=this,a=e.indexOf(" ");return a>=0&&(r=e.slice(a),e=e.slice(0,a)),x.isFunction(t)?(n=t,t=undefined):t&&"object"==typeof t&&(i="POST"),s.length>0&&x.ajax({url:e,type:i,dataType:"html",data:t}).done(function(e){o=arguments,s.html(r?x("<div>").append(x.parseHTML(e)).find(r):e)}).complete(n&&function(e,t){s.each(n,o||[e.responseText,t,e])}),this},x.each(["ajaxStart","ajaxStop","ajaxComplete","ajaxError","ajaxSuccess","ajaxSend"],function(e,t){x.fn[t]=function(e){return this.on(t,e)}}),x.extend({active:0,lastModified:{},etag:{},ajaxSettings:{url:Ut,type:"GET",isLocal:Kt.test(Xt[1]),global:!0,processData:!0,async:!0,contentType:"application/x-www-form-urlencoded; charset=UTF-8",accepts:{"*":sn,text:"text/plain",html:"text/html",xml:"application/xml, text/xml",json:"application/json, text/javascript"},contents:{xml:/xml/,html:/html/,json:/json/},responseFields:{xml:"responseXML",text:"responseText",json:"responseJSON"},converters:{"* text":String,"text html":!0,"text json":x.parseJSON,"text xml":x.parseXML},flatOptions:{url:!0,context:!0}},ajaxSetup:function(e,t){return t?cn(cn(e,x.ajaxSettings),t):cn(x.ajaxSettings,e)},ajaxPrefilter:un(rn),ajaxTransport:un(on),ajax:function(e,t){"object"==typeof e&&(t=e,e=undefined),t=t||{};var n,r,i,o,s,a,u,l,c=x.ajaxSetup({},t),p=c.context||c,f=c.context&&(p.nodeType||p.jquery)?x(p):x.event,h=x.Deferred(),d=x.Callbacks("once memory"),g=c.statusCode||{},m={},y={},v=0,b="canceled",T={readyState:0,getResponseHeader:function(e){var t;if(2===v){if(!o){o={};while(t=Qt.exec(i))o[t[1].toLowerCase()]=t[2]}t=o[e.toLowerCase()]}return null==t?null:t},getAllResponseHeaders:function(){return 2===v?i:null},setRequestHeader:function(e,t){var n=e.toLowerCase();return v||(e=y[n]=y[n]||e,m[e]=t),this},overrideMimeType:function(e){return v||(c.mimeType=e),this},statusCode:function(e){var t;if(e)if(2>v)for(t in e)g[t]=[g[t],e[t]];else T.always(e[T.status]);return this},abort:function(e){var t=e||b;return n&&n.abort(t),k(0,t),this}};if(h.promise(T).complete=d.add,T.success=T.done,T.error=T.fail,c.url=((e||c.url||Ut)+"").replace(Gt,"").replace(en,Xt[1]+"//"),c.type=t.method||t.type||c.method||c.type,c.dataTypes=x.trim(c.dataType||"*").toLowerCase().match(w)||[""],null==c.crossDomain&&(a=tn.exec(c.url.toLowerCase()),c.crossDomain=!(!a||a[1]===Xt[1]&&a[2]===Xt[2]&&(a[3]||("http:"===a[1]?"80":"443"))===(Xt[3]||("http:"===Xt[1]?"80":"443")))),c.data&&c.processData&&"string"!=typeof c.data&&(c.data=x.param(c.data,c.traditional)),ln(rn,c,t,T),2===v)return T;u=c.global,u&&0===x.active++&&x.event.trigger("ajaxStart"),c.type=c.type.toUpperCase(),c.hasContent=!Zt.test(c.type),r=c.url,c.hasContent||(c.data&&(r=c.url+=(Vt.test(r)?"&":"?")+c.data,delete c.data),c.cache===!1&&(c.url=Jt.test(r)?r.replace(Jt,"$1_="+Yt++):r+(Vt.test(r)?"&":"?")+"_="+Yt++)),c.ifModified&&(x.lastModified[r]&&T.setRequestHeader("If-Modified-Since",x.lastModified[r]),x.etag[r]&&T.setRequestHeader("If-None-Match",x.etag[r])),(c.data&&c.hasContent&&c.contentType!==!1||t.contentType)&&T.setRequestHeader("Content-Type",c.contentType),T.setRequestHeader("Accept",c.dataTypes[0]&&c.accepts[c.dataTypes[0]]?c.accepts[c.dataTypes[0]]+("*"!==c.dataTypes[0]?", "+sn+"; q=0.01":""):c.accepts["*"]);for(l in c.headers)T.setRequestHeader(l,c.headers[l]);if(c.beforeSend&&(c.beforeSend.call(p,T,c)===!1||2===v))return T.abort();b="abort";for(l in{success:1,error:1,complete:1})T[l](c[l]);if(n=ln(on,c,t,T)){T.readyState=1,u&&f.trigger("ajaxSend",[T,c]),c.async&&c.timeout>0&&(s=setTimeout(function(){T.abort("timeout")},c.timeout));try{v=1,n.send(m,k)}catch(C){if(!(2>v))throw C;k(-1,C)}}else k(-1,"No Transport");function k(e,t,o,a){var l,m,y,b,w,C=t;2!==v&&(v=2,s&&clearTimeout(s),n=undefined,i=a||"",T.readyState=e>0?4:0,l=e>=200&&300>e||304===e,o&&(b=pn(c,T,o)),b=fn(c,b,T,l),l?(c.ifModified&&(w=T.getResponseHeader("Last-Modified"),w&&(x.lastModified[r]=w),w=T.getResponseHeader("etag"),w&&(x.etag[r]=w)),204===e||"HEAD"===c.type?C="nocontent":304===e?C="notmodified":(C=b.state,m=b.data,y=b.error,l=!y)):(y=C,(e||!C)&&(C="error",0>e&&(e=0))),T.status=e,T.statusText=(t||C)+"",l?h.resolveWith(p,[m,C,T]):h.rejectWith(p,[T,C,y]),T.statusCode(g),g=undefined,u&&f.trigger(l?"ajaxSuccess":"ajaxError",[T,c,l?m:y]),d.fireWith(p,[T,C]),u&&(f.trigger("ajaxComplete",[T,c]),--x.active||x.event.trigger("ajaxStop")))}return T},getJSON:function(e,t,n){return x.get(e,t,n,"json")},getScript:function(e,t){return x.get(e,undefined,t,"script")}}),x.each(["get","post"],function(e,t){x[t]=function(e,n,r,i){return x.isFunction(n)&&(i=i||r,r=n,n=undefined),x.ajax({url:e,type:t,dataType:i,data:n,success:r})}});function pn(e,t,n){var r,i,o,s,a=e.contents,u=e.dataTypes;while("*"===u[0])u.shift(),r===undefined&&(r=e.mimeType||t.getResponseHeader("Content-Type"));if(r)for(i in a)if(a[i]&&a[i].test(r)){u.unshift(i);break}if(u[0]in n)o=u[0];else{for(i in n){if(!u[0]||e.converters[i+" "+u[0]]){o=i;break}s||(s=i)}o=o||s}return o?(o!==u[0]&&u.unshift(o),n[o]):undefined}function fn(e,t,n,r){var i,o,s,a,u,l={},c=e.dataTypes.slice();if(c[1])for(s in e.converters)l[s.toLowerCase()]=e.converters[s];o=c.shift();while(o)if(e.responseFields[o]&&(n[e.responseFields[o]]=t),!u&&r&&e.dataFilter&&(t=e.dataFilter(t,e.dataType)),u=o,o=c.shift())if("*"===o)o=u;else if("*"!==u&&u!==o){if(s=l[u+" "+o]||l["* "+o],!s)for(i in l)if(a=i.split(" "),a[1]===o&&(s=l[u+" "+a[0]]||l["* "+a[0]])){s===!0?s=l[i]:l[i]!==!0&&(o=a[0],c.unshift(a[1]));break}if(s!==!0)if(s&&e["throws"])t=s(t);else try{t=s(t)}catch(p){return{state:"parsererror",error:s?p:"No conversion from "+u+" to "+o}}}return{state:"success",data:t}}x.ajaxSetup({accepts:{script:"text/javascript, application/javascript, application/ecmascript, application/x-ecmascript"},contents:{script:/(?:java|ecma)script/},converters:{"text script":function(e){return x.globalEval(e),e}}}),x.ajaxPrefilter("script",function(e){e.cache===undefined&&(e.cache=!1),e.crossDomain&&(e.type="GET")}),x.ajaxTransport("script",function(e){if(e.crossDomain){var t,n;return{send:function(r,i){t=x("<script>").prop({async:!0,charset:e.scriptCharset,src:e.url}).on("load error",n=function(e){t.remove(),n=null,e&&i("error"===e.type?404:200,e.type)}),o.head.appendChild(t[0])},abort:function(){n&&n()}}}});var hn=[],dn=/(=)\?(?=&|$)|\?\?/;x.ajaxSetup({jsonp:"callback",jsonpCallback:function(){var e=hn.pop()||x.expando+"_"+Yt++;return this[e]=!0,e}}),x.ajaxPrefilter("json jsonp",function(t,n,r){var i,o,s,a=t.jsonp!==!1&&(dn.test(t.url)?"url":"string"==typeof t.data&&!(t.contentType||"").indexOf("application/x-www-form-urlencoded")&&dn.test(t.data)&&"data");return a||"jsonp"===t.dataTypes[0]?(i=t.jsonpCallback=x.isFunction(t.jsonpCallback)?t.jsonpCallback():t.jsonpCallback,a?t[a]=t[a].replace(dn,"$1"+i):t.jsonp!==!1&&(t.url+=(Vt.test(t.url)?"&":"?")+t.jsonp+"="+i),t.converters["script json"]=function(){return s||x.error(i+" was not called"),s[0]},t.dataTypes[0]="json",o=e[i],e[i]=function(){s=arguments},r.always(function(){e[i]=o,t[i]&&(t.jsonpCallback=n.jsonpCallback,hn.push(i)),s&&x.isFunction(o)&&o(s[0]),s=o=undefined}),"script"):undefined}),x.ajaxSettings.xhr=function(){try{return new XMLHttpRequest}catch(e){}};var gn=x.ajaxSettings.xhr(),mn={0:200,1223:204},yn=0,vn={};e.ActiveXObject&&x(e).on("unload",function(){for(var e in vn)vn[e]();vn=undefined}),x.support.cors=!!gn&&"withCredentials"in gn,x.support.ajax=gn=!!gn,x.ajaxTransport(function(e){var t;return x.support.cors||gn&&!e.crossDomain?{send:function(n,r){var i,o,s=e.xhr();if(s.open(e.type,e.url,e.async,e.username,e.password),e.xhrFields)for(i in e.xhrFields)s[i]=e.xhrFields[i];e.mimeType&&s.overrideMimeType&&s.overrideMimeType(e.mimeType),e.crossDomain||n["X-Requested-With"]||(n["X-Requested-With"]="XMLHttpRequest");for(i in n)s.setRequestHeader(i,n[i]);t=function(e){return function(){t&&(delete vn[o],t=s.onload=s.onerror=null,"abort"===e?s.abort():"error"===e?r(s.status||404,s.statusText):r(mn[s.status]||s.status,s.statusText,"string"==typeof s.responseText?{text:s.responseText}:undefined,s.getAllResponseHeaders()))}},s.onload=t(),s.onerror=t("error"),t=vn[o=yn++]=t("abort"),s.send(e.hasContent&&e.data||null)},abort:function(){t&&t()}}:undefined});var xn,bn,wn=/^(?:toggle|show|hide)$/,Tn=RegExp("^(?:([+-])=|)("+b+")([a-z%]*)$","i"),Cn=/queueHooks$/,kn=[An],Nn={"*":[function(e,t){var n=this.createTween(e,t),r=n.cur(),i=Tn.exec(t),o=i&&i[3]||(x.cssNumber[e]?"":"px"),s=(x.cssNumber[e]||"px"!==o&&+r)&&Tn.exec(x.css(n.elem,e)),a=1,u=20;if(s&&s[3]!==o){o=o||s[3],i=i||[],s=+r||1;do a=a||".5",s/=a,x.style(n.elem,e,s+o);while(a!==(a=n.cur()/r)&&1!==a&&--u)}return i&&(s=n.start=+s||+r||0,n.unit=o,n.end=i[1]?s+(i[1]+1)*i[2]:+i[2]),n}]};function En(){return setTimeout(function(){xn=undefined}),xn=x.now()}function Sn(e,t,n){var r,i=(Nn[t]||[]).concat(Nn["*"]),o=0,s=i.length;for(;s>o;o++)if(r=i[o].call(n,t,e))return r}function jn(e,t,n){var r,i,o=0,s=kn.length,a=x.Deferred().always(function(){delete u.elem}),u=function(){if(i)return!1;var t=xn||En(),n=Math.max(0,l.startTime+l.duration-t),r=n/l.duration||0,o=1-r,s=0,u=l.tweens.length;for(;u>s;s++)l.tweens[s].run(o);return a.notifyWith(e,[l,o,n]),1>o&&u?n:(a.resolveWith(e,[l]),!1)},l=a.promise({elem:e,props:x.extend({},t),opts:x.extend(!0,{specialEasing:{}},n),originalProperties:t,originalOptions:n,startTime:xn||En(),duration:n.duration,tweens:[],createTween:function(t,n){var r=x.Tween(e,l.opts,t,n,l.opts.specialEasing[t]||l.opts.easing);return l.tweens.push(r),r},stop:function(t){var n=0,r=t?l.tweens.length:0;if(i)return this;for(i=!0;r>n;n++)l.tweens[n].run(1);return t?a.resolveWith(e,[l,t]):a.rejectWith(e,[l,t]),this}}),c=l.props;for(Dn(c,l.opts.specialEasing);s>o;o++)if(r=kn[o].call(l,e,c,l.opts))return r;return x.map(c,Sn,l),x.isFunction(l.opts.start)&&l.opts.start.call(e,l),x.fx.timer(x.extend(u,{elem:e,anim:l,queue:l.opts.queue})),l.progress(l.opts.progress).done(l.opts.done,l.opts.complete).fail(l.opts.fail).always(l.opts.always)}function Dn(e,t){var n,r,i,o,s;for(n in e)if(r=x.camelCase(n),i=t[r],o=e[n],x.isArray(o)&&(i=o[1],o=e[n]=o[0]),n!==r&&(e[r]=o,delete e[n]),s=x.cssHooks[r],s&&"expand"in s){o=s.expand(o),delete e[r];for(n in o)n in e||(e[n]=o[n],t[n]=i)}else t[r]=i}x.Animation=x.extend(jn,{tweener:function(e,t){x.isFunction(e)?(t=e,e=["*"]):e=e.split(" ");var n,r=0,i=e.length;for(;i>r;r++)n=e[r],Nn[n]=Nn[n]||[],Nn[n].unshift(t)},prefilter:function(e,t){t?kn.unshift(e):kn.push(e)}});function An(e,t,n){var r,i,o,s,a,u,l=this,c={},p=e.style,f=e.nodeType&&Lt(e),h=q.get(e,"fxshow");n.queue||(a=x._queueHooks(e,"fx"),null==a.unqueued&&(a.unqueued=0,u=a.empty.fire,a.empty.fire=function(){a.unqueued||u()}),a.unqueued++,l.always(function(){l.always(function(){a.unqueued--,x.queue(e,"fx").length||a.empty.fire()})})),1===e.nodeType&&("height"in t||"width"in t)&&(n.overflow=[p.overflow,p.overflowX,p.overflowY],"inline"===x.css(e,"display")&&"none"===x.css(e,"float")&&(p.display="inline-block")),n.overflow&&(p.overflow="hidden",l.always(function(){p.overflow=n.overflow[0],p.overflowX=n.overflow[1],p.overflowY=n.overflow[2]}));for(r in t)if(i=t[r],wn.exec(i)){if(delete t[r],o=o||"toggle"===i,i===(f?"hide":"show")){if("show"!==i||!h||h[r]===undefined)continue;f=!0}c[r]=h&&h[r]||x.style(e,r)}if(!x.isEmptyObject(c)){h?"hidden"in h&&(f=h.hidden):h=q.access(e,"fxshow",{}),o&&(h.hidden=!f),f?x(e).show():l.done(function(){x(e).hide()}),l.done(function(){var t;q.remove(e,"fxshow");for(t in c)x.style(e,t,c[t])});for(r in c)s=Sn(f?h[r]:0,r,l),r in h||(h[r]=s.start,f&&(s.end=s.start,s.start="width"===r||"height"===r?1:0))}}function Ln(e,t,n,r,i){return new Ln.prototype.init(e,t,n,r,i)}x.Tween=Ln,Ln.prototype={constructor:Ln,init:function(e,t,n,r,i,o){this.elem=e,this.prop=n,this.easing=i||"swing",this.options=t,this.start=this.now=this.cur(),this.end=r,this.unit=o||(x.cssNumber[n]?"":"px")},cur:function(){var e=Ln.propHooks[this.prop];return e&&e.get?e.get(this):Ln.propHooks._default.get(this)},run:function(e){var t,n=Ln.propHooks[this.prop];return this.pos=t=this.options.duration?x.easing[this.easing](e,this.options.duration*e,0,1,this.options.duration):e,this.now=(this.end-this.start)*t+this.start,this.options.step&&this.options.step.call(this.elem,this.now,this),n&&n.set?n.set(this):Ln.propHooks._default.set(this),this}},Ln.prototype.init.prototype=Ln.prototype,Ln.propHooks={_default:{get:function(e){var t;return null==e.elem[e.prop]||e.elem.style&&null!=e.elem.style[e.prop]?(t=x.css(e.elem,e.prop,""),t&&"auto"!==t?t:0):e.elem[e.prop]},set:function(e){x.fx.step[e.prop]?x.fx.step[e.prop](e):e.elem.style&&(null!=e.elem.style[x.cssProps[e.prop]]||x.cssHooks[e.prop])?x.style(e.elem,e.prop,e.now+e.unit):e.elem[e.prop]=e.now}}},Ln.propHooks.scrollTop=Ln.propHooks.scrollLeft={set:function(e){e.elem.nodeType&&e.elem.parentNode&&(e.elem[e.prop]=e.now)}},x.each(["toggle","show","hide"],function(e,t){var n=x.fn[t];x.fn[t]=function(e,r,i){return null==e||"boolean"==typeof e?n.apply(this,arguments):this.animate(qn(t,!0),e,r,i)}}),x.fn.extend({fadeTo:function(e,t,n,r){return this.filter(Lt).css("opacity",0).show().end().animate({opacity:t},e,n,r)},animate:function(e,t,n,r){var i=x.isEmptyObject(e),o=x.speed(t,n,r),s=function(){var t=jn(this,x.extend({},e),o);(i||q.get(this,"finish"))&&t.stop(!0)};return s.finish=s,i||o.queue===!1?this.each(s):this.queue(o.queue,s)},stop:function(e,t,n){var r=function(e){var t=e.stop;delete e.stop,t(n)};return"string"!=typeof e&&(n=t,t=e,e=undefined),t&&e!==!1&&this.queue(e||"fx",[]),this.each(function(){var t=!0,i=null!=e&&e+"queueHooks",o=x.timers,s=q.get(this);if(i)s[i]&&s[i].stop&&r(s[i]);else for(i in s)s[i]&&s[i].stop&&Cn.test(i)&&r(s[i]);for(i=o.length;i--;)o[i].elem!==this||null!=e&&o[i].queue!==e||(o[i].anim.stop(n),t=!1,o.splice(i,1));(t||!n)&&x.dequeue(this,e)})},finish:function(e){return e!==!1&&(e=e||"fx"),this.each(function(){var t,n=q.get(this),r=n[e+"queue"],i=n[e+"queueHooks"],o=x.timers,s=r?r.length:0;for(n.finish=!0,x.queue(this,e,[]),i&&i.stop&&i.stop.call(this,!0),t=o.length;t--;)o[t].elem===this&&o[t].queue===e&&(o[t].anim.stop(!0),o.splice(t,1));for(t=0;s>t;t++)r[t]&&r[t].finish&&r[t].finish.call(this);delete n.finish})}});function qn(e,t){var n,r={height:e},i=0;for(t=t?1:0;4>i;i+=2-t)n=jt[i],r["margin"+n]=r["padding"+n]=e;return t&&(r.opacity=r.width=e),r}x.each({slideDown:qn("show"),slideUp:qn("hide"),slideToggle:qn("toggle"),fadeIn:{opacity:"show"},fadeOut:{opacity:"hide"},fadeToggle:{opacity:"toggle"}},function(e,t){x.fn[e]=function(e,n,r){return this.animate(t,e,n,r)}}),x.speed=function(e,t,n){var r=e&&"object"==typeof e?x.extend({},e):{complete:n||!n&&t||x.isFunction(e)&&e,duration:e,easing:n&&t||t&&!x.isFunction(t)&&t};return r.duration=x.fx.off?0:"number"==typeof r.duration?r.duration:r.duration in x.fx.speeds?x.fx.speeds[r.duration]:x.fx.speeds._default,(null==r.queue||r.queue===!0)&&(r.queue="fx"),r.old=r.complete,r.complete=function(){x.isFunction(r.old)&&r.old.call(this),r.queue&&x.dequeue(this,r.queue)},r},x.easing={linear:function(e){return e},swing:function(e){return.5-Math.cos(e*Math.PI)/2}},x.timers=[],x.fx=Ln.prototype.init,x.fx.tick=function(){var e,t=x.timers,n=0;for(xn=x.now();t.length>n;n++)e=t[n],e()||t[n]!==e||t.splice(n--,1);t.length||x.fx.stop(),xn=undefined},x.fx.timer=function(e){e()&&x.timers.push(e)&&x.fx.start()},x.fx.interval=13,x.fx.start=function(){bn||(bn=setInterval(x.fx.tick,x.fx.interval))},x.fx.stop=function(){clearInterval(bn),bn=null},x.fx.speeds={slow:600,fast:200,_default:400},x.fx.step={},x.expr&&x.expr.filters&&(x.expr.filters.animated=function(e){return x.grep(x.timers,function(t){return e===t.elem}).length}),x.fn.offset=function(e){if(arguments.length)return e===undefined?this:this.each(function(t){x.offset.setOffset(this,e,t)});var t,n,i=this[0],o={top:0,left:0},s=i&&i.ownerDocument;if(s)return t=s.documentElement,x.contains(t,i)?(typeof i.getBoundingClientRect!==r&&(o=i.getBoundingClientRect()),n=Hn(s),{top:o.top+n.pageYOffset-t.clientTop,left:o.left+n.pageXOffset-t.clientLeft}):o},x.offset={setOffset:function(e,t,n){var r,i,o,s,a,u,l,c=x.css(e,"position"),p=x(e),f={};"static"===c&&(e.style.position="relative"),a=p.offset(),o=x.css(e,"top"),u=x.css(e,"left"),l=("absolute"===c||"fixed"===c)&&(o+u).indexOf("auto")>-1,l?(r=p.position(),s=r.top,i=r.left):(s=parseFloat(o)||0,i=parseFloat(u)||0),x.isFunction(t)&&(t=t.call(e,n,a)),null!=t.top&&(f.top=t.top-a.top+s),null!=t.left&&(f.left=t.left-a.left+i),"using"in t?t.using.call(e,f):p.css(f)}},x.fn.extend({position:function(){if(this[0]){var e,t,n=this[0],r={top:0,left:0};return"fixed"===x.css(n,"position")?t=n.getBoundingClientRect():(e=this.offsetParent(),t=this.offset(),x.nodeName(e[0],"html")||(r=e.offset()),r.top+=x.css(e[0],"borderTopWidth",!0),r.left+=x.css(e[0],"borderLeftWidth",!0)),{top:t.top-r.top-x.css(n,"marginTop",!0),left:t.left-r.left-x.css(n,"marginLeft",!0)}}},offsetParent:function(){return this.map(function(){var e=this.offsetParent||s;while(e&&!x.nodeName(e,"html")&&"static"===x.css(e,"position"))e=e.offsetParent;return e||s})}}),x.each({scrollLeft:"pageXOffset",scrollTop:"pageYOffset"},function(t,n){var r="pageYOffset"===n;x.fn[t]=function(i){return x.access(this,function(t,i,o){var s=Hn(t);return o===undefined?s?s[n]:t[i]:(s?s.scrollTo(r?e.pageXOffset:o,r?o:e.pageYOffset):t[i]=o,undefined)},t,i,arguments.length,null)}});function Hn(e){return x.isWindow(e)?e:9===e.nodeType&&e.defaultView}x.each({Height:"height",Width:"width"},function(e,t){x.each({padding:"inner"+e,content:t,"":"outer"+e},function(n,r){x.fn[r]=function(r,i){var o=arguments.length&&(n||"boolean"!=typeof r),s=n||(r===!0||i===!0?"margin":"border");return x.access(this,function(t,n,r){var i;return x.isWindow(t)?t.document.documentElement["client"+e]:9===t.nodeType?(i=t.documentElement,Math.max(t.body["scroll"+e],i["scroll"+e],t.body["offset"+e],i["offset"+e],i["client"+e])):r===undefined?x.css(t,n,s):x.style(t,n,r,s)},t,o?r:undefined,o,null)}})}),x.fn.size=function(){return this.length},x.fn.andSelf=x.fn.addBack,"object"==typeof module&&module&&"object"==typeof module.exports?module.exports=x:"function"==typeof define&&define.amd&&define("jquery",[],function(){return x}),"object"==typeof e&&"object"==typeof e.document&&(e.jQuery=e.$=x)})(window);;$(document).ready(function () {

    function get_form_data($this) {
        var data = {};
        $this.find(':input').each(function () {
            var name = $(this).attr('name');
            if ($(this).attr('type') == 'checkbox') {
                if ($(this).is(':checked')) {
                    if (name.match(/\[\]/)) {
                        data[name] = data[name] || [];
                        data[name].push($(this).val());
                    } else {
                        data[name] = $(this).val();
                    }
                }
            } else
                data[name] = $(this).val();
        });
        return data;
    }

    var $body = $('body');
    $body.on('click', null, function (event) {
        var $target = $(event.target);
        if (!$target.attr('disabled') && !$target.hasClass('disabled')) {
            if ($target.attr('data-ajax-click') || $target.parent('a').attr('data-ajax-click')) {
                if (!$target.attr('data-ajax-click')) {
                    $target = $target.parent('a');
                }
                event.preventDefault();
                var arr = $target.data('ajax-click').split(':');
                var module = arr[0];
                var act = arr[1];
                var options = {};
                if ($target.data('ajax-shroud')) {
                    options.loading_target = $target.data('ajax-shroud');
                }
                var data = $target.data('ajax-post') || {};
                data['origin'] = $target.attr('id');
                $.fn.ajax_factory(module, act, data, options);
            } else if ($.fn.ajax_factory.defaults.load_pages_ajax && ($target.is('a') || ($target = $target.parent('a')).length)) {
                var href = $target.attr('href');
                var rel = $target.attr('rel');
                if (typeof href != "undefined" && href != '#' && (typeof rel == 'undefined' || rel != 'external')) {
                    if (!href.match('http')) {
                        event.preventDefault();
                        var $page = $("div[data-url='" + href + "']");
                        if ($page.length) {
                            window.history.pushState($.fn.ajax_factory.get_state(href), '', href);
                            if (typeof page_handeler != 'undefined') {
                                page_handeler.toggle_page($page);
                                var state = $.fn.ajax_factory.get_state(href);
                                if (typeof state != "undefined" && typeof state.actions != undefined) {
                                    page_handeler.perform_page_actions($.fn.ajax_factory.get_state(href).actions, href);
                                }
                            }
                        } else {
                            var post = {module: 'core', act: 'load_page'};
                            var options = {call_as_uri: href, loading_target: '#main'};
                            $.fn.ajax_factory('core', 'load_page', post, options);
                        }
                    }
                }
            }
        }
    });
    $body.on('change', ':input', function (event) {
        var $target = $(event.target);
        if (!$target.attr('disabled') && !$target.hasClass('disabled')) {
            if ($target.attr('data-ajax-change')) {
                var options = {};
                event.preventDefault();
                var arr = $target.attr('data-ajax-change').split(':');
                var module = arr[0];
                var act = arr[1];
                var data = $target.data('ajax-post') || {};
                if ($target.attr('type') === 'checkbox') {
                    data.value = ($target.is(':checked') ? 1 : 0 );
                } else {
                    data.value = $target.val();
                }
                data['origin'] = $target.attr('id');
                if ($target.data('ajax-shroud')) {
                    options.loading_target = $target.data('ajax-shroud');
                }
                $.fn.ajax_factory(module, act, data, options);
            } else {
                var $parent = $target.parents('form').eq(0);
                if ($parent.data('ajax-change')) {
                    var arr = $parent.attr('data-ajax-change').split(':');
                    var module = arr[0];
                    var act = arr[1];
                    var ajax_shroud = $parent.attr('data-ajax-shroud');
                    var data = get_form_data($parent);
                    var options = {loading_target: ajax_shroud};
                    data.ajax_origin = $parent.id;
                    $.fn.ajax_factory(module, act, data, options);
                    return false;
                }
            }
        }
    });

    $body.on('click', 'form .submit', function (e) {
        $(this).parents('form').eq(0).submit();
        e.preventDefault();
        return false;
    });

    $body.on('submit', 'form.ajax', function (e) {
        e.preventDefault();
        var arr = $(this).attr('action').split(':');
        var module = arr[0];
        var act = arr[1];
        var ajax_shroud = $(this).attr('data-ajax-shroud');
        var data = get_form_data($(this));
        var options = $.fn.extend($(this).data(), {loading_target: ajax_shroud});
        data.ajax_origin = $(e.target)[0].id;
        $.fn.ajax_factory(module, act, data, options);
        return false;
    });
    $body.on('submit', 'form.noajax', function () {
        var ajax_shroud = $(this).attr('data-ajax-shroud');
        var div = add_loading_shroud(ajax_shroud);
        if ($(this).data('ajax-socket')) {
            var socketId = add_socket_io($(this).data('ajax-socket'), div);
            if (!$(this).find('input[name=data-socket]').length) {
                var input = document.createElement('input');
                input.className = 'hidden';
                input.name = 'data-socket';
                input.type = 'hidden';
                input.value = socketId;
                $(this).prepend(input);
            }
        }
    });
    $.fn.ajax_factory = function (module, act, post, options) {
        options = options || {};
        post = post || {};
        var div = add_loading_shroud(options.loading_target);
        if (options.ajaxSocket) {
            post['data-socket'] = add_socket_io(options.ajaxSocket, div);
        }
        post['module'] = module;
        post['act'] = act;
        $(".error_message").remove();
        $.ajax({
            url: options.call_as_uri || window.location,
            global: false,
            async: true,
            type: 'POST',
            dataType: 'json',
            cache: false,
            data: post,
            success: handle_json_response
        });
    };
    $.fn.ajax_factory.defaults = {
        complete: ['initMlink'],
        load_pages_ajax: false
    };
    $.fn.ajax_factory.states = [];
    $.fn.ajax_factory.get_state = function (state) {
        return this.states[state];
    };

    initMlink();
});

function initMlink() {
    $('select[multiple=multiple]').each(function () {
        if (!$(this).siblings('select').length) {
            var id = $(this).attr('name');
            $(this).hide();
            $(this).after('<select id="' + id + '_select" class="' + $(this).attr('class') + '" onchange="addMlink(\'' + id + '\',this.value)"><option value=\'-1\'>Select Another</option></select><ul id="' + id + '_selected" class="mlink_selected_wrapper"></ul>');
            $(this).find('option:not(optgroup > option) ,optgroup').each(function () {
                $(this).clone().appendTo($('#' + id + '_select'));
                if ($(this).is(':selected')) {
                    addMlink(id, $(this).val());
                }
            })
        }
    });
}

function addMlink(id, value) {
    if (value != -1 && value != 0) {
        var $option = $('#' + id + '_select option[value=' + value + ']');
        var title = $option.html();
        $option.attr('disabled', 'disabled');
        $('select[name="' + id + '"] option[value=' + value + ']').attr('selected', 'selected');
        $('#' + id + '_select').val(-1);
        $('#' + id + '_selected').append('<li data-value="' + value + '">' + title + '<a onclick="removeMlink(\'' + id + '\',\'' + value + '\')">Remove</a></li>');
    }
}

function removeMlink(id, value) {
    if (value != -1 && value != 0) {
        var $option = $('#' + id + '_select option[value=' + value + ']');
        $option.removeAttr('disabled');
        $('select[name="' + id + '"] option[value=' + value + ']').removeAttr('selected');
        $('select[name="' + id + '"]').trigger('change');
        $('#' + id + '_selected [data-value=' + value + ']').remove();

    }
}

function handle_json_response(json) {
    $('.loading_shroud').remove();
    json.pre_inject.each(function (inj) {
        if (inj.over != '') {
            $(inj.over).remove();
        }
        switch (inj.pos) {
            case 'append':
                $(inj.id).append(inj.html);
                break;
            case 'prepend':
                $(inj.id).prepend(inj.html);
                break;
            case 'before':
                $(inj.id).before(inj.html);
                break;
            case 'after':
                $(inj.id).after(inj.html);
                break;
        }
    });
    json.update.each(function (upd) {
        $(upd.id).html(upd.html);
    });
    json.inject.each(function (inj) {
        if (inj.over != '') {
            $(inj.over).remove();
        }
        switch (inj.pos) {
            case 'append':
                $(inj.id).append(inj.html);
                break;
            case 'prepend':
                $(inj.id).prepend(inj.html);
                break;
            case 'before':
                $(inj.id).before(inj.html);
                break;
            case 'after':
                $(inj.id).after(inj.html);
                break;
        }
    });
    if (typeof json.push_state != "undefined") {
        $.fn.ajax_factory.states[json.push_state.url] = json.push_state.data;
        if (json.push_state.push) {
            window.history.pushState(json.push_state.data, json.push_state.title, json.push_state.url);
        } else if (json.push_state.replace) {
            window.history.replaceState(json.push_state.data, json.push_state.title, json.push_state.url);
        }
    }
    if ($.fn.ajax_factory.defaults.complete) {
        $.fn.ajax_factory.defaults.complete.each(function (method, i, json) {
            if (typeof method == 'function') {
                method(json);
            } else {
                window[method](json);
            }
        }, json);
    }
}

Array.prototype.each = function (callback, context) {
    for (var i in this) {
        if (this.hasOwnProperty(i)) {
            callback(this[i], i, context);
        }
    }
};
Array.prototype.count = function () {
    return this.length - 2;
};
String.prototype.isNumber = function () {
    return !isNaN(parseFloat(this)) && isFinite(this);
};

function add_loading_shroud(ajax_shroud) {
    if (typeof ajax_shroud != 'undefined') {
        var div = document.createElement('div');
        div.className = 'loading_shroud';
        div.style.width = $(ajax_shroud).outerWidth() + 'px';
        div.style.height = $(ajax_shroud).outerHeight() + 'px';
        div.style.left = 0;
        div.style.top = 0;
        if ($(ajax_shroud).css('position') != 'absolute' || $(ajax_shroud).css('position') != 'relative') {
            $(ajax_shroud).css({'position': 'relative'});
        }
        $(ajax_shroud).prepend(div);
        return div;
    }
    return false;
}
var socketId = false;
function add_socket_io(ajaxSocket, write_element) {
    if (!socketId) {
        socketId = randomString(32, 'aA#');
        if (write_element) {
            $.getScript(window.location.origin + ':8000/socket.io/socket.io.js', function () {
                var socket = io.connect(window.location.origin + ':8000');
                socket.emit('set nickname', socketId);
                socket.on('message', function (data) {
                    $(write_element).append('<code>' + data + '</code>');
                });
            });
        }
    }
    return socketId;
};$(document).ready(function () {
    recreate_checkboxes();
    $.fn.ajax_factory.defaults.complete.push('recreate_checkboxes')
});

recreate_checkboxes = function () {
    var $checkboxes = $(".checkbox_replace");
    $checkboxes.each(function () {
        var $this = $(this);
        var $input = $this.children("input");
        if ($input.prop('checked')) {
            $this.addClass("checked");
        }
    });
    $checkboxes.click(function () {
        var $this = $(this);
        var $input = $this.children("input");
        if (!$input.prop('checked')) {
            $this.addClass("checked");
            $input.prop('checked', true);
        } else {
            $this.removeClass("checked");
            $input.prop('checked', false);
        }
    });
};

window.onpopstate = function (event) {
    if (typeof page_handeler != 'undefined' && event && event.state) {
        page_handeler.page(event.state.url, event.state, 1);
    }
};

function randomString(length, chars) {
    var mask = '';
    if (chars.indexOf('a') > -1) mask += 'abcdefghijklmnopqrstuvwxyz';
    if (chars.indexOf('A') > -1) mask += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    if (chars.indexOf('#') > -1) mask += '0123456789';
    if (chars.indexOf('!') > -1) mask += '~`!@#$%^&*()_+-={}[]:";\'<>?,./|\\';
    var result = '';
    for (var i = length; i > 0; --i) result += mask[Math.round(Math.random() * (mask.length - 1))];
    return result;
};/*!
 * jScrollPane - v2.0.19 - 2013-11-16
 * http://jscrollpane.kelvinluck.com/
 *
 * Copyright (c) 2013 Kelvin Luck
 * Dual licensed under the MIT or GPL licenses.
 */
!function(a,b,c){a.fn.jScrollPane=function(d){function e(d,e){function f(b){var e,h,j,l,m,n,q=!1,r=!1;if(P=b,Q===c)m=d.scrollTop(),n=d.scrollLeft(),d.css({overflow:"hidden",padding:0}),R=d.innerWidth()+tb,S=d.innerHeight(),d.width(R),Q=a('<div class="jspPane" />').css("padding",sb).append(d.children()),T=a('<div class="jspContainer" />').css({width:R+"px",height:S+"px"}).append(Q).appendTo(d);else{if(d.css("width",""),q=P.stickToBottom&&C(),r=P.stickToRight&&D(),l=d.innerWidth()+tb!=R||d.outerHeight()!=S,l&&(R=d.innerWidth()+tb,S=d.innerHeight(),T.css({width:R+"px",height:S+"px"})),!l&&ub==U&&Q.outerHeight()==V)return d.width(R),void 0;ub=U,Q.css("width",""),d.width(R),T.find(">.jspVerticalBar,>.jspHorizontalBar").remove().end()}Q.css("overflow","auto"),U=b.contentWidth?b.contentWidth:Q[0].scrollWidth,V=Q[0].scrollHeight,Q.css("overflow",""),W=U/R,X=V/S,Y=X>1,Z=W>1,Z||Y?(d.addClass("jspScrollable"),e=P.maintainPosition&&(ab||db),e&&(h=A(),j=B()),g(),i(),k(),e&&(y(r?U-R:h,!1),x(q?V-S:j,!1)),H(),E(),N(),P.enableKeyboardNavigation&&J(),P.clickOnTrack&&o(),L(),P.hijackInternalLinks&&M()):(d.removeClass("jspScrollable"),Q.css({top:0,left:0,width:T.width()-tb}),F(),I(),K(),p()),P.autoReinitialise&&!rb?rb=setInterval(function(){f(P)},P.autoReinitialiseDelay):!P.autoReinitialise&&rb&&clearInterval(rb),m&&d.scrollTop(0)&&x(m,!1),n&&d.scrollLeft(0)&&y(n,!1),d.trigger("jsp-initialised",[Z||Y])}function g(){Y&&(T.append(a('<div class="jspVerticalBar" />').append(a('<div class="jspCap jspCapTop" />'),a('<div class="jspTrack" />').append(a('<div class="jspDrag" />').append(a('<div class="jspDragTop" />'),a('<div class="jspDragBottom" />'))),a('<div class="jspCap jspCapBottom" />'))),eb=T.find(">.jspVerticalBar"),fb=eb.find(">.jspTrack"),$=fb.find(">.jspDrag"),P.showArrows&&(jb=a('<a class="jspArrow jspArrowUp" />').bind("mousedown.jsp",m(0,-1)).bind("click.jsp",G),kb=a('<a class="jspArrow jspArrowDown" />').bind("mousedown.jsp",m(0,1)).bind("click.jsp",G),P.arrowScrollOnHover&&(jb.bind("mouseover.jsp",m(0,-1,jb)),kb.bind("mouseover.jsp",m(0,1,kb))),l(fb,P.verticalArrowPositions,jb,kb)),hb=S,T.find(">.jspVerticalBar>.jspCap:visible,>.jspVerticalBar>.jspArrow").each(function(){hb-=a(this).outerHeight()}),$.hover(function(){$.addClass("jspHover")},function(){$.removeClass("jspHover")}).bind("mousedown.jsp",function(b){a("html").bind("dragstart.jsp selectstart.jsp",G),$.addClass("jspActive");var c=b.pageY-$.position().top;return a("html").bind("mousemove.jsp",function(a){r(a.pageY-c,!1)}).bind("mouseup.jsp mouseleave.jsp",q),!1}),h())}function h(){fb.height(hb+"px"),ab=0,gb=P.verticalGutter+fb.outerWidth(),Q.width(R-gb-tb);try{0===eb.position().left&&Q.css("margin-left",gb+"px")}catch(a){}}function i(){Z&&(T.append(a('<div class="jspHorizontalBar" />').append(a('<div class="jspCap jspCapLeft" />'),a('<div class="jspTrack" />').append(a('<div class="jspDrag" />').append(a('<div class="jspDragLeft" />'),a('<div class="jspDragRight" />'))),a('<div class="jspCap jspCapRight" />'))),lb=T.find(">.jspHorizontalBar"),mb=lb.find(">.jspTrack"),bb=mb.find(">.jspDrag"),P.showArrows&&(pb=a('<a class="jspArrow jspArrowLeft" />').bind("mousedown.jsp",m(-1,0)).bind("click.jsp",G),qb=a('<a class="jspArrow jspArrowRight" />').bind("mousedown.jsp",m(1,0)).bind("click.jsp",G),P.arrowScrollOnHover&&(pb.bind("mouseover.jsp",m(-1,0,pb)),qb.bind("mouseover.jsp",m(1,0,qb))),l(mb,P.horizontalArrowPositions,pb,qb)),bb.hover(function(){bb.addClass("jspHover")},function(){bb.removeClass("jspHover")}).bind("mousedown.jsp",function(b){a("html").bind("dragstart.jsp selectstart.jsp",G),bb.addClass("jspActive");var c=b.pageX-bb.position().left;return a("html").bind("mousemove.jsp",function(a){t(a.pageX-c,!1)}).bind("mouseup.jsp mouseleave.jsp",q),!1}),nb=T.innerWidth(),j())}function j(){T.find(">.jspHorizontalBar>.jspCap:visible,>.jspHorizontalBar>.jspArrow").each(function(){nb-=a(this).outerWidth()}),mb.width(nb+"px"),db=0}function k(){if(Z&&Y){var b=mb.outerHeight(),c=fb.outerWidth();hb-=b,a(lb).find(">.jspCap:visible,>.jspArrow").each(function(){nb+=a(this).outerWidth()}),nb-=c,S-=c,R-=b,mb.parent().append(a('<div class="jspCorner" />').css("width",b+"px")),h(),j()}Z&&Q.width(T.outerWidth()-tb+"px"),V=Q.outerHeight(),X=V/S,Z&&(ob=Math.ceil(1/W*nb),ob>P.horizontalDragMaxWidth?ob=P.horizontalDragMaxWidth:ob<P.horizontalDragMinWidth&&(ob=P.horizontalDragMinWidth),bb.width(ob+"px"),cb=nb-ob,u(db)),Y&&(ib=Math.ceil(1/X*hb),ib>P.verticalDragMaxHeight?ib=P.verticalDragMaxHeight:ib<P.verticalDragMinHeight&&(ib=P.verticalDragMinHeight),$.height(ib+"px"),_=hb-ib,s(ab))}function l(a,b,c,d){var e,f="before",g="after";"os"==b&&(b=/Mac/.test(navigator.platform)?"after":"split"),b==f?g=b:b==g&&(f=b,e=c,c=d,d=e),a[f](c)[g](d)}function m(a,b,c){return function(){return n(a,b,this,c),this.blur(),!1}}function n(b,c,d,e){d=a(d).addClass("jspActive");var f,g,h=!0,i=function(){0!==b&&vb.scrollByX(b*P.arrowButtonSpeed),0!==c&&vb.scrollByY(c*P.arrowButtonSpeed),g=setTimeout(i,h?P.initialDelay:P.arrowRepeatFreq),h=!1};i(),f=e?"mouseout.jsp":"mouseup.jsp",e=e||a("html"),e.bind(f,function(){d.removeClass("jspActive"),g&&clearTimeout(g),g=null,e.unbind(f)})}function o(){p(),Y&&fb.bind("mousedown.jsp",function(b){if(b.originalTarget===c||b.originalTarget==b.currentTarget){var d,e=a(this),f=e.offset(),g=b.pageY-f.top-ab,h=!0,i=function(){var a=e.offset(),c=b.pageY-a.top-ib/2,f=S*P.scrollPagePercent,k=_*f/(V-S);if(0>g)ab-k>c?vb.scrollByY(-f):r(c);else{if(!(g>0))return j(),void 0;c>ab+k?vb.scrollByY(f):r(c)}d=setTimeout(i,h?P.initialDelay:P.trackClickRepeatFreq),h=!1},j=function(){d&&clearTimeout(d),d=null,a(document).unbind("mouseup.jsp",j)};return i(),a(document).bind("mouseup.jsp",j),!1}}),Z&&mb.bind("mousedown.jsp",function(b){if(b.originalTarget===c||b.originalTarget==b.currentTarget){var d,e=a(this),f=e.offset(),g=b.pageX-f.left-db,h=!0,i=function(){var a=e.offset(),c=b.pageX-a.left-ob/2,f=R*P.scrollPagePercent,k=cb*f/(U-R);if(0>g)db-k>c?vb.scrollByX(-f):t(c);else{if(!(g>0))return j(),void 0;c>db+k?vb.scrollByX(f):t(c)}d=setTimeout(i,h?P.initialDelay:P.trackClickRepeatFreq),h=!1},j=function(){d&&clearTimeout(d),d=null,a(document).unbind("mouseup.jsp",j)};return i(),a(document).bind("mouseup.jsp",j),!1}})}function p(){mb&&mb.unbind("mousedown.jsp"),fb&&fb.unbind("mousedown.jsp")}function q(){a("html").unbind("dragstart.jsp selectstart.jsp mousemove.jsp mouseup.jsp mouseleave.jsp"),$&&$.removeClass("jspActive"),bb&&bb.removeClass("jspActive")}function r(a,b){Y&&(0>a?a=0:a>_&&(a=_),b===c&&(b=P.animateScroll),b?vb.animate($,"top",a,s):($.css("top",a),s(a)))}function s(a){a===c&&(a=$.position().top),T.scrollTop(0),ab=a;var b=0===ab,e=ab==_,f=a/_,g=-f*(V-S);(wb!=b||yb!=e)&&(wb=b,yb=e,d.trigger("jsp-arrow-change",[wb,yb,xb,zb])),v(b,e),Q.css("top",g),d.trigger("jsp-scroll-y",[-g,b,e]).trigger("scroll")}function t(a,b){Z&&(0>a?a=0:a>cb&&(a=cb),b===c&&(b=P.animateScroll),b?vb.animate(bb,"left",a,u):(bb.css("left",a),u(a)))}function u(a){a===c&&(a=bb.position().left),T.scrollTop(0),db=a;var b=0===db,e=db==cb,f=a/cb,g=-f*(U-R);(xb!=b||zb!=e)&&(xb=b,zb=e,d.trigger("jsp-arrow-change",[wb,yb,xb,zb])),w(b,e),Q.css("left",g),d.trigger("jsp-scroll-x",[-g,b,e]).trigger("scroll")}function v(a,b){P.showArrows&&(jb[a?"addClass":"removeClass"]("jspDisabled"),kb[b?"addClass":"removeClass"]("jspDisabled"))}function w(a,b){P.showArrows&&(pb[a?"addClass":"removeClass"]("jspDisabled"),qb[b?"addClass":"removeClass"]("jspDisabled"))}function x(a,b){var c=a/(V-S);r(c*_,b)}function y(a,b){var c=a/(U-R);t(c*cb,b)}function z(b,c,d){var e,f,g,h,i,j,k,l,m,n=0,o=0;try{e=a(b)}catch(p){return}for(f=e.outerHeight(),g=e.outerWidth(),T.scrollTop(0),T.scrollLeft(0);!e.is(".jspPane");)if(n+=e.position().top,o+=e.position().left,e=e.offsetParent(),/^body|html$/i.test(e[0].nodeName))return;h=B(),j=h+S,h>n||c?l=n-P.horizontalGutter:n+f>j&&(l=n-S+f+P.horizontalGutter),isNaN(l)||x(l,d),i=A(),k=i+R,i>o||c?m=o-P.horizontalGutter:o+g>k&&(m=o-R+g+P.horizontalGutter),isNaN(m)||y(m,d)}function A(){return-Q.position().left}function B(){return-Q.position().top}function C(){var a=V-S;return a>20&&a-B()<10}function D(){var a=U-R;return a>20&&a-A()<10}function E(){T.unbind(Bb).bind(Bb,function(a,b,c,d){var e=db,f=ab,g=a.deltaFactor||P.mouseWheelSpeed;return vb.scrollBy(c*g,-d*g,!1),e==db&&f==ab})}function F(){T.unbind(Bb)}function G(){return!1}function H(){Q.find(":input,a").unbind("focus.jsp").bind("focus.jsp",function(a){z(a.target,!1)})}function I(){Q.find(":input,a").unbind("focus.jsp")}function J(){function b(){var a=db,b=ab;switch(c){case 40:vb.scrollByY(P.keyboardSpeed,!1);break;case 38:vb.scrollByY(-P.keyboardSpeed,!1);break;case 34:case 32:vb.scrollByY(S*P.scrollPagePercent,!1);break;case 33:vb.scrollByY(-S*P.scrollPagePercent,!1);break;case 39:vb.scrollByX(P.keyboardSpeed,!1);break;case 37:vb.scrollByX(-P.keyboardSpeed,!1)}return e=a!=db||b!=ab}var c,e,f=[];Z&&f.push(lb[0]),Y&&f.push(eb[0]),Q.focus(function(){d.focus()}),d.attr("tabindex",0).unbind("keydown.jsp keypress.jsp").bind("keydown.jsp",function(d){if(d.target===this||f.length&&a(d.target).closest(f).length){var g=db,h=ab;switch(d.keyCode){case 40:case 38:case 34:case 32:case 33:case 39:case 37:c=d.keyCode,b();break;case 35:x(V-S),c=null;break;case 36:x(0),c=null}return e=d.keyCode==c&&g!=db||h!=ab,!e}}).bind("keypress.jsp",function(a){return a.keyCode==c&&b(),!e}),P.hideFocus?(d.css("outline","none"),"hideFocus"in T[0]&&d.attr("hideFocus",!0)):(d.css("outline",""),"hideFocus"in T[0]&&d.attr("hideFocus",!1))}function K(){d.attr("tabindex","-1").removeAttr("tabindex").unbind("keydown.jsp keypress.jsp")}function L(){if(location.hash&&location.hash.length>1){var b,c,d=escape(location.hash.substr(1));try{b=a("#"+d+', a[name="'+d+'"]')}catch(e){return}b.length&&Q.find(d)&&(0===T.scrollTop()?c=setInterval(function(){T.scrollTop()>0&&(z(b,!0),a(document).scrollTop(T.position().top),clearInterval(c))},50):(z(b,!0),a(document).scrollTop(T.position().top)))}}function M(){a(document.body).data("jspHijack")||(a(document.body).data("jspHijack",!0),a(document.body).delegate("a[href*=#]","click",function(c){var d,e,f,g,h,i,j=this.href.substr(0,this.href.indexOf("#")),k=location.href;if(-1!==location.href.indexOf("#")&&(k=location.href.substr(0,location.href.indexOf("#"))),j===k){d=escape(this.href.substr(this.href.indexOf("#")+1));try{e=a("#"+d+', a[name="'+d+'"]')}catch(l){return}e.length&&(f=e.closest(".jspScrollable"),g=f.data("jsp"),g.scrollToElement(e,!0),f[0].scrollIntoView&&(h=a(b).scrollTop(),i=e.offset().top,(h>i||i>h+a(b).height())&&f[0].scrollIntoView()),c.preventDefault())}}))}function N(){var a,b,c,d,e,f=!1;T.unbind("touchstart.jsp touchmove.jsp touchend.jsp click.jsp-touchclick").bind("touchstart.jsp",function(g){var h=g.originalEvent.touches[0];a=A(),b=B(),c=h.pageX,d=h.pageY,e=!1,f=!0}).bind("touchmove.jsp",function(g){if(f){var h=g.originalEvent.touches[0],i=db,j=ab;return vb.scrollTo(a+c-h.pageX,b+d-h.pageY),e=e||Math.abs(c-h.pageX)>5||Math.abs(d-h.pageY)>5,i==db&&j==ab}}).bind("touchend.jsp",function(){f=!1}).bind("click.jsp-touchclick",function(){return e?(e=!1,!1):void 0})}function O(){var a=B(),b=A();d.removeClass("jspScrollable").unbind(".jsp"),d.replaceWith(Ab.append(Q.children())),Ab.scrollTop(a),Ab.scrollLeft(b),rb&&clearInterval(rb)}var P,Q,R,S,T,U,V,W,X,Y,Z,$,_,ab,bb,cb,db,eb,fb,gb,hb,ib,jb,kb,lb,mb,nb,ob,pb,qb,rb,sb,tb,ub,vb=this,wb=!0,xb=!0,yb=!1,zb=!1,Ab=d.clone(!1,!1).empty(),Bb=a.fn.mwheelIntent?"mwheelIntent.jsp":"mousewheel.jsp";"border-box"===d.css("box-sizing")?(sb=0,tb=0):(sb=d.css("paddingTop")+" "+d.css("paddingRight")+" "+d.css("paddingBottom")+" "+d.css("paddingLeft"),tb=(parseInt(d.css("paddingLeft"),10)||0)+(parseInt(d.css("paddingRight"),10)||0)),a.extend(vb,{reinitialise:function(b){b=a.extend({},P,b),f(b)},scrollToElement:function(a,b,c){z(a,b,c)},scrollTo:function(a,b,c){y(a,c),x(b,c)},scrollToX:function(a,b){y(a,b)},scrollToY:function(a,b){x(a,b)},scrollToPercentX:function(a,b){y(a*(U-R),b)},scrollToPercentY:function(a,b){x(a*(V-S),b)},scrollBy:function(a,b,c){vb.scrollByX(a,c),vb.scrollByY(b,c)},scrollByX:function(a,b){var c=A()+Math[0>a?"floor":"ceil"](a),d=c/(U-R);t(d*cb,b)},scrollByY:function(a,b){var c=B()+Math[0>a?"floor":"ceil"](a),d=c/(V-S);r(d*_,b)},positionDragX:function(a,b){t(a,b)},positionDragY:function(a,b){r(a,b)},animate:function(a,b,c,d){var e={};e[b]=c,a.animate(e,{duration:P.animateDuration,easing:P.animateEase,queue:!1,step:d})},getContentPositionX:function(){return A()},getContentPositionY:function(){return B()},getContentWidth:function(){return U},getContentHeight:function(){return V},getPercentScrolledX:function(){return A()/(U-R)},getPercentScrolledY:function(){return B()/(V-S)},getIsScrollableH:function(){return Z},getIsScrollableV:function(){return Y},getContentPane:function(){return Q},scrollToBottom:function(a){r(_,a)},hijackInternalLinks:a.noop,destroy:function(){O()}}),f(e)}return d=a.extend({},a.fn.jScrollPane.defaults,d),a.each(["arrowButtonSpeed","trackClickSpeed","keyboardSpeed"],function(){d[this]=d[this]||d.speed}),this.each(function(){var b=a(this),c=b.data("jsp");c?c.reinitialise(d):(a("script",b).filter('[type="text/javascript"],:not([type])').remove(),c=new e(b,d),b.data("jsp",c))})},a.fn.jScrollPane.defaults={showArrows:!1,maintainPosition:!0,stickToBottom:!1,stickToRight:!1,clickOnTrack:!0,autoReinitialise:!1,autoReinitialiseDelay:500,verticalDragMinHeight:0,verticalDragMaxHeight:99999,horizontalDragMinWidth:0,horizontalDragMaxWidth:99999,contentWidth:c,animateScroll:!1,animateDuration:300,animateEase:"linear",hijackInternalLinks:!1,verticalGutter:4,horizontalGutter:4,mouseWheelSpeed:3,arrowButtonSpeed:0,arrowRepeatFreq:50,arrowScrollOnHover:!1,trackClickSpeed:0,trackClickRepeatFreq:70,verticalArrowPositions:"split",horizontalArrowPositions:"split",enableKeyboardNavigation:!0,hideFocus:!1,keyboardSpeed:0,initialDelay:300,speed:30,scrollPagePercent:.8}}(jQuery,this);

/*! Copyright (c) 2013 Brandon Aaron (http://brandon.aaron.sh)
 * Licensed under the MIT License (LICENSE.txt).
 *
 * Version: 3.1.9
 *
 * Requires: jQuery 1.2.2+
 */

(function (factory) {
    if ( typeof define === 'function' && define.amd ) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // Node/CommonJS style for Browserify
        module.exports = factory;
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function ($) {

    var toFix  = ['wheel', 'mousewheel', 'DOMMouseScroll', 'MozMousePixelScroll'],
        toBind = ( 'onwheel' in document || document.documentMode >= 9 ) ?
            ['wheel'] : ['mousewheel', 'DomMouseScroll', 'MozMousePixelScroll'],
        slice  = Array.prototype.slice,
        nullLowestDeltaTimeout, lowestDelta;

    if ( $.event.fixHooks ) {
        for ( var i = toFix.length; i; ) {
            $.event.fixHooks[ toFix[--i] ] = $.event.mouseHooks;
        }
    }

    var special = $.event.special.mousewheel = {
        version: '3.1.9',

        setup: function() {
            if ( this.addEventListener ) {
                for ( var i = toBind.length; i; ) {
                    this.addEventListener( toBind[--i], handler, false );
                }
            } else {
                this.onmousewheel = handler;
            }
            // Store the line height and page height for this particular element
            $.data(this, 'mousewheel-line-height', special.getLineHeight(this));
            $.data(this, 'mousewheel-page-height', special.getPageHeight(this));
        },

        teardown: function() {
            if ( this.removeEventListener ) {
                for ( var i = toBind.length; i; ) {
                    this.removeEventListener( toBind[--i], handler, false );
                }
            } else {
                this.onmousewheel = null;
            }
        },

        getLineHeight: function(elem) {
            return parseInt($(elem)['offsetParent' in $.fn ? 'offsetParent' : 'parent']().css('fontSize'), 10);
        },

        getPageHeight: function(elem) {
            return $(elem).height();
        },

        settings: {
            adjustOldDeltas: true
        }
    };

    $.fn.extend({
        mousewheel: function(fn) {
            return fn ? this.bind('mousewheel', fn) : this.trigger('mousewheel');
        },

        unmousewheel: function(fn) {
            return this.unbind('mousewheel', fn);
        }
    });


    function handler(event) {
        var orgEvent   = event || window.event,
            args       = slice.call(arguments, 1),
            delta      = 0,
            deltaX     = 0,
            deltaY     = 0,
            absDelta   = 0;
        event = $.event.fix(orgEvent);
        event.type = 'mousewheel';

        if ( 'detail'      in orgEvent ) { deltaY = orgEvent.detail * -1;      }
        if ( 'wheelDelta'  in orgEvent ) { deltaY = orgEvent.wheelDelta;       }
        if ( 'wheelDeltaY' in orgEvent ) { deltaY = orgEvent.wheelDeltaY;      }
        if ( 'wheelDeltaX' in orgEvent ) { deltaX = orgEvent.wheelDeltaX * -1; }

        if ( 'axis' in orgEvent && orgEvent.axis === orgEvent.HORIZONTAL_AXIS ) {
            deltaX = deltaY * -1;
            deltaY = 0;
        }

        delta = deltaY === 0 ? deltaX : deltaY;

        if ( 'deltaY' in orgEvent ) {
            deltaY = orgEvent.deltaY * -1;
            delta  = deltaY;
        }
        if ( 'deltaX' in orgEvent ) {
            deltaX = orgEvent.deltaX;
            if ( deltaY === 0 ) { delta  = deltaX * -1; }
        }

        if ( deltaY === 0 && deltaX === 0 ) { return; }

        if ( orgEvent.deltaMode === 1 ) {
            var lineHeight = $.data(this, 'mousewheel-line-height');
            delta  *= lineHeight;
            deltaY *= lineHeight;
            deltaX *= lineHeight;
        } else if ( orgEvent.deltaMode === 2 ) {
            var pageHeight = $.data(this, 'mousewheel-page-height');
            delta  *= pageHeight;
            deltaY *= pageHeight;
            deltaX *= pageHeight;
        }

        absDelta = Math.max( Math.abs(deltaY), Math.abs(deltaX) );

        if ( !lowestDelta || absDelta < lowestDelta ) {
            lowestDelta = absDelta;

            if ( shouldAdjustOldDeltas(orgEvent, absDelta) ) {
                lowestDelta /= 40;
            }
        }

        if ( shouldAdjustOldDeltas(orgEvent, absDelta) ) {
            delta  /= 40;
            deltaX /= 40;
            deltaY /= 40;
        }

        delta  = Math[ delta  >= 1 ? 'floor' : 'ceil' ](delta  / lowestDelta);
        deltaX = Math[ deltaX >= 1 ? 'floor' : 'ceil' ](deltaX / lowestDelta);
        deltaY = Math[ deltaY >= 1 ? 'floor' : 'ceil' ](deltaY / lowestDelta);
        event.deltaX = deltaX;
        event.deltaY = deltaY;
        event.deltaFactor = lowestDelta;
        event.deltaMode = 0;
        args.unshift(event, delta, deltaX, deltaY);
        if (nullLowestDeltaTimeout) { clearTimeout(nullLowestDeltaTimeout); }
        nullLowestDeltaTimeout = setTimeout(nullLowestDelta, 200);

        return ($.event.dispatch || $.event.handle).apply(this, args);
    }

    function nullLowestDelta() {
        lowestDelta = null;
    }

    function shouldAdjustOldDeltas(orgEvent, absDelta) {
        return special.settings.adjustOldDeltas && orgEvent.type === 'mousewheel' && absDelta % 120 === 0;
    }

}));
;var page_handeler = {
    defaults: {
        complete: []
    },

    init: function () {
        $('body').on('click', 'a', function (e) {
            if (typeof $(this).data('page-post') != 'undefined') {
                e.preventDefault();
                this.page($(this).attr('href'), $(this).data('page-post'));
            }
        });

        $.fn.ajax_factory.defaults.complete.push(function (json) {page_handeler.page_callback(json)});
        $.fn.ajax_factory.defaults.load_pages_ajax = true;
    },

    page_callback: function (json) {
        if (json && json.push_state) {
            var $id = $(json.push_state.data.id);
            if($id.length) {
                this.toggle_page($id);
            }
        }
    },

    toggle_page: function ($page) {
        if ($page.css('z-index') != 2) {
            var $main = $('#main');
            $page.hide();
            $main.stop(true, true).addClass('flipped');
            var $children = $main.children('div');
            setTimeout(function () {
                $children.hide();
                $page.show();
                $main.removeClass('flipped');

                $("a").removeClass('sel').parent('li').removeClass('sel');
                var $links = $('a[href="' + $page.data('url') + '"]');
                $links.addClass('sel').parent('li').addClass('sel');
                $main.animate({scrollTo: 0});

                if (page_handeler.defaults.complete) {
                    page_handeler.defaults.complete.each(function (method) {
                        if (typeof method == 'function') {
                            method();
                        } else {
                            window[method]();
                        }
                    });
                }
            }, 300);
        }
    },

    page: function (url, post, is_popped) {
        var module = post.module;
        var act = post.act;
        post.is_popped = is_popped || 0;
        var $page = $("div[data-url='" + url + "']");
        if ($page.length) {
            if (!is_popped) {
                window.history.pushState(post, '', url);
            }
            this.toggle_page($page);
            this.perform_page_actions(post.actions, url);
        } else {
            delete post.module;
            delete post.act;
            post.url = url;
            $.fn.ajax_factory(module, act, post);
        }
    },

    perform_page_actions: function (actions, url) {
        if (typeof actions != 'undefined') {
            actions.each(function (element) {
                var options = element[3] || {};
                options.post_as_url = url;
                $.fn.ajax_factory(element[0], element[1], element[2] || {}, options);
            });
        }
    }
};

$(document).ready(function () {
    page_handeler.init();
});
;/*
 * 
 * TableSorter 2.0 - Client-side table sorting with ease!
 * Version 2.0.5b
 * @requires jQuery v1.2.3
 * 
 * Copyright (c) 2007 Christian Bach
 * Examples and docs at: http://tablesorter.com
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 * 
 */
/**
 * 
 * @description Create a sortable table with multi-column sorting capabilitys
 * 
 * @example $('table').tablesorter();
 * @desc Create a simple tablesorter interface.
 * 
 * @example $('table').tablesorter({ sortList:[[0,0],[1,0]] });
 * @desc Create a tablesorter interface and sort on the first and secound column column headers.
 * 
 * @example $('table').tablesorter({ headers: { 0: { sorter: false}, 1: {sorter: false} } });
 *          
 * @desc Create a tablesorter interface and disableing the first and second  column headers.
 *      
 * 
 * @example $('table').tablesorter({ headers: { 0: {sorter:"integer"}, 1: {sorter:"currency"} } });
 * 
 * @desc Create a tablesorter interface and set a column parser for the first
 *       and second column.
 * 
 * 
 * @param Object
 *            settings An object literal containing key/value pairs to provide
 *            optional settings.
 * 
 * 
 * @option String cssHeader (optional) A string of the class name to be appended
 *         to sortable tr elements in the thead of the table. Default value:
 *         "header"
 * 
 * @option String cssAsc (optional) A string of the class name to be appended to
 *         sortable tr elements in the thead on a ascending sort. Default value:
 *         "headerSortUp"
 * 
 * @option String cssDesc (optional) A string of the class name to be appended
 *         to sortable tr elements in the thead on a descending sort. Default
 *         value: "headerSortDown"
 * 
 * @option String sortInitialOrder (optional) A string of the inital sorting
 *         order can be asc or desc. Default value: "asc"
 * 
 * @option String sortMultisortKey (optional) A string of the multi-column sort
 *         key. Default value: "shiftKey"
 * 
 * @option String textExtraction (optional) A string of the text-extraction
 *         method to use. For complex html structures inside td cell set this
 *         option to "complex", on large tables the complex option can be slow.
 *         Default value: "simple"
 * 
 * @option Object headers (optional) An array containing the forces sorting
 *         rules. This option let's you specify a default sorting rule. Default
 *         value: null
 * 
 * @option Array sortList (optional) An array containing the forces sorting
 *         rules. This option let's you specify a default sorting rule. Default
 *         value: null
 * 
 * @option Array sortForce (optional) An array containing forced sorting rules.
 *         This option let's you specify a default sorting rule, which is
 *         prepended to user-selected rules. Default value: null
 * 
 * @option Boolean sortLocaleCompare (optional) Boolean flag indicating whatever
 *         to use String.localeCampare method or not. Default set to true.
 * 
 * 
 * @option Array sortAppend (optional) An array containing forced sorting rules.
 *         This option let's you specify a default sorting rule, which is
 *         appended to user-selected rules. Default value: null
 * 
 * @option Boolean widthFixed (optional) Boolean flag indicating if tablesorter
 *         should apply fixed widths to the table columns. This is usefull when
 *         using the pager companion plugin. This options requires the dimension
 *         jquery plugin. Default value: false
 * 
 * @option Boolean cancelSelection (optional) Boolean flag indicating if
 *         tablesorter should cancel selection of the table headers text.
 *         Default value: true
 * 
 * @option Boolean debug (optional) Boolean flag indicating if tablesorter
 *         should display debuging information usefull for development.
 * 
 * @type jQuery
 * 
 * @name tablesorter
 * 
 * @cat Plugins/Tablesorter
 * 
 * @author Christian Bach/christian.bach@polyester.se
 */

(function ($) {
    $.extend({
        tablesorter: new
        function () {

            var parsers = [],
                widgets = [];

            this.defaults = {
                cssHeader: "header",
                cssAsc: "headerSortUp",
                cssDesc: "headerSortDown",
                cssChildRow: "expand-child",
                sortInitialOrder: "asc",
                sortMultiSortKey: "shiftKey",
                sortForce: null,
                sortAppend: null,
                sortLocaleCompare: true,
                textExtraction: "simple",
                parsers: {}, widgets: [],
                widgetZebra: {
                    css: ["even", "odd"]
                }, headers: {}, widthFixed: false,
                cancelSelection: true,
                sortList: [],
                headerList: [],
                dateFormat: "us",
                decimal: '/\.|\,/g',
                onRenderHeader: null,
                selectorHeaders: 'thead th',
                debug: false
            };

            /* debuging utils */

            function benchmark(s, d) {
                log(s + "," + (new Date().getTime() - d.getTime()) + "ms");
            }

            this.benchmark = benchmark;

            function log(s) {
                if (typeof console != "undefined" && typeof console.debug != "undefined") {
                    console.log(s);
                } else {
                    alert(s);
                }
            }

            /* parsers utils */

            function buildParserCache(table, $headers) {

                if (table.config.debug) {
                    var parsersDebug = "";
                }

                if (table.tBodies.length == 0) return; // In the case of empty tables
                var rows = table.tBodies[0].rows;

                if (rows[0]) {

                    var list = [],
                        cells = rows[0].cells,
                        l = cells.length;

                    for (var i = 0; i < l; i++) {

                        var p = false;

                        if ($.metadata && ($($headers[i]).metadata() && $($headers[i]).metadata().sorter)) {

                            p = getParserById($($headers[i]).metadata().sorter);

                        } else if ((table.config.headers[i] && table.config.headers[i].sorter)) {

                            p = getParserById(table.config.headers[i].sorter);
                        }
                        if (!p) {

                            p = detectParserForColumn(table, rows, -1, i);
                        }

                        if (table.config.debug) {
                            parsersDebug += "column:" + i + " parser:" + p.id + "\n";
                        }

                        list.push(p);
                    }
                }

                if (table.config.debug) {
                    log(parsersDebug);
                }

                return list;
            };

            function detectParserForColumn(table, rows, rowIndex, cellIndex) {
                var l = parsers.length,
                    node = false,
                    nodeValue = false,
                    keepLooking = true;
                while (nodeValue == '' && keepLooking) {
                    rowIndex++;
                    if (rows[rowIndex]) {
                        node = getNodeFromRowAndCellIndex(rows, rowIndex, cellIndex);
                        nodeValue = trimAndGetNodeText(table.config, node);
                        if (table.config.debug) {
                            log('Checking if value was empty on row:' + rowIndex);
                        }
                    } else {
                        keepLooking = false;
                    }
                }
                for (var i = 1; i < l; i++) {
                    if (parsers[i].is(nodeValue, table, node)) {
                        return parsers[i];
                    }
                }
                // 0 is always the generic parser (text)
                return parsers[0];
            }

            function getNodeFromRowAndCellIndex(rows, rowIndex, cellIndex) {
                return rows[rowIndex].cells[cellIndex];
            }

            function trimAndGetNodeText(config, node) {
                return $.trim(getElementText(config, node));
            }

            function getParserById(name) {
                var l = parsers.length;
                for (var i = 0; i < l; i++) {
                    if (parsers[i].id.toLowerCase() == name.toLowerCase()) {
                        return parsers[i];
                    }
                }
                return false;
            }

            /* utils */

            function buildCache(table) {

                if (table.config.debug) {
                    var cacheTime = new Date();
                }

                var totalRows = (table.tBodies[0] && table.tBodies[0].rows.length) || 0,
                    totalCells = (table.tBodies[0].rows[0] && table.tBodies[0].rows[0].cells.length) || 0,
                    parsers = table.config.parsers,
                    cache = {
                        row: [],
                        normalized: []
                    };

                for (var i = 0; i < totalRows; ++i) {

                    /** Add the table data to main data array */
                    var c = $(table.tBodies[0].rows[i]),
                        cols = [];

                    // if this is a child row, add it to the last row's children and
                    // continue to the next row
                    if (c.hasClass(table.config.cssChildRow)) {
                        cache.row[cache.row.length - 1] = cache.row[cache.row.length - 1].add(c);
                        // go to the next for loop
                        continue;
                    }

                    cache.row.push(c);

                    for (var j = 0; j < totalCells; ++j) {
                        cols.push(parsers[j].format(getElementText(table.config, c[0].cells[j]), table, c[0].cells[j]));
                    }

                    cols.push(cache.normalized.length); // add position for rowCache
                    cache.normalized.push(cols);
                    cols = null;
                };

                if (table.config.debug) {
                    benchmark("Building cache for " + totalRows + " rows:", cacheTime);
                }

                return cache;
            };

            function getElementText(config, node) {

                var text = "";

                if (!node) return "";

                if (!config.supportsTextContent) config.supportsTextContent = node.textContent || false;

                if (config.textExtraction == "simple") {
                    if (config.supportsTextContent) {
                        text = node.textContent;
                    } else {
                        if (node.childNodes[0] && node.childNodes[0].hasChildNodes()) {
                            text = node.childNodes[0].innerHTML;
                        } else {
                            text = node.innerHTML;
                        }
                    }
                } else {
                    if (typeof(config.textExtraction) == "function") {
                        text = config.textExtraction(node);
                    } else {
                        text = $(node).text();
                    }
                }
                return text;
            }

            function appendToTable(table, cache) {

                if (table.config.debug) {
                    var appendTime = new Date()
                }

                var c = cache,
                    r = c.row,
                    n = c.normalized,
                    totalRows = n.length,
                    checkCell = (n[0].length - 1),
                    tableBody = $(table.tBodies[0]),
                    rows = [];


                for (var i = 0; i < totalRows; i++) {
                    var pos = n[i][checkCell];

                    rows.push(r[pos]);

                    if (!table.config.appender) {

                        //var o = ;
                        var l = r[pos].length;
                        for (var j = 0; j < l; j++) {
                            tableBody[0].appendChild(r[pos][j]);
                        }

                        // 
                    }
                }



                if (table.config.appender) {

                    table.config.appender(table, rows);
                }

                rows = null;

                if (table.config.debug) {
                    benchmark("Rebuilt table:", appendTime);
                }

                // apply table widgets
                applyWidget(table);

                // trigger sortend
                setTimeout(function () {
                    $(table).trigger("sortEnd");
                }, 0);

            };

            function buildHeaders(table) {

                if (table.config.debug) {
                    var time = new Date();
                }

                var meta = ($.metadata) ? true : false;
                
                var header_index = computeTableHeaderCellIndexes(table);

                $tableHeaders = $(table.config.selectorHeaders, table).each(function (index) {

                    this.column = header_index[this.parentNode.rowIndex + "-" + this.cellIndex];
                    // this.column = index;
                    this.order = formatSortingOrder(table.config.sortInitialOrder);
                    
					
					this.count = this.order;

                    if (checkHeaderMetadata(this) || checkHeaderOptions(table, index)) this.sortDisabled = true;
					if (checkHeaderOptionsSortingLocked(table, index)) this.order = this.lockedOrder = checkHeaderOptionsSortingLocked(table, index);

                    if (!this.sortDisabled) {
                        var $th = $(this).addClass(table.config.cssHeader);
                        if (table.config.onRenderHeader) table.config.onRenderHeader.apply($th);
                    }

                    // add cell to headerList
                    table.config.headerList[index] = this;
                });

                if (table.config.debug) {
                    benchmark("Built headers:", time);
                    log($tableHeaders);
                }

                return $tableHeaders;

            };

            // from:
            // http://www.javascripttoolbox.com/lib/table/examples.php
            // http://www.javascripttoolbox.com/temp/table_cellindex.html


            function computeTableHeaderCellIndexes(t) {
                var matrix = [];
                var lookup = {};
                var thead = t.getElementsByTagName('THEAD')[0];
                var trs = thead.getElementsByTagName('TR');

                for (var i = 0; i < trs.length; i++) {
                    var cells = trs[i].cells;
                    for (var j = 0; j < cells.length; j++) {
                        var c = cells[j];

                        var rowIndex = c.parentNode.rowIndex;
                        var cellId = rowIndex + "-" + c.cellIndex;
                        var rowSpan = c.rowSpan || 1;
                        var colSpan = c.colSpan || 1
                        var firstAvailCol;
                        if (typeof(matrix[rowIndex]) == "undefined") {
                            matrix[rowIndex] = [];
                        }
                        // Find first available column in the first row
                        for (var k = 0; k < matrix[rowIndex].length + 1; k++) {
                            if (typeof(matrix[rowIndex][k]) == "undefined") {
                                firstAvailCol = k;
                                break;
                            }
                        }
                        lookup[cellId] = firstAvailCol;
                        for (var k = rowIndex; k < rowIndex + rowSpan; k++) {
                            if (typeof(matrix[k]) == "undefined") {
                                matrix[k] = [];
                            }
                            var matrixrow = matrix[k];
                            for (var l = firstAvailCol; l < firstAvailCol + colSpan; l++) {
                                matrixrow[l] = "x";
                            }
                        }
                    }
                }
                return lookup;
            }

            function checkCellColSpan(table, rows, row) {
                var arr = [],
                    r = table.tHead.rows,
                    c = r[row].cells;

                for (var i = 0; i < c.length; i++) {
                    var cell = c[i];

                    if (cell.colSpan > 1) {
                        arr = arr.concat(checkCellColSpan(table, headerArr, row++));
                    } else {
                        if (table.tHead.length == 1 || (cell.rowSpan > 1 || !r[row + 1])) {
                            arr.push(cell);
                        }
                        // headerArr[row] = (i+row);
                    }
                }
                return arr;
            };

            function checkHeaderMetadata(cell) {
                if (($.metadata) && ($(cell).metadata().sorter === false)) {
                    return true;
                };
                return false;
            }

            function checkHeaderOptions(table, i) {
                if ((table.config.headers[i]) && (table.config.headers[i].sorter === false)) {
                    return true;
                };
                return false;
            }
			
			 function checkHeaderOptionsSortingLocked(table, i) {
                if ((table.config.headers[i]) && (table.config.headers[i].lockedOrder)) return table.config.headers[i].lockedOrder;
                return false;
            }
			
            function applyWidget(table) {
                var c = table.config.widgets;
                var l = c.length;
                for (var i = 0; i < l; i++) {

                    getWidgetById(c[i]).format(table);
                }

            }

            function getWidgetById(name) {
                var l = widgets.length;
                for (var i = 0; i < l; i++) {
                    if (widgets[i].id.toLowerCase() == name.toLowerCase()) {
                        return widgets[i];
                    }
                }
            };

            function formatSortingOrder(v) {
                if (typeof(v) != "Number") {
                    return (v.toLowerCase() == "desc") ? 1 : 0;
                } else {
                    return (v == 1) ? 1 : 0;
                }
            }

            function isValueInArray(v, a) {
                var l = a.length;
                for (var i = 0; i < l; i++) {
                    if (a[i][0] == v) {
                        return true;
                    }
                }
                return false;
            }

            function setHeadersCss(table, $headers, list, css) {
                // remove all header information
                $headers.removeClass(css[0]).removeClass(css[1]);

                var h = [];
                $headers.each(function (offset) {
                    if (!this.sortDisabled) {
                        h[this.column] = $(this);
                    }
                });

                var l = list.length;
                for (var i = 0; i < l; i++) {
                    h[list[i][0]].addClass(css[list[i][1]]);
                }
            }

            function fixColumnWidth(table, $headers) {
                var c = table.config;
                if (c.widthFixed) {
                    var colgroup = $('<colgroup>');
                    $("tr:first td", table.tBodies[0]).each(function () {
                        colgroup.append($('<col>').css('width', $(this).width()));
                    });
                    $(table).prepend(colgroup);
                };
            }

            function updateHeaderSortCount(table, sortList) {
                var c = table.config,
                    l = sortList.length;
                for (var i = 0; i < l; i++) {
                    var s = sortList[i],
                        o = c.headerList[s[0]];
                    o.count = s[1];
                    o.count++;
                }
            }

            /* sorting methods */

            function multisort(table, sortList, cache) {

                if (table.config.debug) {
                    var sortTime = new Date();
                }

                var dynamicExp = "var sortWrapper = function(a,b) {",
                    l = sortList.length;

                // TODO: inline functions.
                for (var i = 0; i < l; i++) {

                    var c = sortList[i][0];
                    var order = sortList[i][1];
                    // var s = (getCachedSortType(table.config.parsers,c) == "text") ?
                    // ((order == 0) ? "sortText" : "sortTextDesc") : ((order == 0) ?
                    // "sortNumeric" : "sortNumericDesc");
                    // var s = (table.config.parsers[c].type == "text") ? ((order == 0)
                    // ? makeSortText(c) : makeSortTextDesc(c)) : ((order == 0) ?
                    // makeSortNumeric(c) : makeSortNumericDesc(c));
                    var s = (table.config.parsers[c].type == "text") ? ((order == 0) ? makeSortFunction("text", "asc", c) : makeSortFunction("text", "desc", c)) : ((order == 0) ? makeSortFunction("numeric", "asc", c) : makeSortFunction("numeric", "desc", c));
                    var e = "e" + i;

                    dynamicExp += "var " + e + " = " + s; // + "(a[" + c + "],b[" + c
                    // + "]); ";
                    dynamicExp += "if(" + e + ") { return " + e + "; } ";
                    dynamicExp += "else { ";

                }

                // if value is the same keep orignal order
                var orgOrderCol = cache.normalized[0].length - 1;
                dynamicExp += "return a[" + orgOrderCol + "]-b[" + orgOrderCol + "];";

                for (var i = 0; i < l; i++) {
                    dynamicExp += "}; ";
                }

                dynamicExp += "return 0; ";
                dynamicExp += "}; ";

                if (table.config.debug) {
                    benchmark("Evaling expression:" + dynamicExp, new Date());
                }

                eval(dynamicExp);

                cache.normalized.sort(sortWrapper);

                if (table.config.debug) {
                    benchmark("Sorting on " + sortList.toString() + " and dir " + order + " time:", sortTime);
                }

                return cache;
            };

            function makeSortFunction(type, direction, index) {
                var a = "a[" + index + "]",
                    b = "b[" + index + "]";
                if (type == 'text' && direction == 'asc') {
                    return "(" + a + " == " + b + " ? 0 : (" + a + " === null ? Number.POSITIVE_INFINITY : (" + b + " === null ? Number.NEGATIVE_INFINITY : (" + a + " < " + b + ") ? -1 : 1 )));";
                } else if (type == 'text' && direction == 'desc') {
                    return "(" + a + " == " + b + " ? 0 : (" + a + " === null ? Number.POSITIVE_INFINITY : (" + b + " === null ? Number.NEGATIVE_INFINITY : (" + b + " < " + a + ") ? -1 : 1 )));";
                } else if (type == 'numeric' && direction == 'asc') {
                    return "(" + a + " === null && " + b + " === null) ? 0 :(" + a + " === null ? Number.POSITIVE_INFINITY : (" + b + " === null ? Number.NEGATIVE_INFINITY : " + a + " - " + b + "));";
                } else if (type == 'numeric' && direction == 'desc') {
                    return "(" + a + " === null && " + b + " === null) ? 0 :(" + a + " === null ? Number.POSITIVE_INFINITY : (" + b + " === null ? Number.NEGATIVE_INFINITY : " + b + " - " + a + "));";
                }
            };

            function makeSortText(i) {
                return "((a[" + i + "] < b[" + i + "]) ? -1 : ((a[" + i + "] > b[" + i + "]) ? 1 : 0));";
            };

            function makeSortTextDesc(i) {
                return "((b[" + i + "] < a[" + i + "]) ? -1 : ((b[" + i + "] > a[" + i + "]) ? 1 : 0));";
            };

            function makeSortNumeric(i) {
                return "a[" + i + "]-b[" + i + "];";
            };

            function makeSortNumericDesc(i) {
                return "b[" + i + "]-a[" + i + "];";
            };

            function sortText(a, b) {
                if (table.config.sortLocaleCompare) return a.localeCompare(b);
                return ((a < b) ? -1 : ((a > b) ? 1 : 0));
            };

            function sortTextDesc(a, b) {
                if (table.config.sortLocaleCompare) return b.localeCompare(a);
                return ((b < a) ? -1 : ((b > a) ? 1 : 0));
            };

            function sortNumeric(a, b) {
                return a - b;
            };

            function sortNumericDesc(a, b) {
                return b - a;
            };

            function getCachedSortType(parsers, i) {
                return parsers[i].type;
            }; /* public methods */
            this.construct = function (settings) {
                return this.each(function () {
                    // if no thead or tbody quit.
                    if (!this.tHead || !this.tBodies) return;
                    // declare
                    var $this, $document, $headers, cache, config, shiftDown = 0,
                        sortOrder;
                    // new blank config object
                    this.config = {};
                    // merge and extend.
                    config = $.extend(this.config, $.tablesorter.defaults, settings);
                    // store common expression for speed
                    $this = $(this);
                    // save the settings where they read
                    $.data(this, "tablesorter", config);
                    // build headers
                    $headers = buildHeaders(this);
                    // try to auto detect column type, and store in tables config
                    this.config.parsers = buildParserCache(this, $headers);
                    // build the cache for the tbody cells
                    cache = buildCache(this);
                    // get the css class names, could be done else where.
                    var sortCSS = [config.cssDesc, config.cssAsc];
                    // fixate columns if the users supplies the fixedWidth option
                    fixColumnWidth(this);
                    // apply event handling to headers
                    // this is to big, perhaps break it out?
                    $headers.click(

                    function (e) {
                        var totalRows = ($this[0].tBodies[0] && $this[0].tBodies[0].rows.length) || 0;
                        if (!this.sortDisabled && totalRows > 0) {
                            // Only call sortStart if sorting is
                            // enabled.
                            $this.trigger("sortStart");
                            // store exp, for speed
                            var $cell = $(this);
                            // get current column index
                            var i = this.column;
                            // get current column sort order
                            this.order = this.count++ % 2;
							// always sort on the locked order.
							if(this.lockedOrder) this.order = this.lockedOrder;
							
							// user only whants to sort on one
                            // column
                            if (!e[config.sortMultiSortKey]) {
                                // flush the sort list
                                config.sortList = [];
                                if (config.sortForce != null) {
                                    var a = config.sortForce;
                                    for (var j = 0; j < a.length; j++) {
                                        if (a[j][0] != i) {
                                            config.sortList.push(a[j]);
                                        }
                                    }
                                }
                                // add column to sort list
                                config.sortList.push([i, this.order]);
                                // multi column sorting
                            } else {
                                // the user has clicked on an all
                                // ready sortet column.
                                if (isValueInArray(i, config.sortList)) {
                                    // revers the sorting direction
                                    // for all tables.
                                    for (var j = 0; j < config.sortList.length; j++) {
                                        var s = config.sortList[j],
                                            o = config.headerList[s[0]];
                                        if (s[0] == i) {
                                            o.count = s[1];
                                            o.count++;
                                            s[1] = o.count % 2;
                                        }
                                    }
                                } else {
                                    // add column to sort list array
                                    config.sortList.push([i, this.order]);
                                }
                            };
                            setTimeout(function () {
                                // set css for headers
                                setHeadersCss($this[0], $headers, config.sortList, sortCSS);
                                appendToTable(
	                                $this[0], multisort(
	                                $this[0], config.sortList, cache)
								);
                            }, 1);
                            // stop normal event by returning false
                            return false;
                        }
                        // cancel selection
                    }).mousedown(function () {
                        if (config.cancelSelection) {
                            this.onselectstart = function () {
                                return false
                            };
                            return false;
                        }
                    });
                    // apply easy methods that trigger binded events
                    $this.bind("update", function () {
                        var me = this;
                        setTimeout(function () {
                            // rebuild parsers.
                            me.config.parsers = buildParserCache(
                            me, $headers);
                            // rebuild the cache map
                            cache = buildCache(me);
                        }, 1);
                    }).bind("updateCell", function (e, cell) {
                        var config = this.config;
                        // get position from the dom.
                        var pos = [(cell.parentNode.rowIndex - 1), cell.cellIndex];
                        // update cache
                        cache.normalized[pos[0]][pos[1]] = config.parsers[pos[1]].format(
                        getElementText(config, cell), cell);
                    }).bind("sorton", function (e, list) {
                        $(this).trigger("sortStart");
                        config.sortList = list;
                        // update and store the sortlist
                        var sortList = config.sortList;
                        // update header count index
                        updateHeaderSortCount(this, sortList);
                        // set css for headers
                        setHeadersCss(this, $headers, sortList, sortCSS);
                        // sort the table and append it to the dom
                        appendToTable(this, multisort(this, sortList, cache));
                    }).bind("appendCache", function () {
                        appendToTable(this, cache);
                    }).bind("applyWidgetId", function (e, id) {
                        getWidgetById(id).format(this);
                    }).bind("applyWidgets", function () {
                        // apply widgets
                        applyWidget(this);
                    });
                    if ($.metadata && ($(this).metadata() && $(this).metadata().sortlist)) {
                        config.sortList = $(this).metadata().sortlist;
                    }
                    // if user has supplied a sort list to constructor.
                    if (config.sortList.length > 0) {
                        $this.trigger("sorton", [config.sortList]);
                    }
                    // apply widgets
                    applyWidget(this);
                });
            };
            this.addParser = function (parser) {
                var l = parsers.length,
                    a = true;
                for (var i = 0; i < l; i++) {
                    if (parsers[i].id.toLowerCase() == parser.id.toLowerCase()) {
                        a = false;
                    }
                }
                if (a) {
                    parsers.push(parser);
                };
            };
            this.addWidget = function (widget) {
                widgets.push(widget);
            };
            this.formatFloat = function (s) {
                var i = parseFloat(s);
                return (isNaN(i)) ? 0 : i;
            };
            this.formatInt = function (s) {
                var i = parseInt(s);
                return (isNaN(i)) ? 0 : i;
            };
            this.isDigit = function (s, config) {
                // replace all an wanted chars and match.
                return /^[-+]?\d*$/.test($.trim(s.replace(/[,.']/g, '')));
            };
            this.clearTableBody = function (table) {
                if ($.browser.msie) {
                    function empty() {
                        while (this.firstChild)
                        this.removeChild(this.firstChild);
                    }
                    empty.apply(table.tBodies[0]);
                } else {
                    table.tBodies[0].innerHTML = "";
                }
            };
        }
    });

    // extend plugin scope
    $.fn.extend({
        tablesorter: $.tablesorter.construct
    });

    // make shortcut
    var ts = $.tablesorter;

    // add default parsers
    ts.addParser({
        id: "text",
        is: function (s) {
            return true;
        }, format: function (s) {
            return $.trim(s.toLocaleLowerCase());
        }, type: "text"
    });

    ts.addParser({
        id: "digit",
        is: function (s, table) {
            var c = table.config;
            return $.tablesorter.isDigit(s, c);
        }, format: function (s) {
            return $.tablesorter.formatFloat(s);
        }, type: "numeric"
    });

    ts.addParser({
        id: "currency",
        is: function (s) {
            return /^[£$€?.]/.test(s);
        }, format: function (s) {
            return $.tablesorter.formatFloat(s.replace(new RegExp(/[£$€]/g), ""));
        }, type: "numeric"
    });

    ts.addParser({
        id: "ipAddress",
        is: function (s) {
            return /^\d{2,3}[\.]\d{2,3}[\.]\d{2,3}[\.]\d{2,3}$/.test(s);
        }, format: function (s) {
            var a = s.split("."),
                r = "",
                l = a.length;
            for (var i = 0; i < l; i++) {
                var item = a[i];
                if (item.length == 2) {
                    r += "0" + item;
                } else {
                    r += item;
                }
            }
            return $.tablesorter.formatFloat(r);
        }, type: "numeric"
    });

    ts.addParser({
        id: "url",
        is: function (s) {
            return /^(https?|ftp|file):\/\/$/.test(s);
        }, format: function (s) {
            return jQuery.trim(s.replace(new RegExp(/(https?|ftp|file):\/\//), ''));
        }, type: "text"
    });

    ts.addParser({
        id: "isoDate",
        is: function (s) {
            return /^\d{4}[\/-]\d{1,2}[\/-]\d{1,2}$/.test(s);
        }, format: function (s) {
            return $.tablesorter.formatFloat((s != "") ? new Date(s.replace(
            new RegExp(/-/g), "/")).getTime() : "0");
        }, type: "numeric"
    });

    ts.addParser({
        id: "percent",
        is: function (s) {
            return /\%$/.test($.trim(s));
        }, format: function (s) {
            return $.tablesorter.formatFloat(s.replace(new RegExp(/%/g), ""));
        }, type: "numeric"
    });

    ts.addParser({
        id: "usLongDate",
        is: function (s) {
            return s.match(new RegExp(/^[A-Za-z]{3,10}\.? [0-9]{1,2}, ([0-9]{4}|'?[0-9]{2}) (([0-2]?[0-9]:[0-5][0-9])|([0-1]?[0-9]:[0-5][0-9]\s(AM|PM)))$/));
        }, format: function (s) {
            return $.tablesorter.formatFloat(new Date(s).getTime());
        }, type: "numeric"
    });

    ts.addParser({
        id: "shortDate",
        is: function (s) {
            return /\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4}/.test(s);
        }, format: function (s, table) {
            var c = table.config;
            s = s.replace(/\-/g, "/");
            if (c.dateFormat == "us") {
                // reformat the string in ISO format
                s = s.replace(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/, "$3/$1/$2");
            } else if (c.dateFormat == "uk") {
                // reformat the string in ISO format
                s = s.replace(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/, "$3/$2/$1");
            } else if (c.dateFormat == "dd/mm/yy" || c.dateFormat == "dd-mm-yy") {
                s = s.replace(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2})/, "$1/$2/$3");
            }
            return $.tablesorter.formatFloat(new Date(s).getTime());
        }, type: "numeric"
    });
    ts.addParser({
        id: "time",
        is: function (s) {
            return /^(([0-2]?[0-9]:[0-5][0-9])|([0-1]?[0-9]:[0-5][0-9]\s(am|pm)))$/.test(s);
        }, format: function (s) {
            return $.tablesorter.formatFloat(new Date("2000/01/01 " + s).getTime());
        }, type: "numeric"
    });
    ts.addParser({
        id: "metadata",
        is: function (s) {
            return false;
        }, format: function (s, table, cell) {
            var c = table.config,
                p = (!c.parserMetadataName) ? 'sortValue' : c.parserMetadataName;
            return $(cell).metadata()[p];
        }, type: "numeric"
    });
    ts.addParser({
        id: 'uk_date',
        is: function(s) {
            return false;
        },
        format: function(s) {
            return s.substr(6,4) + s.substr(3,2) + s.substr(0,2);
        },
        type: 'numeric'
    });
    ts.addParser({
        id: 'score',
        is: function(s) {
            return false;
        },
        format: function(s) {
            return s.substr(1);
        },
        type: 'numeric'
    });
    // add default widgets
    ts.addWidget({
        id: "zebra",
        format: function (table) {
            if (table.config.debug) {
                var time = new Date();
            }
            var $tr, row = -1,
                odd;
            // loop through the visible rows
            $("tr:visible", table.tBodies[0]).each(function (i) {
                $tr = $(this);
                // style children rows the same way the parent
                // row was styled
                if (!$tr.hasClass(table.config.cssChildRow)) row++;
                odd = (row % 2 == 0);
                $tr.removeClass(
                table.config.widgetZebra.css[odd ? 0 : 1]).addClass(
                table.config.widgetZebra.css[odd ? 1 : 0])
            });
            if (table.config.debug) {
                $.tablesorter.benchmark("Applying Zebra widget", time);
            }
        }
    });
})(jQuery);

$.tablesorter.addParser({
    // set a unique id
    id: 'grades',
    is: function(s) {
        // return false so this parser is not auto detected
        return false;
    },
    format: function(s) {
        // format your data for normalization
        return s.toLowerCase().replace(/good/,2).replace(/medium/,1).replace(/bad/,0);
    },
    // set type, either numeric or text
    type: 'numeric'
});;/*!
 Colorbox v1.4.32 - 2013-10-16
 jQuery lightbox and modal window plugin
 (c) 2013 Jack Moore - http://www.jacklmoore.com/colorbox
 license: http://www.opensource.org/licenses/mit-license.php
 */
(function (e, t, i) {
    function o(i, o, n) {
        var r = t.createElement(i);
        return o && (r.id = Z + o), n && (r.style.cssText = n), e(r)
    }

    function n() {return i.innerHeight ? i.innerHeight : e(i).height()}

    function r(e) {
        var t = k.length, i = (z + e) % t;
        return 0 > i ? t + i : i
    }

    function h(e, t) {return Math.round((/%/.test(e) ? ("x" === t ? E.width() : n()) / 100 : 1) * parseInt(e, 10))}

    function s(e, t) {return e.photo || e.photoRegex.test(t)}

    function l(e, t) {return e.retinaUrl && i.devicePixelRatio > 1 ? t.replace(e.photoRegex, e.retinaSuffix) : t}

    function a(e) {"contains"in g[0] && !g[0].contains(e.target) && (e.stopPropagation(), g.focus())}

    function d() {
        var t, i = e.data(N, Y);
        null == i ? (B = e.extend({}, X), console && console.log && console.log("Error: cboxElement missing settings object")) : B = e.extend({}, i);
        for (t in B)e.isFunction(B[t]) && "on" !== t.slice(0, 2) && (B[t] = B[t].call(N));
        B.rel = B.rel || N.rel || e(N).data("rel") || "nofollow", B.href = B.href || e(N).attr("href"), B.title = B.title || N.title, "string" == typeof B.href && (B.href = e.trim(B.href))
    }

    function c(i, o) {e(t).trigger(i), st.trigger(i), e.isFunction(o) && o.call(N)}

    function u(i) {
        q || (N = i, d(), k = e(N), z = 0, "nofollow" !== B.rel && (k = e("." + et).filter(function () {
            var t, i = e.data(this, Y);
            return i && (t = e(this).data("rel") || i.rel || this.rel), t === B.rel
        }), z = k.index(N), -1 === z && (k = k.add(N), z = k.length - 1)), w.css({opacity: parseFloat(B.opacity), cursor: B.overlayClose ? "pointer" : "auto", visibility: "visible"}).show(), J && g.add(w).removeClass(J), B.className && g.add(w).addClass(B.className), J = B.className, B.closeButton ? K.html(B.close).appendTo(y) : K.appendTo("<div/>"), U || (U = $ = !0, g.css({visibility: "hidden", display: "block"}), H = o(lt, "LoadedContent", "width:0; height:0; overflow:hidden"), y.css({width: "", height: ""}).append(H), O = x.height() + C.height() + y.outerHeight(!0) - y.height(), _ = b.width() + T.width() + y.outerWidth(!0) - y.width(), D = H.outerHeight(!0), A = H.outerWidth(!0), B.w = h(B.initialWidth, "x"), B.h = h(B.initialHeight, "y"), H.css({width: "", height: B.h}), Q.position(), c(tt, B.onOpen), P.add(L).hide(), g.focus(), B.trapFocus && t.addEventListener && (t.addEventListener("focus", a, !0), st.one(rt, function () {t.removeEventListener("focus", a, !0)})), B.returnFocus && st.one(rt, function () {e(N).focus()})), m())
    }

    function f() {!g && t.body && (V = !1, E = e(i), g = o(lt).attr({id: Y, "class": e.support.opacity === !1 ? Z + "IE" : "", role: "dialog", tabindex: "-1"}).hide(), w = o(lt, "Overlay").hide(), F = e([o(lt, "LoadingOverlay")[0], o(lt, "LoadingGraphic")[0]]), v = o(lt, "Wrapper"), y = o(lt, "Content").append(L = o(lt, "Title"), S = o(lt, "Current"), I = e('<button type="button"/>').attr({id: Z + "Previous"}), R = e('<button type="button"/>').attr({id: Z + "Next"}), M = o("button", "Slideshow"), F), K = e('<button type="button"/>').attr({id: Z + "Close"}), v.append(o(lt).append(o(lt, "TopLeft"), x = o(lt, "TopCenter"), o(lt, "TopRight")), o(lt, !1, "clear:left").append(b = o(lt, "MiddleLeft"), y, T = o(lt, "MiddleRight")), o(lt, !1, "clear:left").append(o(lt, "BottomLeft"), C = o(lt, "BottomCenter"), o(lt, "BottomRight"))).find("div div").css({"float": "left"}), W = o(lt, !1, "position:absolute; width:9999px; visibility:hidden; display:none; max-width:none;"), P = R.add(I).add(S).add(M), e(t.body).append(w, g.append(v, W)))}

    function p() {
        function i(e) {e.which > 1 || e.shiftKey || e.altKey || e.metaKey || e.ctrlKey || (e.preventDefault(), u(this))}

        return g ? (V || (V = !0, R.click(function () {Q.next()}), I.click(function () {Q.prev()}), K.click(function () {Q.close()}), w.click(function () {B.overlayClose && Q.close()}), e(t).bind("keydown." + Z, function (e) {
            var t = e.keyCode;
            U && B.escKey && 27 === t && (e.preventDefault(), Q.close()), U && B.arrowKey && k[1] && !e.altKey && (37 === t ? (e.preventDefault(), I.click()) : 39 === t && (e.preventDefault(), R.click()))
        }), e.isFunction(e.fn.on) ? e(t).on("click." + Z, "." + et, i) : e("." + et).live("click." + Z, i)), !0) : !1
    }

    function m() {
        var n, r, a, u = Q.prep, f = ++at;
        $ = !0, j = !1, N = k[z], d(), c(ht), c(it, B.onLoad), B.h = B.height ? h(B.height, "y") - D - O : B.innerHeight && h(B.innerHeight, "y"), B.w = B.width ? h(B.width, "x") - A - _ : B.innerWidth && h(B.innerWidth, "x"), B.mw = B.w, B.mh = B.h, B.maxWidth && (B.mw = h(B.maxWidth, "x") - A - _, B.mw = B.w && B.w < B.mw ? B.w : B.mw), B.maxHeight && (B.mh = h(B.maxHeight, "y") - D - O, B.mh = B.h && B.h < B.mh ? B.h : B.mh), n = B.href, G = setTimeout(function () {F.show()}, 100), B.inline ? (a = o(lt).hide().insertBefore(e(n)[0]), st.one(ht, function () {a.replaceWith(H.children())}), u(e(n))) : B.iframe ? u(" ") : B.html ? u(B.html) : s(B, n) ? (n = l(B, n), j = t.createElement("img"), e(j).addClass(Z + "Photo").bind("error",function () {B.title = !1, u(o(lt, "Error").html(B.imgError))}).one("load", function () {
            var t;
            f === at && (e.each(["alt", "longdesc", "aria-describedby"], function (t, i) {
                var o = e(N).attr(i) || e(N).attr("data-" + i);
                o && j.setAttribute(i, o)
            }), B.retinaImage && i.devicePixelRatio > 1 && (j.height = j.height / i.devicePixelRatio, j.width = j.width / i.devicePixelRatio), B.scalePhotos && (r = function () {j.height -= j.height * t, j.width -= j.width * t}, B.mw && j.width > B.mw && (t = (j.width - B.mw) / j.width, r()), B.mh && j.height > B.mh && (t = (j.height - B.mh) / j.height, r())), B.h && (j.style.marginTop = Math.max(B.mh - j.height, 0) / 2 + "px"), k[1] && (B.loop || k[z + 1]) && (j.style.cursor = "pointer", j.onclick = function () {Q.next()}), j.style.width = j.width + "px", j.style.height = j.height + "px", setTimeout(function () {u(j)}, 1))
        }), setTimeout(function () {j.src = n}, 1)) : n && W.load(n, B.data, function (t, i) {f === at && u("error" === i ? o(lt, "Error").html(B.xhrError) : e(this).contents())})
    }

    var w, g, v, y, x, b, T, C, k, E, H, W, F, L, S, M, R, I, K, P, B, O, _, D, A, N, z, j, U, $, q, G, Q, J, V, X = {html: !1, photo: !1, iframe: !1, inline: !1, transition: "elastic", speed: 300, fadeOut: 300, width: !1, initialWidth: "600", innerWidth: !1, maxWidth: !1, height: !1, initialHeight: "450", innerHeight: !1, maxHeight: !1, scalePhotos: !0, scrolling: !0, href: !1, title: !1, rel: !1, opacity: .9, preloading: !0, className: !1, overlayClose: !0, escKey: !0, arrowKey: !0, top: !1, bottom: !1, left: !1, right: !1, fixed: !1, data: void 0, closeButton: !0, fastIframe: !0, open: !1, reposition: !0, loop: !0, slideshow: !1, slideshowAuto: !0, slideshowSpeed: 2500, slideshowStart: "start slideshow", slideshowStop: "stop slideshow", photoRegex: /\.(gif|png|jp(e|g|eg)|bmp|ico|webp)((#|\?).*)?$/i, retinaImage: !1, retinaUrl: !1, retinaSuffix: "@2x.$1", current: "image {current} of {total}", previous: "previous", next: "next", close: "close", xhrError: "This content failed to load.", imgError: "This image failed to load.", returnFocus: !0, trapFocus: !0, onOpen: !1, onLoad: !1, onComplete: !1, onCleanup: !1, onClosed: !1}, Y = "colorbox", Z = "cbox", et = Z + "Element", tt = Z + "_open", it = Z + "_load", ot = Z + "_complete", nt = Z + "_cleanup", rt = Z + "_closed", ht = Z + "_purge", st = e("<a/>"), lt = "div", at = 0, dt = {}, ct = function () {
        function e() {clearTimeout(h)}

        function t() {(B.loop || k[z + 1]) && (e(), h = setTimeout(Q.next, B.slideshowSpeed))}

        function i() {M.html(B.slideshowStop).unbind(l).one(l, o), st.bind(ot, t).bind(it, e), g.removeClass(s + "off").addClass(s + "on")}

        function o() {e(), st.unbind(ot, t).unbind(it, e), M.html(B.slideshowStart).unbind(l).one(l, function () {Q.next(), i()}), g.removeClass(s + "on").addClass(s + "off")}

        function n() {r = !1, M.hide(), e(), st.unbind(ot, t).unbind(it, e), g.removeClass(s + "off " + s + "on")}

        var r, h, s = Z + "Slideshow_", l = "click." + Z;
        return function () {r ? B.slideshow || (st.unbind(nt, n), n()) : B.slideshow && k[1] && (r = !0, st.one(nt, n), B.slideshowAuto ? i() : o(), M.show())}
    }();
    e.colorbox || (e(f), Q = e.fn[Y] = e[Y] = function (t, i) {
        var o = this;
        if (t = t || {}, f(), p()) {
            if (e.isFunction(o))o = e("<a/>"), t.open = !0; else if (!o[0])return o;
            i && (t.onComplete = i), o.each(function () {e.data(this, Y, e.extend({}, e.data(this, Y) || X, t))}).addClass(et), (e.isFunction(t.open) && t.open.call(o) || t.open) && u(o[0])
        }
        return o
    }, Q.position = function (t, i) {
        function o() {x[0].style.width = C[0].style.width = y[0].style.width = parseInt(g[0].style.width, 10) - _ + "px", y[0].style.height = b[0].style.height = T[0].style.height = parseInt(g[0].style.height, 10) - O + "px"}

        var r, s, l, a = 0, d = 0, c = g.offset();
        if (E.unbind("resize." + Z), g.css({top: -9e4, left: -9e4}), s = E.scrollTop(), l = E.scrollLeft(), B.fixed ? (c.top -= s, c.left -= l, g.css({position: "fixed"})) : (a = s, d = l, g.css({position: "absolute"})), d += B.right !== !1 ? Math.max(E.width() - B.w - A - _ - h(B.right, "x"), 0) : B.left !== !1 ? h(B.left, "x") : Math.round(Math.max(E.width() - B.w - A - _, 0) / 2), a += B.bottom !== !1 ? Math.max(n() - B.h - D - O - h(B.bottom, "y"), 0) : B.top !== !1 ? h(B.top, "y") : Math.round(Math.max(n() - B.h - D - O, 0) / 2), g.css({top: c.top, left: c.left, visibility: "visible"}), v[0].style.width = v[0].style.height = "9999px", r = {width: B.w + A + _, height: B.h + D + O, top: a, left: d}, t) {
            var u = 0;
            e.each(r, function (e) {return r[e] !== dt[e] ? (u = t, void 0) : void 0}), t = u
        }
        dt = r, t || g.css(r), g.dequeue().animate(r, {duration: t || 0, complete: function () {o(), $ = !1, v[0].style.width = B.w + A + _ + "px", v[0].style.height = B.h + D + O + "px", B.reposition && setTimeout(function () {E.bind("resize." + Z, Q.position)}, 1), i && i()}, step: o})
    }, Q.resize = function (e) {
        var t;
        U && (e = e || {}, e.width && (B.w = h(e.width, "x") - A - _), e.innerWidth && (B.w = h(e.innerWidth, "x")), H.css({width: B.w}), e.height && (B.h = h(e.height, "y") - D - O), e.innerHeight && (B.h = h(e.innerHeight, "y")), e.innerHeight || e.height || (t = H.scrollTop(), H.css({height: "auto"}), B.h = H.height()), H.css({height: B.h}), t && H.scrollTop(t), Q.position("none" === B.transition ? 0 : B.speed))
    }, Q.prep = function (i) {
        function n() {return B.w = B.w || H.width(), B.w = B.mw && B.mw < B.w ? B.mw : B.w, B.w}

        function h() {return B.h = B.h || H.height(), B.h = B.mh && B.mh < B.h ? B.mh : B.h, B.h}

        if (U) {
            var a, d = "none" === B.transition ? 0 : B.speed;
            H.empty().remove(), H = o(lt, "LoadedContent").append(i), H.hide().appendTo(W.show()).css({width: n(), overflow: B.scrolling ? "auto" : "hidden"}).css({height: h()}).prependTo(y), W.hide(), e(j).css({"float": "none"}), a = function () {
                function i() {e.support.opacity === !1 && g[0].style.removeAttribute("filter")}

                var n, h, a = k.length, u = "frameBorder", f = "allowTransparency";
                U && (h = function () {clearTimeout(G), F.hide(), c(ot, B.onComplete)}, L.html(B.title).add(H).show(), a > 1 ? ("string" == typeof B.current && S.html(B.current.replace("{current}", z + 1).replace("{total}", a)).show(), R[B.loop || a - 1 > z ? "show" : "hide"]().html(B.next), I[B.loop || z ? "show" : "hide"]().html(B.previous), ct(), B.preloading && e.each([r(-1), r(1)], function () {
                    var i, o, n = k[this], r = e.data(n, Y);
                    r && r.href ? (i = r.href, e.isFunction(i) && (i = i.call(n))) : i = e(n).attr("href"), i && s(r, i) && (i = l(r, i), o = t.createElement("img"), o.src = i)
                })) : P.hide(), B.iframe ? (n = o("iframe")[0], u in n && (n[u] = 0), f in n && (n[f] = "true"), B.scrolling || (n.scrolling = "no"), e(n).attr({src: B.href, name: (new Date).getTime(), "class": Z + "Iframe", allowFullScreen: !0, webkitAllowFullScreen: !0, mozallowfullscreen: !0}).one("load", h).appendTo(H), st.one(ht, function () {n.src = "//about:blank"}), B.fastIframe && e(n).trigger("load")) : h(), "fade" === B.transition ? g.fadeTo(d, 1, i) : i())
            }, "fade" === B.transition ? g.fadeTo(d, 0, function () {Q.position(0, a)}) : Q.position(d, a)
        }
    }, Q.next = function () {!$ && k[1] && (B.loop || k[z + 1]) && (z = r(1), u(k[z]))}, Q.prev = function () {!$ && k[1] && (B.loop || z) && (z = r(-1), u(k[z]))}, Q.close = function () {U && !q && (q = !0, U = !1, c(nt, B.onCleanup), E.unbind("." + Z), w.fadeTo(B.fadeOut || 0, 0), g.stop().fadeTo(B.fadeOut || 0, 0, function () {g.add(w).css({opacity: 1, cursor: "auto"}).hide(), c(ht), H.empty().remove(), setTimeout(function () {q = !1, c(rt, B.onClosed)}, 1)}))}, Q.remove = function () {g && (g.stop(), e.colorbox.close(), g.stop().remove(), w.remove(), q = !1, g = null, e("." + et).removeData(Y).removeClass(et), e(t).unbind("click." + Z))}, Q.element = function () {return e(N)}, Q.settings = X)
})(jQuery, document, window);;function Airspace() {
    this.visible = {
        'PROHIBITED': false,
        'RESTRICTED': false,
        'DANGER': false,
        'OTHER': false,
        'CTRCTA': false,
        'ALL': false
    };
    this.loaded = {
        'PROHIBITED': false,
        'RESTRICTED': false,
        'DANGER': false,
        'OTHER': false,
        'CTRCTA': false,
        'ALL': false
    };
    this._airspace = [];
    this.varyWithTrack = false;
    this.maximum_base = 7500;
    this.enabled = false;

    this.isLoaded = function (type) {
        return this.loaded[type];
    };

    this.setLoaded = function (type, bool) {
        this.loaded[type] = bool;
    };

    this.isVisible = function (type) {
        if (!type) {
            var no_visibles = true;
            for (type in this.visible) {
                if (this.visible.hasOwnProperty(type) && this.isVisible(type)) {
                    no_visibles = false;
                }
            }
            return !no_visibles;
        }
        return this.visible[type];
    };

    this.setVisible = function (type, bool) {
        if (!type) {
            for (type in this.visible) {
                if (this.visible.hasOwnProperty(type)) {
                    this.setVisible(type, bool);
                }
            }
            if (bool) {
                $('#airspace_tree .all').addClass('visible');
            } else {
                $('#airspace_tree .all').removeClass('visible');
            }
        } else {
            if (bool && !this.isLoaded(type)) {
                this.load(type);
            }
            this.visible[type] = bool;
            if (bool) {
                $('#airspace_tree #' + type).addClass('visible');
            } else {
                $('#airspace_tree #' + type).removeClass('visible');
            }
        }
    };

    this.getType = function (int) {
        switch (int) {
            case 0:
                return 'PROHIBITED';
            case 1:
                return 'RESTRICTED';
            case 2:
                return 'DANGER';
            case 3:
                return 'OTHER';
            case 4:
                return 'CTRCTA';
        }
        return false;
    };

    this.setHeight = function (val) {
        this.maximum_base = val;
    };

    this.loadAll = function (bool) {
        this.load('PROHIBITED');
        this.load('RESTRICTED');
        this.load('OTHER');
        this.load('DANGER');
        this.load('CTRCTA');
        this.visible = {
            'PROHIBITED': bool,
            'RESTRICTED': bool,
            'DANGER': bool,
            'OTHER': bool,
            'CTRCTA': bool,
            'ALL': bool
        };
    };

    var as = [];
    this.reload = function (currentHeight) {
        this._airspace.each(function (airspace, i, ths) {
            if (ths.varyWithTrack && currentHeight !== undefined) {
                if (airspace.level >= currentHeight / 0.3048 || airspace.top <= currentHeight / 0.3048) {
                    airspace.poly.setMap(null);
                    airspace.visible = false;
                } else if (airspace.level >= ths.maximum_base) {
                    airspace.poly.setMap(null);
                    airspace.visible = false;
                }
                return;
            }
            var c = airspace._class;
            if (!ths.isVisible(c) || airspace.level >= ths.maximum_base) {
                if (airspace.visible) {
                    airspace.poly.setMap(null);
                    airspace.visible = false;
                }
            } else if (!airspace.visible) {
                airspace.poly.setMap(map.internal_map);
                airspace.visible = true;
            }
        }, this);
    };

    this.toggle = function (type) {
        type = this.getType(type);
        this.setVisible(type, !this.isVisible(type));
        this.reload();
    }

    this.add = function (airClass, flightLevel, top, points, strokeWeight, strokeColour, strokeOpacity, fillColour, fillOpacity, name) {
        var polygon = new google.maps.Polygon({
            strokeColor: strokeColour,
            strokeWeight: strokeWeight,
            clickable: true,
            strokeOpacity: strokeOpacity,
            path: google.maps.geometry.encoding.decodePath(points),
            fillColor: fillColour,
            fillOpacity: fillOpacity,
            zIndex: (185 - flightLevel),
            title: name});
        this._airspace.push({poly: polygon, _class: airClass, level: flightLevel, top: top, visible: false});
    }
}
;var map;
var main_scrollpane;
var throttleTimeout;
var $body;
var planner_string = planner_string || false;

$(document).ready(function () {
    map = new UKNXCL_Map($("#map_wrapper"));
    if (typeof google != 'undefined') {
        map.load_map();
        if(planner_string) {
            map.callback(function() {
                setTimeout(function() {
                    map.planner.load_string(planner_string);
                },300);
            });
        }
    } else {
        $('#map').children('p.loading').html('Google maps are unavailable');
        $("#map_interface_3d span.toggle").hide();
        $("#map_interface").hide();
    }
    map.resize();

    $body = $("body");

    reload_scrollpane();
    page_handeler.defaults.complete.push('reload_scrollpane')

    $.fn.ajax_factory.defaults.complete.push('center_colorbox');


    $body.on('change', 'input[name=flights]', function () {
        map.swap(map.kmls[$(this).val()]);
    });

    $body.on('click', '.kmltree .toggler', function (event) {
        kmlPath = new UKNXCL_Map.KmlPath(event, $(this));
        if (kmlPath.load()) {
            kmlPath.toggle();
        }
    });

    $body.on('click', '.kmltree .expander', function () {
        var $li = $(this).parent();
        if ($li.hasClass('open')) {
            $li.removeClass('open');
            $li.find('li').removeClass('open');
        } else {
            $li.addClass('open');
            $li.find('li').addClass('open');
        }
    });

    $(document).bind('cbox_complete', 'center_colorbox');
});

function center_colorbox() {
    $.fn.colorbox.resize();
    var $cb = $('#colorbox');
    var width = $cb.width();
    if (width < 725) {
        $cb.animate({left: (725 - width) / 2});
    } else {
        $cb.animate({left: 0});
    }
}

function reload_scrollpane() {
    //if ($body.width() < 750) {
    //    if (main_scrollpane) {
    //        main_scrollpane.destroy();
    //    }
    //} else if (main_scrollpane) {
    //    main_scrollpane.reinitialise();
    //} else {
    //    main_scrollpane = $("#main_wrapper").jScrollPane().data('jsp');
    //}
}

window.onresize = function () {
    if (!throttleTimeout) {
        throttleTimeout = setTimeout(function () {
            if (map) {
                map.resize();
            }
            if ($('#colorbox').width()) {
                center_colorbox();
            }
            reload_scrollpane();
            throttleTimeout = null;
        }, 50);
    }
};

page_handeler.toggle_page = function ($page) {
    if ($page.css('z-index') != 2) {
        var $main = $('#main');
        $page.css({left:800, top:0, position: "absolute"});
        $main.stop(true, true).animate({left: -800}, 200, 'linear', function () {
            var $children = $main.children('div');
            $children.addClass('remove');
            $page.show().removeClass('remove');
            $main.children('div.remove').remove();
            $main.css({left:0});
            $page.css({left:0, position:"relative"});
            $("a").removeClass('sel').parent('li').removeClass('sel');
            var $links = $('a[href="' + $page.data('url') + '"]');
            $links.addClass('sel').parent('li').addClass('sel');
            $main.animate({scrollTo: 0});
            if (page_handeler.defaults.complete) {
                page_handeler.defaults.complete.each(function (method) {
                    if (typeof method == 'function') {
                        method();
                    } else {
                        window[method]();
                    }
                });
            }
        });
    }
};

/* ===========================================================
 * Bootstrap: fileinput.js v3.1.3
 * http://jasny.github.com/bootstrap/javascript/#fileinput
 * ===========================================================
 * Copyright 2012-2014 Arnold Daniels
 *
 * Licensed under the Apache License, Version 2.0 (the "License")
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */

+function ($) {

    var isIE = window.navigator.appName == 'Microsoft Internet Explorer'

    // FILEUPLOAD PUBLIC CLASS DEFINITION
    // =================================

    var Fileinput = function (element, options) {
        this.$element = $(element)

        this.$input = this.$element.find(':file')
        if (this.$input.length === 0) return

        this.name = this.$input.attr('name') || options.name

        this.$hidden = this.$element.find('input[type=hidden][name="' + this.name + '"]')
        if (this.$hidden.length === 0) {
            this.$hidden = $('<input type="hidden">').insertBefore(this.$input)
        }

        this.$preview = this.$element.find('.fileinput-preview')
        var height = this.$preview.css('height')
        if (this.$preview.css('display') !== 'inline' && height !== '0px' && height !== 'none') {
            this.$preview.css('line-height', height)
        }

        this.original = {
            exists: this.$element.hasClass('fileinput-exists'),
            preview: this.$preview.html(),
            hiddenVal: this.$hidden.val()
        }

        this.listen()
    }

    Fileinput.prototype.listen = function() {
        this.$input.on('change.bs.fileinput', $.proxy(this.change, this))
        $(this.$input[0].form).on('reset.bs.fileinput', $.proxy(this.reset, this))

        this.$element.find('[data-trigger="fileinput"]').on('click.bs.fileinput', $.proxy(this.trigger, this))
        this.$element.find('[data-dismiss="fileinput"]').on('click.bs.fileinput', $.proxy(this.clear, this))
    },

        Fileinput.prototype.change = function(e) {
            var files = e.target.files === undefined ? (e.target && e.target.value ? [{ name: e.target.value.replace(/^.+\\/, '')}] : []) : e.target.files

            e.stopPropagation()

            if (files.length === 0) {
                this.clear()
                return
            }

            this.$hidden.val('')
            this.$hidden.attr('name', '')
            this.$input.attr('name', this.name)

            var file = files[0]

            if (this.$preview.length > 0 && (typeof file.type !== "undefined" ? file.type.match(/^image\/(gif|png|jpeg)$/) : file.name.match(/\.(gif|png|jpe?g)$/i)) && typeof FileReader !== "undefined") {
                var reader = new FileReader()
                var preview = this.$preview
                var element = this.$element

                reader.onload = function(re) {
                    var $img = $('<img>')
                    $img[0].src = re.target.result
                    files[0].result = re.target.result

                    element.find('.fileinput-filename').text(file.name)

                    // if parent has max-height, using `(max-)height: 100%` on child doesn't take padding and border into account
                    if (preview.css('max-height') != 'none') $img.css('max-height', parseInt(preview.css('max-height'), 10) - parseInt(preview.css('padding-top'), 10) - parseInt(preview.css('padding-bottom'), 10)  - parseInt(preview.css('border-top'), 10) - parseInt(preview.css('border-bottom'), 10))

                    preview.html($img)
                    element.addClass('fileinput-exists').removeClass('fileinput-new')

                    element.trigger('change.bs.fileinput', files)
                }

                reader.readAsDataURL(file)
            } else {
                this.$element.find('.fileinput-filename').text(file.name)
                this.$preview.text(file.name)

                this.$element.addClass('fileinput-exists').removeClass('fileinput-new')

                this.$element.trigger('change.bs.fileinput')
            }
        },

        Fileinput.prototype.clear = function(e) {
            if (e) e.preventDefault()

            this.$hidden.val('')
            this.$hidden.attr('name', this.name)
            this.$input.attr('name', '')

            //ie8+ doesn't support changing the value of input with type=file so clone instead
            if (isIE) {
                var inputClone = this.$input.clone(true);
                this.$input.after(inputClone);
                this.$input.remove();
                this.$input = inputClone;
            } else {
                this.$input.val('')
            }

            this.$preview.html('')
            this.$element.find('.fileinput-filename').text('')
            this.$element.addClass('fileinput-new').removeClass('fileinput-exists')

            if (e !== undefined) {
                this.$input.trigger('change')
                this.$element.trigger('clear.bs.fileinput')
            }
        },

        Fileinput.prototype.reset = function() {
            this.clear()

            this.$hidden.val(this.original.hiddenVal)
            this.$preview.html(this.original.preview)
            this.$element.find('.fileinput-filename').text('')

            if (this.original.exists) this.$element.addClass('fileinput-exists').removeClass('fileinput-new')
            else this.$element.addClass('fileinput-new').removeClass('fileinput-exists')

            this.$element.trigger('reset.bs.fileinput')
        },

        Fileinput.prototype.trigger = function(e) {
            this.$input.trigger('click')
            e.preventDefault()
        }


    // FILEUPLOAD PLUGIN DEFINITION
    // ===========================

    var old = $.fn.fileinput

    $.fn.fileinput = function (options) {
        return this.each(function () {
            var $this = $(this),
                data = $this.data('bs.fileinput')
            if (!data) $this.data('bs.fileinput', (data = new Fileinput(this, options)))
            if (typeof options == 'string') data[options]()
        })
    }

    $.fn.fileinput.Constructor = Fileinput


    // FILEINPUT NO CONFLICT
    // ====================

    $.fn.fileinput.noConflict = function () {
        $.fn.fileinput = old
        return this
    }


    // FILEUPLOAD DATA-API
    // ==================

    $(document).on('click.fileinput.data-api', '[data-provides="fileinput"]', function (e) {
        var $this = $(this)
        if ($this.data('bs.fileinput')) return
        $this.fileinput($this.data())

        var $target = $(e.target).closest('[data-dismiss="fileinput"],[data-trigger="fileinput"]');
        if ($target.length > 0) {
            e.preventDefault()
            $target.trigger('click.bs.fileinput')
        }
    })

}(window.jQuery);
;function Graph($container) {
    this.$container = $container;
    this.$a_canvas = null;
    this.width = this.$container.width();
    this.height = this.$container.height();
    this.obj = null;
    this.type = 0;
    this.options = {};
    this.options.toggles = [
        {"name": "Height", "index": 1, "xAxis": "Height (m)", "min_value": "min_ele", "max_value": "max_ele"},
        {"name": "Climb Rate", "index": 2, "xAxis": "Climb Rate (m/s)", "min_value": "min_cr", "max_value": "max_cr"},
        {"name": "Speed", "index": 3, "xAxis": "Speed (m/s)", "min_value": "min_speed", "max_value": "max_speed"}
    ];
    this.$container.css({position: "relative"});
    this.$container.html("<canvas class='graph_a_canvas' style='height:100%;width:100%' width='1000' height='" + this.height + "'></canvas>");
    this.$a_canvas = this.$container.find('.graph_a_canvas');
    this.$container.hide();
    this.initiated = true;
    this.legend = {
        show: false,
        position: {
            x: "right",
            y: "top"
        }
    };
    this.grid = {
        x: {
            show: true,
            count: 20
        },
        y: {
            show: true,
            count: 10
        }
    };
    this.initiated = false;
}

Graph.prototype.add_radios = function () {
    this.$container.find('.graph_toggles').remove();
    if (this.options.toggles) {
        var html = '<span class="graph_toggles">';
        this.options.toggles.each(function (toggle, count, ths) {
            html += '<label><input type="radio" name="graph_type" data-type="' + count + '" ' + (count == ths.type ? 'checked' : '' ) + '/>' + toggle.name + '</label>';
        }, this);
        html += '</span>';
        this.$container.prepend(html);
    }
    var ths = this;
    this.$container.find("[name=graph_type]").change(function () {
        ths.changeType($(this).data('type'));
    });
};
Graph.prototype.set_subsets = function (options) {
    this.options.toggles = options;
    if (this.initiated) {
        this.add_radios();
    }
};
Graph.prototype.resize = function (width) {
    if (this.initiated) {
        this.width = width - 5;
        this.$a_canvas[0].width = (width);
        this.setGraph();
    }
};
Graph.prototype.swap = function (obj) {
    this.obj = obj;
    this.setGraph();
};
Graph.prototype.changeType = function (val) {
    this.type = val;
    this.setGraph();
};
Graph.prototype.setGraph = function () {
    if (this.obj === null) {
        this.$container.hide();
        return;
    }
    this.$container.show();
    var title = '';
    var index = '';
    var min_value = 0;
    var max_value = 0;
    if (typeof this.options.toggles[this.type] != 'undefined') {
        var title = this.options.toggles[this.type].xAxis;
        var index = this.options.toggles[this.type].index;
        var min_value = this.options.toggles[this.type].min_value;
        var max_value = this.options.toggles[this.type].max_value;
    }
    var max = -1000000;
    var min = 10000000;
    this.obj.nxcl_data.track.each(function (track) {
        if (track.draw_graph) {
            if (parseFloat(track[max_value]) > max) {
                max = parseFloat(track[max_value]);
            }
            if (parseFloat(track[min_value]) < min) {
                min = parseFloat(track[min_value]);
            }
        }
    });
    this.draw_graph(max.roundUp(), min.roundDown(), '#' + this.obj.nxcl_data.track[0].colour, index, title);
};
Graph.prototype.addLegend = function (colour, obj) {
    this.$container.find('.legend').remove();
    if (this.legend.show) {
        var html = '';
        obj.track.each(function (track) {
            colour = track.colour || colour;
            html += '<span class="legend_entry" style="display: block">' + track.name + '<span class="line" style="display:inline-block; margin-left:10px; width:7px; height:2px; margin-bottom: 5px; background:#' + colour + ';text-indent:-999px;overflow:hidden">' + colour + '</span></span>';
        });
        this.$container.prepend('<div class="legend" style="background-color:#ffffff; padding: 4px; border: 1px solid #EEEEEE; position: absolute;' + this.legend.position.x + ':10px;' + this.legend.position.y + ':10px;">' + html + '</div>');
    }
};

Graph.prototype.draw_graph = function (max, min, colour, index, text) {
    // Get graph data;
    if (this.obj && this.obj.nxcl_data.track.length) {
        this.$container.show();
        this.$a_canvas[0].width = this.$a_canvas.width();
        this.width = this.$container.width();
        this.height = this.$container.height();
        var context = this.$a_canvas[0].getContext('2d');
        context.fillStyle = "rgba(255, 255, 255, 0.7)";
        context.fillRect(0, 0, this.width, this.height);

        if (this.grid.x.show) {
            for (var x1 = 0; x1 <= (this.grid.x.count - 1); x1++) {
                var x_coord = x1 * this.width / (this.grid.x.count - 1);
                context.moveTo(x_coord, 0);
                context.lineTo(x_coord, this.height);
            }
        }
        if (this.grid.y.show) {
            for (var y1 = 0; y1 <= (this.grid.y.count - 1); y1++) {
                var y_coord = y1 * this.height / (this.grid.y.count - 1);
                context.moveTo(0, y_coord);
                context.lineTo(this.width, y_coord);
            }
        }
        context.strokeStyle = '#DBDBDB';
        context.stroke();
        var obj = this.obj.nxcl_data
        this.addLegend(colour, obj);
        var Xscale = this.width / (obj.xMax - obj.xMin);
        var Yscale = this.height / (max - min);
        if (obj.track.count) {
            obj.track.each(function (track, count, ths) {
                if (track.draw_graph) {
                    context.beginPath();
                    context.strokeStyle = track.colour ? ('#' + track.colour) : colour;
                    for (j in track.data) {
                        var coord = track.data[j];
                        context.lineTo(coord[0] * Xscale, ths.height - ((parseInt(coord[index]) - min) * Yscale));
                    }
                    context.stroke();
                }
            }, this);
        } 
        context.font = '12px sans-serif';
        context.fillStyle = '#444';
        context.fillText(max, 10, 15);
        context.fillText(min, 10, this.height - 5);
        context.fillText(text, 10, this.height / 2);
        this.add_radios();
    } else {
        this.$container.hide();
    }
}

Number.prototype.roundDown = function (significant) {
    significant = significant || 2;
    var power = Math.floor(Math.log(this) / Math.LN10);
    if (power > significant) {
        var new_number = (this / Math.pow(10, power - significant));
        return (this < 0 ? Math.ceil(new_number) : Math.floor(new_number)) * Math.pow(10, power - significant);
    }
    return Math.floor(this);
};

Number.prototype.roundUp = function (significant) {
    significant = significant || 2;
    var power = Math.floor(Math.log(this) / Math.LN10);
    if (power > significant) {
        var new_number = (this / Math.pow(10, power - significant));
        return (this > 0 ? Math.ceil(new_number) : Math.floor(new_number)) * Math.pow(10, power - significant);
    }
    return Math.ceil(this);
};;/*global
 google:true,
 Planner:true,
 Airspace:true,
 Graph:true,
 geoXML3:true,
 JQuery:true,
 slider:true,
 */
function UKNXCL_Map($container) {
    this.MAP = 1;
    this.EARTH = 2;

    this.initialised = false;

    this.planner = new Planner(this);
    this.airspace = new Airspace();
    this.graph = new Graph($('#graph_wrapper'));
    if (typeof google != 'undefined') {
        this.internal_map = new google.maps.Map(document.getElementById('map'), {
            zoom: 7,
            center: new google.maps.LatLng(53, -2),
            mapTypeId: google.maps.MapTypeId.TERRAIN,
            streetViewControl: false
        });
    }

    this._callbacks = [];
    this.callback = function (callable) {
        if (!this.initialised) {
            this._callbacks = callable;
            return true;
        }
        if (typeof callable == 'function') {
            callable(this)
        } else {
            window[callable](this);
        }
    };

    this.$container = $container;
    this.$body = $('body');
    //this.$slider = $('#slider');
    this.$tree = $('#tree_content');
    this.mode = this.EARTH;
    /*@param {google.earth}*/
    this.ge = null;
    this.map = null;
    this.obj = null;
    this.kmls = [];
    this.drawRadius = false;
    this.timer = 0;
    this.playCycles = 10;
    this.playCount = 0;
    this.comp = null;

    var $interface = $('#map_interface_3d');
    $interface.find('span.show').click(function () {
        $("body").addClass('interface-visible');
    });
    $interface.find('span.hide').click(function () {
        $("body").removeClass('interface-visible');
    });
    this.resize = function () {
        var pageWidth = this.$body.width();
        var pageHeight = this.$body.height();
        if (pageWidth < 730) {
            this.$container.hide();
        } else {
            this.$container.show();
        }
        this.$container.css({width: pageWidth - 727});
        this.graph.resize(pageWidth - 745);
        $('#main_wrapper').css({'height': pageHeight - 35});
    };

    this.load_map = function () {
        map.mode = map.MAP;
        var $map = $('#map').hide();
        var $earth = $('#map3d').show();
        $earth.hide();
        $map.children('p.loading').remove();
        $map.show().css({display: 'block'});
        google.maps.event.addListener(this.internal_map, 'click', function (event) {
            if (map.planner.enabled) {
                var latlon = event.latLng;
                map.planner.add_marker(latlon.lat(), latlon.lng());
            }
        });

        this.radiusCircle = new google.maps.Circle({
            center: new google.maps.LatLng(0, 0),
            radius: 0,
            map: this.internal_map,
            strokeColor: "#FFFFFF",
            strokeOpacity: 1,
            zIndex: 1
        });

        this.GeoXMLsingle = new geoXML3.Parser({
            map: this.internal_map,
            singleInfoWindow: true,
            processStyles: true,
            afterParse: function (doc) {
                var path = doc[0].url.split('&id=');
                if ((path[1].isNumber())) {
                    map.kmls[path[1]].google_data = doc[0];
                    map.kmls[path[1]].is_ready();
                }
            }
        });
        this.GeoXMLcomp = new geoXML3.Parser({
            map: this.internal_map,
            singleInfoWindow: true,
            afterParse: function (doc) {
                map.comp.google_data = doc[0];
                map.comp.is_ready();
            }
        });
        this.initialised = true;
        this._callbacks.each(function (callable) {
            if (typeof callable == 'function') {
                callable(this);
            } else {
                window[callable](this);
            }
        });
    };

    this.swap = function (obj) {
        if (this.obj) { this.obj.hide(); }
        obj.show();
        obj.center();

        if (obj.type === 0 && map.isMap()) {
            //this.internal_map.fitBounds(obj.get_bounds());
        }
        //this.$slider.slider({max: obj.size()});
        this.graph.swap(obj);
        $('#airspace').hide();
        this.obj = obj;
    };

    this.move = function (value) {
        if (this.obj !== null) {
            value = parseInt(value, 10);
            this.obj.move_marker(value);
            this.setTime(value);
        }
    };

    this.parseKML = function (url, caller) {
        map.caller = caller;
        google.earth.fetchKml(this.ge, 'http://' + window.location.hostname + '/' + url, function (kmlObject) {
            if (!kmlObject) { alert('Error loading KML'); }
            map.ge.getFeatures().appendChild(kmlObject);
            kmlObject.setVisibility(true);
            map.caller.google_data = {root: kmlObject};
            map.caller.is_ready();
            delete map.caller;
        });
    };

    this.center = function (object) {
        if (typeof object.center == 'function') {
            object.center();
        } else {
            if (this.isMap()) {
                var bound = new google.maps.LatLngBounds();
                object.each(function (latLng) {
                    if (typeof latLng.toLatLng == 'function') {
                        bound.union(new google.maps.LatLngBounds(latLng.toLatLng(), latLng.toLatLng()));
                    } else {
                        bound.union(new google.maps.LatLngBounds(latLng, latLng));
                    }
                });
                this.internal_map.fitBounds(bound);
            }
        }
    }

    this.isMap = function () {
        return this.mode == this.MAP;
    }

    this.isEarth = function () {
        return this.mode == this.EARTH;
    }

    this.load_airspace = function () {
        if (this.isMap()) {
            $.fn.ajax_factory('\\object\\airspace', 'load_js');
        } else {
            this.parseKML('/resources/airspace.kmz', this.airspace);
        }
        $(".load_airspace").remove();
        $("#tree_content").prepend('<div id="airspace_tree" class=\'kmltree new\'><ul class=\'kmltree\'>' + '<li data-path=\'{"type":"airspace","path":[]}\' class=\'kmltree-item check KmlFolder visible open all\'><div class=\'expander\'></div><div class=\'toggler\'></div>Airspace<ul>' + '<li id="PROHIBITED" data-path=\'{"type":"airspace","path":[0]}\' class=\'kmltree-item check KmlFolder hideChildren visible\'><div class=\'expander\'></div><div class=\'toggler\'></div>Prohibited</li>' + '<li id="RESTRICTED" data-path=\'{"type":"airspace","path":[1]}\' class=\'kmltree-item check KmlFolder hideChildren visible\'><div class=\'expander\'></div><div class=\'toggler\'></div>Restricted</li>' + '<li id="DANGER" data-path=\'{"type":"airspace","path":[2]}\' class=\'kmltree-item check KmlFolder hideChildren visible\'><div class=\'expander\'></div><div class=\'toggler\'></div>Danger</li>' + '<li id="OTHER" data-path=\'{"type":"airspace","path":[3]}\' class=\'kmltree-item check KmlFolder hideChildren visible\'><div class=\'expander\'></div><div class=\'toggler\'></div>Other</li>' + '<li id="CTRCTA" data-path=\'{"type":"airspace","path":[4]}\' class=\'kmltree-item check KmlFolder hideChildren visible\'><div class=\'expander\'></div><div class=\'toggler\'></div>CTR/CTA</li>' + '</ul></li></ul></div>');
        return false;
    };

    this.setTime = function (index) {
        var timeInSecs = index * (this.obj.nxcl_data.xMax - this.obj.nxcl_data.xMin) / this.obj.size();
        var hours = Math.floor(timeInSecs / 3600);
        var min = Math.floor((timeInSecs - hours * 3600) / 60);
        var sec = Math.floor(timeInSecs - hours * 3600 - min * 60);
        if (min < 10) {
            min = '0' + min;
        }
        if (sec < 10) {
            sec = '0' + sec;
        }
        $('#Time').html(hours + ":" + min + ":" + sec);
    };

    this.play = function () {
        clearTimeout(this.timer);
        this.playCount = 0;
        this.playCycles = this.obj.size() / 100;
        this.playing();
    };

    this.pause = function () {
        clearTimeout(this.timer);
    };

    this.clear = function() {
        this.kmls.each(function(flight) {
            flight.setMap(null);
        });
    }

    this.playing = function () {
        if (this.playCount < this.obj.size() - this.playCycles) {
            this.move(this.playCount += this.playCycles);
        } else {
            this.move(this.obj.size());
            clearTimeout(this.timer);
            return;
        }
        this.timer = setTimeout(function () {map.playing();}, 100);
    };

    this.add_flight_coordinates = function (coordinate_string, id) {
        this.$tree.find('.track_' + id).remove();
        this.$tree.append('<div class="track_' + id + '"><div class="kmltree" data-post=\'{"id":' + id + '}\'><ul class="kmltree"><li data-path=\'{"type":"coordinates","path":[]}\' class="kmltree-item check KmlFolder visible closed open"><div class="toggler"></div>Flight ' + id + '<ul><li data-path=\'{"type":"flight","path":[0]}\' class="kmltree-item check KmlFolder visible open"></li></ul></div></div>');
        var lat_lng_array = [];
        var coordinates = coordinate_string.split(';');

        var type = 'od';
        if(coordinates.length == 4 && coordinates[0] === coordinates[3]) {
            type = 'tr';
        } else if (coordinates.length == 3 && coordinates[0] === coordinates[2]) {
            type = 'or';
        }

        coordinates.each(function (os, i) {
            var coordinate = new Coordinate();
            coordinate.set_from_OS(os);
            lat_lng_array[i] = new google.maps.LatLng(coordinate.lat(), coordinate.lng());
        });
        this.draw_coordinates(lat_lng_array, id, type);
    };

    this.draw_coordinates = function (coordinates, id, type) {
        if (this.isMap()) {
            if (this.mapObject) {
                this.mapObject.setMap(null);
            }
            this.kmls[id] = new google.maps.Polyline({
                path: coordinates,
                strokeColor: (type == 'od' ? "000000" : (type == 'or' ? 'FF0000' : '00FF00')),
                strokeOpacity: 1,
                strokeWeight: 1.4
            });
            this.kmls[id].setMap(this.internal_map);
            this.center(coordinates);
        } else {
            if (!this.mapObject) {
                var lineStringPlacemark = this.parent.ge.createPlacemark('');
                this.mapObject = this.parent.ge.createLineString('');
                lineStringPlacemark.setGeometry(this.mapObject);
                this.mapObject.setTessellate(true);
                this.parent.ge.getFeatures().appendChild(lineStringPlacemark);
                lineStringPlacemark.setStyleSelector(this.parent.ge.createStyle(''));
                lineStringPlacemark.getStyleSelector().getLineStyle().setColor('FFFFFF');
            }
            this.mapObject.getCoordinates().clear();
            this.coordinates.each(function (coordinate, i, context) {
                context.mapObject.getCoordinates().pushLatLngAlt(coordinate.lat(), coordinate.lng(), 0);
            });
        }
    }

    this.add_flight = function (id, airspace, reload_flight, temp, split) {
        this.$tree.find('.track_' + id).remove();
        this.$tree.append('<div class="track_' + id + '"></div>');
        if (this.kmls[id] === undefined || reload_flight) {
            this.kmls[id] = new Track(id, temp, split || false);
            this.kmls[id].load();
        } else {
            this.swap(map.kmls[id]);
        }
    };

    this.add_comp = function (id) {
        $('#comp_list').prepend('<div class="loading_shroud">Loading...</div>');
        if (this.comp !== null) {
            this.comp.remove();
        }
        this.$tree.find('.comp_' + id).remove();
        this.$tree.append('<div class="comp_' + id + '"></div>');
        this.comp = new Comp(id);
    };

    this.remove = function (id) {
        this.kmls[id].remove();
        if (this.obj.id === id) {
            Graph.setGraph(null);
        }
    };

    this.load_earth = function () {
        //google.load("earth", "1", {'callback': 'map.init_earth'});
    };

    this.init_earth = function () {
        $('#map').hide();
        var $earth = $('#map3d').show().css({display: 'block'});
        /*        google.earth.createInstance('map3d', function (instance) {
         $earth.children('p.loading').remove();
         map.mode = map.EARTH;
         map.ge = instance;
         map.ge.getWindow().setVisibility(true);
         map.ge.getNavigationControl().setVisibility(map.ge.VISIBILITY_AUTO);
         map.ge.getLayerRoot().enableLayerById(map.ge.LAYER_ROADS, true);
         var la = map.ge.createLookAt('');
         la.set(52, -2, 5, map.ge.ALTITUDE_RELATIVE_TO_GROUND, 0, 0, 500000);
         map.ge.getView().setAbstractView(la);
         google.earth.addEventListener(map.ge.getGlobe(), 'mousedown', function (event) {
         map._earth_drag = false;
         });
         google.earth.addEventListener(map.ge.getGlobe(), 'mousemove', function (event) {
         map._earth_drag = true;
         });
         google.earth.addEventListener(map.ge.getGlobe(), 'mouseup', function (event) {
         if (map.planner.enabled) {
         if (!map._earth_drag && !map.dragInfo) {
         map.planner.addWaypoint(event.getLatitude(), event.getLongitude(), 'http://maps.google.com/intl/en_us/mapfiles/ms/micons/blue.png');
         }
         map.planner.writeplanner();
         map.planner.draw();
         }
         map.dragInfo = null;
         map.mousedown = false;
         });
         $earth.css({display: 'block'});
         if (map.callback) {
         map.callback();
         }
         }, function () {*/
        map.load_map();
        //});
    };
    this.resize();
}


function Track(id, temp, split) {
    var ths = this;
    this.type = 0;
    this.id = id;
    this.google_data = null;
    this.nxcl_data = null;
    this.loaded = false;
    this.visible = true;
    this.temp = temp ? '&temp=true' : '';
    this.split = split ? '&split=true' : '';

    this.add_google_data = function () {
        if (map.isMap()) {
            map.GeoXMLsingle.parse('?module=\\object\\flight&act=download&type=kml' + this.temp + this.split + '&id=' + this.id, null, this.id);
        } else {
            map.parseKML('/uploads/flight/' + this.temp + this.id + '/track_earth.kmz', this);
        }
    };

    this.center = function () {
        if (map.isEarth()) {
            var lookAt = map.ge.createLookAt('');
            lookAt.setLatitude(this.nxcl_data.bounds.center.lat);
            lookAt.setLongitude(this.nxcl_data.bounds.center.lon);
            lookAt.setRange(this.nxcl_data.bounds.range);
            map.ge.getView().setAbstractView(lookAt)
        } else {

        }
    };

    this.add_nxcl_data = function (callback) {
        $.ajax({
            url: '?module=\\object\\flight&act=get_js&id=' + this.id,
            context: this,
            cache: false,
            type: 'POST',
            dataType: 'json',
            success: function (result) {
                this.nxcl_data = new trackData(); 
                this.nxcl_data.loadFromAjax(result);
                callback();
            }
        });
    };

    this.add_marker = function () {
        if (map.isMap()) {
            this.marker = new google.maps.Marker({
                position: new google.maps.LatLng(this.nxcl_data.track[0].coords[0].lat, this.nxcl_data.track[0].coords[0].lng),
                map: map.internal_map,
                cursor: this.nxcl_data.track[0].pilot,
                title: this.nxcl_data.track[0].pilot,
                icon: "../img/Markers/" + this.nxcl_data.track[0].colour + "-" + ( this.nxcl_data.track[0].pilot[0] || 'a' ) + ".png"
            });
        }
    };

    this.is_ready = function () {
        if (this.nxcl_data && this.google_data) {
            this.loaded = true;
            this.add_marker();
            $('#tree_content .track_' + this.id).html(map.isMap() ? this.nxcl_data.html : this.nxcl_data.html_earth);
            map.swap(this);
        }
    };

    this.show = function () {
        if (map.isMap()) {
            this.marker.setMap(map.internal_map);
            this.google_data.gpolylines.each(function (polyline) {
                polyline.setMap(map.internal_map);
            });
        }
        this.center();
        this.visible = true;
        map.graph.setGraph();
    };

    this.hide = function () {
        if (map.isMap()) {
            this.marker.setMap(null);
            this.google_data.gpolylines.each(function (polyline) {
                polyline.setMap(null);
            });
            map.graph.setGraph();
        }
    };

    this.remove = function (depth) {
        this.hide();
    };

    this.get_bounds = function () {
        return this.google_data.bounds;
    };

    this.size = function () {
        return this.nxcl_data.track[0].coords.length - 1;
    };

    this.move_marker = function (pos) {
        this.marker.setPosition(new google.maps.LatLng(this.nxcl_data.track[0].coords[pos].lat, this.nxcl_data.track[0].coords[pos].lng));
        if (map.drawRadius) {
            map.radiusCircle.setCenter(new google.maps.LatLng(this.nxcl_data.track[0].coords[pos].lat, this.nxcl_data.track[0].coords[pos].lng));
            map.radiusCircle.setRadius(400);
        } else {
            map.radiusCircle.setRadius(0);
        }
        if (map.airspace.varyWithTrack) {
            this.airspace.reload(this.nxcl_data.track[0].coords[pos].ele);
        }
    };

    this.toggle_track = function (id, bool) {
        id++;
        if (bool) {
            this.google_data.gpolylines[id].setMap(map.internal_map);
        } else {
            this.google_data.gpolylines[id].setMap(null);
        }
    };

    this.load = function () {
        this.add_google_data();
        this.add_nxcl_data(function () {ths.is_ready()});
    }
}

function Comp(id) {
    // variables
    this.type = 1;
    this.id = id;
    this.google_data = null;
    this.nxcl_data = new trackData();
    this.loaded = false;
    this.visible = true;
    this.marker = [];
    this.temp = '';

    this.add_google_data = function () {
        if (map.isMap()) {
            map.GeoXMLcomp.parse('?module=\\module\\comps\\object\\comp&act=download&type=kmz' + this.temp + '&id=' + this.id, null, this.id);
            this.google_data = true;
            this.is_ready();
        } else {
            map.parseKML('/uploads/comp/' + this.id + '/track_earth.kmz', this);
        }
    };

    this.add_nxcl_data = function () {
        $.ajax({
            url: '?module=\\module\\comps\\object\\comp&act=get_js&id=' + this.id,
            context: this,
            cache: false,
            async: true,
            type: 'POST',
            dataType: 'json',
            success: function (result) {
                this.nxcl_data.loadFromAjax(result);
                this.is_ready();
            }
        });
    };

    this.add_marker = function () {
        if (map.isMap()) {
            this.nxcl_data.track.each(function (track, a, root) {
                root.marker[a] = new google.maps.Marker({
                    position: new google.maps.LatLng(track.coords[0].lat, track.coords[0].lng),
                    map: map.internal_map,
                    cursor: track.pilot,
                    title: track.pilot,
                    icon: "../img/Markers/" + track.colour + "-" + track.pilot[0] + ".png"
                });
            }, this);
        }
    };

    this.is_ready = function () {
        if (this.nxcl_data.loaded && this.google_data) {
            this.loaded = true;
            this.add_marker();
            $('#WriteHereComp').html(map.isMap() ? this.nxcl_data.html : this.nxcl_data.html);
            $('#comp_list .loading_shroud').remove();
            map.swap(this);
        }
    };

    this.center = function () {
        if (map.isEarth()) {
            var lookAt = map.ge.createLookAt('');
            lookAt.setLatitude(this.nxcl_data.bounds.center.lat);
            lookAt.setLongitude(this.nxcl_data.bounds.center.lon);
            lookAt.setRange(this.nxcl_data.bounds.range);
            map.ge.getView().setAbstractView(lookAt)
        } else {

        }
    };

    this.show = function () {
        this.nxcl_data.track.each(function (track) {
            track.draw_graph = true;
        });
        this.marker.each(function (marker) {
            marker.setMap(map.internal_map);
        });
        if (map.isMap()) {
            this.google_data.gpolylines.each(function (element) {
                element.setMap(map.internal_map);
            });
            this.google_data.gpolygons.each(function (element) {
                element.setMap(map.internal_map);
            });
            this.visible = true;
            map.graph.setGraph();
        }
    };

    this.hide = function () {
        if ( typeof this.nxcl_data.track != 'undefined' ) {
            this.nxcl_data.track.each(function (track) {
                track.draw_graph = false;
            });
            this.marker.each(function (marker) {
                marker.setMap(null);
            });
            this.google_data.gpolylines.each(function (polyline) {
                polyline.setMap(null);
            });
            this.google_data.gpolygons.each(function (polygons) {
                polygons.setMap(null);
            });
        }
        this.visible = false;
        map.graph.setGraph();
    };

    this.remove = function () {
        this.hide();
    };

    this.get_bounds = function () {
        return this.google_data.bounds;
    };

    this.size = function () {
        return this.nxcl_data.track[0].coords.length - 1;
    };

    this.toggle_track = function (id, bool) {
        if (!bool) {
            this.marker[id].setMap(null);
            this.google_data.gpolylines[id].setMap(null);
            this.nxcl_data.track[id].draw_graph = bool;
        } else {
            this.marker[id].setMap(map.internal_map);
            this.google_data.gpolylines[id].setMap(map.internal_map);
            this.nxcl_data.track[id].draw_graph = bool;
        }
        map.graph.setGraph();
    };

    this.move_marker = function (pos) {
        this.marker.each(function (marker, a, root) {
            marker.setPosition(new google.maps.LatLng(root.nxcl_data.track[a].coords[pos].lat, root.nxcl_data.track[a].coords[pos].lng));
        }, this);
    };

    // construct
    this.add_google_data();
    this.add_nxcl_data();
}

function Coordinate(lat, lon) {
    this.ele = 0;
    if (typeof lat == 'object') {
        this.placemark = lat;
        this.lat = function () {
            if (this.placemark.hasOwnProperty('getLatitude')) {
                return this.placemark.getLatitude();
            } else {
                return this.placemark.position.lat();
            }
        };
        this.lng = function () {
            if (this.placemark.hasOwnProperty('getLongitude')) {
                return this.placemark.getLongitude();
            } else {
                return this.placemark.position.lng();
            }
        };
    } else {
        lat = parseFloat(lat);
        lon = parseFloat(lon);
        this._lat = lat || 0;
        this._lon = lon || 0;
        this.lat = function () {
            return this._lat;
        };
        this.lng = function () {
            return this._lon;
        };
        this.set_lat = function (lat) {
            this._lat = lat;
        };
        this.set_lng = function (lng) {
            this._lng = lng;
        };
    }
    this._gridref = null;

    this.toLatLng = function() {
        return new google.maps.LatLng(this.lat(), this.lng());
    }

    this.set_from_OS = function (gridref) {
        this._gridref = gridref;
        var l1 = gridref.toUpperCase().charCodeAt(0) - 'A'.charCodeAt(0);
        var l2 = gridref.toUpperCase().charCodeAt(1) - 'A'.charCodeAt(0);
        // shuffle down letters after 'I' since 'I' is not used in grid:
        if (l1 > 7) {l1--;}
        if (l2 > 7) {l2--;}

        // convert grid letters into 100km-square indexes from false origin (grid square SV):
        var e = ((l1 - 2) % 5) * 5 + (l2 % 5);
        var n = (19 - Math.floor(l1 / 5) * 5) - Math.floor(l2 / 5);
        if (e < 0 || e > 6 || n < 0 || n > 12) {return false;}

        // skip grid letters to get numeric part of ref, stripping any spaces:
        gridref = gridref.slice(2).replace(/ /g, '');

        // append numeric part of references to grid index:
        e += gridref.slice(0, gridref.length / 2);
        n += gridref.slice(gridref.length / 2);

        // normalise to 1m grid, rounding up to centre of grid square:
        switch (gridref.length) {
            case 0:
                e += '50000';
                n += '50000';
                break;
            case 2:
                e += '5000';
                n += '5000';
                break;
            case 4:
                e += '500';
                n += '500';
                break;
            case 6:
                e += '50';
                n += '50';
                break;
            case 8:
                e += '5';
                n += '5';
                break;
            case 10:
                break; // 10-digit refs are already 1m
        }
        var E = e;
        var N = n;

        var a = 6377563.396, b = 6356256.910;              // Airy 1830 major & minor semi-axes
        var F0 = 0.9996012717;                             // NatGrid scale factor on central meridian
        var lat0 = 49 * Math.PI / 180, lon0 = -2 * Math.PI / 180;  // NatGrid true origin
        var N0 = -100000, E0 = 400000;                     // northing & easting of true origin, metres
        var e2 = 1 - (b * b) / (a * a);                          // eccentricity squared
        n = (a - b) / (a + b);
        var n2 = n * n, n3 = n * n * n;

        var lat = lat0, M = 0;
        do {
            lat = (N - N0 - M) / (a * F0) + lat;

            var Ma = (1 + n + (5 / 4) * n2 + (5 / 4) * n3) * (lat - lat0);
            var Mb = (3 * n + 3 * n * n + (21 / 8) * n3) * Math.sin(lat - lat0) * Math.cos(lat + lat0);
            var Mc = ((15 / 8) * n2 + (15 / 8) * n3) * Math.sin(2 * (lat - lat0)) * Math.cos(2 * (lat + lat0));
            var Md = (35 / 24) * n3 * Math.sin(3 * (lat - lat0)) * Math.cos(3 * (lat + lat0));
            M = b * F0 * (Ma - Mb + Mc - Md);                // meridional arc

        } while (N - N0 - M >= 0.00001);  // ie until < 0.01mm

        var cosLat = Math.cos(lat), sinLat = Math.sin(lat);
        var nu = a * F0 / Math.sqrt(1 - e2 * sinLat * sinLat);              // transverse radius of curvature
        var rho = a * F0 * (1 - e2) / Math.pow(1 - e2 * sinLat * sinLat, 1.5);  // meridional radius of curvature
        var eta2 = nu / rho - 1;

        var tanLat = Math.tan(lat);
        var tan2lat = tanLat * tanLat, tan4lat = tan2lat * tan2lat, tan6lat = tan4lat * tan2lat;
        var secLat = 1 / cosLat;
        var nu3 = nu * nu * nu, nu5 = nu3 * nu * nu, nu7 = nu5 * nu * nu;
        var VII = tanLat / (2 * rho * nu);
        var VIII = tanLat / (24 * rho * nu3) * (5 + 3 * tan2lat + eta2 - 9 * tan2lat * eta2);
        var IX = tanLat / (720 * rho * nu5) * (61 + 90 * tan2lat + 45 * tan4lat);
        var X = secLat / nu;
        var XI = secLat / (6 * nu3) * (nu / rho + 2 * tan2lat);
        var XII = secLat / (120 * nu5) * (5 + 28 * tan2lat + 24 * tan4lat);
        var XIIA = secLat / (5040 * nu7) * (61 + 662 * tan2lat + 1320 * tan4lat + 720 * tan6lat);

        var dE = (E - E0), dE2 = dE * dE, dE3 = dE2 * dE, dE4 = dE2 * dE2, dE5 = dE3 * dE2, dE6 = dE4 * dE2, dE7 = dE5 * dE2;
        lat = lat - VII * dE2 + VIII * dE4 - IX * dE6;
        var lon = lon0 + X * dE - XI * dE3 + XII * dE5 - XIIA * dE7;

        this._lat = lat.toDeg();
        this._lon = lon.toDeg();
    };

    this.gridref = function () {
        if (!this._gridref) {
            this._gridref = this.set_grid_ref();
        }
        return this._gridref;
    };
    this.is_valid_gridref = function () {
        return this._gridref.match(/^(h[l-z]|n[a-hj-z]|s[a-hj-z]|t[abfglmqrvw])[0-9]{6}$/i);

    };

    this.set_grid_ref = function () {
        var lat = this.lat().toRad();
        var lon = this.lng().toRad();

        var a = 6377563.396, b = 6356256.910;          // Airy 1830 major & minor semi-axes
        var F0 = 0.9996012717;                         // NatGrid scale factor on central meridian
        var lat0 = (49).toRad(), lon0 = (-2).toRad();  // NatGrid true origin is 49ºN,2ºW
        var N0 = -100000, E0 = 400000;                 // northing & easting of true origin, metres
        var e2 = 1 - (b * b) / (a * a);                      // eccentricity squared
        var n = (a - b) / (a + b), n2 = n * n, n3 = n * n * n;

        var cosLat = Math.cos(lat), sinLat = Math.sin(lat);
        var nu = a * F0 / Math.sqrt(1 - e2 * sinLat * sinLat);              // transverse radius of curvature
        var rho = a * F0 * (1 - e2) / Math.pow(1 - e2 * sinLat * sinLat, 1.5);  // meridional radius of curvature
        var eta2 = nu / rho - 1;

        var Ma = (1 + n + (5 / 4) * n2 + (5 / 4) * n3) * (lat - lat0);
        var Mb = (3 * n + 3 * n * n + (21 / 8) * n3) * Math.sin(lat - lat0) * Math.cos(lat + lat0);
        var Mc = ((15 / 8) * n2 + (15 / 8) * n3) * Math.sin(2 * (lat - lat0)) * Math.cos(2 * (lat + lat0));
        var Md = (35 / 24) * n3 * Math.sin(3 * (lat - lat0)) * Math.cos(3 * (lat + lat0));
        var M = b * F0 * (Ma - Mb + Mc - Md);              // meridional arc

        var cos3lat = cosLat * cosLat * cosLat;
        var cos5lat = cos3lat * cosLat * cosLat;
        var tan2lat = Math.tan(lat) * Math.tan(lat);
        var tan4lat = tan2lat * tan2lat;

        var I = M + N0;
        var II = (nu / 2) * sinLat * cosLat;
        var III = (nu / 24) * sinLat * cos3lat * (5 - tan2lat + 9 * eta2);
        var IIIA = (nu / 720) * sinLat * cos5lat * (61 - 58 * tan2lat + tan4lat);
        var IV = nu * cosLat;
        var V = (nu / 6) * cos3lat * (nu / rho - tan2lat);
        var VI = (nu / 120) * cos5lat * (5 - 18 * tan2lat + tan4lat + 14 * eta2 - 58 * tan2lat * eta2);

        var dLon = lon - lon0;
        var dLon2 = dLon * dLon, dLon3 = dLon2 * dLon, dLon4 = dLon3 * dLon, dLon5 = dLon4 * dLon, dLon6 = dLon5 * dLon;

        var N = I + II * dLon2 + III * dLon4 + IIIA * dLon6;
        var E = E0 + IV * dLon + V * dLon3 + VI * dLon5;

        var e = E, n = N;
        if (e == NaN || n == NaN) {
            return '??';
        }

        // get the 100km-grid indices
        var e100k = Math.floor(e / 100000), n100k = Math.floor(n / 100000);

        if (e100k < 0 || e100k > 6 || n100k < 0 || n100k > 12) return '';

        // translate those into numeric equivalents of the grid letters
        var l1 = (19 - n100k) - (19 - n100k) % 5 + Math.floor((e100k + 10) / 5);
        var l2 = (19 - n100k) * 5 % 25 + e100k % 5;

        // compensate for skipped 'I' and build grid letter-pairs
        if (l1 > 7) {l1++;}
        if (l2 > 7) {l2++;}
        var letPair = String.fromCharCode(l1 + 'A'.charCodeAt(0), l2 + 'A'.charCodeAt(0));

        // strip 100km-grid indices from easting & northing, and reduce precision
        e = Math.floor((e % 100000) / Math.pow(10, 2));
        n = Math.floor((n % 100000) / Math.pow(10, 2));

        var gridRef = letPair + e.padLz(3) + n.padLz(3);

        return gridRef;
    }
}

function trackData() {
    this.loaded = false;
    this.id = 0;
    this.xMin = 0;
    this.xMax = 0;
    this.od_score = 0;
    this.or_score = 0;
    this.tr_score = 0;
    this.od_time = 0;
    this.or_time = 0;
    this.tr_time = 0;
    this.track = [];
    this.loadFromAjax = function (json) {
        for (var i in json) {
            if (json[i]) {
                this[i] = json[i];
            }
        }
        this.loaded = true;
    };
}

function trackData() {
    this.loaded = false;
    this.draw_graph = 1;
    this.pilot = 0;
    this.colour = 0;
    this.max_ele = 0;
    this.min_ele = 0;
    this.max_cr = 0;
    this.min_cr = 0;
    this.max_speed = 0;
    this.total_dist = 0;
    this.av_speed = 0;
    this.coords = [];
    this.data = [];
    this.xMin = 0;
    this.EndT = 0;
    this.loadFromAjax = function (json) {
        for (var i in json) {
            if (typeof json[i] != "function") {
                this[i] = json[i];
            }
        }
        this.loaded = true;
    };
}

UKNXCL_Map.KmlPath = function (event, ths) {
    this.internal_array = [];
    this.event = event;

    this.root = '';
    this.kml = '';
    this.root_data = ths.parents('div.kmltree').eq(0).data('post');
    this.$li = ths.parent();
    this.data = this.$li.data("path");
    this.$parent_li = this.$li.parents("li");


    this.push = function (object) {
        this.internal_array.push(object);
    };

    this.index = function (i) {
        if (i < 0) {
            if (this.internal_array.length + (i - 1) >= 0) {
                return this.internal_array[this.internal_array.length + (i - 1) ];
            }
        } else if (this.internal_array.length >= i) {
            return this.internal_array[i];
        }
        return false

    };

    this.last = function () {
        return this.internal_array[this.internal_array.length - 1];
    };

    this.load = function () {
        if (map.isEarth()) {
            return this._earth_load();
        } else {
            return this._map_load()
        }
    };

    this.toggle = function () {
        if (map.isEarth()) {
            this._earth_toggle();
        } else {
            this._map_toggle();
        }
    };

    this.recursiveHide = function (earthObject) {
        if (map.isEarth()) {
            this._earth_recursiveHide(earthObject);
        } else {
            this._map_recursiveHide(earthObject);
        }
    };

    this.recursiveShow = function (earthObject) {
        if (map.isEarth()) {
            this._earth_recursiveShow(earthObject);
        } else {
            this._map_recursiveShow(earthObject);
        }
    };

    this.setVisibility = function (i, bool) {
        if (map.isEarth()) {
            this.index(i).setVisibility(bool);
        } else {
            if (bool) {
                this._map_recursiveHide(this.index(i));
            } else {
                this._map_recursiveShow(this.index(i));
            }
        }
    };

    this._earth_load = function () {
        if (this.data.type == "comp") {
            this.root = map.comp;
            this.push(this.root.google_data.root);
            this.kml = this.index(0).getFeatures().getChildNodes().item(0);
        } else if (this.data.type == "flight") {
            this.root = map.kmls[this.root_data.id];
            this.push(this.root.google_data.root);
            this.kml = this.index(0).getFeatures().getChildNodes().item(0);
        } else {
            this.root = map.airspace;
            this.kml = map.airspace.google_data.root;
        }
        if (this.data.path !== null) {
            this.data.path.each(function (index, i, ths) {
                var kml = ths.kml.getFeatures().getChildNodes().item(index);
                ths.push(kml);
                ths.kml = kml;
            }, this);
        }
        return true;
    };

    this._map_load = function () {
        if (this.data.type == "comp") {
            this.root = map.comp;
            this.push(this.root.google_data.structure[0][0]);
            this.path = this.root.google_data.structure[0][0];
        } else if (this.data.type == "flight") {
            this.root = map.kmls[this.root_data.id];
            this.push(this.root.google_data.structure[0][0]);
            this.path = this.root.google_data.structure[0][0];
        } else if (this.data.type == "coordinates") {
            this.root = map.kmls[this.root_data.id];
            this.root.setVisible(!this.root.getVisible());
            if(this.root.getVisible()) {
                this.$li.addClass('visible');
            } else {
                this.$li.removeClass('visible');
            }
            return false;
        } else {
            this.root = map.airspace;
            this.root.toggle(this.data.path[0]);
            return false;
        }
        if (this.data.path !== null) {
            this.data.path.each(function (index, i, ths) {
                ths.path = ths.path[index];
                ths.push(ths.path);

            }, this);
        }
        return true;
    };

    this._earth_toggle = function () {
        if (this.$li.hasClass('visible')) {
            if (this.$parent_li.hasClass('radioFolder')) {
                return;
            }
            if (this.$li.hasClass('radioFolder') || this.$li.hasClass('KmlFolder')) {
                this.recursiveHide(this.last());
            }
            this.$li.removeClass('visible');
            this.$li.find('li').removeClass('visible');
        } else {
            if (this.$parent_li.hasClass('radioFolder')) {
                this.recursiveHide(this.index(-1));
                this.setVisibility(-1, true);
                this.$parent_li.addClass('visible');
                this.$li.siblings("li").removeClass('visible');
            }
            this.last().setVisibility(true);
            this.$li.addClass('visible');
            if (this.$li.hasClass('radioFolder')) {
                this.recursiveShow(this.last().getFeatures().getFirstChild());
                this.$li.find('li').eq(0).addClass('visible');

            } else {
                this.$li.find('li').addClass('visible');
                this.recursiveShow(this.last());
            }
        }
        this.root.center();
    };

    this._map_toggle = function () {
        if (this.$li.hasClass('visible')) {
            if (this.$parent_li.hasClass('radioFolder')) {
                return;
            }
            if (this.$li.hasClass('radioFolder') || this.$li.hasClass('KmlFolder')) {
                this.recursiveHide(this.last());
            }
            this.$li.removeClass('visible');
            this.$li.find('li').removeClass('visible');
        } else {
            if (this.$parent_li.hasClass('radioFolder')) {
                this.recursiveHide(this.index(-1));
                this.setVisibility(-1, true);
                this.$parent_li.addClass('visible');
                this.$li.siblings("li").removeClass('visible');
            }
            this.setVisibility();
            this.$li.addClass('visible');
            if (this.$li.hasClass('radioFolder')) {
                this.recursiveShow(this.last());
                this.$li.find('li').eq(0).addClass('visible');

            } else {
                this.$li.find('li').addClass('visible');
                this.recursiveShow(this.last());
            }
        }
        this.root.center();
    };

    this._earth_recursiveHide = function (earthObject) {
        if (typeof earthObject == 'object' && typeof earthObject.getFeatures == 'function') {
            var siblings = earthObject.getFeatures().getChildNodes();
            var length = siblings.getLength();
            for (var i = 0; i < length; i++) {
                this.recursiveHide(siblings.item(i));
            }
            earthObject.setVisibility(false);
        }
    };

    this._map_recursiveHide = function (relative_placemarks) {
        for (var i = 0; i < relative_placemarks.length; i++) {
            if (typeof relative_placemarks[i] == 'object') {
                this.recursiveHide(relative_placemarks[i]);
            } else {
                var object = this.root.google_data.placemarks[relative_placemarks[i]];
                if (typeof object.polyline != 'undefined') object.polyline.setMap(null);
                if (typeof object.polygon != 'undefined') object.polygon.setMap(null);
            }
        }
    };

    this._earth_recursiveShow = function (earthObject) {
        if (typeof earthObject == 'object' && typeof earthObject.getFeatures == 'function') {
            var siblings = earthObject.getFeatures().getChildNodes();
            var length = siblings.getLength();
            for (var i = 0; i < length; i++) {
                this.recursiveShow(siblings.item(i));
            }
            earthObject.setVisibility(true);
        }
    };

    this._map_recursiveShow = function (relative_placemarks) {
        for (var i = 0; i < relative_placemarks.length; i++) {
            if (typeof relative_placemarks[i] == 'object') {
                this.recursiveShow(relative_placemarks[i]);
            } else {
                var object = this.root.google_data.placemarks[relative_placemarks[i]];
                if (typeof object.polyline != 'undefined') object.polyline.setMap(map.internal_map);
                if (typeof object.polygon != 'undefined') object.polygon.setMap(map.internal_map);
            }
        }
    }
};

function yessan(coordinates) {
    var projection = map.internal_map.getProjection();
    coordinates.each(function (point, i) {
        coordinates[i] = projection.fromLatLngToPoint(new google.maps.LatLng(point[0], point[1]));
    });
    var sign = direction_check(coordinates);
    var offset = new google.maps.Point(coordinates[1].x - coordinates[0].x, coordinates[1].y - coordinates[0].y);
    var bearing = sign * Math.atan2(offset.y, offset.x);
    map.b = bearing;
    var e, f, h;
    var base_leg = Math.sqrt((offset.x * offset.x) + (offset.y * offset.y));
    var triangle = Array(49);
    for (f = 28; 44 > f; ++f) {
        e = base_leg * f / 28;
        h = base_leg * (72 - f) / 28;
        e = (h * h + base_leg * base_leg - e * e) / (2 * base_leg);
        h = Math.sqrt(h * h - e * e);
        triangle[f - 28] = new google.maps.Point(coordinates[0].x + e * Math.cos(bearing) - h * Math.sin(bearing), coordinates[0].y + sign * (e * Math.sin(bearing) + h * Math.cos(bearing)));
    }
    for (f = 28; 44 > f; ++f) {
        e = base_leg * (72 - f) / f;
        h = 28 * base_leg / f;
        e = (h * h + base_leg * base_leg - e * e) / (2 * base_leg);
        h = Math.sqrt(h * h - e * e);
        triangle[16 + f - 28] = new google.maps.Point(coordinates[0].x + e * Math.cos(bearing) - h * Math.sin(bearing), coordinates[0].y + sign * (e * Math.sin(bearing) + h * Math.cos(bearing)));
    }
    for (f = 44; 28 <= f; --f) {
        e = 28 * base_leg / f;
        h = base_leg * (72 - f) / f;
        e = (h * h + base_leg * base_leg - e * e) / (2 * base_leg);
        h = Math.sqrt(h * h - e * e);
        triangle[76 - f] = new google.maps.Point(coordinates[0].x + e * Math.cos(bearing) - h * Math.sin(bearing), coordinates[0].y + sign * (e * Math.sin(bearing) + h * Math.cos(bearing)));
    }
    triangle.each(function (point, i) {
        triangle[i] = projection.fromPointToLatLng(point);
    });
    map.triangles = triangle;
    return triangle;
}
function direction_check(a) {
    if (3 > a.length) {
        return 0;
    }
    a = cyclic_loop(a, function (a, c) {return new google.maps.Point(a.x - c.x, a.y - c.y)});
    a = cyclic_loop(a, function (a, c) {return (a.x * c.y) - (a.y * c.x)});
    a = loop(a, function (a, c) {return {min: Math.min(a.min, c), max: Math.max(a.max, c)}}, {min: a[0], max: a[1]});
    return 0 > a.max ? -1 : 0 < a.min ? 1 : 0
}

function cyclic_loop(a, b) {
    var c = a.length;
    var d = Array(c);
    for (var e = 0; e < c; ++e) {
        d[e] = b.call(this, a[e], a[(e + 1) % c], e, a);
    }
    return d
}

function loop(a, b, c) {
    if (a.reduce)
        return a.reduce(b, c);
    var d = c;
    a.each(function (c, f) {
        d = b.call(k, d, c, f, a)
    });
    return d
}


Number.prototype.toRad = function () {  // convert degrees to radians
    return this * Math.PI / 180;
};
Number.prototype.toDeg = function () {  // convert radians to degrees (signed)
    return this * 180 / Math.PI;
};
Number.prototype.padLz = function (w) {
    var n = this.toString();
    var l = n.length;
    for (var i = 0; i < w - l; i++) n = '0' + n;
    return n;
};
Number.prototype.round = function (dp) {
    return Math.floor(this * Math.pow(10, dp)) / Math.pow(10, dp);
};;/*
 geoxml3.js

 Renders KML on the Google Maps JavaScript API Version 3
 http://code.google.com/p/geoxml3/

 Copyright 2010 Sterling Udell, Larry Ross

 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.

 */

// Extend the global String object with a method to remove leading and trailing whitespace
if (!String.prototype.trim) {
    String.prototype.trim = function () {
        return this.replace(/^\s+|\s+$/g, '');
    };
}

// Declare namespace
geoXML3 = window.geoXML3 || {instances: []};

// Constructor for the root KML parser object
geoXML3.Parser = function (options) {
    // Private variables
    var parserOptions = geoXML3.combineOptions(options, {
        singleInfoWindow: false,
        processStyles: true,
        zoom: true
    });
    var docs = []; // Individual KML documents
    var parserName;
    if (typeof parserOptions.suppressInfoWindows == "undefined") parserOptions.suppressInfoWindows = false;
    if (!parserOptions.infoWindow && parserOptions.singleInfoWindow)
        parserOptions.infoWindow = new google.maps.InfoWindow();
    // Private methods

    var parse = function (urls, docSet, id) {
        // Process one or more KML documents
        if (!parserName) {
            parserName = 'geoXML3.instances[' + (geoXML3.instances.push(this) - 1) + ']';
        }

        if (typeof urls === 'string') {
            // Single KML document
            urls = [urls];
        }

        // Internal values for the set of documents as a whole
        var internals = {
            parser: this,
            docSet: docSet || [],
            remaining: urls.length,
            parseOnly: !(parserOptions.afterParse || parserOptions.processStyles)
        };
        var thisDoc, j;
        for (var i = 0; i < urls.length; i++) {
            var baseUrl = urls[i].split('?')[0];
            for (j = 0; j < docs.length; j++) {
                if (baseUrl === docs[j].baseUrl) {
                    // Reloading an existing document
                    thisDoc = docs[j];
                    thisDoc.reload = true;
                    docs.splice(j, 1);
                    break;
                }
            }
            if (j >= docs.length) {
                thisDoc = {};
                thisDoc.baseUrl = baseUrl;
            }
            thisDoc.url = urls[i];
            thisDoc.internals = internals;
            internals.docSet.push(thisDoc);
            fetchDoc(thisDoc.url, thisDoc, id);
        }
    };

    function fetchDoc(url, doc, id) {
        geoXML3.fetchXML(url, function (responseXML) {
            render(responseXML, doc, id);
        });
    }

    var hideDocument = function (doc) {
        if (!doc) doc = docs[0];
        // Hide the map objects associated with a document
        var i;
        if (!!doc.markers) {
            for (i = 0; i < doc.markers.length; i++) {
                if (!!doc.markers[i].infoWindow) doc.markers[i].infoWindow.close();
                doc.markers[i].setVisible(false);
            }
        }
        if (!!doc.ggroundoverlays) {
            for (i = 0; i < doc.ggroundoverlays.length; i++) {
                doc.ggroundoverlays[i].setOpacity(0);
            }
        }
        if (!!doc.gpolylines) {
            for (i = 0; i < doc.gpolylines.length; i++) {
                doc.gpolylines[i].setMap(null);
            }
        }
        if (!!doc.gpolygons) {
            for (i = 0; i < doc.gpolygons.length; i++) {
                doc.gpolygons[i].setMap(null);
            }
        }
    };

    var showDocument = function (doc) {
        if (!doc) doc = docs[0];
        // Show the map objects associated with a document
        var i;
        if (!!doc.markers) {
            for (i = 0; i < doc.markers.length; i++) {
                doc.markers[i].setVisible(true);
            }
        }
        if (!!doc.ggroundoverlays) {
            for (i = 0; i < doc.ggroundoverlays.length; i++) {
                doc.ggroundoverlays[i].setOpacity(doc.ggroundoverlays[i].percentOpacity_);
            }
        }
        if (!!doc.gpolylines) {
            for (i = 0; i < doc.gpolylines.length; i++) {
                doc.gpolylines[i].setMap(parserOptions.map);
            }
        }
        if (!!doc.gpolygons) {
            for (i = 0; i < doc.gpolygons.length; i++) {
                doc.gpolygons[i].setMap(parserOptions.map);
            }
        }
    };

    var defaultStyle = {
        color: "ff000000", // black
        width: 1,
        fill: true,
        outline: true,
        fillcolor: "3fff0000" // blue
    };

    function processStyle(thisNode, styles, styleID) {
        var nodeValue = geoXML3.nodeValue;
        styles[styleID] = styles[styleID] || clone(defaultStyle);
        var styleNodes = thisNode.getElementsByTagName('Icon');
        if (!!styleNodes && !!styleNodes.length && (styleNodes.length > 0)) {
            styles[styleID].href = nodeValue(styleNodes[0].getElementsByTagName('href')[0]);
            styles[styleID].scale = nodeValue(styleNodes[0].getElementsByTagName('scale')[0]);
            if (!isNaN(styles[styleID].scale)) styles[styleID].scale = 1.0;
        }
        styleNodes = thisNode.getElementsByTagName('LineStyle');
        if (!!styleNodes && !!styleNodes.length && (styleNodes.length > 0)) {
            styles[styleID].color = nodeValue(styleNodes[0].getElementsByTagName('color')[0]);
            styles[styleID].width = nodeValue(styleNodes[0].getElementsByTagName('width')[0]);
        }
        styleNodes = thisNode.getElementsByTagName('PolyStyle');
        if (!!styleNodes && !!styleNodes.length && (styleNodes.length > 0)) {
            styles[styleID].outline = getBooleanValue(styleNodes[0].getElementsByTagName('outline')[0]);
            styles[styleID].fill = getBooleanValue(styleNodes[0].getElementsByTagName('fill')[0]);
            styles[styleID].fillcolor = nodeValue(styleNodes[0].getElementsByTagName('color')[0]);
        }
        return styles[styleID];
    }

    // from http://stackoverflow.com/questions/122102/what-is-the-most-efficient-way-to-clone-a-javascript-object
    // http://keithdevens.com/weblog/archive/2007/Jun/07/javascript.clone
    function clone(obj) {
        if (obj == null || typeof(obj) != 'object') return obj;
        var temp = new obj.constructor();
        for (var key in obj) temp[key] = clone(obj[key]);
        return temp;
    }

    function processStyleMap(thisNode, styles, styleID) {
        var nodeValue = geoXML3.nodeValue;
        var pairs = thisNode.getElementsByTagName('Pair');
        var map = {};
        // add each key to the map
        for (var pr = 0; pr < pairs.length; pr++) {
            var pairkey = nodeValue(pairs[pr].getElementsByTagName('key')[0]);
            var pairstyle = nodeValue(pairs[pr].getElementsByTagName('Style')[0]);
            var pairstyleurl = nodeValue(pairs[pr].getElementsByTagName('styleUrl')[0]);
            if (!!pairstyle) {
                processStyle(pairstyle, map[pairkey], styleID);
            } else if (!!pairstyleurl && !!styles[pairstyleurl]) {
                map[pairkey] = clone(styles[pairstyleurl]);
            }
        }
        if (!!map["normal"]) {
            styles[styleID] = clone(map["normal"]);
        } else {
            styles[styleID] = clone(defaultStyle);
        }
        if (!!map["highlight"]) {
            processStyleID(map["highlight"]);
        }
        styles[styleID].map = clone(map);
    }

    function getBooleanValue(node) {
        var nodeContents = geoXML3.nodeValue(node);
        if (!nodeContents) return true;
        if (nodeContents) nodeContents = parseInt(nodeContents);
        if (isNaN(nodeContents)) return true;
        return nodeContents != 0;
    }

    function processPlacemarkCoords(node, tag) {
        var parent = node.getElementsByTagName(tag);
        var coordListA = [];
        for (var i = 0; i < parent.length; i++) {
            var coordNodes = parent[i].getElementsByTagName('coordinates');
            if (!coordNodes) {
                if (coordListA.length > 0) {
                    break;
                } else {
                    return [
                        {coordinates: []}
                    ];
                }
            }

            for (var j = 0; j < coordNodes.length; j++) {
                var coords = geoXML3.nodeValue(coordNodes[j]).trim();
                coords = coords.replace(/,\s+/g, ',');
                var path = coords.split(/\s+/g);
                var pathLength = path.length;
                var coordList = [];
                for (var k = 0; k < pathLength; k++) {
                    coords = path[k].split(',');
                    if (!isNaN(coords[0]) && !isNaN(coords[1])) {
                        coordList.push({
                            lat: parseFloat(coords[1]),
                            lng: parseFloat(coords[0]),
                            alt: parseFloat(coords[2])
                        });
                    }
                }
                coordListA.push({coordinates: coordList});
            }
        }
        return coordListA;
    }

    function buildStructure(xml, structure, placemark_index) {
        if (xml.childElementCount) {
            for (var i = 0; i < xml.childElementCount; i++) {
                var Node = xml.children[i];
                if (Node.nodeName == 'Folder') {
                    var sub = [];
                    var res = buildStructure(Node, sub, placemark_index);
                    placemark_index = res[1];
                    structure.push(res[0]);
                } else if (Node.nodeName == 'Placemark') {
                    structure.push(placemark_index);
                    placemark_index++;
                } else {
                    res = buildStructure(Node, [], placemark_index);
                    placemark_index = res[1];
                    if (res[0].length) {
                        for (var j = 0; j < res[0].length; j++) {
                            structure.push(res[0][j]);
                        }
                    }
                }
            }
        }
        return [structure, placemark_index];
    }

    var render = function (responseXML, doc) {
        // Callback for retrieving a KML document: parse the KML and display it on the map
        if (!responseXML) {
            // Error retrieving the data
            geoXML3.log('Unable to retrieve ' + doc.url);
            if (parserOptions.failedParse) {
                parserOptions.failedParse(doc);
            }
        } else if (!doc) {
            throw 'geoXML3 internal error: render called with null document';
        } else { //no errors
            var i;
            var styles = {};
            doc.placemarks = [];
            doc.groundoverlays = [];
            doc.ggroundoverlays = [];
            doc.networkLinks = [];
            doc.gpolygons = [];
            doc.gpolylines = [];
            doc.structure = [];


            // Parse styles
            var styleID;
            nodes = responseXML.getElementsByTagName('Style');
            nodeCount = nodes.length;
            for (i = 0; i < nodeCount; i++) {
                var thisNode = nodes[i];
                var thisNodeId = thisNode.getAttribute('id');
                if (!!thisNodeId) {
                    styleID = '#' + thisNodeId;
                    processStyle(thisNode, styles, styleID);
                }
            }
            // rudamentary support for StyleMap
            // use "normal" mapping only
            nodes = responseXML.getElementsByTagName('StyleMap');
            for (i = 0; i < nodes.length; i++) {
                thisNode = nodes[i];
                thisNodeId = thisNode.getAttribute('id');
                if (!!thisNodeId) {
                    styleID = '#' + thisNodeId;
                    processStyleMap(thisNode, styles, styleID);
                }
            }
            doc.styles = styles;
            if (!!parserOptions.processStyles || !parserOptions.createMarker) {
                // Convert parsed styles into GMaps equivalents
                processStyles(doc);
            }

            // Parse placemarks
            if (!!doc.reload && !!doc.markers) {
                for (i = 0; i < doc.markers.length; i++) {
                    doc.markers[i].active = false;
                }
            }
            doc.structure = buildStructure(responseXML, [], 0);

            var placemark, node, marker, poly, pathLength, polygonNodes, coordList;
            var placemarkNodes = responseXML.getElementsByTagName('Placemark');
            for (var pm = 0; pm < placemarkNodes.length; pm++) {
                // Init the placemark object
                node = placemarkNodes[pm];
                placemark = {
                    name: geoXML3.nodeValue(node.getElementsByTagName('name')[0]),
                    description: geoXML3.nodeValue(node.getElementsByTagName('description')[0]),
                    styleUrl: geoXML3.nodeValue(node.getElementsByTagName('styleUrl')[0])
                };
                placemark.style = doc.styles[placemark.styleUrl] || clone(defaultStyle);
                // inline style overrides shared style
                var inlineStyles = node.getElementsByTagName('Style');
                if (inlineStyles && (inlineStyles.length > 0)) {
                    var style = processStyle(node, doc.styles, "inline");
                    processStyleID(style);
                    if (style) placemark.style = style;
                }
                if (/^https?:\/\//.test(placemark.description)) {
                    placemark.description = ['<a href="', placemark.description, '">', placemark.description, '</a>'].join('');
                }

                // process MultiGeometry
                var GeometryNodes = node.getElementsByTagName('coordinates');
                var Geometry = null;
                if (!!GeometryNodes && (GeometryNodes.length > 0)) {
                    for (var gn = 0; gn < GeometryNodes.length; gn++) {
                        if (!GeometryNodes[gn].parentNode || !GeometryNodes[gn].parentNode.nodeName) {

                        } else { // parentNode.nodeName exists
                            var GeometryPN = GeometryNodes[gn].parentNode;
                            Geometry = GeometryPN.nodeName;

                            // Extract the coordinates
                            // What sort of placemark?
                            switch (Geometry) {
                                case "Point":
                                    placemark.Point = processPlacemarkCoords(node, "Point")[0];
                                    placemark.latlng = new google.maps.LatLng(placemark.Point.coordinates[0].lat, placemark.Point.coordinates[0].lng);
                                    pathLength = 1;
                                    break;
                                case "LinearRing":
                                    // Polygon/line
                                    polygonNodes = node.getElementsByTagName('Polygon');
                                    // Polygon
                                    if (!placemark.Polygon)
                                        placemark.Polygon = [
                                            {
                                                outerBoundaryIs: {coordinates: []},
                                                innerBoundaryIs: [
                                                    {coordinates: []}
                                                ]
                                            }
                                        ];
                                    for (var pg = 0; pg < polygonNodes.length; pg++) {
                                        placemark.Polygon[pg] = {
                                            outerBoundaryIs: {coordinates: []},
                                            innerBoundaryIs: [
                                                {coordinates: []}
                                            ]
                                        };
                                        placemark.Polygon[pg].outerBoundaryIs = processPlacemarkCoords(polygonNodes[pg], "outerBoundaryIs");
                                        placemark.Polygon[pg].innerBoundaryIs = processPlacemarkCoords(polygonNodes[pg], "innerBoundaryIs");
                                    }
                                    coordList = placemark.Polygon[0].outerBoundaryIs;
                                    break;

                                case "LineString":
                                    pathLength = 0;
                                    placemark.LineString = processPlacemarkCoords(node, "LineString");
                                    break;

                                default:
                                    break;
                            }
                        } // parentNode.nodeName exists
                    } // GeometryNodes loop
                } // if GeometryNodes
                // call the custom placemark parse function if it is defined
                if (!!parserOptions.pmParseFn) parserOptions.pmParseFn(node, placemark);
                doc.placemarks.push(placemark);

                if (placemark.Point) {
                    if (!!google.maps) {
                        doc.bounds = doc.bounds || new google.maps.LatLngBounds();
                        doc.bounds.extend(placemark.latlng);
                    }

                    if (!!parserOptions.createMarker) {
                        // User-defined marker handler
                        parserOptions.createMarker(placemark, doc);
                    } else { // !user defined createMarker
                        // Check to see if this marker was created on a previous load of this document
                        var found = false;
                        if (!!doc) {
                            doc.markers = doc.markers || [];
                            if (doc.reload) {
                                for (var j = 0; j < doc.markers.length; j++) {
                                    if (doc.markers[j].getPosition().equals(placemark.latlng)) {
                                        found = doc.markers[j].active = true;
                                        break;
                                    }
                                }
                            }
                        }

                        if (!found) {
                            // Call the built-in marker creator
                            marker = createMarker(placemark, doc);
                            marker.active = true;
                        }
                    }
                }
                if (placemark.Polygon) { // poly test 2
                    if (!!doc) {
                        doc.gpolygons = doc.gpolygons || [];
                    }

                    if (!!parserOptions.createPolygon) {
                        // User-defined polygon handler
                        poly = parserOptions.createPolygon(placemark, doc);
                    } else {  // ! user defined createPolygon
                        // Check to see if this marker was created on a previous load of this document
                        poly = createPolygon(placemark, doc);
                        poly.active = true;
                    }
                    if (!!google.maps) {
                        doc.bounds = doc.bounds || new google.maps.LatLngBounds();
                        doc.bounds.union(poly.bounds);
                    }
                }
                if (placemark.LineString) { // polyline
                    if (!!doc) {
                        doc.gpolylines = doc.gpolylines || [];
                    }
                    if (!!parserOptions.createPolyline) {
                        // User-defined polyline handler
                        poly = parserOptions.createPolyline(placemark, doc);
                    } else { // ! user defined createPolyline
                        // Check to see if this marker was created on a previous load of this document
                        poly = createPolyline(placemark, doc);
                        poly.active = true;
                    }
                    if (!!google.maps) {
                        doc.bounds = doc.bounds || new google.maps.LatLngBounds();
                        doc.bounds.union(poly.bounds);
                    }
                }

            } // placemark loop

            if (!!doc.reload && !!doc.markers) {
                for (i = doc.markers.length - 1; i >= 0; i--) {
                    if (!doc.markers[i].active) {
                        if (!!doc.markers[i].infoWindow) {
                            doc.markers[i].infoWindow.close();
                        }
                        doc.markers[i].setMap(null);
                        doc.markers.splice(i, 1);
                    }
                }
            }
        }

        if (!!doc.bounds) {
            doc.internals.bounds = doc.internals.bounds || new google.maps.LatLngBounds();
            doc.internals.bounds.union(doc.bounds);
        }
        if (!!doc.markers || !!doc.groundoverlays || !!doc.gpolylines || !!doc.gpolygons) {
            doc.internals.parseOnly = false;
        }

        doc.internals.remaining -= 1;
        if (doc.internals.remaining === 0) {
            // We're done processing this set of KML documents
            // Options that get invoked after parsing completes
            if (parserOptions.zoom && !!doc.internals.bounds) {
                parserOptions.map.fitBounds(doc.internals.bounds);
            }
            if (parserOptions.afterParse) {
                parserOptions.afterParse(doc.internals.docSet);
            }

            if (!doc.internals.parseOnly) {
                // geoXML3 is not being used only as a real-time parser, so keep the processed documents around
                for (i = (doc.internals.docSet.length - 1); i >= 0; i--) {
                    docs.push(doc.internals.docSet[i]);
                }
            }
        }
    };

    var kmlColor = function (kmlIn) {
        var kmlColor = {};
        if (kmlIn) {  // CHANGED BE ROB_CHET FOR RANDOM COLOUR ONLY origionaly --- if (kmlIn) ---{
            aa = kmlIn.substr(0, 2);
            bb = kmlIn.substr(2, 2);
            gg = kmlIn.substr(4, 2);
            rr = kmlIn.substr(6, 2);
            kmlColor.color = "#" + rr + gg + bb;
            kmlColor.opacity = parseInt(aa, 16) / 256;
        } else {
            // defaults
            kmlColor.color = randomColor();
            kmlColor.opacity = 1;
        }
        return kmlColor;
    };

    var randomColor = function () {
        var color = "#";
        var colorNum = Math.random() * 8388607.0;  // 8388607 = Math.pow(2,23)-1
        var colorStr = colorNum.toString(16);
        color += colorStr.substring(0, colorStr.indexOf('.'));
        return color;
    };

    var processStyleID = function (style) {
        var zeroPoint = new google.maps.Point(0, 0);
        if (!!style.href) {
            var markerRegEx = /\/(red|blue|green|yellow|lightblue|purple|pink|orange|pause|go|stop)(-dot)?\.png/;
            if (markerRegEx.test(style.href)) {
                //bottom middle
                var anchorPoint = new google.maps.Point(16 * style.scale, 32 * style.scale);
            } else {
                anchorPoint = new google.maps.Point(16 * style.scale, 16 * style.scale);
            }
            // Init the style object with a standard KML icon
            style.icon = new google.maps.MarkerImage(style.href, new google.maps.Size(32 * style.scale, 32 * style.scale), zeroPoint, // bottom middle
                anchorPoint, new google.maps.Size(32, 32)

            );

            // Look for a predictable shadow
            var stdRegEx = /\/(red|blue|green|yellow|lightblue|purple|pink|orange)(-dot)?\.png/;
            var shadowSize = new google.maps.Size(59, 32);
            var shadowPoint = new google.maps.Point(16, 32);
            if (stdRegEx.test(style.href)) {
                // A standard GMap-style marker icon
                style.shadow = new google.maps.MarkerImage('http://maps.google.com/mapfiles/ms/micons/msmarker.shadow.png', shadowSize, zeroPoint, shadowPoint);
            } else if (style.href.indexOf('-pushpin.png') > -1) {
                // Pushpin marker icon
                style.shadow = new google.maps.MarkerImage('http://maps.google.com/mapfiles/ms/micons/pushpin_shadow.png', shadowSize, zeroPoint, shadowPoint);
            } else {
                // Other MyMaps KML standard icon
                style.shadow = new google.maps.MarkerImage(style.href.replace('.png', '.shadow.png'), shadowSize, zeroPoint, shadowPoint);
            }
        }
    };

    var processStyles = function (doc) {
        for (var styleID in doc.styles) {
            processStyleID(doc.styles[styleID]);
        }
    };

    var createMarker = function (placemark, doc) {
        // create a Marker to the map from a placemark KML object

        // Load basic marker properties
        var markerOptions = geoXML3.combineOptions(parserOptions.markerOptions, {
            map: parserOptions.map,
            position: new google.maps.LatLng(placemark.Point.coordinates[0].lat, placemark.Point.coordinates[0].lng),
            title: placemark.name,
            zIndex: 500,
            icon: placemark.style.icon,
            shadow: placemark.style.shadow
        });

        // Create the marker on the map
        var marker = new google.maps.Marker(markerOptions);
        if (!!doc) {
            doc.markers.push(marker);
        }

        // Set up and create the infowindow if it is not suppressed
        if (!parserOptions.suppressInfoWindows) {
            var infoWindowOptions = geoXML3.combineOptions(parserOptions.infoWindowOptions, {
                content: '<div class="geoxml3_infowindow"><h3>' + placemark.name + '</h3><div>' + placemark.description + '</div></div>',
                pixelOffset: new google.maps.Size(0, 2)
            });
            if (parserOptions.infoWindow) {
                marker.infoWindow = parserOptions.infoWindow;
            } else {
                marker.infoWindow = new google.maps.InfoWindow(infoWindowOptions);
            }
            // Infowindow-opening event handler
            google.maps.event.addListener(marker, 'click', function () {
                this.infoWindow.close();
                marker.infoWindow.setOptions(infoWindowOptions);
                this.infoWindow.open(this.map, this);
            });
        }
        placemark.marker = marker;
        return marker;
    };

    var createOverlay = function (groundOverlay, doc) {
        // Add a ProjectedOverlay to the map from a groundOverlay KML object

        if (!window.ProjectedOverlay) {
            throw 'geoXML3 error: ProjectedOverlay not found while rendering GroundOverlay from KML';
        }

        var bounds = new google.maps.LatLngBounds(new google.maps.LatLng(groundOverlay.latLonBox.south, groundOverlay.latLonBox.west), new google.maps.LatLng(groundOverlay.latLonBox.north, groundOverlay.latLonBox.east));
        var overlayOptions = geoXML3.combineOptions(parserOptions.overlayOptions, {percentOpacity: groundOverlay.opacity * 100});
        var overlay = new ProjectedOverlay(parserOptions.map, groundOverlay.icon.href, bounds, overlayOptions);

        if (!!doc) {
            doc.ggroundoverlays = doc.ggroundoverlays || [];
            doc.ggroundoverlays.push(overlay);
        }

        return overlay;
    };

    // Create Polyline
    var createPolyline = function (placemark, doc) {
        var path = [];
        for (var j = 0; j < placemark.LineString.length; j++) {
            var coords = placemark.LineString[j].coordinates;
            var bounds = new google.maps.LatLngBounds();
            for (var i = 0; i < coords.length; i++) {
                var pt = new google.maps.LatLng(coords[i].lat, coords[i].lng);
                path.push(pt);
                bounds.extend(pt);
            }
        }
        // point to open the infowindow if triggered
        var point = path[Math.floor(path.length / 2)];
        // Load basic polyline properties
        var kmlStrokeColor = kmlColor(placemark.style.color);
        var polyOptions = geoXML3.combineOptions(parserOptions.polylineOptions, {
            map: parserOptions.map,
            path: path,
            strokeColor: kmlStrokeColor.color,
            strokeWeight: placemark.style.width,
            strokeOpacity: kmlStrokeColor.opacity,
            title: placemark.name,
            zIndex: 500
        });
        var p = new google.maps.Polyline(polyOptions);
        p.bounds = bounds;
        // setup and create the infoWindow if it is not suppressed
        if (!parserOptions.suppressInfoWindows) {
            var infoWindowOptions = geoXML3.combineOptions(parserOptions.infoWindowOptions, {
                content: '<div class="geoxml3_infowindow"><h3>' + placemark.name + '</h3><div>' + placemark.description + '</div></div>',
                pixelOffset: new google.maps.Size(0, 2)
            });
            if (parserOptions.infoWindow) {
                p.infoWindow = parserOptions.infoWindow;
            } else {
                p.infoWindow = new google.maps.InfoWindow(infoWindowOptions);
            }
            // Infowindow-opening event handler
            google.maps.event.addListener(p, 'click', function (e) {
                p.infoWindow.close();
                p.infoWindow.setOptions(infoWindowOptions);
                if (e && e.latLng) {
                    p.infoWindow.setPosition(e.latLng);
                } else {
                    p.infoWindow.setPosition(point);
                }
                p.infoWindow.open(this.map);
            });
        }
        if (!!doc) doc.gpolylines.push(p);
        placemark.polyline = p;
        return p;
    };

    // Create Polygon
    var createPolygon = function (placemark, doc) {
        var bounds = new google.maps.LatLngBounds();
        var pathsLength = 0;
        var paths = [];
        for (var polygonPart = 0; polygonPart < placemark.Polygon.length; polygonPart++) {
            for (var j = 0; j < placemark.Polygon[polygonPart].outerBoundaryIs.length; j++) {
                var coords = placemark.Polygon[polygonPart].outerBoundaryIs[j].coordinates;
                var path = [];
                for (var i = 0; i < coords.length; i++) {
                    var pt = new google.maps.LatLng(coords[i].lat, coords[i].lng);
                    path.push(pt);
                    bounds.extend(pt);
                }
                paths.push(path);
                pathsLength += path.length;
            }
            for (j = 0; j < placemark.Polygon[polygonPart].innerBoundaryIs.length; j++) {
                coords = placemark.Polygon[polygonPart].innerBoundaryIs[j].coordinates;
                path = [];
                for (i = 0; i < coords.length; i++) {
                    pt = new google.maps.LatLng(coords[i].lat, coords[i].lng);
                    path.push(pt);
                    bounds.extend(pt);
                }
                paths.push(path);
                pathsLength += path.length;
            }
        }

        // Load basic polygon properties
        var kmlStrokeColor = kmlColor(placemark.style.color);
        var kmlFillColor = kmlColor(placemark.style.fillcolor);
        if (!placemark.style.fill) kmlFillColor.opacity = 0.0;
        var strokeWeight = placemark.style.width;
        if (!placemark.style.outline) {
            strokeWeight = 0;
            kmlStrokeColor.opacity = 0.0;
        }
        var polyOptions = geoXML3.combineOptions(parserOptions.polygonOptions, {
            map: parserOptions.map,
            paths: paths,
            title: placemark.name,
            clickable: false,
            strokeColor: kmlStrokeColor.color,
            strokeWeight: strokeWeight,
            strokeOpacity: kmlStrokeColor.opacity,
            fillColor: kmlFillColor.color,
            fillOpacity: 0.1
        });
        var p = new google.maps.Polygon(polyOptions);
        p.bounds = bounds;
        if (!parserOptions.suppressInfoWindows) {
            var infoWindowOptions = geoXML3.combineOptions(parserOptions.infoWindowOptions, {
                content: '<div class="geoxml3_infowindow"><h3>' + placemark.name + '</h3><div>' + placemark.description + '</div></div>',
                pixelOffset: new google.maps.Size(0, 2)
            });
            if (parserOptions.infoWindow) {
                p.infoWindow = parserOptions.infoWindow;
            } else {
                p.infoWindow = new google.maps.InfoWindow(infoWindowOptions);
            }
            // Infowindow-opening event handler
            google.maps.event.addListener(p, 'click', function (e) {
                p.infoWindow.close();
                p.infoWindow.setOptions(infoWindowOptions);
                if (e && e.latLng) {
                    p.infoWindow.setPosition(e.latLng);
                } else {
                    p.infoWindow.setPosition(p.bounds.getCenter());
                }
                p.infoWindow.open(this.map);
            });
        }
        if (!!doc) doc.gpolygons.push(p);
        placemark.polygon = p;
        return p;
    };

    return {
        // Expose some properties and methods

        options: parserOptions,
        docs: docs,

        parse: parse,
        hideDocument: hideDocument,
        showDocument: showDocument,
        processStyles: processStyles,
        createMarker: createMarker,
        createOverlay: createOverlay,
        createPolyline: createPolyline,
        createPolygon: createPolygon
    };
};
// End of KML Parser

// Helper objects and functions
geoXML3.getOpacity = function (kmlColor) {
    // Extract opacity encoded in a KML color value. Returns a number between 0 and 1.
    if (!!kmlColor && (kmlColor !== '') && (kmlColor.length == 8)) {
        var transparency = parseInt(kmlColor.substr(0, 2), 16);
        return transparency / 255;
    } else {
        return 1;
    }
};

// Log a message to the debugging console, if one exists
geoXML3.log = function (msg) {
    if (!!window.console) {
        console.log(msg);
    } else {
        alert("log:" + msg);
    }
};

// Combine two options objects: a set of default values and a set of override values
geoXML3.combineOptions = function (overrides, defaults) {
    var result = {};
    if (!!overrides) {
        for (var prop in overrides) {
            if (overrides.hasOwnProperty(prop)) {
                result[prop] = overrides[prop];
            }
        }
    }
    if (!!defaults) {
        for (prop in defaults) {
            if (defaults.hasOwnProperty(prop) && (result[prop] === undefined)) {
                result[prop] = defaults[prop];
            }
        }
    }
    return result;
};

// Retrieve an XML document from url and pass it to callback as a DOM document
geoXML3.fetchers = [];

// parse text to XML doc
/**
 * Parses the given XML string and returns the parsed document in a
 * DOM data structure. This function will return an empty DOM node if
 * XML parsing is not supported in this browser.
 * @param {string} str XML string.
 * @return {Element|Document} DOM.
 */
geoXML3.xmlParse = function (str) {
    if (typeof ActiveXObject != 'undefined' && typeof GetObject != 'undefined') {
        var doc = new ActiveXObject('Microsoft.XMLDOM');
        doc.loadXML(str);
        return doc;
    }

    if (typeof DOMParser != 'undefined') {
        return (new DOMParser()).parseFromString(str, 'text/xml');
    }

    return createElement('div', null);
};

geoXML3.fetchXML = function (url, callback) {
    function timeoutHandler() {
        callback();
    }

    var xhrFetcher = {};
    if (!!geoXML3.fetchers.length) {
        xhrFetcher = geoXML3.fetchers.pop();
    } else {
        if (!!window.XMLHttpRequest) {
            xhrFetcher.fetcher = new window.XMLHttpRequest(); // Most browsers
        } else if (!!window.ActiveXObject) {
            xhrFetcher.fetcher = new window.ActiveXObject('Microsoft.XMLHTTP'); // Some IE
        }
    }

    if (!xhrFetcher.fetcher) {
        geoXML3.log('Unable to create XHR object');
        callback(null);
    } else {
        if (xhrFetcher.fetcher.overrideMimeType) {
            xhrFetcher.fetcher.overrideMimeType('text/xml');
        }
        xhrFetcher.fetcher.open('GET', url, true);
        xhrFetcher.fetcher.onreadystatechange = function () {
            if (xhrFetcher.fetcher.readyState === 4) {
                // Retrieval complete
                if (!!xhrFetcher.xhrtimeout)
                    clearTimeout(xhrFetcher.xhrtimeout);
                if (xhrFetcher.fetcher.status >= 400) {
                    geoXML3.log('HTTP error ' + xhrFetcher.fetcher.status + ' retrieving ' + url);
                    callback();
                } else {
                    // Returned successfully
                    callback(geoXML3.xmlParse(xhrFetcher.fetcher.responseText));
                }
                // We're done with this fetcher object
                geoXML3.fetchers.push(xhrFetcher);
            }
        };
        xhrFetcher.xhrtimeout = setTimeout(timeoutHandler, 60000);
        xhrFetcher.fetcher.send(null);
    }
};

//nodeValue: Extract the text value of a DOM node, with leading and trailing whitespace trimmed
geoXML3.nodeValue = function (node) {
    var retStr = "";
    if (!node) {
        return '';
    }
    if (node.nodeType == 3 || node.nodeType == 4 || node.nodeType == 2) {
        retStr += node.nodeValue;
    } else if (node.nodeType == 1 || node.nodeType == 9 || node.nodeType == 11) {
        for (var i = 0; i < node.childNodes.length; ++i) {
            retStr += arguments.callee(node.childNodes[i]);
        }
    }
    return retStr;
};;function Planner(parent) {
    this.parent = parent || {};
    this.waypoints = [];
    this.enabled = false;
    this.count = 0;
    this.mapObject = null;
    this.coordinates = [];
    this.distance_array = [0];
    this.total_distance_array = [0];
    this.R = 6371;

    this.enable = function () {
        this.enabled = true;
        $("body").addClass('waypoint_mode');
    };

    this.get_share_link = function() {
        var out = document.location.host + '/planner/';
        this.coordinates.each(function(coordinate) {
            out += coordinate.lat().toFixed(6) + ',' + coordinate.lng().toFixed(6) + ';';
        });
        return out.trim(';');
    };

    this.writeplanner = function () {
        this.calculate_distances();
        var out =
            "<table class='results main' style='width:100%'>" +
            "<thead><tr><th></th><th>Lat</th><th>Lng</th><th>Distance</th><th></th><th></th></tr></thead>";
        this.coordinates.each(function (coordinate, a) {
            out += '<tr>' + '<td>Turnpoint ' + a + '</td>' + '<td>Lat:' + Math.round(coordinate.lat() * 10000) / 10000 + '</td>' + '<td>Lng:' + Math.round(coordinate.lng() * 10000) / 10000 + '</td>' + '<td>' + Math.round(map.planner.distance_array[a] * 10000) / 10000 + 'km</td>' + '<td>' + Math.round((map.planner.total_distance_array[a] / map.planner.get_total_distance()) * 10000) / 100 + '%</td>' + '<td><a class="remove" href="#" onclick="map.planner.remove(' + a + '); return false;">[x]</a></td>' + '</tr>';
        });
        out += '<tr class="total"><td>Total</td><td/><td/><td>' + Math.floor(this.get_total_distance() * 10000) / 10000 + 'km</td><td/><td></td></tr>';
        $('#path').html(out + '</table><h4 class="heading">Share this track:</h4><p><pre>' + this.get_share_link() + '</pre></p>');
        var ft = this.get_flight_type();
        if (ft == 'od') { $('#decOD').removeAttr('disabled'); } else { $('#decOD').attr('disabled', 'disabled');}
        if (ft == 'or') { $('#decOR').removeAttr('disabled'); } else { $('#decOR').attr('disabled', 'disabled');}
        if (ft == 'tr') { $('#decTR').removeAttr('disabled'); } else { $('#decTR').attr('disabled', 'disabled');}

        var coordinates = this.get_coordinates();
        $('#decOR, #decOD, #decTR').each(function () {
            var obj = $(this).data('ajax-post');
            obj.coordinates = coordinates;
            $(this).data('ajax-post', obj);
        });
        reload_scrollpane();
    };

    this.set_triangle_guides = function () {
        if (this.parent.isMap()) {
            this.triangle_guides = [
                new google.maps.Polygon({clickable: false, fillColor: '#FF0000', fillOpacity: 0.25, strokeColor: '#FF0000', strokeWeight: 0.8}), new google.maps.Polygon({clickable: false, fillColor: '#FF0000', fillOpacity: 0.25, strokeColor: '#FF0000', strokeWeight: 0.8}), new google.maps.Polygon({clickable: false, fillColor: '#FF0000', fillOpacity: 0.25, strokeColor: '#FF0000', strokeWeight: 0.8})
            ];
        } else {
            this.triangle_guides = Array(3);
            var polygonPlacemark = this.parent.ge.createPlacemark('');
            polygonPlacemark.setStyleSelector(this.parent.ge.createStyle(''));
            var lineStyle = polygonPlacemark.getStyleSelector().getLineStyle();
            var polyStyle = polygonPlacemark.getStyleSelector().getPolyStyle();
            lineStyle.setWidth(2);
            lineStyle.getColor().set('990000ff');
            polyStyle.getColor().set('330000ff');
            polyStyle.setFill(1);

            var polygon = this.parent.ge.createPolygon('');
            polygonPlacemark.setGeometry(polygon);
            var polygon2 = this.parent.ge.createPolygon('');
            polygonPlacemark.setGeometry(polygon);
            var polygon3 = this.parent.ge.createPolygon('');
            polygonPlacemark.setGeometry(polygon);

            ge.getFeatures().appendChild(polygonPlacemark);

            this.triangle_guides[0] = polygon;
            this.triangle_guides[1] = polygon2;
            this.triangle_guides[2] = polygon3;
        }
    };

    this.get_flight_type = function () {
        if (this.count === 4 && this.is_equal(0, 3)) {
            if (this.min_leg() > 0.28 * this.get_total_distance()) {
                return 'tr';
            } else {
                return 'ftr';
            }
        }
        if (this.is_equal(0, 2)) { return 'or';}
        if ((this.count >= 2 && this.count <= 5)) { return 'od';}
    };

    this.min_leg = function () {
        var min = this.distance_array[1];
        this.distance_array.each(function (distance) {
            if (distance < min && distance) {
                min = distance;
            }
        });
        return min;
    };

    this.get_coordinates = function () {
        var str = [];
        this.coordinates.each(function (coordinate) {
            str.push(coordinate.gridref());
        });
        return str.join(';');
    };

    this.toGoogleEarth = function () {
        var arr = [];
        this.coordinates.each(function (c) {
            arr.push(c.toLatLng());
        });
        return arr;
    };

    this.draw = function () {
        if (!this.triangle_guides) {
            this.set_triangle_guides();
        }
        if (this.parent.isMap()) {
            if (this.mapObject) {
                this.mapObject.setMap(null);
            }
            this.mapObject = new google.maps.Polyline({
                map: this.parent.internal_map,
                path: map.planner.toGoogleEarth(),
                strokeColor: "FF0000",
                strokeOpacity: 1,
                strokeWeight: 3
            });
            google.maps.event.addListener(this.mapObject, 'click', function(h) {
                var point = map.planner.getClosestPoint(h.latLng);
                var marker = map.planner.add_marker(point.x, point.y);
                map.planner.add_waypoint(marker, point.i -1);
            });
        } else {
            if (!this.mapObject) {
                var lineStringPlacemark = this.parent.ge.createPlacemark('');
                this.mapObject = this.parent.ge.createLineString('');
                lineStringPlacemark.setGeometry(this.mapObject);
                this.mapObject.setTessellate(true);
                this.parent.ge.getFeatures().appendChild(lineStringPlacemark);
                lineStringPlacemark.setStyleSelector(this.parent.ge.createStyle(''));
                var style = lineStringPlacemark.getStyleSelector().getLineStyle();
                style.getColor().set('AA0000CC');
                style.setWidth(4);
            }
            this.mapObject.getCoordinates().clear();
            this.coordinates.each(function (coordinate, i, context) {
                context.mapObject.getCoordinates().pushLatLngAlt(coordinate.lat(), coordinate.lng(), 0);
            }, this);
        }
        var type = this.get_flight_type();
        if (type == 'tr' || type == 'ftr') {
            this.draw_triangle_guides();
        } else {
            this.hide_triangle_guides();
        }
    };

    this.getClosestPoint = function(latLng) {
        var minDist;
        var fTo;
        var fFrom;
        var x;
        var y;
        var i;
        var dist;

        if (this.count > 1) {
            for (var n = 1 ; n < this.count ; n++) {
                if (this.coordinates[n].lat() != this.coordinates[n - 1].lat()) {
                    var a = (this.coordinates[n].lng() - this.coordinates[n - 1].lng()) / (this.coordinates[n].lat() - this.coordinates[n - 1].lat());
                    var b = this.coordinates[n].lng() - a * this.coordinates[n].lat();
                    dist = Math.abs(a * latLng.lat() + b - latLng.lng()) / Math.sqrt(a * a + 1);
                } else {
                    dist = Math.abs(latLng.lat() - this.coordinates[n].lat())
                }
                var rl2 = Math.pow(this.coordinates[n].lng() - this.coordinates[n - 1].lng(), 2) + Math.pow(this.coordinates[n].lat() - this.coordinates[n - 1].lat(), 2);
                var ln2 = Math.pow(this.coordinates[n].lng() - latLng.lng(), 2) + Math.pow(this.coordinates[n].lat() - latLng.lat(), 2);
                var lnm12 = Math.pow(this.coordinates[n - 1].lng() - latLng.lng(), 2) + Math.pow(this.coordinates[n - 1].lat() - latLng.lat(), 2);
                var dist2 = Math.pow(dist, 2);
                var calcrl2 = ln2 - dist2 + lnm12 - dist2;
                if (calcrl2 > rl2) {
                    dist = Math.sqrt(Math.min(ln2, lnm12));
                }

                if ((minDist == null) || (minDist > dist)) {
                    if (calcrl2 > rl2) {
                        if (lnm12 < ln2) {
                            fTo = 0;//nearer to previous point
                            fFrom = 1;
                        } else {
                            fFrom = 0;//nearer to current point
                            fTo = 1;
                        }
                    } else {
                        // perpendicular from point intersects line segment
                        fTo = ((Math.sqrt(lnm12 - dist2)) / Math.sqrt(rl2));
                        fFrom = ((Math.sqrt(ln2 - dist2)) / Math.sqrt(rl2));
                    }
                    minDist = dist;
                    i = n;
                }
            }

            var dx = this.coordinates[i - 1].lat() - this.coordinates[i].lat();
            var dy = this.coordinates[i - 1].lng() - this.coordinates[i].lng();

            x = this.coordinates[i - 1].lat() - (dx * fTo);
            y = this.coordinates[i - 1].lng() - (dy * fTo);

        }

        return { 'x': x, 'y': y, 'i': i, 'fTo': fTo, 'fFrom': fFrom };
    }

    this.hide_triangle_guides = function () {
        if (this.parent.isMap()) {
            this.triangle_guides[0].setMap(null);
            this.triangle_guides[1].setMap(null);
            this.triangle_guides[2].setMap(null);
        } else {
            this.triangle_guides[0].setMap(null);
        }
    };

    this.draw_triangle_guides = function () {
        var point1 = [this.coordinates[0].lat(), this.coordinates[0].lng()];
        var point2 = [this.coordinates[1].lat(), this.coordinates[1].lng()];
        var point3 = [this.coordinates[2].lat(), this.coordinates[2].lng()];
        if (this.parent.isMap()) {
            this.triangle_guides[0].setPath(yessan([point1, point2, point3]));
            this.triangle_guides[1].setPath(yessan([point2, point3, point1]));
            this.triangle_guides[2].setPath(yessan([point3, point1, point2]));
            this.triangle_guides[0].setMap(this.parent.internal_map);
            this.triangle_guides[1].setMap(this.parent.internal_map);
            this.triangle_guides[2].setMap(this.parent.internal_map);
        } else {
            var t = function (points, polygon) {
                polygon.getCoordinates().clear();
                var inner = this.parent.ge.createLinearRing('');
                points.each(function (a) {
                    inner.getCoordinates().pushLatLngAlt(a.lat(), a.lng(), 0);
                });
                polygon.setOuterBoundary(inner);

            };
            var points = yessan([point1, point2, point3]);
            t(points, this.triangle_guides[0]);
            points = yessan([point2, point3, point1]);
            t(points, this.triangle_guides[1]);
            points = yessan([point3, point1, point2]);
            t(points, this.triangle_guides[2]);

        }
    };

    this.clear = function () {

        this.waypoints.each(function (point, count, ths) {
            if (ths.parent.isMap()) {
                point.setMap(null);
            } else {
                ths.parent.ge.getFeatures().removeChild(point);
            }
        }, this);
        this.waypoints = [];
        this.coordinates = [];
        this.count = 0;
        this.enabled = false;
        $("body").removeClass('waypoint_mode');
        this.writeplanner();
        this.draw();
    };

    this.push = function (coordinate, index) {
        this.coordinates.splice(index, 0, coordinate);
        this.calculate_distances();
        this.count++;
    };
    this.remove = function (index) {
        var cords = [];
        this.coordinates.each(function (coordinate, i) {
            if (i != index) {
                cords.push(coordinate);
            }
        });
        this.coordinates = cords;
        this.calculate_distances();
        this.draw();
        this.writeplanner();
        this.count--;
        return false;
    };

    this.calculate_distances = function () {
        this.distance_array = [0];
        this.total_distance_array = [0];
        this.coordinates.each(function (coordinate, a, context) {
            if (a >= 1) {
                var d = Math.acos(Math.sin(context.coordinates[a - 1].lat().toRad()) * Math.sin(coordinate.lat().toRad()) + Math.cos(context.coordinates[a - 1].lat().toRad()) * Math.cos(coordinate.lat().toRad()) * Math.cos(context.coordinates[a - 1].lng().toRad() - coordinate.lng().toRad())) * context.R;

                context.distance_array.push(d);
                context.total_distance_array.push(context.total_distance_array[a - 1] + d);
            }
        }, this);
    };

    this.is_equal = function (a, b) {
        var count = this.coordinates.length;
        if (count > a && count > b) {
            if (Math.abs(this.coordinates[a].lat() - this.coordinates[b].lat()) < 0.0001 && Math.abs(this.coordinates[a].lng() - this.coordinates[b].lng()) < 0.0001) {
                return true;
            }
        }
        return false;
    };

    this.get_total_distance = function () {
        return this.total_distance_array[this.total_distance_array.length - 1];
    };

    this.add_marker = function (lat, lon, image) {
        if (!this.enabled) {
            return;
        }
        if (this.parent.isEarth()) {
            var placemark = this.parent.ge.createPlacemark('');
            placemark.setName('');
            var point = this.parent.ge.createPoint('');
            point.setLatitude(parseFloat(lat));
            point.setLongitude(parseFloat(lon));
            placemark.setGeometry(point);
            var icon = this.parent.ge.createIcon('');
            icon.setHref(image || 'http://maps.google.com/mapfiles/kml/paddle/red-circle.png');
            var style = this.parent.ge.createStyle('');
            style.getIconStyle().setIcon(icon);
            style.getIconStyle().getHotSpot().set(0.5, this.parent.ge.UNITS_FRACTION, 0, this.parent.ge.UNITS_FRACTION);
            placemark.setStyleSelector(style);
            google.earth.addEventListener(placemark, 'mousedown', function (event) {
                map.dragInfo = {
                    placemark: event.getTarget(),
                    dragged: false
                };
            });
            google.earth.addEventListener(placemark, 'mousemove', function (event) {
                if (map.dragInfo) {
                    event.preventDefault();
                    var point = map.dragInfo.placemark.getGeometry();
                    point.setLatitude(event.getLatitude());
                    point.setLongitude(event.getLongitude());
                    map.dragInfo.dragged = true;
                    map.planner.calculate_distances();
                    map.planner.writeplanner();
                    map.planner.draw();
                }
            });
            google.earth.addEventListener(placemark, 'mouseup', function (event) {
                if (map.dragInfo && map.dragInfo.dragged) {
                    map.dragInfo = null;
                } else {
                    map.planner.push(new Coordinate(map.dragInfo.placemark.getGeometry()));
                    map.dragInfo = null;
                    map.event = event;
                }
                map.planner.writeplanner();
                map.planner.draw();
                event.preventDefault();
                event.stopPropagation();
            });
            this.parent.ge.getFeatures().appendChild(placemark);
            map.planner.waypoints.push(placemark);
        } else {
            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(lat, lon),
                map: map.internal_map,
                draggable: true
            });
            marker.id = map.planner.waypoints.length;
            google.maps.event.addListener(marker, 'click', function (event) {
                map.planner.add_waypoint(marker);
                map.event = event;
            });
            google.maps.event.addListener(marker, 'drag', function (event) {
                map.planner.writeplanner();
                map.planner.draw();
                map.planner.calculate_distances()
            });
            google.maps.event.addListener(marker, 'dragend', function (event) {
                map.planner.writeplanner();
                map.planner.draw();
                map.planner.calculate_distances()
            });

            map.planner.waypoints.push(marker);
        }
        return marker;
    };

    this.add_waypoint = function(marker, index) {
        if (index) {
            index = index || map.planner.count;
            index++;
            if (index >= map.planner.count) index = map.planner.count - 1;
        } else {
            index = map.planner.count;
        }
        //alert(index);
        map.planner.push(new Coordinate(marker), index);
        map.planner.writeplanner();
        map.planner.draw();
    }

    this.load_string = function(string) {
        this.enable();
        var groups = string.split('|');
        groups.each(function(group, g_count, ths) {
            var parts = group.split(';');
            parts.each(function(part, count, ths) {
                var sub = part.split(',');
                if(sub.length > 1) {
                    var marker = ths.add_marker(sub[0], sub[1]);
                    if (g_count == 0) {
                        new google.maps.event.trigger(marker, 'click');
                    }
                }
            }, ths);
        },this);
        map.center(this.coordinates);
    };

    this.add_point_full = function(lat, lng) {
        this.enable();
        var marker = this.add_marker(lat, lng);
        new google.maps.event.trigger( marker, 'click' );
    }
}