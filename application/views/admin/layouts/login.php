<? $this->load->view('admin/layouts/includes/header', $data); ?>

<div id="wrapper-login">
	<div class="wrap">
		<div class="well">
			<?=isset($content) ? $content : NULL?>
		</div>
		<div class="text-center text-muted footer-text">
			<small>TrafficTower <?=app_version(TRUE)?> &copy; <?=date('Y')?> <a href="http://www.bitman.nl" target="_blank" title="Bitman.nl">Bitman.nl</a></small>
		</div>
	</div>
</div>

<? $this->load->view('admin/layouts/includes/footer', $data); ?>