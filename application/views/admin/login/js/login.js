$(document).ready(function(){
	// log the user out first
	navigator.id.logout();

	$('#login-method-classic').click(function(){
		$('.classic-login').show();
		$('.persona-login').hide();
	});

	$('#login-method-persona').click(function(){
		$('.persona-login').show();
		$('.classic-login').hide();
	});

	$('#login-method-classic').prop('checked', true);
	$('#login-method-classic').click();

	// laat gebruikers inloggen met Persona
	$('#persona-signin').click(function(){
		navigator.id.request();
	});

	navigator.id.watch({
		onlogin: function(assertion) {

			$('#persona-assertion').val(assertion);
			$('#persona-form').submit();

		},
		onlogout: function() {
			// We already logged the user out on this page, so no further action required (Daan)
		}
	});

	// stop een eventuele location hash in de submit-formulieren
	$('input[name=location_hash]').val(document.location.hash);
});