$(document).ready(function(){

	$('.fancybox').fancybox();

	$.each($('.link-copy'), function(i, link){
		$(link).click(function(){
			$(this).select();
		});
		$(link).focus(function(){
			$(this).select();
		});
	});

	$('.btn-delete').click(function(){
		var link = $(this).attr('href');
		CMS.modal({
			title: 'Bestand verwijderen',
			message: 'Weet je zeker dat je dit bestand wilt verwijderen?',
			submitButton: 'Verwijder',
			callBackSubmit: function() {
				location.href = link;
			}
		});
		return false;
	});

});