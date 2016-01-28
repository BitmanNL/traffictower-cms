<?php
//$this->set_css($this->default_theme_path.'/twitter-bootstrap/css/bootstrap.min.css');
//$this->set_css($this->default_theme_path.'/twitter-bootstrap/css/bootstrap-responsive.min.css');
$this->set_css($this->default_theme_path.'/bootstrap/css/style.css');
//$this->set_css($this->default_theme_path.'/bootstrap/css/jquery-ui/flick/jquery-ui-1.9.2.custom.css');

//$this->set_js_lib($this->default_javascript_path.'/'.grocery_CRUD::JQUERY);

//	JAVASCRIPTS - JQUERY-UI
//$this->set_js($this->default_theme_path.'/bootstrap/js/jquery-ui/jquery-ui-1.9.2.custom.js');

//	JAVASCRIPTS - JQUERY LAZY-LOAD
$this->set_js_lib($this->default_javascript_path.'/common/lazyload-min.js');

if (!$this->is_IE7()) {
	$this->set_js_lib($this->default_javascript_path.'/common/list.js');
}
//	JAVASCRIPTS - TWITTER BOOTSTRAP
//$this->set_js($this->default_theme_path.'/twitter-bootstrap/js/libs/bootstrap/bootstrap.min.js');
//$this->set_js($this->default_theme_path.'/twitter-bootstrap/js/libs/bootstrap/application.js');
//	JAVASCRIPTS - MODERNIZR
$this->set_js($this->default_theme_path.'/bootstrap/js/libs/modernizr/modernizr-2.6.1.custom.js');
//	JAVASCRIPTS - TABLESORTER
$this->set_js($this->default_theme_path.'/bootstrap/js/libs/tablesorter/jquery.tablesorter.min.js');
//	JAVASCRIPTS - JQUERY-COOKIE
$this->set_js($this->default_theme_path.'/bootstrap/js/cookies.js');
//	JAVASCRIPTS - JQUERY-FORM
$this->set_js($this->default_theme_path.'/bootstrap/js/jquery.form.js');
//	JAVASCRIPTS - JQUERY-NUMERIC
$this->set_js($this->default_javascript_path.'/jquery_plugins/jquery.numeric.min.js');
//	JAVASCRIPTS - JQUERY FANCYBOX
$this->set_js($this->default_javascript_path.'/jquery_plugins/jquery.fancybox-1.3.4.js');
//	JAVASCRIPTS - JQUERY EASING
$this->set_js($this->default_javascript_path.'/jquery_plugins/jquery.easing-1.3.pack.js');

//	JAVASCRIPTS - JQUERY UI
//$this->set_js_lib($this->default_javascript_path.'/jquery_plugins/ui/'.grocery_CRUD::JQUERY_UI_JS);
//$this->load_js_jqueryui();

//	JAVASCRIPTS - twitter-bootstrap - CONFIGURAÇÕES
$this->set_js($this->default_theme_path.'/bootstrap/js/app/twitter-bootstrap-edit.js');
//	JAVASCRIPTS - JQUERY-FUNCTIONS
$this->set_js($this->default_theme_path.'/bootstrap/js/jquery.functions.js');
?>
<div class="twitter-bootstrap crud-form row">
	<div class="col-md-12">

		<h3><?php echo $this->l('form_edit'); ?> <?php echo $subject?></h3>

		<!-- CONTENT FOR ALERT MESSAGES -->
		<div id="message-box"></div>

		<div id="main-table-box">
			<?php echo form_open( $update_url, 'method="post" id="crudForm" class="form-div" role="form" autocomplete="off" enctype="multipart/form-data" onsubmit="return false"'); ?>
			
			<table class="table">
			<tbody>
			<?php foreach($fields as $field): ?>
				<tr id="<?php echo $field->field_name; ?>_field_box" class="form-field-box">
					<th class="col-md-3">
						<label class="form-display-as-box" id="<?php echo $field->field_name; ?>_display_as_box" for="field-<?php echo $field->field_name; ?>">
							<?php echo $input_fields[$field->field_name]->display_as?><?php echo ($input_fields[$field->field_name]->required)? '<span class="required">*</span>' : ""?> :
						</label>
					</th>
					<td>
						<div class="form-input-box" id="<?php echo $field->field_name; ?>_input_box">
							<?php echo $input_fields[$field->field_name]->input?>
						</div>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
			</table>
			
			<?php
			//	Hidden Elements
			if(!empty($hidden_fields)){
				foreach($hidden_fields as $hidden_field){
					echo $hidden_field->input;
				}
			}?>
			
			<div class="pull-right">
				<span class="hide loading" id="ajax-loading"><?php echo $this->l('form_update_loading'); ?></span>
				<?php 	if(!$this->unset_back_to_list) { ?>
					<input type="button" value="<?php echo $this->l('form_cancel'); ?>" class="btn btn-default return-to-list" />
				<?php } ?>
				<input type="button" value="<?php echo $this->l('form_update_changes'); ?>" class="btn btn-success submit-form"/>
				<?php 	if(!$this->unset_back_to_list) { ?>
					<input type="button" value="<?php echo $this->l('form_update_and_go_back'); ?>" id="save-and-go-back-button" class="btn btn-success"/>
				<?php 	} ?>
			</div>

			<?php echo form_close(); ?>
		</div>

	</div>
</div>
<script>
	var validation_url = "<?php echo $validation_url?>",
		list_url = "<?php echo $list_url?>",
		message_alert_edit_form = "<?php echo $this->l('alert_edit_form')?>",
		message_update_error = "<?php echo $this->l('update_error')?>";
</script>