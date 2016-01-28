<div class="element element-text">
	<?php if (!empty($text['title'])):?>
		
		<?php if ($element_position == 'content' && $element_type_order == 0): ?>
			<h1><?=$text['title']?></h1>
		<?php else: ?>
			<h2><?=$text['title']?></h2>
		<?php endif; ?>

	<?php endif;?>
	<?=$text['content']?>
</div>