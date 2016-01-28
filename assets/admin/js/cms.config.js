$(document).ready(function() {

	$('.datetimepicker').datetimepicker({
    	format: 'Y-m-d H:i',
    	allowBlank: true,
    	lang: 'nl',
    	step: 30,
    	dayOfWeekStart: 1,
        roundTime: 'round'
    });

    $('.datepicker').datetimepicker({
    	format: 'Y-m-d',
    	timepicker: false,
    	allowBlank: true,
    	lang: 'nl',
    	dayOfWeekStart: 1
    });

});