<?=lang('email_template_dear')?> <?=empty($to_name) ? lang('email_template_sir_madam') : $to_name?>,

<?=string_empty($body_text)?> 

<?=lang('email_template_yours_sincerely')?>,

<?=string_empty($from_name)?> 
<?=string_empty($from_email)?>