<?php return array (
  'logs' => 
  array (
    'path' => 'backup/logs',
    'type' => 'file',
  ),
  'DB' => 
  array (
    'type' => 'mysqli',
    'tablePre' => 'iwebshop_',
    'read' => 
    array (
      0 => 
      array (
        'host' => 'localhost:3306',
        'user' => 'root',
        'passwd' => 'root',
        'name' => 'bshop',
      ),
    ),
    'write' => 
    array (
      'host' => 'localhost:3306',
      'user' => 'root',
      'passwd' => 'root',
      'name' => 'bshop',
    ),
  ),
  'interceptor' => 
  array (
    0 => 'themeroute@onCreateController',
    1 => 'layoutroute@onCreateView',
    2 => 'plugin',
  ),
  'langPath' => 'language',
  'viewPath' => 'views',
  'skinPath' => 'skin',
  'classes' => 'classes.*',
  'rewriteRule' => 'pathinfo',
  'theme' => 
  array (
    'pc' => 
    array (
      'sysdefault' => 'green',
      'default' => 'red',
      'sysseller' => 'green',
    ),
    'mobile' => 
    array (
      'sysdefault' => 'default',
      'default' => 'red',
      'sysseller' => 'green',
    ),
  ),
  'timezone' => 'Etc/GMT-8',
  'upload' => 'upload',
  'dbbackup' => 'backup/database',
  'safe' => 'session',
  'lang' => 'zh_sc',
  'debug' => '2',
  'configExt' => 
  array (
    'site_config' => 'config/site_config.php',
  ),
  'encryptKey' => 'd3b1aee12c4f894f8d0608911beaa2b3',
  'authorizeCode' => '',
)?>