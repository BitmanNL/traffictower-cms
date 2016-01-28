<div class="element_video">

	<?php if(!empty($video['title'])): ?>
		<strong><?=$video['title']?></strong>
	<?php endif;?>
		
	<? if($video['type'] === 'youtube'): ?>
		<?php if (!empty($video['thumbnail'])): ?>
			<a href="http://youtube.com/watch?v=<?=$video['key']?>" title="Bekijk video op Youtube" target="_blank"><img class="img-responsive" alt="Video thumbnail" src="<?=$video['thumbnail']?>"></a>
		<?php else: ?>
			<iframe width="100%" height="250" src="//www.youtube.com/embed/<?=$video['key']?>?rel=0" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
		<?php endif; ?>
	<? endif; ?>

	<? if($video['type'] === 'vimeo'): ?>
		<?php if (!empty($video['thumbnail'])): ?>
			<a href="http://www.vimeo.com/<?=$video['key']?>" title="Bekijk video op Vimeo" target="_blank"><img class="img-responsive" alt="Video thumbnail" src="<?=$video['thumbnail']?>"></a>
		<?php else: ?>
			<iframe width="100%" height="250" src="http://player.vimeo.com/video/<?=$video['key']?>?title=0&byline=0&portrait=0&badge=0" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
		<?php endif; ?>
	<? endif; ?>

</div>