<script type="text/javascript">
    document.cms_base_url = '<?=rtrim(base_url(), '/')?>';
    document.cms_site_url = '<?=rtrim(site_url(), '/')?>';
    document.cms_current_url = '<?=rtrim(current_url(), '/')?>';
    document.cms_controller = '<?=$this->router->class?>';
    document.cms_method = '<?=$this->router->method?>';
    document.cms_tinymce_minimal = <?=tinymce_config('minimal')?>;
    document.cms_tinymce_full = <?=tinymce_config('full')?>;

    <?php foreach ($javascript_params as $key => $value): ?>
    document.cms_<?=$key?> = <?=$value?>;
    <?php endforeach ?>
</script>
