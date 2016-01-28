    <? //JS global ?>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

    <?php foreach($preloaded_javascript_files as $file): ?>
        <script src="<?= $file; ?>"></script>
    <?php endforeach; ?>

    <? //Set site wide config options?>
    <?php $this->load->view('admin/layouts/includes/javascript_params') ?>

    <script src="<?=asset_url('assets/admin/js/bootstrap.min.js')?>"></script>
    <script src="<?=asset_url('assets/admin/js/cms.min.js')?>"></script>
    <?php foreach ($javascript_files as $file): ?>
        <script src="<?php echo $file; ?>"></script>
    <?php endforeach; ?>

    <?php //JS custom ?>
    <?php if (isset($javascript) && $javascript !== ''): ?>
        <script type="text/javascript">
            <?= $javascript; ?>
        </script>
    <?php endif; ?>

</body>
</html>
