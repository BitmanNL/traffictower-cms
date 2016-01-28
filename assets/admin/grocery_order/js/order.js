$(document).ready(function(){

	$('#grocery-order table tbody').sortable({
		helper: function(e, tr) {
			var $originals = tr.children();
			var $helper = tr.clone();
			$helper.children().each(function(index) {
				// Set helper cell sizes to match the original sizes
				$(this).width($originals.eq(index).outerWidth());
			});
			return $helper;
		},
		update: function(event, ui) {
			var itemId = ui.item.data('id');
			var previousItemId = ui.item.prev().data('id');
			
			if(typeof(previousItemId) !== 'number') {
				previousItemId = 0;
			}

			$.ajax({
				url: document.cms_current_url + '/move?item='+itemId+'&previous='+previousItemId,
				type: 'get',
				dataType: 'json',
				success: function(data) {
					if(!data.success) {
						CMS.alert(data.message);
					}
				},
				error: function()
				{
					CMS.alert('Geen verbinding met de server.');
				}
			});

		}
	});

});