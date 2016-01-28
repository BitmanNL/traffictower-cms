<?php if (!empty($log_types)): ?>
	<div class="row">
		<div class="col-sm-3">
			<select id="log_type" class="form-control" name="log_type">
				<option value="">Bekijk alle logs</option>
				<?php foreach ($log_types as $log_type): ?>
					<?php $selected = ($selected_log_type == $log_type) ? ' selected="selected"' : ''?>
					<option<?=$selected?> value="<?=$log_type?>"><?=$log_type?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
<?php endif; ?>