<div id="grocery-order">
	
	<h3><?=$subject?></h3>

	<?php if(!empty($items)): ?>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th class="table-order-column"></th>
					<?php foreach($fields as $field): ?>
						<th><?=$display_as[$field]?></th>
					<?php endforeach;?>
				</tr>
			</thead>
			<tbody>
				<?php foreach($items as $key => $item): ?>
					<tr data-id="<?=$item_data[$key][$primary_key_field]?>">
						<td class="text-center"><i class="glyphicon glyphicon-move"></i></td>
						<?php foreach($fields as $field): ?>
							<td><?=$item[$field]?></td>
						<?php endforeach;?>	
					</tr>
				<?php endforeach;?>
			</tbody>
		</table>
	<?php else: ?>
		<p>Geen records aanwezig.</p>
	<?php endif; ?>

</div>