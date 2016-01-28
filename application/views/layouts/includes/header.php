<!DOCTYPE html>
<html lang="<?=$language_data[$this->config->item('language')]['code']?>">
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="height=device-height, width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"><? //no scaling for mobile devices ?>

    <?=string_empty($language_alternates)?>

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

    <title><?=!empty($app['title']) ? $app['title'] : $app['site_name']?></title>

    <? if(!empty($app['description'])): ?>
        <meta name="description" content="<?=meta_description($app['description'])?>">
    <? endif;?>

    <meta name="generator" content="TrafficTower">
    <link rel="author" type="text/html" href="http://www.bitman.nl/">
    
    <? // open graph tags (for facebook, linkedin) ?>
    <meta property="og:url" content="<?=full_current_url()?>">
    <meta property="og:site_name" content="<?=$app['site_name']?>">
    <meta property="og:title" content="<?=!empty($app['title']) ? $app['title'] : $app['site_name']?>">
    <meta property="og:type" content="<?=!empty($app['og_type']) ? $app['og_type'] : 'website'?>">
    <? if(!empty($app['description'])): ?>
        <meta property="og:description" content="<?=meta_description($app['description'])?>">
    <? endif; ?>
    <? if(!empty($app['image'])): ?>
        <meta property="og:image" content="<?=asset_url($app['image'])?>">
    <? endif; ?>

    <? // ipad and iphone logo for shortcut ?>
    <? if(!empty($app['apple_touch_icon'])): ?>
        <link rel="apple-touch-icon" href="<?=asset_url($app['apple_touch_icon'])?>">
    <? endif; ?>

    <link rel="icon" href="<?=asset_url('assets/img/favicon.ico')?>" type="image/x-icon">

    <? //CSS global ?>
    <link rel="stylesheet" href="<?=asset_url('assets/css/bootstrap.min.css')?>">

    <link rel="stylesheet" href="<?=less_url('assets/less/screen.less')?>" media="screen">
    <link rel="stylesheet" href="<?=less_url('assets/less/tablet.less')?>" media="screen and (min-width: 768px) and (max-width: 991px)">
    <link rel="stylesheet" href="<?=less_url('assets/less/mobile.less')?>" media="screen and (max-width: 767px)">
    <?php foreach($css_files as $file): ?>
        <link rel="stylesheet" href="<?php echo $file; ?>">
    <?php endforeach; ?>
    
    <? //CSS custom ?>
    <? if(isset($css) && $css !== ''): ?>
    	<style>
			<?=$css?>
		</style>
    <? endif; ?>

    <?php
    if(!do_not_track()){
        // Put Google Analytics code here
        google_analytics_tag();
    }
    ?>
    
</head>
<body>