<input type="hidden" name="element_type" value="<?=$element_type?>">
<input type="hidden" name="element_position" value="<?=$element_position?>">
<input type="hidden" name="page_id" value="<?=$page_id?>">

<? if (isset($element_id)):?>
	<input type="hidden" name="element_id" value="<?=$element_id?>">
	<input type="hidden" name="element_content_id" value="<?=$element_content_id?>">
<? endif;?>