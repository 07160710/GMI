$.fn.setOverlay = function(){
	if($('.overlay:visible').length>0){
		$('body').css('overflow','hidden').on('touchmove', function(event) {
			event.preventDefault();
			event.stopPropagation();
		});		
	}
	else{
		$('body').css('overflow','auto').off('touchmove');
	}
}

$(function() {
	$.ajaxSetup({cache: false});
	
	$('a[href*=#],area[href*=#]').click(function() {
		if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
			var $target = $(this.hash);
			$target = $target.length && $target || $('[name=' + this.hash.slice(1) + ']');
			if ($target.length) {
				var targetOffset = $target.offset().top;console.log(targetOffset);
				$('html,body').animate({
					scrollTop: targetOffset
				},
				1000);
				return false;
			}
		}
	});
	
	$(window).scroll(function(){
		if($(window).scrollTop()>300){
			$('#to_top').fadeIn('slow');
		}else{
			$('#to_top').fadeOut('slow');
		}
	});
	
	$(window).unload(function(){
		window.location = 'manage_login.php?logout=1';
	});
	
	$(window).bind('keydown', function(event) {
		if (event.ctrlKey || event.metaKey) {
			switch (String.fromCharCode(event.which).toLowerCase()) {
				case 's':					
					if($('form button.save:visible').length>0){
						event.preventDefault();
						save_page_info();
					}
					break;
				case 'p':					
					if($('form button.publish:visible').length>0){
						event.preventDefault();
						save_page_info('publish');
					}
					break;
			}
		}
	});
});

$(document).bind('click', function(event){
	var $target = $(event.target);
	if(	!$target.is('#lnk_contact') && 
		!$target.is('#contact_info_panel') && 
		!$target.is('#contact_info_panel td') && 
		!$target.is('#contact_info_panel a') && 
		!$target.is('#contact_info_panel img')
	){
		$('#contact_info_panel').removeClass('show');
		setTimeout(function(){$('#contact_info_panel').hide();},300);
	}
	else{
		$('#contact_info_panel').show();
		setTimeout(function(){$('#contact_info_panel').addClass('show');},100);
	}
}).bind('touchstart', function(event){
	var $target = $(event.target);
	if(	!$target.is('#lnk_contact') && 
		!$target.is('#contact_info_panel') && 
		!$target.is('#contact_info_panel td') && 
		!$target.is('#contact_info_panel a') && 
		!$target.is('#contact_info_panel img')
	){
		$('#contact_info_panel').removeClass('show');
		setTimeout(function(){$('#contact_info_panel').hide();},300);
	}
	else{
		$('#contact_info_panel').show();
		setTimeout(function(){$('#contact_info_panel').addClass('show');},100);
	}
});