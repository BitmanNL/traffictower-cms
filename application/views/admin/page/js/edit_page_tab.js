var PageTab = {};

PageTab.addEvents = function() {
	$('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
		var tab = $(e.target).attr('href').substr(1);

		$.bbq.pushState({
			tab: tab
		});
	});

	var state = $.bbq.getState();

	if (typeof state.tab == 'undefined') {
		state.tab = 'page-meta';
	}

	$('.tab-content .tab-pane li').removeClass('active');
	$('.tab-content #'+state.tab).addClass('active');
	
	$('.nav-tabs li').removeClass('active');
	$('.nav-tabs li.tab-'+state.tab).addClass('active');
};