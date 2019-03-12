<?php 
define ('DS',DIRECTORY_SEPARATOR);

define('BASE_URL', $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
define('BASE_DIR', __DIR__);
define('DIR_SAM_CONFIG', __DIR__.DS.'Sam'.DS.'Installation'.DS.'Config');

require_once(__DIR__ . DS . 'functions.php');

require_once(DIR_SAM_CONFIG . DS . 'CustomizableClass.php');
require_once(DIR_SAM_CONFIG . DS . 'ConfigCombiner.php');
require_once(DIR_SAM_CONFIG . DS . 'ConfigEditor.php');
require_once(DIR_SAM_CONFIG . DS . 'ConfigFormRenderer.php');

use Sam\Installation\Config\ConfigCombiner;
use Sam\Installation\Config\ConfigEditor;
use Sam\Installation\Config\ConfigFormRenderer;

$configName = (isset($_GET['name']) && is_string($_GET['name'])) ? $_GET['name'] : 'core'; 

$configGombiner = ConfigCombiner::getInstance();
$configGombiner->setConfigName($configName);
if ($configGombiner->validate()) {
  $data = $configGombiner->buildWebData();
  $data['page'] = 'configForm';
  $data['renderErrors'] = $configGombiner->getErrors();  
  $renderer = ConfigFormRenderer::getInstance();
  $renderer->setViewData($data);
  $renderer->render();
} else {

}







