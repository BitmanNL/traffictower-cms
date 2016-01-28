<div class="panel panel-default">
	<div class="panel-heading"><h3 class="panel-title">Recente logs</a></div>
	<div class="panel-body">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th>Datum</th>
					<th>Log</th>
					<th>Gebruiker</th>
				</tr>
			</thead>
			<tbody>
				<?php if (!empty($logs)): ?>
					<?php foreach ($logs as $log): ?>
						<tr>
							<td><?=$log['date_created']?></td>
							<td><?=$log['message']?></td>
							<td><?=string_empty($log['user']['screen_name'])?></td>
						</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<tr>
						<td colspan="2">Geen logs gevonden</td>
					</tr>
				<?php endif;?>
			</tbody>
		</table>
		<br>
		<a href="<?=site_url('admin/log')?>" class="pull-right">Bekijk meer logs</a>
	</div>
</div>