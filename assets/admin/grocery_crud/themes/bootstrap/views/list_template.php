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
	//	JAVASCRIPTS - JQUERY-PRINT-ELEMENT
	$this->set_js($this->default_theme_path.'/bootstrap/js/libs/print-element/jquery.printElement.min.js');
	$this->set_js($this->default_theme_path.'/bootstrap/js/libs/mb.browser/jquery.mb.browser.min.js');
	//	JAVASCRIPTS - JQUERY FANCYBOX
	$this->set_js($this->default_javascript_path.'/jquery_plugins/jquery.fancybox-1.3.4.js');
	//	JAVASCRIPTS - JQUERY EASING
	$this->set_js($this->default_javascript_path.'/jquery_plugins/jquery.easing-1.3.pack.js');
	
	//	JAVASCRIPTS - twitter-bootstrap - CONFIGURAÇÕES
	$this->set_js($this->default_theme_path.'/bootstrap/js/app/twitter-bootstrap.js');
	//	JAVASCRIPTS - JQUERY-FUNCTIONS
	$this->set_js($this->default_theme_path.'/bootstrap/js/jquery.functions.js');
?>

<script type="text/javascript">
	var base_url = "<?php echo base_url();?>",
		subject = "<?php echo $subject?>",
		ajax_list_info_url = "<?php echo $ajax_list_info_url?>",
		unique_hash = "<?php echo $unique_hash; ?>",
		message_alert_delete = "<?php echo $this->l('alert_delete'); ?>";
</script>

<!-- UTILIZADO PARA IMPRESSÃO DA LISTAGEM -->
<div id="hidden-operations" class="hide"></div>

<h3><?php echo $subject?></h3>

<div class="twitter-bootstrap row">
	<div id="main-table-box">
		<div id="options-content" class="col-md-12">
			<?php
			if(!$unset_add || !$unset_export || !$unset_print){?>
				<?php if(!$unset_add){?>
					<a href="<?php echo $add_url?>" title="<?php echo $this->l('list_add'); ?> <?php echo $subject?>" class="add-anchor btn btn-success">
						<?php echo $this->l('list_add'); ?>
					</a>
	 			<?php
	 			}
	 			if(!$unset_export) { ?>
		 			<a class="export-anchor btn btn-info" data-url="<?php echo $export_url; ?>" rel="external">
		 				<?php echo $this->l('list_export');?>
		 			</a>
	 			<?php
	 			}
	 			if(!$unset_print) { ?>
		 			<a class="print-anchor btn btn-info" data-url="<?php echo $print_url; ?>">
		 				<?php echo $this->l('list_print');?>
		 			</a>
	 			<?php
	 			}
	 		} ?>

	 		<div class="pull-right">
	 			<?php echo form_open( $ajax_list_url, 'method="post" id="filtering_form" autocomplete = "off" class="form-inline" role="form"'); ?>
				<div class="sDiv" id="quickSearchBox">
					<div class="sDiv2">
						<input type="hidden" name="page" value="1" size="4" id="crud_page">
						<input type="hidden" name="per_page" id="per_page" value="<?php echo $default_per_page; ?>" />
						<input type="hidden" name="order_by[0]" id="hidden-sorting" value="<?php if(!empty($order_by[0])){?><?php echo $order_by[0]?><?php }?>" />
						<input type="hidden" name="order_by[1]" id="hidden-ordering"  value="<?php if(!empty($order_by[1])){?><?php echo $order_by[1]?><?php }?>"/>

						<select name="search_field" id="search_field" class="form-control">
							<option value=""><?php echo $this->l('list_search_all');?></option>
							<?php foreach($columns as $column){?>
								<option value="<?php echo $column->field_name?>"><?php echo $column->display_as; ?></option>
							<?php }?>
						</select>
					
						<input type="text" class="qsbsearch_fieldox form-control" name="search_text" style="width: 160px;" id="search_text" placeholder="<?php echo $this->l('list_search');?>">
					
						<input type="button" class="btn btn-default" data-dismiss="modal" value="<?php echo $this->l('list_search');?>" id="crud_search">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="<?php echo $this->l('list_clear_filtering');?>" id="search_clear">
						
					</div>
				</div>
				<?php echo form_close(); ?>
	 		</div>
 		</div>
		<br/>

		<!-- CONTENT FOR ALERT MESSAGES -->
		<?php if($success_message !== null): ?>
		<script>
		var alert_message = "<?=$success_message?>";
		var alert_type = 'success';
		<?php $this->set_js($this->default_theme_path.'/bootstrap/js/cms_alert.js'); ?>
		</script>
		<?php endif; ?>


		<div id="ajax_list" class="col-md-12">
			<?php echo $list_view; ?>
		</div>

		<div class="pGroup col-md-12">
			<div class="form-inline">
				<select name="tb_per_page" id="tb_per_page" class="form-control">
					<?php foreach($paging_options as $option){?>
						<option value="<?php echo $option; ?>" <?php echo ($option == $default_per_page) ? 'selected="selected"' : ''; ?> ><?php echo $option; ?></option>
					<?php }?>
				</select>

				<a href="javascript:void(0);"><span class="btn btn-default pFirst pButton first-button">
					<i class="glyphicon glyphicon-fast-backward"></i>
				</span></a>
				<a href="javascript:void(0);"><span class="btn btn-default pPrev pButton prev-button">
					<i class="glyphicon glyphicon-step-backward"></i>
				</span></a>
				<a href="javascript:void(0);"><span class="btn btn-default pNext pButton next-button">
					<i class="glyphicon glyphicon-step-forward"></i>
				</span></a>
				<a href="javascript:void(0);"><span class="btn btn-default pLast pButton last-button">
					<i class="glyphicon glyphicon-fast-forward"></i>
				</span></a>

				<span class="pcontrol">&nbsp;
					<?php echo $this->l('list_page'); ?>
					<input name="tb_crud_page" type="text" style="width: 60px;" id="tb_crud_page" class="form-control" value="1">
					<?php echo $this->l('list_paging_of'); ?>
					<span id="last-page-number"><?php echo ceil($total_results / $default_per_page); ?></span>
				</span>

				<div class="hide loading" id="ajax-loading"><?php echo $this->l('form_update_loading'); ?></div>

			</div>

			<br />

			<span class="pPageStat">
				<?php
				$paging_starts_from = '<span id="page-starts-from">1</span>';
				$paging_ends_to = '<span id="page-ends-to">'. ($total_results < $default_per_page ? $total_results : $default_per_page) .'</span>';
				$paging_total_results = '<span id="total_items">'.$total_results.'</span>';
				echo str_replace( array('{start}','{end}','{results}'), array($paging_starts_from, $paging_ends_to, $paging_total_results), $this->l('list_displaying')); ?>
			</span>

		</div>
	</div>
</div>