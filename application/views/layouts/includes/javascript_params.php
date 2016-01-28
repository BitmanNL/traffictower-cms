<script type="text/javascript">
    document.cms_base_url = '<?=rtrim(base_url(), '/')?>';
    document.cms_site_url = '<?=rtrim(site_url(), '/')?>';
    
	<?php foreach ($javascript_params as $key => $value): ?>
	document.cms_<?=$key?> = <?=$value?>;
	<?php endforeach ?>
</script>