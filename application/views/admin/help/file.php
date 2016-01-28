<h3>Documentatie bestanden</h3>

<?php echo form_open_multipart('admin/help/file_save', 'class="form-inline" role="form"'); ?>
	<div class="form-group">
		<input type="file" name="file"> 
	</div>
	<div class="form-group">
		<button type="submit" class="btn btn-success">Upload bestand</button>
	</div>
	<br><br>
<?php echo form_close(); ?>

<?php if(!empty($files)): ?>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th style="width: 200px;">Bestand</th>
				<th style="width: 500px;">URL</th>
				<th>Laatst gewijzigd</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($files as $file): ?>
				<tr>
					<td class="text-center">
						<?php if($file['type'] == 'image'): ?>
							<a class="fancybox" rel="fancybox-group" href="<?=base_url($dir.$file['file_name'])?>"><img src="<?=base_url($dir.$file['file_name'])?>" style="width: 200px;"></a>
						<?php else: ?>
							<br>
							<a class="btn btn-info" href="<?=base_url($dir.$file['file_name'])?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span> Download bestand</a>
						<?php endif; ?>
					</td>
					<td>
						<textarea class="form-control link-copy" style="width: 100%;">/file=<?=$dir.$file['file_name']?></textarea>
					</td>
					<td><?=$file['modified']?></td>
					<td>
						<a class="btn btn-danger btn-delete" href="<?=site_url('admin/help/file_delete/'.urlencode(base64_encode(FCPATH.$dir.$file['file_name'])))?>">Verwijder</button>
					</td>
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>
<?php else: ?>
	<p>Geen bestanden gevonden</p>
<?php endif; ?>