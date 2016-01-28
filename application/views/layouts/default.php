<?php $this->load->view('layouts/includes/header'); ?>

<div class="wrapper container">

	<?php $this->load->view('layouts/includes/body_header'); ?>

	<div class="content">
		
		<div class="main">
			<?=isset($elements['content']) ? $elements['content'] : NULL?>
			<?=isset($content) ? $content : NULL?>
		</div>

	</div><? // end of content ?>
	
</div><? // end of wrapper ?>

<?php $this->load->view('layouts/includes/body_footer'); ?>

<?php $this->load->view('layouts/includes/footer'); ?>