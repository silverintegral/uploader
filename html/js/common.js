if (navigator.userAgent.toLowerCase().indexOf(' line/') > -1) {
	// Androidの場合はLINE内ブラウザから離脱
	if (navigator.userAgent.indexOf('Android') > -1) {
		location.replace('intent://' + location.hostname + location.pathname
			+ '#Intent;scheme=' + location.protocol.replace(':', '') + ';package=com.android.chrome;end');
	//} else {
	//	location.replace(location.origin + location.pathname.replace(new RegExp("(?:\\\/+[^\\\/]*){0,1}$"), "/") + data['path']);
	}
}

function copy() {
	var tmp = document.createElement("div");
	var pre = document.createElement('pre');
	pre.style.webkitUserSelect = 'auto';
	pre.style.userSelect = 'auto';
	tmp.appendChild(pre).textContent = $('#copy_url').val();
	var s = tmp.style;
	s.position = 'fixed';
	s.right = '200%';
	document.body.appendChild(tmp);
	document.getSelection().selectAllChildren(tmp);
	var result = document.execCommand("copy");
	document.body.removeChild(tmp);
	return result;
}
