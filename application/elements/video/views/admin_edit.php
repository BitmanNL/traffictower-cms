<?=form_open($action, array('id' => 'new_edit_element', 'role' => 'form'))?>
	<?=form_hidden('element_type', $element_type)?>
	<?=form_hidden('element_position', $element_position)?>
	<?=form_hidden('page_id', $page_id)?>
	<? if (isset($element_id)):?>
		<?=form_hidden('element_id', $element_id)?>
		<?=form_hidden('element_content_id', $element_content_id)?>
	<? endif;?>

	<h3>
		<? if ($element_id):?>
			Video element bewerken
		<? else:?>
			Nieuw video element
		<? endif;?>
	</h3>

	<table class="table">
		<tr>
			<th class="col-md-3"><label for="form_type">Type :</label></th>
			<td class="control-group">
				<div class="row">
					<div class="col-md-6">
						<select id="form_type" name="type" class="form-control">
							<? foreach($video_types as $type => $type_url): ?>
							<? $selected = ($type == cms_set_value('type', $video['type'])) ? ' selected="selected"' : ''?>
							<option<?=$selected?> value="<?=$type?>"><?=ucfirst($type)?></option>
							<? endforeach;?>
						</select>
					</div>
				</div>
			</td>
		</tr>

		<tr>
			<th>
				<i class="glyphicon glyphicon-info-sign pull-right cms-popover" data-popover-title="Video URL" data-popover-content="Geldige URL's zijn:<br />&raquo;&nbsp;youtube.com/watch?v=h2iUofyGOG0<br />&raquo;&nbsp;vimeo.com/70323400"></i>
				<label for="form_url">Video URL :</label>
			</th>
			<td class="control-group<?=(cms_form_error('url') != '') ? ' has-error' : NULL?>">
				<div class="row">
					<div class="col-md-6">
						<input id="form_url" type="text" class="form-control" name="url" value="<?=cms_set_value('url', ($video['key']) ? $video_types[$video['type']].$video['key'] : '')?>" />
					</div>
				</div>
				<span class="help-block"><?=cms_form_error('url')?></span>
			</td>
		</tr>

		<tr>
			<th><label for="form_title">Titel :</label></th>
			<td>
				<div class="radio">
					<label><input class="title_from_api" type="radio" name="title_from_api" value="1" /> Haal titel op van Youtube/Vimeo</label>
				</div>
				<div class="radio">
					<label><input checked="checked" class="title_from_api" type="radio" id="form_title" name="title_from_api" value="0" /> Vul zelf een titel in</label> 
				</div>
				<input type="text" name="title" value="<?=cms_set_value('title', $video['title'])?>" class="form-control form_title" />
			</td>
		</tr>

		<tr>
			<th><label for="form_autoplay">Automatisch afspelen :</label></th>
			<td class="control-group<?=(cms_form_error('autoplay') != '') ? ' has-error' : NULL?>">
				<div class="radio">
					<label><input<?=(cms_set_value('autoplay', $video['autoplay']) == '0') ? ' checked="checked"' : ''?> id="form_autoplay" type="radio" class="input-medium" name="autoplay" value="0" /> Nee</label>
				</div>
				<div class="radio">
					<label><input<?=(cms_set_value('autoplay', $video['autoplay']) == '1') ? ' checked="checked"' : ''?> type="radio" class="input-medium" name="autoplay" value="1" /> Ja</label>
				</div>
				<span class="help-block"><?=cms_form_error('autoplay')?></span>
			</td>
		</tr>

		<tr>
			<th><label for="form_format">Breedte x hoogte</label></th>
			<td>

				<label class="radio">
					<input id="form_format" class="form_format" type="radio" name="format_type" value="relative"<?=(cms_set_value('format_type', $video['format_type']) != 'absolute') ? ' checked="checked"' : ''?> />
					Formaat in percentage (breedte)
				</label>

				<div class="format-relative" style="margin-left: 20px;">
					<div class="form-group<?=(cms_form_error('width_percentage') != '') ? ' has-error' : NULL?>">
						<label for="form_width_percentage">Breedte: </label>
						<div class="input-group col-md-2">
							<input id="form_width_percentage" type="text" class="form-control" name="width_percentage" value="<?=cms_set_value('width_percentage', string_empty($video['width']))?>" />
							<span class="input-group-addon">%</span>
						</div>
						<span class="help-block"><?=cms_form_error('width_percentage')?></span>
					</div>
					<div class="form-group<?=(cms_form_error('height_percentage') != '') ? ' has-error' : NULL?>">
						<label for="form_height_percentage">Hoogte (in pixels): </label>
						<div class="input-group col-md-2">
							<input id="form_height_percentage" type="text" class="form-control" name="height_percentage" value="<?=cms_set_value('height_percentage', string_empty($video['height']))?>" />
							<span class="input-group-addon">px</span>
						</div>
						<span class="help-block"><?=cms_form_error('height_percentage')?></span>
					</div>
				</div>

				<label class="radio">
					<input class="form_format" type="radio" name="format_type" value="absolute"<?=(cms_set_value('format_type', $video['format_type']) == 'absolute') ? ' checked="checked"' : ''?> />
					Formaat absoluut (in pixels)
				</label>

				<div class="format-absolute" style="margin-left: 20px;">
					<div class="form-group<?=(cms_form_error('width') != '') ? ' has-error' : NULL?>">
						<label for="form_width">Breedte: </label>
						<div class="input-group col-md-2">
							<input id="form_width" type="text" class="form-control" name="width" value="<?=cms_set_value('width', $video['width'])?>" />
							<span class="input-group-addon">px</span>
						</div>
						<span class="help-block"><?=cms_form_error('width')?></span>
					</div>
					<div class="form-group<?=(cms_form_error('height') != '') ? ' has-error' : NULL?>">
						<label for="form_height">Hoogte: </label>
						<div class="input-group col-md-2">
							<input id="form_height" type="text" class="form-control" name="height" value="<?=cms_set_value('height', $video['height'])?>" />
							<span class="input-group-addon">px</span>
						</div>
						<span class="help-block"><?=cms_form_error('height')?></span>
					</div>
				</div>

			</td>
		</tr>

	</table>

	<div class="pull-right">
		<a class="btn btn-default" href="<?=site_url('admin/page#page_id='.$page_id)?>">Annuleren</a>
		<input type="submit" name="submit" class="btn btn-success" value="Opslaan" />
	</div>

<?=form_close()?>