<? if($element_id): ?>

$(document).ready(function(){

	$('#text_revisions').change(function(){
		var revision_id = $(this).val();
		location.href = '<?=current_url()?>?revision='+revision_id;
	});

	$('#btn_submit').click(function(){
		return text_conceptCheck('submit');
	});

	$('#btn_publish').click(function(){
		return text_conceptCheck('publish');
	});

	$('#btn_remove_concept').click(function(){
		
		CMS.modal({
			title: 'Concept verwerpen',
			message: 'Weet u zeker dat u dit concept wilt verwerpen?',
			cancelButton: 'Annuleren',
			submitButton: 'Verwerp',
			callBackSubmit: function(){
				$('#btn_remove_concept').unbind().click();
			}
		});

		return false;
	});

});

function text_conceptCheck(formType){
	
	<?php if(!empty($concept)): ?>
		var concept_id = <?=intval($concept['revision_id'])?>;
	<?php else: ?>
		var concept_id = 0;
	<?php endif; ?>

	if($('#overwrite_concept').val() == '0'){
		if(concept_id > 0 && concept_id != <?=$element_revision_id?>){
			CMS.modal({
				title: 'Let op!',
				message: 'Er is nog een ander concept aanwezig. Weet u zeker dat u deze wilt overschrijven?',
				callBackSubmit: function(){
					$('#overwrite_concept').val(1);
					$('#btn_'+formType).click();
				}
			});
			return false;
		}
	}
}

<? endif; ?>