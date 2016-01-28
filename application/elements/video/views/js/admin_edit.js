$(document).ready(function(){

	$('.title_from_api').click(function(){
		var title_from_api = $('#form_title').is(':checked');

		if(title_from_api){
			$('.form_title').fadeIn();
		}else{
			$('.form_title').hide();
		}
	});

	videoElement_formFormat();
	$('.form_format').change(function(){
		videoElement_formFormat();
	});

});

function videoElement_formFormat(){
	var format_type = $('#form_format').prop('checked');

	if(format_type){
		// relative
		$('.format-relative').show();
		$('.format-absolute').hide();
		$('.format-absolute input').val('');
	}
	else{
		// absolute
		$('.format-relative').hide();
		$('.format-absolute').show();
		$('.format-relative input').val('');
	}
}