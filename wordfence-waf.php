<?php
// Before removing this file, please verify the PHP ini setting `auto_prepend_file` does not point to this.

if (file_exists('/home/web/projects/utabit_com/wp-content/plugins/wordfence/waf/bootstrap.php')) {
	define("WFWAF_LOG_PATH", '/home/web/projects/utabit_com/wp-content/wflogs/');
	include_once '/home/web/projects/utabit_com/wp-content/plugins/wordfence/waf/bootstrap.php';
}
?>
