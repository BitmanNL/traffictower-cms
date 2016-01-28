<div class="element-positions-holder">
	<h4><?=$position_title?></h3>
	<ul data-position="<?=$position?>" class="element-positions">
		<? if (isset($elements[$position])):?>
			<? foreach ($elements[$position] as $element):?>
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
						<i class="glyphicon glyphicon-move"></i> <strong><?=$element['type_name']?></strong>
					</div>
					<div class="element_content">
						<?=$element['content']?>
					</div>
				</li>
			<? endforeach;?>
		<? endif;?>
	</ul>
	
	<div class="add_elements">
		<form class="form-inline">
			<select class="element_new_<?=$position?> form-control">
				<? foreach($loaded_elements as $element => $element_name): ?>
					<option value="<?=$element?>"><?=$element_name?></option>
				<? endforeach; ?>
			</select>
			<button type="button" class="btn btn-info btn_new" data-position="<?=$position?>">Voeg toe</button>
		</form>
	</div>
</div>