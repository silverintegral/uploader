var waitList = [];

function beep_ok() {
	$('#beep_ok_btn').on('click', function() {
		$('#beep_ok_src').get(0).play();
	});
	$('#beep_ok_btn').click();
}

function beep_ng() {
	$('#beep_ng_btn').on('click', function() {
		$('#beep_ng_src').get(0).play();
	});
	$('#beep_ng_btn').click();
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

function addWaitList(files) {
	for (var i = 0; i < files.length; i++){
		var sameName=-1;
		for (var j = 0; j < waitList.length; j++) {
			if (files.item(i).name == waitList[j].name) {
				sameName = j;
				break;
			}
		}
		if (sameName < 0) {
			waitList.push(files.item(i));
			$('#waitingList').append('<li class="waitFileList">' + files.item(i).name + '</li>');
		} else {
			waitList[sameName] = files.item(i);
		}
	}
}

$(function() {
	$("#dbox").on('dragenter', function(e) {
		$(this).removeClass('d_on').addClass('d_off');
		e.stopPropagation();
		e.preventDefault();
	});

	$("#dbox").on('dragleave', function(e) {
		$(this).removeClass('d_on').addClass('d_off');
		e.stopPropagation();
		e.preventDefault();
	});

	$("#dbox").on('dragover', function(e) {
		$(this).removeClass('d_off').addClass('d_on');
		e.stopPropagation();
		e.preventDefault();
	});

	$("#dbox").on('drop', function(e) {
		$(this).removeClass('d_on').addClass('d_off');
		e.preventDefault();
		if (waitList.length == 0) {
			$('#copy_url').val('');
			$('#copy_ret').html('');
		}
		addWaitList(e.originalEvent.dataTransfer.files);
	});

	$(document).on('dragenter', function(e) {
		e.stopPropagation();
		e.preventDefault();
	});

	$(document).on('dragover', function(e) {
		e.stopPropagation();
		e.preventDefault();
	});

	$(document).on('drop', function(e) {
		e.stopPropagation();
		e.preventDefault();
	});

	$('#clearWaitList').on('click',function() {
		$('.waitFileList').remove();
		$('#selinput').val('');
		waitList = [];
	});

	$('#selbtn, #selbtn_big').on('click', function() {
		$('#selinput').click();				
	});

	$('#selinput').on('change', function(e) {
		addWaitList(e.target.files);
	});

	$('#upload').on('click',function() {
		if (waitList.length == 0)
			return;

		var fd = new FormData();
		for (var i = 0; i < waitList.length; i++) {
			$("[id^='HiddenFile']").each(function() {
				if ($(this).val() == waitList[i].name) {
					overwriteFiles.push($(this).val());
					return false;
				}
			});
			fd.append('file['+i+']', waitList[i]);
		}

		if ($('#msg').val()) {
			fd.append('msg', $('#msg').val());
		}

		if ($('#pass').val()) {
			fd.append('pass', $('#pass').val());
		}

		$.ajax({
			url: "./ajax_up.php",
			type: "POST",
			dataType: "json",
			contentType: false,
			processData: false,
			cache: false,
			timeout: 10000000,
			data: fd,
			xhr : function() {
				var XHR = $.ajaxSettings.xhr();
				if (XHR.upload) {
					XHR.upload.addEventListener('progress', function(e) {
						if (e.loaded == e.total)
							$('#copy_ret').html('<b>最適化中...（数分かかる事があります）</b>');
						else
							$('#copy_ret').html('<b>' + parseInt(e.loaded / e.total * 100) + ' %</b>');
					}, false);
				}
				return XHR;
			},
			success: function(data) {
				beep_ok();
				var url = location.origin + location.pathname.replace(new RegExp("(?:\\\/+[^\\\/]*){0,1}$"), "/") + data['path'];
				$('#copy_url').val(url);
				$('#copy_ret').html('成功：　<a href="./' + data['path'] + '" target="_blank">' + url + '</a>　<a href="#" onclick="copy();return false">COPY</a>');
				$('.waitFileList').remove();
				$('#selinput').val('');
				$('#msg').val('');
				$('#pass').val('');
				waitList = [];

				$.ajax({
					url: url + '/Z',
					type: "GET",
					contentType: false,
					processData: false,
					cache: false,
					timeout: 3000
				});
			},
			error: function(data) {
				beep_ng();
				//alert('アップロードに失敗しました');
				$('#copy_ret').html('<b>エラー</b> アップロード失敗');
			}
		});
	});
});