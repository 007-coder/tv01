<?php 
define ('DS',DIRECTORY_SEPARATOR);
define('BASE_URL', $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
define('DIR_CONFIG', __DIR__.DS.'_configuration');

define('CSS_URL', BASE_URL.'assets/css/');
define('JS_URL', BASE_URL.'assets/js/');
define('IMG_URL', BASE_URL.'assets/img/');


require(DIR_CONFIG.DS.'SamObserverNamespace.php');
require(__DIR__.DS.'functions.php');



// Getting Core Config array
$coreConf = require(__DIR__.DS.'_configuration'.DS.'core.php');

if (isset($_POST) && count($_POST)) {
  require(__DIR__.DS.'processPost.php');  
}

// Getting Core Local Config array or empty array
// if core.local.php not exists
$coreLocalConf = 
  (file_exists(DIR_CONFIG.DS.'core.local.php')) 
  ?  include(DIR_CONFIG.DS.'core.local.php') : [];

$configMeta = 
  (file_exists(DIR_CONFIG.DS.'configMeta.php')) 
  ?  include(DIR_CONFIG.DS.'configMeta.php') : [];



$buildConfigFormData = (count($coreLocalConf)) ? array_replace_recursive($coreConf, $coreLocalConf) : $coreConf;



//wrap_pre($configMeta);

$ConfFormNav = [];
foreach ($buildConfigFormData as $kc => $confVal) {
  $ConfFormNav[$kc] = [
    'name'=>''
  ];
}

$confFormViewData = [
  'appConfig'=>$buildConfigFormData,
  'configMeta' => $configMeta,
  'formNav'=>$ConfFormNav,
];


require(__DIR__.DS.'view'.DS.'configForm.php');
