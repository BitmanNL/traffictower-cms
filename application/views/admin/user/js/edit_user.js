$(document).ready(function(){

    // remove grocery_cruds new_password field
    $('#field-new_password_repeat').remove();

	// type new password button
	$('#type_new_password').click(function(){

        $('#send_mail_to_user').removeClass('hide');

        // hide generate password field if shown
        $('#generate_new_password_holder').addClass('hide');

        // clear password fields
		$('#new_password').val('');
        $('#new_password_repeat').val('');

		// show new password fields
		$('#type_new_password_holder').removeClass('hide');

	});

	// generate new password button
	$('#generate_new_password').click(function(){
        
        $('#send_mail_to_user').removeClass('hide');

		// generate new password
		var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
        var string_length = 10;
        var password = '';
        for (var i=0; i<string_length; i++) {
            var rnum = Math.floor(Math.random() * chars.length);
            password += chars.substring(rnum,rnum+1);
        }

        $('#new_generated_password').text(password);
        $('#new_password').val(password);
        $('#new_password_repeat').val(password);

        // hide type password field if shown
        $('#type_new_password_holder').addClass('hide');

		// show new password fields
		$('#generate_new_password_holder').removeClass('hide');

	});

});