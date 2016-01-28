<div class="element element-video">

	<? if(!empty($video['title'])): ?>
	<h2><?=$video['title']?></h2>
	<? endif; ?>
	
	<iframe width="<?=($video['format_type'] == 'relative') ? $video['width'].'%' : $video['width']?>" height="<?=$video['height']?>" src="//www.youtube.com/embed/<?=$video['key']?>?rel=0&autoplay=<?=$video['autoplay']?>" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>

</div>