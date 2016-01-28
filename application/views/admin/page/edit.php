<div class="pull-right">
	<a href="<?=site_url($page_preview_url)?>" target="_blank" class="btn btn-default">Preview pagina</a>
	<button id="btn_show_page" class="btn btn-info btn_show_hide_page" <? if ($page['is_visible'] == 'yes'):?>style="display:none;"<? endif;?>>Toon pagina</button>
	<?php if($page['is_system_page'] === 'no' || is_super_user()): ?>
		<button id="btn_hide_page" class="btn btn-<?=($page['is_system_page'] === 'yes' ? 'warning' : 'info')?> btn_show_hide_page" <? if ($page['is_visible'] != 'yes'):?>style="display:none;"<? endif;?>>Verberg pagina</button>
		<button id="btn_delete_page" class="btn btn-<?=($page['is_system_page'] === 'yes' ? 'warning' : 'danger')?>">Verwijder pagina</button>
	<?php endif; ?>
</div>

<ul class="nav nav-tabs">
  <li class="tab-page-meta"><a href="#page-meta" data-toggle="tab"><strong>Meta</strong></a></li>
  <li class="tab-page-content"><a href="#page-content" data-toggle="tab"><strong>Inhoud</strong></a></li>
</ul>

<div class="clearfix"></div>

<br>

<div class="tab-content">
	<div class="tab-pane" id="page-meta">

		<?php if ($page['is_system_page'] === 'yes'): ?>
			<div class="alert alert-warning">
				<strong>Let op:</strong> Deze pagina is gekoppeld aan project-specifieke code. <strong>Gooi deze pagina niet weg!</strong>
			</div>
		<?php endif; ?>

		<form id="edit_page" role="form">
			<? if(!is_super_user()):?>
				<input type="hidden" name="controller" value="<?=$page['controller']?>" />
				<input type="hidden" name="layout" value="<?=$page['layout']?>" />
			<? endif;?>
			<input type="hidden" name="language" value="<?=$page['language']?>" />
			<table class="table">

				<tr>
					<th><label for="page_title">Titel :</label></th>
					<td>
						<input id="page_title" type="text" class="form-control" name="title" value="<?=$page['title']?>" />
					</td>
				</tr>

				<tr>
					<th><label for="page_slug">URL :</label><span class="help pull-right" data-title="URL" data-description="Gebruikersvriendelijke link waarop deze pagina bereikbaar is.<br>Instelbaar indien de pagina verborgen is.">?</span></th>
					<td>
						<div class="input-group">
							<span class="input-group-addon"><?=rtrim(site_url($language_append_url), '/')?>/</span>
							<input id="page_slug" type="text" class="form-control" name="slug" value="<?=$page['slug']?>"<?=(!is_super_user()  && (!empty($page['controller']) || $page['is_visible'] == 'yes')) ? ' disabled="disabled"' : NULL?> />
						</div>
					</td>
				</tr>
			
				<tr>
					<th><label for="page_description">Omschrijving :</label><span class="help pull-right" data-title="Meta omschrijving" data-description="Wordt ingesteld als meta omschrijving (Zoekresultaten Google, delen op Facebook e.a.).<br>Indien leeg wordt de standaard meta omschrijving gebruikt, in te stellen onder 'Site' > 'Website instellingen'.">?</span></th>
					<td>
						<textarea id="page_description" class="form-control" name="description" rows="3" maxlength="255" placeholder="Pagina specifieke meta-omschrijving"><?=$page['description']?></textarea>
					</td>
				</tr>

				<tr>
					<th><label for="page_in_menu">Zichtbaar in menu :</label></th>
					<td>
						<label class="radio-inline"><input id="page_in_menu" type="radio" name="in_menu" value="yes"<?=($page['in_menu'] != 'no') ? ' checked="checked"' : ''?> /> Ja</label>
						<label class="radio-inline"><input type="radio" name="in_menu" value="no"<?=($page['in_menu'] == 'no') ? ' checked="checked"' : ''?> /> Nee</label>
					</td>
				</tr>

				<tr>
					<th><label for="replace_by">Vervang pagina door :</label></th>
					<td>
						<div class="row">
							<div class="col-md-6">
									<select name="replace_by" class="replace-by-selector form-control">
									<option value="">Geen</option>
									<option value="internal"<?=($page['replace_by'] == 'internal') ? ' selected="selected"' : ''?>>Interne pagina</option>
									<option value="external"<?=($page['replace_by'] == 'external') ? ' selected="selected"' : ''?>>Custom URL</option>
									<option value="first_sub"<?=($page['replace_by'] == 'first_sub') ? ' selected="selected"' : ''?>>Eerste subpagina</option>
								</select>
							</div>
						</div>
					</td>
				</tr>
				<tr class="replace-by replace-by-external hide">
					<th><label for="replace_value_external">Custom URL :</label><span class="help pull-right" data-title="Custom URL" data-description="Zowel interne als externe URL's zijn mogelijk.<br><br>Voorbeelden:<br>- zoeken#resultaten<br>- /artikelen/?pagina=2<br>- http://www.bitman.nl<br>- //www.bitman.nl">?</span></th>
					<td>
						<div class="input-group">
							<span class="input-group-addon"><span class="glyphicon glyphicon-globe"></span></span>	
							<input type="text" class="form-control" name="replace_value_external" id="replace_value_external" placeholder="http://www.bitman.nl" value="<?=$page['replace_value_external']?>">
						</div>
					</td>
				</tr>
				<tr class="replace-by replace-by-internal hide">
					<th><label for="replace_value_internal">Interne pagina :</label></th>
					<td>
						<div class="row">
							<div class="col-md-6">
								<select id="replace_value_internal" name="replace_value_internal" class="form-control">
									<option value="">Kies pagina</option>
									<?php foreach($internal_pages as $internal_page): ?>
										<?php if(!is_null($internal_page['id'])): ?>
											<option value="<?=$internal_page['id']?>"<?=($internal_page['id'] == $page['replace_value_internal']) ? ' selected="selected"' : ''?>><?=$internal_page['title']?></option>
										<?php else: ?>
											<option disabled="disabled"><?=$internal_page['title']?></option>
										<?php endif; ?>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</td>
				</tr>

				<?php if(empty($page['controller']) || is_super_user()): ?>
					<tr>
						<th><label for="page_layout">Layout :</label></th>
						<td>
							<div class="row">
								<div class="col-md-6">
									<?=form_dropdown('layout', $layouts, $page['layout'], 'class="form-control"')?>
								</div>
							</div>
						</td>
					</tr>
				<? endif;?>

				<? if(!$is_default_language):?>
					<tr>
						<th><label for="page_layout">Gerelateerd aan :</label></th>
						<td>
							<div class="row">
								<div class="col-md-6">
									<?=form_dropdown('relative_page_id', $related_pages, $page['relative_page_id'], 'class="form-control"')?>
								</div>
							</div>
						</td>
					</tr>
				<? endif;?>

				<? if(is_super_user()):?>
					<tr class="warning">
						<th>
							<span class="help pull-right" data-title="Controller" data-description="Koppel deze pagina aan een controller.<br>Format:<br>- [controller]<br>- [controller]/[method]<br><br><b>Let op:</b><br>Om deze pagina naar de controller te verwijzen in de navigatie moet de slug overeenkomen met de controller aanroep!">?</span>
							<label for="page_controller">Controller :</label>
						</th>
						<td>
							<div class="row">
								<div class="col-md-6">
									<input id="page_controller" type="text" class="form-control" name="controller" value="<?=$page['controller']?>" />
								</div>
							</div>
						</td>
					</tr>
					<tr class="warning">
						<th>
							<span class="help pull-right" data-title="Controller parameters" data-description="Breid controller koppeling uit met een set parameters.<br>Format: query string<br><br>Voorbeelden:<br>- id=13<br>- id=14&type=theme">?</span>
							<label for="page_controller_params">Controller parameters :</label>
						</th>
						<td>
							<div class="row">
								<div class="col-md-6">
									<input id="page_controller_params" type="text" class="form-control" name="controller_params" value="<?=$page['controller_params']?>" />
								</div>
							</div>
						</td>
					</tr>
					<tr class="warning">
						<th>
							<span class="help pull-right" data-title="Systeempagina's" data-description="Een systeempagina wordt gebruikt door een controller of als succes of fail pagina. Deze pagina's mogen dus niet zomaar verwijderd worden, ook mag de slug niet gewijzigd worden. Als de Systeempagina op 'ja' staat, mogen deze acties alleen door de superuser gedaan worden.">?</span>
							<label for="page_is_system_page">Is systeem pagina :</label>
						</th>
						<td>
							<div class="row">
								<div class="col-md-6">
									<label class="radio-inline"><input id="page_is_system_page" type="radio" name="is_system_page" value="yes"<?=($page['is_system_page'] != 'no') ? ' checked="checked"' : ''?> /> Ja</label>
									<label class="radio-inline"><input type="radio" name="is_system_page" value="no"<?=(empty($page['is_system_page']) || $page['is_system_page'] == 'no') ? ' checked="checked"' : ''?> <?php if (!empty($page['controller'])):?>disabled<?php endif;?> /> Nee</label>
								</div>
							</div>
						</td>
					</tr>
					<?php if ($page['parent_id'] == 0 && !empty($loaded_modules)):?>
						<tr class="warning">
							<th><label for="page_module">Module :</label></th>
							<td>
								<div class="row">
									<div class="col-md-6">
										<select name="module" id="page_module" class="form-control">
											<option value=''></option>
											<?php foreach ($loaded_modules as $module):?>
												<option value='<?=$module?>' <?=($page['module'] == $module) ? 'selected="selected"' : ''?>><?=ucfirst($module)?></option>
											<?php endforeach;?>
										</select>
									</div>
								</div>
							</td>
						</tr>
					<?php endif;?>
				<?php endif; ?>

			</table>
		</form>

		<button id="btn_saved_page" class="btn btn-success edit_page disabled">Wijzigingen opgeslagen</button>
		<button id="btn_save_page" class="btn btn-success edit_page" style="display:none;">Wijzigingen opslaan</button>

		<br style="clear:both;" />

	</div>

	<div class="tab-pane" id="page-content" data-page-id="<?=$page['id']?>">

		<?=$elements_container?>

		<? if (isset($lost_elements) AND !empty($lost_elements)):?>
			<div id="lost-elements" class="element-positions-holder">
				<h4>Dakloze elementen</h3>
				<ul data-position="lost-elements" class="element-positions">
					<? foreach ($lost_elements as $element):?>
						<li data-element-id="<?=$element['id']?>" class="ui-state-default element-<?=$element['id']?> <?=($element['is_visible'] == 'no' ? 'element_invisible' : '')?><?=($element['is_global']) ? ' element-global' : ''?>">
							<div class="element_bar element-handler">
								<div class="element_controls template" style="display:none;">
									<i name="global_element" title="Maak dit element globaal" class="glyphicon glyphicon-globe <?=($element['is_global'] ? 'hide' : '')?>"></i>
									<i name="local_element" title="Maak dit element lokaal" class="glyphicon glyphicon-adjust <?=(!$element['is_global'] ? 'hide' : '')?>"></i>
									<i name="hide_element" title="Verberg" class="glyphicon glyphicon-ban-circle <?=($element['is_visible'] == 'no' ? 'hide' : '')?>"></i>
									<i name="show_element" title="Toon" class="glyphicon glyphicon-ok-sign <?=($element['is_visible'] == 'yes' ? 'hide' : '')?>"></i>
									<i name="edit_element" title="Wijzig" class="glyphicon glyphicon-pencil"></i>
									<i name="delete_element" title="Verwijder" class="glyphicon glyphicon-trash"></i>
								</div>
								<i class="glyphicon glyphicon-move"></i> <strong><?=ucfirst($element['type'])?></strong>
							</div>
							<div class="element_content">
								<?=$element['content']?>
							</div>
						</li>
					<? endforeach;?>
				</ul>
			</div>
		<? endif;?>

	</div>
</div>