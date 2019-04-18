<?php
define('DS', DIRECTORY_SEPARATOR);

define('BASE_URL',
    $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']
    . $_SERVER['REQUEST_URI']
);
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
} else if (isset($_GET['name']) && is_string($_GET['name'])) {
    $configName = $_GET['name'];
} else {
    $configName = 'core';
}


$configCombiner = ConfigCombiner::getInstance();
$configCombiner->setConfigName($configName);
$configCombiner->setValidConfigNames($validConfigNames);

// processing POST
if (isset($_POST) && count($_POST)) {
    $configEditor = ConfigEditor::getInstance();
    $configEditor->setConfigName($configCombiner->getConfigName());
    $configEditor->setValidConfigNames($configCombiner->getValidConfigNames());
    $configEditor->setPostData($_POST);

    if ($configEditor->validate()) {
        $configUpdated = $configEditor->updateConfig();
    } else {
        $editorErrors = $configEditor->getErrors();
        $validatedPost = $configEditor->getValidatedPost();
        $configCombiner->setEditorValidationErrors($editorErrors);
        $configCombiner->setValidatedPost($validatedPost);
    }
}

if ($configCombiner->validate()) {
    $data = $configCombiner->buildWebData();
    $data['configName'] = $configCombiner->getConfigName();
    $data['page'] = 'configForm';
} else {
    $data['page'] = 'error';
}
$data['renderErrors'] = $configCombiner->getErrors();

if (isset($_POST) && count($_POST)) {
    $data['configUpdated'] = $configUpdated;
}


// Rendering Config Form
$renderer = ConfigFormRenderer::getInstance();
$renderer->setViewData($data);
$renderer->render();







