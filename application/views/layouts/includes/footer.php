    <? // load cookielaw banner ?>
    <?=string_empty($cookielaw_banner)?>

    <? // if less than ie8, show notification upgrade browser ?>
    <?php $this->load->view('layouts/includes/ie7_notification'); ?>

    <?php foreach($preloaded_javascript_files as $file): ?>
        <script src="<?= $file; ?>"></script>
    <?php endforeach; ?>

    <? //Set site wide config options?>
    <?php $this->load->view('layouts/includes/javascript_params'); ?>

	<? //JS global ?>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="<?=asset_url('assets/js/bootstrap.min.js')?>"></script>

    <? // HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries ?>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <?php foreach($javascript_files as $file): ?>
		<script src="<?php echo $file; ?>"></script>
	<?php endforeach; ?>

    <? //JS custom ?>
    <? if(isset($javascript) && $javascript !== ''){ ?>
	    <script type="text/javascript">
	    	<?=$javascript?>
	    </script>
    <? } ?>

    <?php
    if(!do_not_track()){
        // Put Piwik code here
        piwik_tag();
    }
    ?>

</body>
</html>
