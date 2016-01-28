
<li class="user-stats">
	<a href="#">
		<small>
			<?php if (count($last_user_logs) > 0):?>
				<strong>Ingelogd sinds:</strong>
				<?=human_date_recent($last_user_logs[0]['date_created'])?> uur
			<?php endif;?>

			<?php if (count($last_user_logs) > 1):?>
				<br>
				<strong>Vorige login:</strong>
				<?=human_date_recent($last_user_logs[1]['date_created'])?> uur<br>
				<strong>Van host:</strong> <?=gethostbyaddr($last_user_logs[1]['ip_hash'])?>
			<?php endif;?>
		</small>
	</a>
</li>