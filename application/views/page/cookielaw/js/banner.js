$(document).ready(function()
{
	function getDocumentHash()
	{
		return escape(document.location.hash.substr(1));
 	}

	$('#cookielaw_accept').click(function()
	{
		var location = document.cms_site_url + '/cookielaw/accept?hash=' + getDocumentHash();

		document.location.href = location;
	});

	$('#cookielaw_reject').click(function()
	{
		var location = document.cms_site_url + '/cookielaw/reject?hash=' + getDocumentHash();

		document.location.href = location;
	});
});