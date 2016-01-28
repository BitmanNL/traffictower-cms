<header>
	<div class="header">
		
		<? 
		$trace = $this->navigation->get_trace($page['id']);
		$main_navigation = $this->navigation->get_main_navigation(); 
		?>
		
		<? if (is_array($main_navigation)):?>
			<nav>
				<div class="navbar navbar-default" role="navigation">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand" href="<?=site_url()?>"><?=$app['site_name']?></a>
					</div>  
					<div class="navbar-collapse collapse">  
						<ul class="nav navbar-nav">
							<? foreach ($main_navigation as $item):?>
								<li class="<?= (in_array($item['id'], $trace) ? 'active' : '') ?>"><a href="<?=site_url($item['slug'])?>"><?=$item['title']?></a></li> 
							<? endforeach;?>
						</ul>
					</div>
				</div>
			</nav>
		<? endif;?>

		<? $breadcrumb = $this->navigation->get_breadcrumb($trace); ?>
		<? if(!empty($breadcrumb)): ?>
			<ol class="breadcrumb">
				<? foreach($breadcrumb as $item): ?>
					<? if(!isset($item['active'])): ?>
						<li><a href="<?=site_url($item['slug'])?>"><?=$item['title']?></a></li>
					<? else: ?>
						<li class="active"><?=$app['title']?></li>
					<? endif; ?>
				<? endforeach; ?>
			</ol>
		<? endif; ?>

		<? $sub_navigation = $this->navigation->get_sub_navigation($page['id']); ?>
		<? if (is_array($sub_navigation) && !empty($sub_navigation)):?>
			<ul class="nav nav-tabs">
				<? foreach ($sub_navigation as $item):?>
					<li class="<?= (in_array($item['id'], $trace) ? 'active' : '') ?>"><a href="<?=site_url($item['slug'])?>"><?=$item['title']?></a></li> 
				<? endforeach;?>
			</ul>
		<? endif;?>

		<?php if (count($languages) > 1):?>
			<?php foreach ($languages as $language):?>
				<a href="<?=original_site_url($language_relative_pages[$language])?>"><?=$language_data[$language]['short_name']?></a>
			<?php endforeach;?>
		<?php endif;?>

	</div><? // end of header ?>
</header>