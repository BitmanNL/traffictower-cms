<div class="element_text">
	
	<? if(empty($text) && !empty($concept)): ?>
		<div class="text-danger">- Concept -</div>
	<? else: ?>
		<?=!empty($text['title']) ? '<strong>'.$text['title'].'</strong>' : NULL?>
		<div>
			<?=character_limiter(strip_tags(string_empty($text['content'])), 500)?>
		</div>
		<? if(!empty($concept)): ?>
			<div class="text-danger pull-right">- Concept aanwezig -</div>
			<div class="clearfix"></div>
		<? endif; ?>
	<? endif;?>

</div>