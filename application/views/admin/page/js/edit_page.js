
// namespace
var EditPage = {};

// store wether changes are made to the page
EditPage.changed = false;

// store the node
EditPage.node = null;

/**
 * Edit Page
 *
 * This function is called when the user wants to edit a page
 */
EditPage.editPage = function(node)
{
	EditPage.node = node;

	// executated when to user makes a change
	var formChanged = function(){
		EditPage.changed = true;
		$('#btn_save_page').show();
		$('#btn_saved_page').hide();
	};

	// execute when the title changes, to change to slug
	function pageChange()
	{
		// only change the slug if the controller field is empty
		// and the page is hidden
		// otherwise the slug has to be identical to the controller it is
		// attached to
		if ($('#edit_page input[name=controller]').val() == '' && node.data.addClass.indexOf('hidden-page') !== -1)
		{
			var title = $(this).val();
			$('#edit_page input[name=slug]').val(EditPage.convertToSlug(title));
		}

		EditPage.editableSlug();
	}
	$('#edit_page').find('input[name=title]').keyup(pageChange).change(pageChange);

	// make the elements draggable
	$( "#edit_holder .element-positions" ).sortable({
		connectWith: ".element-positions",
		placeholder: "ui-state-highlight",
		handle: '.element-handler',
		stop: EditPage.elementsSortingChanged
	}).disableSelection();

	// put a on mouseenter event on the li's
	$( "#edit_holder .element-positions li" ).mouseenter(function()
	{
		$(this).find('.element_controls').show();
	});
	// put a on mouseleave event on the li's
	$( "#edit_holder .element-positions li").mouseleave(function()
	{
		$(this).find('.element_controls').hide();
	});

	// put a click event on the buttons in the element controls
	$( "#edit_holder i[name=delete_element]" ).click(function()
	{
		var element_id = $(this).parent().parent().parent().data('element-id');

		EditPage.deleteElement($(this).parent().parent().parent(), element_id);
	});

	// put a click event on the buttons in the element controls

	// handle show and hide element
	$( "#edit_holder").find("i[name=hide_element], i[name=show_element]").click(function()
	{
		var element_id = $(this).parent().parent().parent().data('element-id');

		// show or hide
		if ($(this).attr('name') == 'hide_element')
		{
			var show_or_hide = 'hide';
		}
		else
		{
			var show_or_hide = 'show';
		}

		EditPage.showhideElement(show_or_hide, element_id, $(this).parent().parent().parent());
	});

	// handle global local
	$( "#edit_holder").find("i[name=global_element], i[name=local_element]").click(function()
	{
		var element_id = $(this).parent().parent().parent().data('element-id');

		// show or hide
		if ($(this).attr('name') == 'global_element')
		{
			var global_or_local = 'global';
			var modalTitle = 'Element globaal maken';
			var modalMessage = 'Weet je zeker dat je dit element globaal wilt maken?<br><br><em>Met het globaal maken wordt dit element op alle pagina\'s met dezelfde layout geplaatst.</em>';
		}
		else
		{
			var global_or_local = 'local';
			var modalTitle = 'Element lokaal maken';
			var modalMessage = 'Weet je zeker dat je dit element lokaal wilt maken?<br><br><em>Met het lokaal maken wordt dit element van alle pagina\'s met dezelfde layout verwijderd.</em>';
		}

		var that = this;

		CMS.modal({
			title: modalTitle,
			message: modalMessage,
			// cancelButton: 'Annuleren', // no cancel button displayed if set to false or null
			// submitButton: 'Ok', // no submit button displayed if set to false or null
			/* callBackCancel: function() {
				// custom code when clicked cancel
			},*/
			callBackSubmit: function() {
				// custom code when clicked submit
				EditPage.globalLocalElement(global_or_local, element_id, $(that).parent().parent().parent());
			}
		});
		//EditPage.globalLocalElement(global_or_local, element_id, $(this).parent().parent().parent());
	});

	// handle edit element
	$( "#edit_holder i[name=edit_element]").click(function()
	{
		var element_id = $(this).parent().parent().parent().data('element-id');
		EditPage.editElement(element_id);
	});

	// toggle body element
	$('#edit_holder .toggle_element').click(function(){
		var element_id = $(this).parent().parent().parent().data('element-id');
		EditPage.toggleElementBody(element_id);
	});

	// add handler for new element select	
	$('#edit_holder .add_elements .btn_new').click(function(){
		var position = $(this).data('position');
		var element = $('#edit_holder .add_elements .element_new_'+position).val();
		EditPage.newElement(element, position);
	});

	// add handler when saving
	$('#btn_save_page').click(EditPage.savePage);
	

	// hook for all custom script to execute when page is loaded
	EditPage.hookPageLoaded();

	// add event handlers on form
	$('#edit_page').find('input, textarea, select').change(formChanged).keydown(formChanged);

	if (node.data.addClass === null)
	{
		EditPage.node.data.addClass = '';
	}

	// check if the page is in menu and / or a system page and update the tree
	if ($('#page_in_menu').length > 0)
	{
		if ($('#page_in_menu').attr('checked') === 'checked')
		{
			EditPage.node.data.addClass = EditPage.node.data.addClass.replace(' not-in-menu-page', '');
			EditPage.node.data.addClass = EditPage.node.data.addClass.replace('not-in-menu-page', '');
		}
		else if (node.data.addClass !== null && node.data.addClass.indexOf('not-in-menu-page') === -1)
		{
			EditPage.node.data.addClass += ' not-in-menu-page';
		}
	}

	// check if the page is in menu and / or a system page and update the tree
	if ($('#page_is_system_page').length > 0)
	{
		if ($('#page_is_system_page').attr('checked') !== 'checked')
		{
			EditPage.node.data.addClass = EditPage.node.data.addClass.replace(' system-page', '');
			EditPage.node.data.addClass = EditPage.node.data.addClass.replace('system-page', '');
		}
		else if (node.data.addClass !== null && node.data.addClass.indexOf('system-page') === -1)
		{
			EditPage.node.data.addClass += ' system-page';
		}
	}
	
	EditPage.node.render();
};

/**
 * Save Page
 *
 * Save the changes of a page to the server
 */
EditPage.savePage = function()
{
	var formData = $('#edit_page').serializeArray();
	
	// executed when an error occurs
	var showError = function(data){

		// collect data about the error
		if(typeof data != 'object')
		{
			data = {};
		}
		if(typeof data.errorMessage != 'string')
		{
			data.errorMessage = 'Het opslaan van de wijzigingen is mislukt. Neem contact op met Bitman als dit probleem aanhoudt.';
		}

		// show the error
		CMS.alert(data.errorMessage);

		// clear all error highlighting
		$('#edit_page').find('input', 'select', 'checkbox', 'radio').parent().removeClass('error');

		// highlight the fields that have an error
		if (typeof data.errorFields == 'object')
		{
			for (var i in data.errorFields)
			{
				var fieldName = data.errorFields[i];
				$('#edit_page').find('input[name='+fieldName+']', 'select[name='+fieldName+']', 'checkbox[name='+fieldName+']', 'radio[name='+fieldName+']').parent().addClass('error');
			}
		}
	};

	// send the updated page to the server
	$.ajax({
		url: '<?=site_url('admin/page/update')?>/'+EditPage.node.data.key,
		method: 'GET',
		data: formData,
		success: function(data){
			if(data.success)
			{
    			$('#btn_save_page').hide();
				$('#btn_saved_page').show();
				EditPage.changed = false;

				// clear all error highlighting
				$('#edit_page').find('input', 'select', 'checkbox', 'radio').parent().removeClass('error');

				// update the title in the tree
				EditPage.node.data.title = data.page.title;
				EditPage.node.render();

				// reload the edit page
				Page.editPage(EditPage.node);

			}else{
				showError(data);
			}
		},
		error: showError
	});
};


EditPage.elementsSortingChanged = function(event, ui)
{	
	// get the order of the elements
	var element_order = {};
	$( "#edit_holder .element-positions li" ).each(function()
	{
		var position = $(this).parent().data('position');
		if (typeof element_order[position] !== 'object')
		{
			element_order[position] = [];
		}
		element_order[position].push($(this).data('element-id'));
	});

	// executed when an error occurs
	var showError = function(data){

		var errorMessage = 'Het wijzigen van de ordening van elementen op deze pagina is mislukt. Neem contact op met Bitman als dit probleem aanhoudt.';

		// show the error
		CMS.alert(errorMessage);

		// revert the change in ordering
		$( "#edit_holder .element-positions" ).sortable( "cancel" );
	};

	// send data to the server
	$.ajax({
		url: '<?=site_url('admin/elements/update_element_order')?>',
		method: 'GET',
		data: {'element_order':element_order, page_id: $('#page-content').data('page-id')},
		success: function(data){
			if(!data.success)
			{
				showError(data);
			}
		},
		error: showError
	});
};

EditPage.newElement = function(element_type, element_position)
{
	// check if the current page has changes
 	if( EditPage.changed )
 	{
		CMS.modal({
			title: 'Huidige wijzigingen gaan verloren',
			submitButton: 'Ga door',
			message: 'Als u doorgaat, gaan de wijzigingen die u heeft gedaan verloren. Wilt u doorgaan?',
    		callBackSubmit: function(){
    			EditPage.changed = false;
    			EditPage.newElement(element_type, element_position);
    		}
    	});
 	}else{
 		window.location.href = '<?=site_url('admin/elements/new_element')?>/' + element_type + '/' + EditPage.node.data.key + '/' + element_position;
 	}
};

EditPage.editElement = function(element_id)
{
	// check if the current page has changes
 	if( EditPage.changed )
 	{
		CMS.modal({
			title: 'Huidige wijzigingen gaan verloren',
			submitButton: 'Ga door',
			message: 'Als u doorgaat, gaan de wijzigingen die u heeft gedaan verloren. Wilt u doorgaan?',
    		callBackSubmit: function(){
    			EditPage.changed = false;
    			EditPage.editElement(element_id);
    		}
    	});
 	}else{
 		window.location.href = '<?=site_url('admin/elements/edit_element')?>/' + element_id + '/' + $('#page-content').data('page-id');
 	}
};

EditPage.globalLocalElement = function(global_or_local, element_id, $element_holder)
{
	// executed when an error occurs
	var showError = function(data){

		// global or local text
		if (global_or_local == 'local')
		{
			var global_or_local_text = "lokaal maken";
		}
		else
		{
			var global_or_local_text = "globaal maken";
		}

		// collect data about the error
		if(typeof data != 'object')
		{
			data = {};
		}
		if(typeof data.errorMessage != 'string')
		{
			data.errorMessage = 'Het '+global_or_local_text+' van het element is mislukt. Neem contact op met Bitman als dit probleem aanhoudt.';
		}

		// show the error
		CMS.alert(data.errorMessage);
	};

	// show or hide element
	$.ajax({
		url: '<?=site_url('admin/elements')?>/make_element_'+global_or_local+'/'+element_id + '/' + $('#page-content').data('page-id'),
		success: function(data)
		{
			if(data.success)
			{
    			// set the element to hidden/shown
				$element_holder.toggleClass('element-global');
				$element_holder.find('i[name=local_element], i[name=global_element]').toggleClass('hide');

			}else{
				showError(data);
			}
		},
		error: showError,
		method:'GET'
	});
};

EditPage.showhideElement = function(show_or_hide, element_id, $element_holder)
{
	// executed when an error occurs
	var showError = function(data){

		// show or hide text
		if (show_or_hide == 'hide')
		{
			var show_or_hide_text = "verbergen";
		}
		else
		{
			var show_or_hide_text = "tonen";
		}

		// collect data about the error
		if(typeof data != 'object')
		{
			data = {};
		}
		if(typeof data.errorMessage != 'string')
		{
			data.errorMessage = 'Het '+show_or_hide_text+' van het element is mislukt. Neem contact op met Bitman als dit probleem aanhoudt.';
		}

		// show the error
		CMS.alert(data.errorMessage);
	};

	// show or hide element
	$.ajax({
		url: '<?=site_url('admin/elements')?>/'+show_or_hide+'_element/'+element_id + '/' + $('#page-content').data('page-id'),
		success: function(data)
		{
			if(data.success)
			{
    			// set the element to hidden/shown
				$element_holder.toggleClass('element_invisible');
				$element_holder.find('i[name=hide_element], i[name=show_element]').toggleClass('hide');

			}else{
				showError(data);
			}
		},
		error: showError,
		method:'GET'
	});
};

EditPage.deleteElement = function(element_node, element_id)
{
	// ask the user if deleting this is ok
	CMS.modal({
		title: 'Element verwijderen',
		submitButton: 'Verwijderen',
		message: 'Weet u zeker dat u dit element wilt verwijderen?',
		callBackSubmit: function(){
			// remove element
        	$.ajax({
        		url: '<?=site_url('admin/elements/delete_element')?>/'+element_id,
        		success: function(data){
        			// remove the element from view
					$(element_node).remove(); 
        		},
        		error: function(data){
        			CMS.alert('Het verwijderen van het element is mislukt. Neem contact op met Bitman als dit probleem aanhoudt.');
        			console.error('Deleting element failed, response data:', data);
        		},
        		method:'GET'
        	});
		}
	});
};

EditPage.convertToSlug = function(Text)
{
	// Replace all accented characters
	Text = Text.replace(/[ÀÁÂÃÄÅàáâãäåĀāĂăĄąǍǎǺǻ']/g, 'a');
	Text = Text.replace(/[ÆæǼǽ']/g, 'ae');
	Text = Text.replace(/[ÇçĆćĈĉĊċČč']/g, 'c');
	Text = Text.replace(/[ÈÉÊËèéêëĒēĔĕĖėĘęĚě']/g, 'e');
	Text = Text.replace(/[ÌÍÎÏìíîïĨĩĪīĬĭĮįİıǏǐ']/g, 'i');
	Text = Text.replace(/[ÐĎďĐđ']/g, 'd');
	Text = Text.replace(/[ÑñŃńŅņŇňŉ']/g, 'n');
	Text = Text.replace(/[ÒÓÔÕÖØòóôõöøŌōŎŏŐőƠơǑǒǾǿ']/g, 'o');
	Text = Text.replace(/[ÙÚÛÜùúûüŨũŪūŬŭŮůŰűŲųƯưǓǔǕǖǗǘǙǚǛǜ']/g, 'u');
	Text = Text.replace(/[ÝýÿŶŷŸ']/g, 'y');
	Text = Text.replace(/[ß']/g, 'ss');
	Text = Text.replace(/[ĜĝĞğĠġĢģ']/g, 'g');
	Text = Text.replace(/[ĤĥĦħ']/g, 'h');
	Text = Text.replace(/[Ĳĳ']/g, 'ij');
	Text = Text.replace(/[Ĵĵ']/g, 'j');
	Text = Text.replace(/[Ķķ']/g, 'k');
	Text = Text.replace(/[ĹĺĻļĽľĿŀŁł']/g, 'l');
	Text = Text.replace(/[Œœ']/g, 'oe');
	Text = Text.replace(/[ŔŕŖŗŘř']/g, 'r');
	Text = Text.replace(/[ŚśŜŝŞşŠš']/g, 's');
	Text = Text.replace(/[ŢţŤťŦŧ']/g, 't');
	Text = Text.replace(/[Ŵŵ']/g, 'w');
	Text = Text.replace(/[ŹźŻżŽž']/g, 'z');
	Text = Text.replace(/[ſƒ']/g, 'f');

    return Text
        .toLowerCase()
        .replace(/ /g,'-')
        .replace(/[^\w-]+/g,'')
        ;
};

EditPage.toggleElementBody = function(element_id)
{
	if(!$('.element-'+element_id+' .element_body').is(':visible')){
		$('.element-'+element_id+' .element_body').fadeIn();
	}else{
		$('.element-'+element_id+' .element_body').hide();
	}
};

EditPage.editableSlug = function()
{
	if ($('#edit_page input[name=controller]').val() == '' && EditPage.node.data.addClass.indexOf('hidden-page') !== -1)
	{
		$('#edit_page input[name=slug]').removeAttr('disabled');
	}
	else
	{
		<?php if (!is_super_user()): ?>
		$('#edit_page input[name=slug]').attr({'disabled': 'disabled'});
		<?php endif; ?>
	}
};

/**
 * Hook for all custom script which need to be loaded with every new page edit load
 */
EditPage.hookPageLoaded = function() {

	// reload page redirect options and reconnect to selector
	PageReplaceBy.showReplaceValueInput();
	$('.replace-by-selector').unbind().change(PageReplaceBy.showReplaceValueInput);

	// Page tab update
	PageTab.addEvents();

};