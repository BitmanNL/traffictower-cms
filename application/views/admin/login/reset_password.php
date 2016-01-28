<h2>Reset wachtwoord</h2>

<?php if($failed):?>
	<div class="alert alert-danger">
		<?=$error_message?>
	</div>
<?php endif; ?>

<p>Geef een nieuw wachtwoord op.</p>

<?php echo form_open('admin/login/reset_password/'.$code, 'role="form"'); ?>

	<div class="form-group">
		<label for="field-password" class="control-label">Nieuw wachtwoord:</label>
		<input type="password" class="form-control" name="password" id="field-password" value="" placeholder="Wachtwoord" autocomplete="off">
	</div>

	<div class="form-group">
		<label for="field-password_confirm" class="control-label">Wachtwoord bevestiging:</label>
		<input type="password" class="form-control" name="password_confirm" id="field-password_confirm" value="" placeholder="Wachtwoord">
	</div>

	<button type="submit" class="btn btn-primary">Reset wachtwoord</button>

<?php echo form_close(); ?>