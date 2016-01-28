<?php $this->load->view('layouts/includes/header'); ?>

<div class="wrapper container">

	<?php $this->load->view('layouts/includes/body_header'); ?>

	<div class="content row">
		
		<div class="sidebar col-md-4">
			<?=isset($elements['sidebar']) ? $elements['sidebar'] : NULL?>
		</div>

		<div class="main col-md-8">
			<?=isset($elements['content']) ? $elements['content'] : NULL?>
			<?=isset($content) ? $content : NULL?>
		</div>
		
	</div><? // end of content ?>
	
</div><? // end of wrapper ?>

<?php $this->load->view('layouts/includes/body_footer'); ?>

<? $this->load->view('layouts/includes/footer'); ?>