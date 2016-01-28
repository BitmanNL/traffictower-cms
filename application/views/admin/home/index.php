<div class="jumbotron">
	<div class="pull-left">
		<img src="<?=base_url('assets/admin/img/bitman_logo.png')?>">
	</div>
	<div class="pull-left" style="padding: 10px 0 0 20px;">
		<h2>Welkom, <?=$site_name?>!</h2>
		<p>Dit is het <strong>TrafficTower CMS</strong> van Bitman.<? /* Nieuw? Lees dan eerst de <a href="<?=site_url('admin/help')?>">documentatie</a>.*/?></p>
	</div>
	<div class="clearfix"></div>
</div>

<div class="row">
	<div class="col-md-6">
		<?=string_empty($dashboard_panels['odd'])?>
	</div>
	<div class="col-md-6">
		<?=string_empty($dashboard_panels['even'])?>
	</div>
</div>