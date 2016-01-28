/**
 * Tree
 *
 * In this file, the tree with all the pages is created
 */

var Tree = {};

// var to hold the root nodes per language
Tree.rootNodes = {};

$(document).ready(function(){

	/**
	 * Configure the tree object
	 */
	$('#tree').dynatree({

		imagePath: '<?=base_url('assets/admin/img')?>/',

		//DRAG N DROP
		dnd: {
			onDragStart: function(node) {
				return true;
			},

			autoExpandMS: 1000,
			
			preventVoidMoves: true, // Prevent dropping nodes 'before self', etc.
			
			onDragEnter: function(node, sourceNode) {
				if(!sourceNode.data.isFolder){
					return true;
				}
			},

			onDragOver: function(node, sourceNode, hitMode) {
				// check source and to node for same language
				if (node.data.language != sourceNode.data.language)
				{
					return false;
				}
				// check source and to node for same secondary navigation
				/*if (node.data.secondary_navigation != sourceNode.data.secondary_navigation)
				{
					return false;
				}*/

				if(node.isDescendantOf(sourceNode)){
					return false;
				}
				if(node.data.key == 'keyRoot' && (hitMode == 'before' || hitMode == 'after')){
					return false;
				}
				// no page
				if(hitMode == 'after' && isNaN(parseInt(node.data.key,10)))
				{
					return false;
				}
			},

			onDrop: function(node, sourceNode, hitMode, ui, draggable) {
				Page.movePage(node, sourceNode, hitMode, ui, draggable);
			}
		},
		//END DRAG N DROP

		//REMEMBER
		persist: true,
		//END REMEMBER

		//fancy drop effect
		fx: { height: "toggle", duration: 200 },

		onDblClick: function(node, event){
			node.toggleExpand();
		},

		onActivate: function(node, event){
			if(!node.data.isFolder){
		    	// set the hash state
	    		$.bbq.pushState({page_id:node.data.key});
			}
		}

	});
	
	/**
	 * Add events new page button
	 */
	$('a.new-page').click(function(event){
		event.preventDefault();
		var language = $(this).data('language');
		Page.addPage(Tree.rootNodes[language]);
	});


	/**
	 * Create the contect menus
	 */

	// page context menu
	$.contextMenu({
		className: 'dropdown-menu',
        selector: '#tree li',
        callback: function(key, options) {
            var node = this[0].dtnode;

            if(key === 'add'){

            	Page.addPage(node);
            	
            }
            if(key === 'cut'){
            	
            	Page.deletePage(node);

            }
            if(key === 'show/hide'){
            	
            	Page.showhidePage(node);

            }
        },
        items: {
            "add": {name: "Voeg hierbinnen pagina toe", icon: ""},
            //"cut": {name: "Verwijder pagina", icon: ""},
            "show/hide": {name: "Toon/verberg", icon: ""},
            //"sep1": "---------",
            //"quit": {name: "Quit", icon: ""}
        }
    });

	// siteRoot context menu
    $.contextMenu({
		className: 'dropdown-menu',
        selector: '#tree li .dynatree-folder',
        callback: function(key, options) {
            var node = this.parent()[0].dtnode;
          
            if(key === 'add'){
            	
            	Page.addPage(node);

            }
        },
        items: {
            "add": {name: "Voeg pagina toe"}
        }
    });

    /**
	 * Create the tree from the list of pages
	 */

	function createTree(tree, node, language, secondary_navigation)
	{
		for(var i in tree)
		{	
			var childNode = node.addChild({
		        title: tree[i].title,
		        key: tree[i].id,
	        	language: language,
	        	secondary_navigation: secondary_navigation
		    });

			if (firstNode === null)
			{
				firstNode = childNode;
			}

			if(tree[i].is_visible == 'no')
			{
				childNode.data.addClass += ' hidden-page';
			}

			if(tree[i].is_system_page == 'yes')
			{
				childNode.data.addClass += ' system-page'
			}

			if(tree[i].in_menu == 'no')
			{
				childNode.data.addClass += ' not-in-menu-page'
			}

			if(tree[i].children.length)
			{
				createTree(tree[i].children, childNode, language, secondary_navigation);
			}
		}
	}

	var languages = $.parseJSON('<?=$languages_encoded?>');
    var tree = $.parseJSON('<?=$tree_encoded?>');
	var language_data = $.parseJSON('<?=$language_data_encoded?>');
    var firstNode = null;

    var secondary_navigations = $.parseJSON('<?=$secondary_navigations_encoded?>');
    var secondary_navigation_tree = $.parseJSON('<?=$secondary_navigation_tree_encoded?>');

	for (var i in languages)
	{

    	var language = languages[i];
		if (typeof(Object.keys) !== 'undefined' && Object.keys(languages).length === 1)
		{
			var node = $("#tree").dynatree("getRoot").addChild({
		        title: 'Site structuur',
		        isFolder: true,
		        language: language,
		        secondary_navigation: null
		    });
		}
		else
		{
			var node = $("#tree").dynatree("getRoot").addChild({
		        title: language_data[language]['name'],
		        isFolder: true,
		        icon: 'flags/dyna-' + language + '.png',
		        language: language,
		        secondary_navigation: null
		    });
		}

		Tree.rootNodes[language] = node;

		// secondary menu's
		for (var secondary_navigation in secondary_navigations){
			var menuNode = node.addChild({
		        title: secondary_navigations[secondary_navigation],
		        isFolder: true,
	        	language: language,
	        	secondary_navigation: secondary_navigation
		    });

		    createTree(secondary_navigation_tree[language][secondary_navigation], menuNode, language, secondary_navigation)
		}
		
		// create the actual tree
		createTree(tree[language], node, language, null);


		// epand the root node
		node.expand(true);
	}

	/**
	 * Bind the history events
	 * So the user can use the back-button to switch
	 * between pages
	 */

	// if the hash state changes, execute the editPage function
    $(window).bind( 'hashchange', function(e)
    {
    	// get the current state
    	var state = $.bbq.getState();

    	// get the page id from the current state
    	var page_id = state.page_id;

    	// get the node for this page id
    	var node = $("#tree").dynatree("getTree").getNodeByKey(page_id);

    	// if this node (still) exists, start editing
    	if(node)
    	{
	    	// select this node
	    	node.activate();

	    	// start editing
	    	Page.editPage(node);
	    }
	    else
	    {
	    	// open first item
	    	if(firstNode !== null && typeof(firstNode.data) !== 'undefined' && typeof(firstNode.data.key) !== 'undefined'){
				$.bbq.pushState({
					page_id:firstNode.data.key
				});
			}
	    }
    });

    // trigger a haschanged event when the page loads
    $(window).trigger('hashchange');
});