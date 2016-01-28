<h1>
	<img src="<?=asset_url('assets/admin/img/bitman_logo.png')?>" alt="Bitman">
	TrafficTower CMS
</h1>

<? if($failed):?>
	<div class="alert alert-danger">
		<?=$error_message?>
	</div>
<? endif;?>

<form role="form">
	<div class="form-group">
		<div class="radio">
			<label class="control-label">
				<input id="login-method-classic" type="radio" name="login_method" value="classic" checked="checked">
				Inloggen met gebruikersnaam/wachtwoord
			</label>
		</div>
		<div class="radio">
			<label class="control-label">
				<input id="login-method-persona" type="radio" name="login_method" value="persona">
				Inloggen met <em>Mozilla Persona</em>
			</label>
		</div>
	</div>
</form>

<div class="classic-login" <?php if($login_method !== 'classic'):?>style="display:none;"<?php endif;?>>
	<?=form_open('admin/login/authenticate', 'role="form"')?>
		<input type="hidden" name="location_hash" value="">
		<div class="form-group">
			<label class="control-label" for="user_email">Gebruikersnaam:</label>
			<input class="form-control" id="user_email" type="email" name="email" value="<?=string_empty($email)?>" placeholder="E-mailadres" autofocus="autofocus">
		</div>
		<div class="form-group">
			<label class="control-label" for="user_password">Wachtwoord:</label>
			<input class="form-control" id="user_password" type="password" name="password" placeholder="Wachtwoord">
		</div>
		<div class="pull-right">
			<a href="<?=site_url('admin/login/forgot_password')?>" title="Wachtwoord vergeten?">Wachtwoord vergeten?</a>
		</div>
		<button type="submit" class="btn btn-primary">Inloggen</button>
	</form>
</div>

<div class="persona-login" <?php if($login_method !== 'persona'):?>style="display:none;"<?php endif;?>>
	<?=form_open('admin/login/authenticate_persona', 'id="persona-form" role="form"')?>
		<input type="hidden" name="location_hash" value="">
		<input type="hidden" id="persona-assertion" name="assertion" value="">
		
		<p>
			<small>Log in via <a href="http://www.mozilla.org/en-US/persona/" target="_blank" title="Mozilla Persona">Mozilla Persona</a>. Het voordeel hiervan is dat het inloggen bij Mozilla Persona plaatsvind, je kunt dan met hetzelfde account bij alle diensten inloggen die Persona ondersteunen.</small>
		</p>
		
		<button id="persona-signin" type="button" class="btn btn-primary">Inloggen</button>
	</form>
</div>