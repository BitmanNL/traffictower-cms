<div class="element element-image">

	<? if(!empty($image['title'])): ?>
		<h2><?=$image['title']?></h2>
	<? endif; ?>

	<?php if(!empty($image['link'])): ?>
		<a href="<?=$image['link']?>" <?=!is_null($image['target']) ? 'target="'.$image['target'].'"' : ''?>>
	<?php endif; ?>

	<img class="img-responsive" src="<?=$image['image']?>" alt="<?=htmlspecialchars($image['alt'])?>" />

	<?php if(!empty($image['link'])): ?>
		</a>
	<?php endif; ?>

</div>