$(document).ready(function(){

	<? if($form_submit_success): ?>
		CMS.alert('Website gegevens met succes opgeslagen', 'success');
	<? endif; ?>

	CMS.fileManager('app_image');

	CMS.fileManager(
		'app_apple_touch_icon',
		{
			extensions: 'png',
			format: 'square'
		}
	);
});
