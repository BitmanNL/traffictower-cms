<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><? //get last IE engine and load Google Chrome frame plugin (for HTML5) ?>
    
    <!--
    
    @
    @@                    
    @@           @@       @@@@@   @@  @@@@@@  @@     @@     @@     @@    @@
    @@@          @@@@     @@  @@  @@  @@@@@@  @@@   @@@    @@@@    @@@   @@
    @@@ @@@@@@@@ @@       @@  @@  @@    @@    @@@@ @@@@   @@  @@   @@@@  @@
    @@@@@@@@@@@@@@@       @@@@@   @@    @@    @@ @ @ @@  @@@@@@@@  @@ @@ @@
    @@ @@@@@@@@@ @@       @@  @@  @@    @@    @@ @@@ @@  @@@@@@@@  @@  @@@@
    @@@  @@@@@  @@@       @@  @@  @@    @@    @@  @  @@  @@    @@  @@   @@@
    @@@@@@@@@@@@@@@       @@@@@   @@    @@    @@     @@  @@    @@  @@    @@
     @@   @@@   @@
      @@@     @@@            
        @@@@@@@           - powered by TrafficTower CMS  -  www.bitman.nl -

    -->

    <title>
        TrafficTower
        <? if (ENVIRONMENT !== 'production'):?>
                - <?=ENVIRONMENT?> omgeving
        <? endif;?>
    </title>

    <?php // https://support.google.com/webmasters/answer/93710?hl=en ?>
    <meta name="robots" content="noindex">
    
    <link rel="icon" href="<?=asset_url('assets/admin/img/favicon.ico')?>" type="image/x-icon">
    
    <? //CSS global ?>
    <link rel="stylesheet" href="<?=asset_url('assets/admin/css/bootstrap.min.css')?>">
    <link rel="stylesheet" href="<?=asset_url('assets/admin/css/admin.css')?>">
    <?php foreach($css_files as $file): ?>
        <link rel="stylesheet" href="<?php echo $file; ?>">
    <?php endforeach; ?>
    
    <? //CSS custom ?>
    <? if(isset($css) && $css !== ''){ ?>
    	<style>
			<?=$css?>
		</style>
    <? } ?>
    
</head>
<body>
