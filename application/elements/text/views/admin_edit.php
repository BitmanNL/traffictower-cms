<?=form_open($action, 'id="new_edit_element" role="form"')?>
	<?=form_hidden('element_type', $element_type)?>
	<?=form_hidden('element_position', $element_position)?>
	<?=form_hidden('page_id', $page_id)?>
	<? if (isset($element_id)):?>
		<?=form_hidden('element_id', $element_id)?>
		<?=form_hidden('element_content_id', $element_content_id)?>
		<?=form_hidden('element_revision_id', $element_revision_id)?>
	<? endif;?>

	<h3>
		<? if ($element_id):?>
			Tekstelement bewerken
		<? else:?>
			Nieuw tekstelement
		<? endif;?>
	</h3>

	<? if($current_status != 'new'): ?>
		<? if($current_status == 'concept'): ?>
			<div class="alert alert-warning">
				<strong>Concept</strong> (druk op 'Publiceer' om deze versie beschikbaar te maken op de frontpage)
			</div>
		<? elseif($current_status == 'published'): ?>
			<div class="alert alert-success">
				<strong>Actieve publicatie</strong> van <?=date('d-m-Y H:i', strtotime($published['date_modified']))?> door <?=$published['user']?>
			</div>
		<? else: ?>
			<div class="alert alert-info">
				<strong>Oude publicatie</strong> van <?=date('d-m-Y H:i', strtotime($text['date_modified']))?> door <?=$text['user']?>
			</div>
		<? endif; ?>
	<? endif; ?>


	<table class="table">
		<tr>
			<th class="col-md-3"><label for="text_title">Titel :</label></th>
			<td class="control-group"><input id="text_title" type="text" class="form-control" name="element_title" value="<?=$text['title']?>" /></td>
		</tr>
		<tr>
			<th><label for="text_content">Inhoud :</label></th>
			<td class="control-group"><textarea id="text_content" name="element_content" class="input-xxlarge mini-texteditor"><?=$text['content']?></textarea></td>
		</tr>

		<?php if (!empty($element_text_types)): ?>
		<tr>
			<th><label for="text_type">Type :</label></th>
			<td class="control-group">
				<div class="row">
					<div class="col-lg-4 col-md-6">
						<select name="element_text_type" id="text_type" class="form-control">
							<option value="">- Geen -</option>
							<?php foreach($element_text_types as $key => $element_text_type): ?>
								<option value="<?=$key?>"<?=($key == $text['type']) ? ' selected="selected"' : ''?>><?=$element_text_type?></option>
							<?php endforeach; ?>
						</div>
					</div>
				</select>
			</td>
		</tr>
		<?php endif; ?>

	</table>

	<div class="pull-right">
		<input type="hidden" name="overwrite_concept" id="overwrite_concept" value="0" />



		<a class="btn btn-default" href="<?=site_url('admin/page#page_id='.$page_id)?>">Terug naar overzicht</a>
		
		<? if($current_status == 'new' || $current_status == 'concept'): ?>
			<? if($current_status == 'concept' && count($revisions) > 0): ?>
				<input id="btn_remove_concept" type="submit" name="remove_concept" class="btn btn-danger" value="Verwerp concept" />
			<? endif; ?>
			<input id="btn_submit" type="submit" name="submit" class="btn btn-info" value="Concept opslaan" />
			<input id="btn_publish" type="submit" name="publish" class="btn btn-success" value="Publiceer" />
		<? else: ?>
			<input id="btn_submit" type="submit" name="submit" class="btn btn-info" value="Opslaan als nieuw concept" />
			<input id="btn_publish" type="submit" name="publish" class="btn btn-success" value="Publiceer" />
		<? endif; ?>

	</div>

	<div class="clearfix"></div>

	<? if($current_status != 'concept' && count($revisions) > 1): ?>
		<h3>Geschiedenis</h3>
		<p>Bekijk publicaties uit het verleden.</p>
		<div class="row"><div class="col-md-6">
		<?=form_dropdown('revision_id', $revisions, $element_revision_id, 'id="text_revisions" class="form-control"')?>
		</div></div>
	<? endif; ?>

<?=form_close()?>