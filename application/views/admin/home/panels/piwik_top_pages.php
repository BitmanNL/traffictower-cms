<div id="top_pages" class="panel panel-default">
	<div class="panel-heading"><h3 class="panel-title">Meest bezochte pagina's vandaag</h3></div>
	<div class="panel-body">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th>Paginatitel</th>
					<th>Unieke bezoekers</th>
					<th>Bounce rate</th>
				</tr>
			</thead>
			<?php $i = 0; ?>
			<?php foreach ($top_pages as $page):?>
				<?php if ($i >= 15) { break; } ?>
				<tr>
					<td><?=$page['label']?></td>
					<td><?=$page['sum_daily_nb_uniq_visitors']?></td>
					<td><?=$page['bounce_rate']?></td>
				</tr>
				<?php $i++; ?>
			<?php endforeach;?>
			<?php if (empty($top_pages)): ?>
				<tr>
					<td colspan="3">Er zijn vandaag nog geen pagina's bezocht.</td>
				</tr>
			<?php endif; ?>
		</table>
	</div>
</div>