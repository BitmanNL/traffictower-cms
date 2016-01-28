<? $this->load->view('admin/layouts/includes/header', $data); ?>

<div id="wrapper">

	<? $this->load->view('admin/layouts/includes/navigation', $data); ?>

	<div class="wrap">
		<div class="container">
			<?=isset($help_general) ? $help_general : NULL?>
			<?=isset($content) ? $content : NULL?>
		</div>
	</div>

</div>

<div class="container footer-text text-muted">
	<hr>
	<small>TrafficTower <?=app_version(TRUE)?> &copy; <?=date('Y')?> <a href="http://www.bitman.nl" target="_blank">Bitman.nl</a></small>
</div>

<? $this->load->view('admin/layouts/includes/footer', $data); ?>