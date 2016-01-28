$(document).ready(function(){

	<?php if(!empty($form_submit)): ?>
	CMS.alert("<?=$form_message?>", "<?=$form_submit?>");
	<?php endif; ?>

	$('#help-page .btn-delete').click(function(){
		var url = $(this).attr('href');
		var type = $(this).data('type');
		var id = $(this).data('id');

		if(type == 'page') {
			var modalTitle = "Pagina verwijderen";
			var modalContent = "Weet je zeker dat je deze pagina inclusief alle alinea's wilt verwijderen?";
		}
		if(type == 'paragraph') {
			var modalTitle = "Alinea verwijderen";
			var modalContent = "Weet je zeker dat je deze alinea wilt verwijderen?";
		}

		if(type == 'page' || type == 'paragraph') {
			CMS.modal({
				title: modalTitle,
				message: modalContent,
				submitButton: 'Verwijder',
				callBackSubmit: function() {
					$.ajax({
						url: url,
						dataType: 'json',
						success: function(results) {
							if(type == 'page') {
								location.href = '<?=site_url('admin/help')?>';
							}
							if(type == 'paragraph') {
								CMS.alert('Alinea met succes verwijderd!', 'success');
								$('#paragraph-'+id).remove();
							}
						},
						error: function() {
							CMS.alert('Verbinding met de server is verbroken.');
						}
					});			
				}
			});
		}

		return false;
	});

	$.each($('#help-page .content a img'), function(i, img){
		$(img).parent().fancybox();
	});

});