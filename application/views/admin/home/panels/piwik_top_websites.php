<div id="websites" class="panel panel-default">
	<div class="panel-heading"><h3 class="panel-title">Doorverwijzende websites vandaag</a></div>
	<div class="panel-body">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th>Website</th>
					<th>Unieke bezoekers</th>
				</tr>
			</thead>
			<?php $i = 0; ?>
			<?php foreach ($websites as $website):?>
				<?php if ($i >= 15) { break; } ?>
				<tr>
					<td><?=$website['label']?></td>
					<td><?=$website['sum_daily_nb_uniq_visitors']?></td>
				</tr>
				<?php $i++; ?>
			<?php endforeach;?>
			<?php if (empty($websites)): ?>
				<tr>
					<td colspan="2">Er zijn vandaag nog geen doorverwijzende websites.</td>
				</tr>
			<?php endif; ?>
		</table>
	</div>
</div>