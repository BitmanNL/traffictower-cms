<html>
<head>
	<title><?=$subject?></title>
</head>
<body>

	<?=lang('email_template_dear')?> <?=empty($to_name) ? lang('email_template_sir_madam') : $to_name?>,
	<br />
	<?=string_empty($body_html)?>
	<br />
	<?=lang('email_template_yours_sincerely')?>,<br />
	<br />
	<?=string_empty($from_name)?><br />
	<?=string_empty($from_email)?>

</body>
</html>