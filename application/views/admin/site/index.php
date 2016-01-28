<h3>Website instellingen</h3>

<?=form_open('admin/site/update', 'role="form"')?>

	<table class="table">
		<tbody>

			<tr>
				<th class="col-md-3"><?=form_label('Website naam :', 'app_site_name')?></th>
				<td<?php if(cms_form_error('site_name') != ''): ?> class="has-error"<?php endif; ?>>
					<div class="row"><div class="col-md-6">
						<?=form_input(array('id' => 'app_site_name', 'name' => 'site_name', 'value' => cms_set_value('site_name', string_empty($app_settings['site_name'])), 'class' => 'form-control'))?>
					</div></div>
					<?php if(cms_form_error('site_name') != ''): ?><p class="help-block"><?=cms_form_error('site_name')?></p><?php endif; ?>
				</td>
			</tr>

			<tr>
				<th>
					<span class="help pull-right" data-title="Omschrijving" data-description="Default omschrijving, wordt ingesteld als OpenGraph omschrijving (zichtbaar op o.a. Facebook en LinkedIn)">?</span>
					<?=form_label('Omschrijving :', 'app_description')?>
				</th>
				<td<?php if(cms_form_error('description') != ''): ?> class="has-error"<?php endif; ?>>
					<?=form_textarea(array('id' => 'app_description', 'name' => 'description', 'value' => cms_set_value('description', string_empty($app_settings['description'])), 'class' => 'form-control', 'rows' => 3))?>
					<?php if(cms_form_error('description') != ''): ?><p class="help-block"><?=cms_form_error('description')?></p><?php endif; ?>
				</td>
			</tr>

			<tr>
				<th><?=form_label('URL :', 'app_url')?></th>
				<td<?php if(cms_form_error('url') != ''): ?> class="has-error"<?php endif; ?>>
					<div class="input-group col-md-6">
						<span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
						<?=form_input(array('id' => 'app_url', 'name' => 'url', 'value' => cms_set_value('url', string_empty($app_settings['url'])), 'class' => 'form-control', 'placeholder' => 'http://www.bitman.nl'))?>
					</div>
					<?php if(cms_form_error('url') != ''): ?><p class="help-block"><?=cms_form_error('url')?></p><?php endif; ?>
				</td>
			</tr>

			<tr>
				<th><?=form_label('E-mailadres :', 'app_email')?></th>
				<td<?php if(cms_form_error('email') != ''): ?> class="has-error"<?php endif; ?>>
					<div class="input-group col-md-6">
						<span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
						<?=form_input(array('id' => 'app_email', 'name' => 'email', 'value' => cms_set_value('email', string_empty($app_settings['email'])), 'class' => 'form-control', 'type' => 'email', 'placeholder' => 'info@bitman.nl'))?>
					</div>
					<?php if(cms_form_error('email') != ''): ?><p class="help-block"><?=cms_form_error('email')?></p><?php endif; ?>
				</td>
			</tr>

			<tr>
				<th>
					<span class="help pull-right" data-title="Afbeelding" data-description="Default afbeelding, wordt ingesteld als OpenGraph afbeelding (zichtbaar op o.a. Facebook en LinkedIn)">?</span>
					<?=form_label('Afbeelding :', 'app_image')?>
				</th>
				<td>
					<?=form_input(array('id' => 'app_image', 'name' => 'image', 'value' => cms_set_value('image', string_empty($app_settings['image'])), 'type' => 'text'))?>
				</td>
			</tr>

			<tr>
				<th>
					<span class="help pull-right" data-title="Apple-touch-icon" data-description="Bureaublad-icoon voor op de iPad en iPhone. Restricties: PNG-formaat, hoogte-breedte gelijk (vierkant).">?</span>
					<?=form_label('Apple-touch-icon :', 'app_apple_touch_icon')?>
				</th>
				<td>
					<?=form_input(array('id' => 'app_apple_touch_icon', 'name' => 'apple_touch_icon', 'value' => cms_set_value('apple_touch_icon', string_empty($app_settings['apple_touch_icon'])), 'type' => 'text'))?>
				</td>
			</tr>

		</tbody>
	</table>

	<div class="pull-right">
		<?=form_button(array('type' => 'submit', 'class' => 'btn btn-success', 'content' => 'Opslaan'))?>
	</div>

	<div class="clearfix"></div>

	<textarea class="mini-texteditor" style="display: none;"></textarea>

<?=form_close()?>
