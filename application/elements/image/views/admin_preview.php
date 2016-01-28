<div class="element_video">

	<?php if(!empty($image['title'])): ?>
		<strong><?=$image['title']?></strong>
	<?php endif;?>

	<img alt="<?=htmlspecialchars($image['alt'])?>" src="<?=$image['image']?>" />

</div>