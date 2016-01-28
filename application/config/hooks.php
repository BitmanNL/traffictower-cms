<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['pre_system'] = array(
								'class' => 'Language_hook',
								'function' => 'multi_domain_mapping',
								'filename' => 'Language_hook.php',
								'filepath' => '../traffictower/hooks'
								);

$hook['post_controller'] = array(
                                'class'    => 'Profiler_hook',
                                'function' => 'enable_profiler',
                                'filename' => 'Profiler_hook.php',
                                'filepath' => '../traffictower/hooks'
                                );

/* End of file hooks.php */
/* Location: ./application/config/hooks.php */