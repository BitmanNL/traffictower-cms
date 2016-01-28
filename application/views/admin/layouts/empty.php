<? $this->load->view('admin/layouts/includes/header', $data); ?>

<?=isset($help_general) ? $help_general : NULL?>

<?=isset($content) ? $content : NULL?>

<? $this->load->view('admin/layouts/includes/footer', $data); ?>