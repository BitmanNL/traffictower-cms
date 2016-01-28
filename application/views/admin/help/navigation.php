<?php
function help_navigation($items, $page, $preview_modus, $level = 0, $parent_id = 0)
{
	?>
	<ul class="nav nav-pills nav-stacked" style="margin-left: <?=$level*15?>px;">
		<?php foreach($items as $item): ?>
			<li<?=($item['id'] == $page['id']) ? ' class="active"' : ''?>>
				<a href="<?=site_url('admin/help/index/'.$item['id'].'/'.url_title($item['title'], '-', TRUE))?>"<?=($item['is_visible'] == 'no') ? ' class="text-danger"' : NULL?>>
					<?php if($item['is_visible'] == 'no'): ?>
						<span class="glyphicon glyphicon-ban-circle"></span> 
					<?php endif; ?>
					<?=$item['title']?>
				</a>
			</li>

			<?php if($item['id'] == $page['id'] || $item['id'] == $page['parent_id']): ?>
				<?php if($level < 1): ?>
					<?php if(!empty($item['sub_items'])): ?>
						<?php help_navigation($item['sub_items'], $page, $preview_modus, $level+1, $item['id']); ?>
					<?php else: ?>
						<?php if(is_super_user() && $preview_modus === 'off'): ?>
							<ul class="nav nav-pills nav-stacked" style="margin-left: <?=($level+1)*15?>px;">
								<li><a href="<?=site_url('admin/help/page/add?parent_id='.$item['id'])?>" class="text-muted"><span class="glyphicon glyphicon-plus"></span> Nieuwe pagina hier</a></li>
							</ul>
						<?php endif; ?>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>

		<?php endforeach; ?>

		<?php if(is_super_user() && $preview_modus === 'off'): ?>
		<li>
			<a href="<?=site_url('admin/help/page/add?parent_id='.$parent_id)?>" class="text-muted"><span class="glyphicon glyphicon-plus"></span> Nieuwe pagina hier</a>
		</li>
		<?php endif; ?>

	</ul>
	<?php 
}
?>

<?php help_navigation($items, $page, $preview_modus); ?>

<?php if(is_super_user() && $preview_modus === 'off'): ?>
<br>
<a href="<?=site_url('admin/help/file')?>" class="btn btn-default"><span class="glyphicon glyphicon-import" title="Bestandsbeheer"></span> Bestanden</a> 
<?php endif; ?>