<div class="header-background navbar-environment-<?=ENVIRONMENT?>"></div>

<div class="header hidden-print">
	<div class="container">
		<nav class="navbar navbar-inverse navbar-environment-<?=ENVIRONMENT?>">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="<?php echo site_url('/admin/home')?>"><?=$app['site_name']?></a>
   			</div>
			<div class="collapse navbar-collapse" id="navbar-collapse">
				<? if (check_login()):?>
					<ul class="nav navbar-nav">
						<li class="dropdown">
							<a href="#" data-toggle="dropdown" class="dropdown-toggle"><span class="glyphicon glyphicon-home"></span>&nbsp;&nbsp;Site <span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li><a href="<?=site_url('admin/home')?>"><span class="glyphicon glyphicon-dashboard"></span>&nbsp;&nbsp;Dashboard</a></li>
								<li><a href="<?=site_url('admin/page')?>"><span class="glyphicon glyphicon-file"></span>&nbsp;&nbsp;Pagina's</a></li>
								<li><a href="<?=site_url('admin/site')?>"><span class="glyphicon glyphicon-cog"></span>&nbsp;&nbsp;Website instellingen</a></li>
								<li><a href="<?=site_url('admin/user')?>"><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;Gebruikers</a></li>
								<li><a href="<?=site_url('admin/user_group')?>"><span class="glyphicon glyphicon-th-large"></span>&nbsp;&nbsp;Gebruikersgroepen</a></li>
								<li><a href="<?=site_url('admin/email')?>"><span class="glyphicon glyphicon-envelope"></span>&nbsp;&nbsp;E-mail</a></li>
								<li><a href="<?=site_url('admin/file')?>"><span class="glyphicon glyphicon-folder-open"></span>&nbsp;&nbsp;Bestanden</a></li>
								<li><a href="<?=site_url('admin/log')?>"><span class="glyphicon glyphicon-book"></span>&nbsp;&nbsp;Logs</a></li>
								<? /*<li><a href="<?=site_url('admin/help')?>"><span class="glyphicon glyphicon-question-sign"></span>&nbsp;&nbsp;Help</a></li>*/?>
							</ul>
						</li>
						<?php /* ### INSERT_NAVIGATION_PLACEHOLDER ### */ ?>
					</ul>
			    	<ul class="nav navbar-nav navbar-right">
						<li class="dropdown">
							<a href="#" data-toggle="dropdown" class="dropdown-toggle"><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;<?=$user_data['screen_name']?> <span class="caret"></span></a>
							<ul class="dropdown-menu">
								<?=$this->load->view('admin/layouts/includes/user_stats', $data, TRUE)?>
								<li class="divider"></li>
								<li><a href="<?=site_url('admin/login/logout')?>"><span class="glyphicon glyphicon-off"></span>&nbsp;&nbsp;Uitloggen</a></li>
							</ul>
						</li>
					</ul>
			   	<? endif;?>
			</div>
		</nav>
	</div>
</div>

<?php if(ENVIRONMENT !== 'production'): ?>
<div id="environment" class="container hidden-print">
	<?php if (ENVIRONMENT === 'development'):?>
	    <div class="environment-inner text-development">
	    	<strong>LET OP:</strong> 
	    	Dit is de <abbr title="De development-omgeving is bedoeld voor de bouw van de website/applicatie.">development-omgeving</abbr>. 
	    	Voor preview aan de opdrachtgever tijdens ontwikkeling, gebruik de <abbr title="De staging-omgeving is bekend als de test-omgeving voor de opdrachtgever.">staging-omgeving</abbr>.
	    </div>
	<?php elseif (ENVIRONMENT === 'staging'):?>
	    <div class="environment-inner text-staging">
	    	<strong>LET OP:</strong> 
	    	Dit is de <abbr title="De test-omgeving wordt gebruikt voor preview-doeleinden tijdens ontwikkeling.">test-omgeving</abbr>. 
	    	Ingevoerde content wordt niet doorgezet en weergegeven op de <abbr title="De live-omgeving is zichtbaar voor publiek via de URL van de website/applicatie.">live-omgeving</abbr>.
	    </div>
	<?php endif; ?>
</div>
<?php endif; ?>