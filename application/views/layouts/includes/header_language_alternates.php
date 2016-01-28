<?php if (count($alternates) > 1): ?>
	<?php if (!empty($alternate_default_href)): ?>
		<link rel="alternate" href="<?=original_site_url($alternate_default_href)?>" hreflang="x-default">
	<?php endif; ?>
	<?php foreach ($alternates as $alternate): ?>
		<link rel="alternate" href="<?=original_site_url($alternate['href'])?>" hreflang="<?=$alternate['hreflang']?>">
	<?php endforeach; ?>
<?php endif; ?>