<?php

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

$url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']
    . $_SERVER['REQUEST_URI'];
$parseBaseUrl = parse_url($url);
$urlPath = '';
if (isset($parseBaseUrl['path']) && !empty($parseBaseUrl['path'])) {
    $explPath = explode('/', $parseBaseUrl['path']);
    foreach ($explPath as $k => $path) {
        if (stripos($path, '.php') !== false) {
            unset($explPath[$k]);
        }
    }
    $urlPath = implode('/', $explPath);
}
define('BASE_URL', $parseBaseUrl['scheme'] . '://' . $parseBaseUrl['host']);


define('BASE_DIR', __DIR__);
define('DIR_SAM_CONFIG', BASE_DIR . DS . 'Sam' . DS . 'Installation' . DS . 'Config');
define('DIR_SAM_VALIDATE', BASE_DIR . DS . 'Sam' . DS . 'Core' . DS . 'Validate');

require_once BASE_DIR . DS . 'functions.php';

require_once DIR_SAM_CONFIG . DS . 'CustomizableClass.php';
require_once DIR_SAM_CONFIG . DS . 'ConfigCombiner.php';
require_once DIR_SAM_CONFIG . DS . 'ConfigEditor.php';
require_once DIR_SAM_CONFIG . DS . 'ConfigFormRenderer.php';

require_once DIR_SAM_CONFIG . DS . 'ConfigValidator.php';
require_once DIR_SAM_VALIDATE . DS . 'NumberValidator.php';
require_once DIR_SAM_VALIDATE . DS . 'Floating.php';

use Sam\Installation\Config\ConfigCombiner;
use Sam\Installation\Config\ConfigEditor;
use Sam\Installation\Config\ConfigFormRenderer;


$editorErrors = [];
$configUpdated = false;
$configName = '';
$validConfigNames = ['core', 'megaCore', 'intelInside'];

// Setting up current config name
if (isset($_POST) && count($_POST)) {
    $configName = (isset($_POST['configName']) && is_string($_POST['configName']))
        ? $_POST['configName'] : 'core';
} else if (isset($_GET['file']) && is_string($_GET['file'])) {
    $configName = $_GET['file'];
} else {
    $configName = 'core';
}


$configCombiner = ConfigCombiner::getInstance();
$configCombiner->setValidConfigNames($validConfigNames);
$configCombiner->setConfigName($configName);

$workConfigName = $configCombiner->getConfigName();



// processing POST
if (isset($_POST) && count($_POST) && !is_null($workConfigName)) {
    $configEditor = ConfigEditor::getInstance();
    $configEditor->setValidConfigNames($configCombiner->getValidConfigNames());

    if ($configEditor->setConfigName($workConfigName)) {
        $configEditor->setPostData($_POST);
        if (isset($_POST['action']) && !empty($_POST['action'])) {
            $configEditor->setTaskAction($_POST['action']);
            $configUpdated = $configEditor->doAction();
        } else {
            if ($configEditor->validate()) {
                $configUpdated = $configEditor->updateConfig();
            } else {
                $editorErrors = $configEditor->getErrors();
                $validatedPost = $configEditor->getValidatedPost();
                $configCombiner->setEditorValidationErrors($editorErrors);
                $configCombiner->setValidatedPost($validatedPost);
            }
        }
    }
}


if ($configCombiner->validate()) {
    $data = $configCombiner->buildWebData();
    $data['configName'] = $workConfigName;
    $data['page'] = 'configForm';
} else {
    $data['page'] = 'error';
}
$data['renderErrors'] = $configCombiner->getErrors();

if (isset($_POST) && count($_POST) && !is_null($workConfigName)) {
    $data['configUpdated'] = $configUpdated;
}


// Rendering Config Form
$renderer = ConfigFormRenderer::getInstance();
$renderer->setViewData($data);
$renderer->render();







