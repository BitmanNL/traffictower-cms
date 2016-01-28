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
			Image element bewerken
		<? else:?>
			Nieuw image element
		<? endif;?>
	</h3>

	<table class="table">

		<tr>
			<th class="col-md-3"><label for="form_title">Titel :</label></th>
			<td>
				<input id="form_title" type="text" name="title" value="<?=cms_set_value('title', $image['title'])?>" class="form-control form_title" />
			</td>
		</tr>

		<tr>
			<th class="span3"><label for="form_alt">Alt titel *:</label></th>
			<td class="control-group<?=(cms_form_error('alt') != '') ? ' has-error' : NULL?>">
				<input id="form_alt" type="text" name="alt" value="<?=cms_set_value('alt', $image['alt'])?>" class="form-control form_alt" />
				<span class="help-block"><?=cms_form_error('alt')?></span>
			</td>
		</tr>

		<tr>
			<th class="span3"><label for="form_image">Afbeelding *:</label></th>
			<td class="control-group<?=(cms_form_error('image') != '') ? ' has-error' : NULL?>">
				<input id="form_image" type="text" name="image" value="<?=cms_set_value('image', $image['image'])?>" class="form-control form_image" />
				<span class="help-block"><?=cms_form_error('image')?></span>
			</td>
		</tr>

		<tr>
			<th class="col-md-3"><label for="form_link">Link :</label></th>
			<td>
				<div class="row">
					<div class="col-md-6">
						<input id="form_link" type="text" name="link" value="<?=cms_set_value('link', $image['link'])?>" class="form-control form_link" />
					</div>
				</div>
			</td>
		</tr>


	</table>

	<div class="pull-right">
		<a class="btn btn-default" href="<?=site_url('admin/page#page_id='.$page_id)?>">Annuleren</a>
		<input type="submit" name="submit" class="btn btn-success" value="Opslaan" />
	</div>

	<script src="/assets/admin/grocery_crud/texteditor/tinymce4/tinymce.min.js"></script>
	<textarea class="mini-texteditor" style="display: none;"></textarea>

<?=form_close()?>
