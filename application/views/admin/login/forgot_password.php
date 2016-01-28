<h2>Wachtwoord vergeten?</h2>

<? if($failed):?>
	<div class="alert alert-danger">
		<?=$error_message?>
	</div>
<? endif;?>

<p>Vraag een nieuw wachtwoord aan door je gebruikersnaam in te vullen.</p>

<?php echo form_open('admin/login/forgot_password', 'role="form"'); ?>

	<div class="form-group">
		<label for="field-email" class="control-label">Gebruikersnaam:</label>
		<input type="email" class="form-control" name="email" id="field-email" value="" placeholder="E-mailadres">
	</div>

	<button type="submit" class="btn btn-primary">Wachtwoord aanvragen</button>

<?php echo form_close(); ?>