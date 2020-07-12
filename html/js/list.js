var sel_mode = 0;
var sel_list = [];

$(function() {
	$(window).on('resize', function() {
		var body = $('html').width() - 20 - 6;
		var cou = Math.floor(body / 210);
		//console.log(sels.length * 210 + '/' + body);
		if ((sels.length + 1) * 210 < body) {
			$('#lightbox-margin').css('width', 0);
			$('#lightbox-margin-table').css('margin', 'auto');
		} else {
			$('#lightbox-margin').css('width', Math.floor((body - cou * 210) / 2) + 'px');
			$('#lightbox-margin-table').css('margin', '0');
		}
	});
	$(window).resize();
	$('#lightbox-margin-table').css('opacity','1');
  

	$('.simg').on('load', function() {
		$(this).css('padding', '0').css('height', 'auto').animate({opacity: '1'}, 500);
	});

	$('.simg').each(function() {
		$(this).attr('src', $(this).attr('s'));
	});
	
	$('.simg').on('click', function() {
		var now = sels.indexOf($(this).attr('title')) + 1;
		$('.sbox:nth-of-type(' + now + ')').css('border', 'solid 2px #8af');
		$('.sbox:nth-of-type(' + sel + ')').css('border', 'solid 2px #666');
		sel = now;
	});

	$('.simg').dblclick(function(){
		$(this).css('opacity', '0.1').animate({opacity: '1'}, 700);
		location.href = $(this).attr('url');
	});

	var g = $('.lightbox a').simpleLightbox({sourceAttr: 'm', fileExt: 'm|s', doubleTapZoom: 2, animationSpeed: 150, overlay:true});
	g.on('changed.simplelightbox', function() {
		var url = $('.sl-image').parent().find('img').attr('src');
		/*
		if (url.substr(0, 3) == 'qr/') {
			$('.sl-org').css('display', 'none');
			$('.sl-dl').css('display', 'none');
		} else {
			$('.sl-org').css('display', 'block');
			$('.sl-dl').css('display', 'block');
		}
		*/
		url = url.substring(url.lastIndexOf('/') + 1);
		var now = sels.indexOf(url.substring(0, url.length - 2)) + 1;
		$('.sbox:nth-of-type(' + now + ')').css('border', 'solid 2px #8af');
		$('.sbox:nth-of-type(' + sel + ')').css('border', 'solid 2px #666');
		sel = now;
	})
	.on('shown.simplelightbox', function() {
		if (navigator.userAgent.indexOf('iPhone') > -1 || navigator.userAgent.indexOf('iPad') > -1 || navigator.userAgent.indexOf('iPod') > -1) {
			$('.sl-navigation').append('<div class="sl-org">ORG</div>');
		} else {
			$('.sl-navigation').append('<div class="sl-org">ORG</div><div class="sl-dl">DL</div>');

			$('.sl-dl').off('click');
			$('.sl-dl').on('click',function(e) {
				var dl_url = $('.sl-image').parent().find('img').attr('src');
				location.href = dl_url.substring(0, dl_url.length - 2) + '/D';
				e.stopPropagation();
				return false;
			});
		}
		var url = $('.sl-image').parent().find('img').attr('src');
		if (url.substr(0, 3) == 'qr/') {
			$('.sl-org').css('display', 'none');
			$('.sl-dl').css('display', 'none');
		} else {
			$('.sl-org').css('display', 'block');
			$('.sl-dl').css('display', 'block');
		}

		$('.sl-org').off('click');
		$('.sl-org').on('click',function(e) {
			var org_url = $('.sl-image').parent().find('img').attr('src');
			window.open(org_url.substring(0, org_url.length - 2));
			e.stopPropagation();
			return false;
		});
	});

	$('.sbox').on('long-press',function() {
		if (sel_mode == 1) {
			$(this).css('opacity', '0.1').animate({opacity: '1'}, 700);
			window.open($(this).attr('url'));
		} else {
			$(this).css('opacity', '0.1').animate({opacity: '1'}, 700);
			location.href = $(this).attr('url') + '/D';
		}
	});

	$('.bbox').on('click',function() {
		if (sel_mode > 0) {
			var title = $(this).parent('div').find('img').attr('title');
			if (sel_list.indexOf(title) > -1) {
				sel_list.splice(title, 1);
				$(this).css('background-color', '#000');
			} else {
				sel_list.push(title);
				$(this).css('background-color', '#79f');
			}
		} else {
			$(this).parent('div').find('img').click();
		}
	});

	$('.simg').on('click',function() {
		if (sel_mode > 0) {
			var title = $(this).attr('title');
				//alert($(this).closest('.sbox').attr('url'));
			if (sel_list.indexOf(title) > -1) {
				sel_list.splice(title, 1);
				$(this).closest('.sbox').find('div').css('background-color', '#000');
			} else {
				sel_list.push(title);
				$(this).closest('.sbox').find('div').css('background-color', '#79f');
			}
			return false;
		}
	});
	
	$('#sel_start,#sel_cancel').on('click', function(e) {
		if (sel_mode == 0) {
			if ($('.bbox').css('pointer-events') == 'none')
				sel_mode = 1;
			else
				sel_mode = 2;
			$('.bbox').css('background-color', '#000').css('opacity', '0.5');
			$('#dlpanel').css('display', 'block');
		} else if (sel_mode == 1) {
			sel_mode = 0;
			sel_list = [];
			$('.bbox').css('pointer-events', 'none').css('background-color', '#000').css('opacity', '0');
			$('#dlpanel').css('display', 'none');
		} else {
			sel_mode = 0;
			sel_list = [];
			$('.bbox').css('background-color', '#000').css('opacity', '0');
			$('#dlpanel').css('display', 'none');
		}

		return false;
	});

	$('#sel_dl').on('click', function() {
		if (sel_list.length == 0)
			return false;

		$('#dlpanel').css('display', 'none');
		$('#dlpanel_e').css('display', 'block');
		setTimeout(function() {
			$('#dlpanel_e').animate({opacity: '0'}, 500, function() {
				$('#dlpanel_e').css('display', 'none').css('opacity', '1');
			});
		}, 3000);
		
		var arg  = [];
		url = location.search.substring(1).split('&');
		for(i=0; url[i]; i++) {
			var k = url[i].split('=');
			arg[k[0]] = k[1];
		}

		console.log(sel_list);
		console.log(sel_list.join(','));
		$('.bbox').css('background-color', '#000').css('opacity', '0');
		//alert(location.origin + location.pathname + '/Z/' + sel_list.join(','));
		location.href = location.origin + location.pathname + '/Z/' + sel_list.join(',');

		sel_list = [];
		sel_mode = 0;
		return false;
	});

	$('#sel_reset').on('click', function() {
		sel_list = [];
		$('.bbox').css('background-color', '#000').css('opacity', '0.4');

		return false;
	});
});
