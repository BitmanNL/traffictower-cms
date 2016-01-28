<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    
    <title>TrafficTower CMS install checker</title>
    
    <link rel="stylesheet" href="<?=base_url('assets/admin/css/bootstrap.min.css')?>">
    
</head>
<body>

	<div class="container">

		<h1>TrafficTower CMS install checker</h1>
		<br>

		<p>Succesmeter:</p>
		<div class="progress">
			<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?=$success_perc?>" aria-valuemin="0" aria-valuemax="100" style="width: <?=$success_perc?>%"></div>
		</div>

		<br>

	    <?php foreach($checks as $check): ?>
	    	<?php if(!is_null($check)): ?>
	    	<blockquote>
	    		<?php if($check['success']): ?>
	    	
		    	<h4 class="text-success"><span class="glyphicon glyphicon-ok"></span> <?=$check['message']?></h4>

		    	<?php else: ?>
		    	
		    	<h4 class="text-<?=isset($check['type']) ? $check['type'] : 'danger'?>"><span class="glyphicon glyphicon-exclamation-sign"></span> <?=$check['message']?></h4>
		    	<?php if (isset($check['instruction']) && $check['instruction'] !== ''): ?><footer><?=$check['instruction']?></footer><?php endif; ?>

	    		<?php endif; ?>
	    	</blockquote>
	    	<?php endif; ?>
		<?php endforeach; ?>

	</div>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="<?=base_url('assets/admin/js/bootstrap.min.js')?>"></script>
    
</body>
</html>