/**
 * Page
 *
 * In this file, the page actions are handled. That is:
 *  - Adding a page
 *  - Deleting a page
 *  - Moving a page in the tree
 *  - Start editing a page. The actual code for editing the page is in edit_page.js
 */

var Page = {};

/**
 * Add Page
 *
 * Add a page to the tree
 *
 * @param object node The node to use as parent
 */
Page.addPage = function(node){
	
	var language = node.data.language;

	// define the error function, for when things go south
	var showError = function(data){
		CMS.alert('Het toevoegen van de pagina is mislukt. Neem contact op met Bitman als dit probleem aanhoudt.');
		console.error('Adding page failed, response data:', data);
	};

	// tell the server to add the page
	$.ajax({
		url: '<?=site_url('admin/page/create_page')?>/'+node.data.key,
		data: {language: language, secondary_navigation: node.data.secondary_navigation},
		method: 'GET',
		success: function(data){
			if(data.success)
			{
				var secondary_navigation = (typeof node.data.secondary_navigation == 'undefined' ? null : node.data.secondary_navigation);
				// add the page to the tree
				var childNode = node.addChild({
		    		title: data['title'],
		    		key: data['id'],
		    		addClass: 'hidden-page',
		    		language: language,
		    		secondary_navigation: secondary_navigation
				});

				// expand the parent
				node.expand(true);

				// select the node
				childNode.activate();

			}
			else
			{
				showError(data);
			}
		},
		error: showError
	});
};

/**
 * Delete Page
 *
 * Delete a page from the tree
 *
 * @param object The node to delete
 */
Page.deletePage = function(node)
{	
	var showError = function(data)
	{
		if (typeof data == 'undefined' || typeof data.error == 'undefined')
		{
			CMS.alert('Het verwijderen van de pagina is mislukt. Neem contact op met Bitman als dit probleem aanhoudt.');
			console.error('Deleting page failed, response data:', data);
		}
		else
		{
			CMS.alert(data.error);
		}
	};

	// ask the user if deleting this is ok
	CMS.modal({
		title: 'Pagina verwijderen',
		submitButton: 'Verwijderen',
		message: 'Weet u zeker dat u pagina "' + node.data.title + '" wilt verwijderen?',
		callBackSubmit: function(){
			// remove page
        	$.ajax({
        		url: '<?=site_url('admin/page/delete_page')?>/'+node.data.key,
        		success: function(data)
        		{	
        			// remove the node from the tree
        			if (typeof(data) == 'object' && data.success)
        			{
        				node.remove();

        				// reset editPage changed, there are no changes in a deleted page
        				EditPage.changed = false;

						// remove the edit page from view
						$('#edit_holder').html(''); 
        			}
        			else
        			{
        				showError(data);
        			}
        		},
        		error: showError
        	});
		}
	});
};

/**
 * Show Hide Page
 *
 * Toggle the visibility of a page
 *
 * @param object node The node to show or hide
 */
Page.showhidePage = function(node)
{
	var hideNode;
	var showhideText = '';
	var url;
	
	if (node.data.addClass === null)
	{
		EditPage.node.data.addClass = '';
	}

	if(node.data.addClass.indexOf('hidden-page') > -1){
		hideNode = false;
		showhideText = 'show';
		url = '<?=site_url('admin/page/show_page')?>/';
	}else{
		hideNode = true;
		showhideText = 'verbergen';
		url = '<?=site_url('admin/page/hide_page')?>/';
	}

	var showError = function(data){
		if (typeof data == 'undefined' || typeof data.error == 'undefined')
		{
			CMS.alert('Het '+showhideText+' van de pagina is mislukt. Neem contact op met Bitman als dit probleem aanhoudt.');
			console.error('Hide/show page failed, response data:', data);		}
		else
		{
			CMS.alert(data.error);
		}
	};

	// add page
	$.ajax({
		url: url+node.data.key,
		success: function(data){
			if(data.success){

				if(hideNode){
            		node.data.addClass += ' hidden-page';
            	}else{
            		node.data.addClass = node.data.addClass.replace(' hidden-page','');
            		node.data.addClass = node.data.addClass.replace('hidden-page','');
            	}
            	
            	node.render();

            	$('.btn_show_hide_page').toggle();

            	EditPage.editableSlug();

			}else{
				showError(data);
			}
		},
		error: showError
	});
};

/**
 * Move Page
 *
 * Move a page to another position within the tree
 */
Page.movePage = function(node, sourceNode, hitMode, ui, draggable)
{
	var showError = function(data){
		CMS.alert('Het verplaatsen van de pagina is mislukt. Neem contact op met Bitman als dit probleem aanhoudt.');
		console.error('Moving page failed, response data:', data);
	};

	$.ajax({
		url: '<?=site_url('admin/page/move_page')?>/' + sourceNode.data.key + '/' + node.data.key + '/' + hitMode + '/' + node.data.secondary_navigation,
		success: function(data){
			if(data.success){
				// move the node
    			sourceNode.move(node, hitMode);

				// expand the drop target
				if (hitMode == 'over')
				{
					node.expand(true);
				}

				// reload edit page for module select
				Page.editPage(sourceNode);
				
			}else{
				showError(data);
			}
			
		},
		error: showError
	});
};

/**
 * Edit Page
 *
 * Start editing a page. Get the editing form from the server.
 *
 * @param object node The node of the page to edit
 */
 Page.editPage = function(node)
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
    			$('#treedialog').modal('hide');
    			$(this).unbind();
    			Page.editPage(node);
    		}
    	});
 	}else{

		var showError = function(data){
			CMS.alert('Het openen van de pagina is mislukt. Neem contact op met Bitman als dit probleem aanhoudt.');
			console.error('Opening page failed, response data:', data);
    	};

    	// add page
    	$.ajax({
    		url: '<?=site_url('admin/page/edit')?>/'+node.data.key,
    		success: function(data){
    			$('#edit_holder').html(data);

    			$('#btn_delete_page').click(function(){
					Page.deletePage(node);
				});
		    	
		    	$('.btn_show_hide_page').click(function(){
					Page.showhidePage(node);
				});

		    	// start editing
				EditPage.editPage(node);
    		},
    		error: showError
    	});
 	}
 };