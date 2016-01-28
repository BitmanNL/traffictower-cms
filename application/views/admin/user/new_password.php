<button type="button" id="generate_new_password" class="btn btn-info">Genereer nieuw wachtwoord</button>
<button type="button" id="type_new_password" class="btn btn-info">Voer nieuw wachtwoord in</button>

<div id="type_new_password_holder" class="hide row">
	<div class="col-md-6">
		<input type="password" name="new_password" class="form-control" id="new_password" placeholder="Nieuw wachtwoord" autocomplete="off" />

	</div>
	<div class="clearfix"></div>
	<br>
	<div class="col-md-6">
		<input type="password" name="new_password_repeat" class="form-control" id="new_password_repeat" placeholder="Herhaal nieuw wachtwoord" autocomplete="off" />
	</div>
</div>

<div id="generate_new_password_holder" class="hide">
	<div class="control-group">
		<span id="new_generated_password">password</span>
	</div>
</div>

<div id="send_mail_to_user" class="hide checkbox">
	<label for="user_send_mail_to_user">
		<input type="checkbox" id="user_send_mail_to_user" name="send_mail_to_user" value="1" />
		Verzend e-mail naar deze gebruiker met bovenstaande wachtwoord
	</label>
</div>