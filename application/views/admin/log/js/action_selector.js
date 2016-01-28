$(document).ready(function() {
	$('#log_type').change(function() {
		location.href = document.cms_site_url + '/admin/log/index/' + $('#log_type').val();
	});
});