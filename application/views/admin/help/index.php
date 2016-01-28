<h3>
	<?php if(is_super_user()): ?>
		<a class="btn btn-success pull-right hidden-print" href="?preview=<?=($preview_modus == 'on') ? 'off' : 'on'?>">Wijzig-modus = <?=($preview_modus == 'on') ? 'UIT' : 'AAN'?></a>
	<?php endif; ?>
	TrafficTower documentatie
</h3>

<br>

<div class="row">
	<div class="col-sm-3 hidden-print">
		<?=$help_navigation?>
	</div>
	<div class="col-sm-8 col-sm-offset-1">
		<?=$help_content?>
	</div>
</div>