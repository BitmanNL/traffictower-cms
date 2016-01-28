$(document).ready(function() {
	// reload page redirect options and reconnect to selector
	PageReplaceBy.showReplaceValueInput();
	$('.replace-by-selector').change(PageReplaceBy.showReplaceValueInput);
});

var PageReplaceBy = {};

PageReplaceBy.showReplaceValueInput = function() {
	var replaceBy = $('.replace-by-selector').val();
	$('.replace-by').addClass('hide');
	$('.replace-by-'+replaceBy).removeClass('hide');
};