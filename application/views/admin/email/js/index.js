$(document).ready(function() {

	emailUpdateFrom();
	emailUpdateTo();

	$('#field-from_available').on('change', function(evt, params) {
		emailUpdateFrom();
	});

	$('#field-to_available').on('change', function(evt, params) {
		emailUpdateTo();
	});

	if ($('#field-template').val() == '') {
		$('#field-template').val('default');
	}

	function emailUpdateFrom() {
		var value = $('#field-from_available').val();
		if (value == 'no') {
			$('#from_email_field_box').hide();
			$('#from_name_field_box').hide();
			$('#field-from_email').val('');
			$('#field-from_name').val('');
		} else {
			$('#from_email_field_box').show();
			$('#from_name_field_box').show();
		}
	}

	function emailUpdateTo() {
		var value = $('#field-to_available').val();
		if (value == 'no') {
			$('#to_email_field_box').hide();
			$('#to_name_field_box').hide();
			$('#field-to_email').val('');
			$('#field-to_name').val('');
		} else {
			$('#to_email_field_box').show();
			$('#to_name_field_box').show();
		}
	}

});