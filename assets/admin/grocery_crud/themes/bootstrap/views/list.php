<?php
if(!empty($list)){ ?>
<div class="span12" >
	<table class="table table-bordered tablesorter table-striped">
		<thead>
			<tr>
				<?php foreach($columns as $column){?>
				<th class="column-<?=$column->field_name?>">
					<div class="text-left field-sorting <?php if(isset($order_by[0]) &&  $column->field_name == $order_by[0]){?><?php echo $order_by[1]?><?php }?>"
						rel="<?php echo $column->field_name?>">
						<?php echo $column->display_as; ?>
					</div>
				</th>
				<?php }?>
				<?php if(!$unset_delete || !$unset_edit || !empty($actions)){?>
				<th class="no-sorter">
						<?php //echo $this->l('list_actions'); ?>
				</th>
				<?php }?>
			</tr>
		</thead>
		<tbody>
			<?php foreach($list as $num_row => $row){ ?>
			<tr class="<?php echo ($num_row % 2 == 1) ? 'erow' : ''; ?>">
				<?php foreach($columns as $column){?>
					<td class="<?php if(isset($order_by[0]) &&  $column->field_name == $order_by[0]){?>sorted<?php }?> column-<?=$column->field_name?>">
						<div class="text-left"><?php echo ($row->{$column->field_name} != '') ? $row->{$column->field_name} : '&nbsp;' ; ?></div>
					</td>
				<?php }?>
				<?php if(!$unset_delete || !$unset_edit || !$unset_read || !empty($actions)){?>
				<td align="left" style="width: 80px">
					<div class="tools">
						<div class="btn-group">
							<button class="btn btn-info dropdown-toggle" data-toggle="dropdown">
								<?php echo $this->l('list_actions'); ?>
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								<?php
								if(!$unset_read){?>
									<li>
										<a href="<?php echo $row->read_url?>" title="<?php echo $this->l('list_view')?> <?php echo $subject?>">
											<i class="glyphicon glyphicon-eye-open"></i>
											<?php echo $this->l('list_view'); ?>
										</a>
									</li>
								<?php
								}
								if(!$unset_edit){?>
									<li>
										<a href="<?php echo $row->edit_url?>" title="<?php echo $this->l('list_edit')?> <?php echo $subject?>">
											<i class="glyphicon glyphicon-pencil"></i>
											<?php echo $this->l('list_edit'); ?>
										</a>
									</li>
								<?php
								}
								if(!$unset_delete){?>
									<li>
										<a href="javascript:void(0);" data-target-url="<?php echo $row->delete_url?>" title="<?php echo $this->l('list_delete')?> <?php echo $subject?>" class="delete-row" >
											<i class="glyphicon glyphicon-trash"></i>
											<?php echo $this->l('list_delete'); ?>
										</a>
									</li>
								<?php
								}
								if(!empty($row->action_urls)){
									foreach($row->action_urls as $action_unique_id => $action_url){
										$action = $actions[$action_unique_id];
										?>
										<li>
											<a href="<?php echo $action_url; ?>" class="<?php echo $action->css_class; ?> crud-action" title="<?php echo strip_tags($action->label)?>"><?php
											if(!empty($action->image_url)){ ?>
												<img src="<?php echo $action->image_url; ?>" alt="" />
											<?php
											}
											echo ' '.$action->label;
											?>
											</a>
										</li>
									<?php
									}
								}
								?>
								</ul>
							</div>
					</div>
				</td>
				<?php }?>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
<?php }else{ ?>
	<br/><div class="span12"><p><?php echo $this->l('list_no_items'); ?></p></div><br/><br /><br />
<?php }?>