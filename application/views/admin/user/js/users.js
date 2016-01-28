$(document).ready(function(){

    <? if(string_empty($this->session->flashdata('alert'))): ?>
    CMS.alert('<?=addslashes($this->session->flashdata('alert_message'))?>', '<?=addslashes($this->session->flashdata('alert'))?>');
    <? endif; ?>

	// Delete user button clicked
	$('#users button.delete_user').click(function(){
        var that = this;

        var user_id = $(this).data('user-id');
        var email = $(this).data('email');

        // ask the user if deleting this is ok
        CMS.modal({
            title: 'Gebruiker verwijderen',
            submitButton: 'Verwijderen',
            message: 'Weet u zeker dat u de gebruiker ' + email + ' wilt verwijderen?',
            callBackSubmit: function(){
                // remove element
                document.location.href = '<?=site_url('admin/user/delete')?>/'+user_id;
            }
        });

	});

});