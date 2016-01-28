<?php if (count($languages) > 1 && ($state === 'list' || $state === 'success')):?>
	<ul class="nav nav-tabs">
		<?php foreach ($languages as $lng):?>
			<li <?php if ($lng === $language):?>class="active"<?php endif;?>>
			 	<a href="<?=site_url('/admin/email/language')?>/<?=$lng?>">
					<img src="<?=base_url()?>/assets/admin/img/flags/<?=$lng?>.png" />
			 		<?=$language_data[$lng]['name']?>
			 	</a>
			</li>
		<?php endforeach;?>
	</ul>
<?php endif;?>