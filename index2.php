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

/*class Foo {

    private $sourceArray = [
        'key0' => 'value0',
        'key1' => [
            'key11' => [
                'key111' => 123,
                'key112' => 'value112',
            ],
            'key12' => [
                'key121' => 'value121',
                'key122' => 'value122',
            ]
        ],
        'key2' => [
            'key21' => 'value21',
        ],
        'key3' => 'value3',
    ];

    private $resultArray;

    public function __construct()
    {
        $this->resultArray = $this->validateArray($this->sourceArray);
    }

    private function validateArray($mixed)
    {
        $resultArray = [];
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $result = $this->validateArray($value);
                if (is_bool($result)) {
                    $resultArray[$key] = [$value, $result];
                } else {
                    $resultArray[$key] = $result;
                }
            }
        } else {
            return $this->validateValue($mixed);
        }

        return $resultArray;
    }

    private function validateValue($value)
    {
        return is_string($value);
    }

    public function getResultArray()
    {
        return $this->resultArray;
    }
}*/

/*$foo = new Foo();
wrap_pre($foo->getResultArray(), '$foo->getResultArray()');*/

$editorErrors = [];
$configUpdated = false;
$configName = '';

// Setting up current config name
if (isset($_POST) && count($_POST)) {
    $configName = (isset($_POST['configName']) && is_string($_POST['configName']))
        ? $_POST['configName'] : 'core';
} else if (isset($_GET['name']) && is_string($_GET['name'])) {
    $configName = $_GET['name'];
} else {
    $configName = 'core';
}

// processing POST
/*if (isset($_POST) && count($_POST)) {
  $configEditor = ConfigEditor::getInstance();
  $configEditor->setConfigName($configName);
  $configEditor->setPostData($_POST);

  if ($configEditor->validate()){
    $configUpdated = $configEditor->updateConfig();
  } else {
      $editorErrors = $configEditor->getErrors();

      wrap_pre($editorErrors, '$editorErrors in '.__FILE__);
  }
}*/

$configCombiner = ConfigCombiner::getInstance();
$configCombiner->setConfigName($configName);

// processing POST
if (isset($_POST) && count($_POST)) {
    $configEditor = ConfigEditor::getInstance();
    $configEditor->setConfigName($configName);
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
    $data['configName'] = $configName;
    $data['page'] = 'configForm';
} else {
    $data['page'] = 'error';
}
$data['renderErrors'] = $configCombiner->getErrors();
if (isset($_POST) && count($_POST)) {
    $data['configUpdated'] = $configUpdated;
}


// Render Config Form
$renderer = ConfigFormRenderer::getInstance();
$renderer->setViewData($data);
$renderer->render();







