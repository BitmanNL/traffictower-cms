<?php $this->load->view('layouts/includes/header'); ?>

<div class="wrapper container">

	<?php $this->load->view('layouts/includes/body_header'); ?>

	<div class="content row">
		
		<div class="sidebar_left col-md-3">
			<?=isset($elements['sidebar_left']) ? $elements['sidebar_left'] : NULL?>
		</div>

		<div class="main col-md-6">
			<?=isset($elements['content']) ? $elements['content'] : NULL?>
			<?=isset($content) ? $content : NULL?>
		</div>

		<div class="sidebar_right col-md-3">
			<?=isset($elements['sidebar_right']) ? $elements['sidebar_right'] : NULL?>
		</div>
		
	</div><? // end of content ?>
	
</div><? // end of wrapper ?>

<?php $this->load->view('layouts/includes/body_footer'); ?>

<? $this->load->view('layouts/includes/footer'); ?>