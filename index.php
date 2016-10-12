<?php
$iweb = dirname(__FILE__)."/lib/iweb.php";
$config = dirname(__FILE__)."/config/config.php";
require($iweb);
$app = IWeb::createWebApp($config);
//print_r(get_included_files());
$app->run();
?>