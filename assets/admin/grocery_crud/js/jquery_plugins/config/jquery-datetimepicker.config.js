$(document).ready(function() {

	$('.datetime-input').datetimepicker({
    	format: 'd/m/Y H:i',
    	allowBlank: true,
    	lang: 'nl',
    	step: 30,
    	dayOfWeekStart: 1,
        roundTime: 'round'
    });

    $('.date-input').datetimepicker({
    	format: 'd/m/Y',
    	timepicker: false,
    	allowBlank: true,
    	lang: 'nl',
    	dayOfWeekStart: 1
    });
	
});