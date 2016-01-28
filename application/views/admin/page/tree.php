<?php if (count($languages) > 1):?>
	<div class="btn-group">
	    <a class="btn dropdown-toggle btn-success" data-toggle="dropdown" href="#">
	    	Nieuwe pagina
	    	<span class="caret"></span>
	    </a>
	    <ul class="dropdown-menu">
	    	<?php foreach ($languages as $language):?>
	    		<li><a href="#" class="new-page" data-language="<?=$language?>"><?=$language_data[$language]['name']?></a></li>
	    	<?php endforeach;?>
	    </ul>
    </div>
<?php else:?>
	<?php foreach ($languages as $language):?>
		<a href="#" class="btn btn-success new-page" data-language="<?=$language?>">Nieuwe pagina</a>
	<?php endforeach;?>
<?php endif;?>
<br /><br />

<!-- show the tree -->
<div id="tree"></div>
