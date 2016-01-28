<div id="help-page">
	<?php if(!empty($page)): ?>
		
		<?php if($page['is_visible'] == 'no'): ?>
		<div class="text-muted">
		<?php endif; ?>

		<?php if(is_super_user() && $preview_modus === 'off'): ?>
			<a href="<?=site_url('admin/help/page/delete/'.$page['id'])?>" data-type="page" class="btn btn-default pull-right btn-delete hidden-print"><span class="glyphicon glyphicon-trash" title="Verwijder pagina"></span></a>
			<a href="<?=site_url('admin/help/page/edit/'.$page['id'])?>" class="btn btn-default pull-right hidden-print"><span class="glyphicon glyphicon-pencil" title="Wijzig pagina"></span></a>
		<?php endif; ?>

		<h1><?php if($page['is_visible'] == 'no'): ?><span class="glyphicon glyphicon-ban-circle"></span> <?php endif; ?><?=$page['title']?></h1>

		<?php if(!empty($page['controller'])): ?>
			<div class="alert alert-warning">
				<span class="glyphicon glyphicon-arrow-right"></span>
				<a href="<?=site_url('admin/'.trim($page['controller'], '/'))?>">ga naar dit onderdeel in het CMS</a>
			</div>
		<?php endif; ?>

		<div class="content"><?=$page['content']?></div>
		<div class="clearfix"></div>

		<hr>
		<div class="pull-right text-muted">
			<small>Laatste update: <?=cms_date('j F Y H:i', strtotime($page['date_modified']))?></small>
		</div>
		<div class="clearfix"></div>

		<?php foreach($paragraphs as $paragraph): ?>
			
			<?php if($paragraph['is_visible'] == 'no'): ?>
			<div class="text-muted">
			<?php endif; ?>

			<div class="paragraph" id="paragraph-<?=$paragraph['id']?>">		
				<a name="<?=$paragraph['key']?>"></a>
		
				<?php if(is_super_user() && $preview_modus === 'off'): ?>
					<a href="<?=site_url('admin/help/paragraph/delete/'.$paragraph['id'])?>" data-type="paragraph" data-id="<?=$paragraph['id']?>" class="btn btn-sm btn-default pull-right btn-delete hidden-print"><span class="glyphicon glyphicon-trash" title="Verwijder alinea"></span></a>
					<a href="<?=site_url('admin/help/paragraph/edit/'.$paragraph['id'])?>" class="btn btn-sm btn-default pull-right hidden-print"><span class="glyphicon glyphicon-pencil" title="Wijzig alinea"></span></a>
					<a href="<?=site_url('admin/help/paragraph_order?page_id='.$page['id'])?>" class="btn btn-sm btn-default pull-right hidden-print"><span class="glyphicon glyphicon-sort" title="Order alinea's"></span></a>
				<?php endif; ?>

				<h2><?php if($paragraph['is_visible'] == 'no'): ?><span class="glyphicon glyphicon-ban-circle"></span> <?php endif; ?><?=$paragraph['title']?></h2>
				<div class="content"><?=$paragraph['content']?></div>
				<div class="clearfix"></div>

				<hr>
				<div class="pull-right text-muted">
					<small>Laatste update: <?=cms_date('j F Y H:i', strtotime($paragraph['date_modified']))?></small>
				</div>
				<div class="clearfix"></div>
			</div>

			<?php if($paragraph['is_visible'] == 'no'): ?>
			</div>
			<?php endif; ?>

		<?php endforeach; ?>

		<?php if(is_super_user() && $preview_modus === 'off'): ?>
			<a href="<?=site_url('admin/help/paragraph/add?page_id='.$page['id'])?>" class="btn btn-sm btn-default hidden-print"><span class="glyphicon glyphicon-plus" title="Voeg alinea toe"></span></a> 
		<?php endif; ?>

		<?php if($page['is_visible'] == 'no'): ?>
		</div>
		<?php endif; ?>

	<?php endif; ?>
</div>