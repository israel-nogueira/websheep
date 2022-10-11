var _window 		= window;
var _console 		= console;
var _url_ws_rec 	= "/admin/app/ws-modules/ws-logs/functions.php";

_window.onerror 	= function (msg, url, lineNo, columnNo, error) {
	_console.error("\nURL: "+url+":"+lineNo+" \nLINHA: "+lineNo+"\nPOSIÇÃO : "+columnNo+"\nERRO: "+error+"\n");
	_window.sendWSLog("type=error&url=" + url + "&linha=" + lineNo + "&coluna="+columnNo+"&mensagem=" + error,url);
	return true;
};
_window.addEventListener('error', function (e) {
	e.preventDefault();
	var ie = _window.event || {};
	var errMsg = e.message || ie.errorMessage || "404 error on " + _window.location;
	var errSrc = (e.filename || ie.errorUrl) + ': ' + (e.lineno || ie.errorLine);
	var target = e.path;
	var TagName = target[0].localName;
	var url;
	if (TagName == "img") 		{url =	target[0].currentSrc;} 
	if (TagName == "link") 		{url =	target[0].href;}
	if (TagName == "script") 	{url =	target[0].src;}
	_window.sendWSLog("type=error&url=" + url + "&linha=null&coluna=null&mensagem=Erro 404",url);
	return false;
}, true);
_window.sendWSLog = function (params) {
	var http = new XMLHttpRequest();
	var params = "function=recLogError&" + params;
	http.open("POST", _url_ws_rec, true);
	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http.onreadystatechange = function () {
		if (http.readyState == 4 && http.status == 200) {
			 _console.log(http.responseText);
		}
	}
	http.send(params);
}

var open 		= XMLHttpRequest.prototype.open;
var send 		= XMLHttpRequest.prototype.send;
var done 		= XMLHttpRequest.prototype.done;
XMLHttpRequest.prototype.open = function (method, url, async, user, pass) {
	this._url = url;
	open.call(this, method, url, async, user, pass);
};

XMLHttpRequest.prototype.send = function (data) {
	var self = this;
	var start;
	var oldOnReadyStateChange;
	var url = this._url;
		if (self.readyState == 4) {
			if (self.status === 0) {
				_console.error('Desconectado, verifique a internet');
				_window.sendWSLog("type=warn&url=" + self._url + "&linha=null&coluna=null&mensagem=Offline",self._url);
			} else if (self.status == 404) {
				_console.error('Arquivo inexistente. [404]');
				_window.sendWSLog("type=error&url=" + self._url + "&linha=null&coluna=null&mensagem=Erro 404",self._url);
			} else if (self.status == 500) {
				_console.error('Erro de servidor [500]');
				_window.sendWSLog("type=error&url=" + self._url + "&linha=null&coluna=null&mensagem=Erro 500",self._url);
			}
		}
	send.call(this, data);
}

_window.onload = function () {
	 var print_my_arguments= function(){
	 	var objConsole = []
		for (var i = 0; i < arguments[0].length; i++) {
			var item = arguments[0][i];
			objConsole.push(item);
		}
		return objConsole;
	};
	console = {
		info: function () {
			 _console.info(JSON.parse(JSON.stringify(arguments)));
			 _window.sendWSLog("type=info&url=null&linha=null&coluna=null&mensagem="+JSON.stringify(arguments),null);
		},
		warn: function () {
			 _console.warn(JSON.parse(JSON.stringify(arguments)));
			 _window.sendWSLog("type=warn&url=null&linha=null&coluna=null&mensagem="+JSON.stringify(arguments),null);
		},
		error: function () {
			 _console.error(JSON.parse(JSON.stringify(arguments)));
			 _window.sendWSLog("type=error&url=null&linha=null&coluna=null&mensagem="+JSON.stringify(arguments),null);
		},
		log: function () {
			 _console.log(JSON.parse(JSON.stringify(arguments)));
			 _window.sendWSLog("type=log&url=null&linha=null&coluna=null&mensagem="+JSON.stringify(arguments),null);
		}
	};
};