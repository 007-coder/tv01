<?php

namespace Sam\Installation\Config;


if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

/**
 * Class ConfigCombiner
 * @package Sam\Installation\Config
 * @author Vakulenko Yura
 */
class ConfigCombiner extends \CustomizableClass
{

    /**
     * Current config name
     * @var string
     */
    protected $configName = '';

    /**
     * Array with List of all valid config names
     * @var array
     */
    protected $validConfigNames = ['core', 'core2'];

    /**
     * @var array
     */
    protected $validateErrorCodes = [];

    /**
     * ConfigEditor::validationErrors generated during validation POST data
     * @var array
     */
    protected $editorValidationErrors = [];

    /**
     * validated POST values if ConfigEditor::validationErrors not empty.
     * To prevent the client from re-entering such values
     * @var array
     */
    protected $validatedPost = [];

    /**
     * @var array
     */
    protected $configMeta = [];

    /**
     * @var array
     */
    protected $globalConfig = [];

    /**
     * @var array
     */
    protected $localConfig = [];

    const PATH_CONFIG = BASE_DIR . DS . '_configuration';

    /**
     * Class instantiation method
     * @return $this
     */
    public static function getInstance()
    {
        $instance = parent::_getInstance(__CLASS__);
        return $instance;
    }

    /**
     * Setting up current config name
     * @param string: $configName
     * @return void
     * @author
     **/
    public function setConfigName($configName)
    {
        $configName = (
            is_string($configName)
            && in_array($configName, $this->validConfigNames)
        ) ? $configName : 'core';

        $this->configName = $configName;
    }


    /**
     * @return string
     */
    public function getConfigName()
    {
        return $this->configName;
    }

    /**
     * Setting valid config names
     * @param array $validNames
     */
    public function setValidConfigNames(array $validNames)
    {
        $this->validConfigNames = empty($validNames) ? $validNames : [];
    }


    /**
     * Get valid config names
     * @return array
     */
    public function getValidConfigNames()
    {
        return $this->validConfigNames;
    }


    /**
     * store ConfigEditor::validationErrors for using in this->buildWebData()
     * for building input validation error messages
     * @param array $data
     */
    public function setEditorValidationErrors($data = [])
    {
        if (is_array($data) && count($data)) {
            $this->editorValidationErrors = $data;
        }
    }

    /**
     * @param array $data
     */
    public function setValidatedPost($data = [])
    {
       if (is_array($data) && count($data)) {
            $this->validatedPost = $data;
        }
    }

    /**
     * check for core.php file existence or read access
     * check that core.php data is correct php array
     * check that core.local.php data is correct php array, if exists
     *
     * @return bool true or false
     * @author
     **/
    public function validate()
    {
        $this->getGlobalConfig();
        $this->getLocalConfig();
        $this->getConfigMeta();

        return (count($this->globalConfig)) ? true : false;
    }

    /**
     * build web ready form config data array for
     * ConfigFormRendered:render();
     *
     * @return array()
     * @author
     **/
    public function buildWebData()
    {
        $delimiter = '.';
        $formNavNames = [];
        $webData = [];

        $webData['formData'] =
            (count($this->localConfig))
                ? array_replace_recursive($this->globalConfig, $this->localConfig)
                : $this->globalConfig;
        foreach ($webData['formData'] as $kc => $confVal) {
            $webData['formNav'][$kc] = [
                'name' => isset($formNavNames[$kc]) ? $formNavNames[$kc] : $kc,
                'urlHash' => '#' . $kc,
            ];
        }

        $webData['formData'] = readyFormData($webData['formData'],
            $this->configMeta, $delimiter);

        $localConfigOneDim =
            count($this->localConfig)
                ? MultiDimToOneDimArray($delimiter, $this->localConfig)
                : [];
        $globalConfigOneDim = MultiDimToOneDimArray($delimiter, $this->globalConfig);
        $configMetaOneDim = MultiDimToOneDimArray($delimiter, $this->configMeta);

        $formValidationErrors =
        $formValidatedPost = $webData['formData']['validationErrors'] = [];

        // $formValidationErrors
        if (count($this->editorValidationErrors)) {
            $formValidationErrors =
                readyFormValidationErrors (
                    $this->editorValidationErrors,
                    $delimiter
                );
            foreach ($formValidationErrors as $areaError => $errorData){
                foreach ($errorData as $subAreaError => $subErrorData) {
                   $webData['formData']['validationErrors'][] = [
                       'title' => $areaError.'->'
                           .str_replace($delimiter, "->", $subAreaError),
                       'urlHash' => '#option-'.$areaError.'-'.$subAreaError
                   ];
                }
            }
        }

        // $formValidatedPost
        if (count($this->validatedPost)) {
            $formValidatedPost =
                readyFormValidatedPost ($this->validatedPost, $delimiter);
            foreach ($formValidatedPost as $areaFVP => $dataFVP){
                foreach ($dataFVP as $subAreaFVP => $valueFVP) {
                   $webData['formData']['validationValid'][] = [
                       'title' => $areaFVP.'->'
                           .str_replace($delimiter, "->", $subAreaFVP),
                       'urlHash' => '#option-'.$areaFVP.'-'.$subAreaFVP
                   ];
                }
            }
        }

        $tmpFormData = [];
        foreach ($webData['formData']['form'] as $configArea => $configAreaData) {

            foreach ($configAreaData as $attrName => $inputData) {
                $tmpFormData[$configArea][$attrName] = $inputData;

                $tmpFormData[$configArea][$attrName]['fromLocalConfig'] =
                    (isset($localConfigOneDim[$configArea . $delimiter . $attrName]))
                        ? true : false;


                // --- setting up data type for input
                if (isset($configMetaOneDim[$configArea . $delimiter .
                    $attrName . $delimiter. 'inputDataType'])) {
                    $dataType = $configMetaOneDim[$configArea . $delimiter .
                    $attrName . $delimiter .'inputDataType'];
                } else {
                    $dataType = gettype($globalConfigOneDim[$configArea . $delimiter . $attrName]);
                }
                $tmpFormData[$configArea][$attrName]['inputDataType'] = $dataType;
                // -----------------------


                //------setting up default value for input
                if ($tmpFormData[$configArea][$attrName]['fromLocalConfig']) {
                    $defaultValue = '';
                    if (isset($globalConfigOneDim[$configArea . $delimiter . $attrName])) {
                        switch ($dataType) {
                            case ConfigValidator::T_BOOL:
                                $defaultValue =
                                    ($globalConfigOneDim[$configArea . $delimiter . $attrName])
                                        ? 'true' : 'false';
                                break;
                            case ConfigValidator::T_NULL:
                                $defaultValue =
                                    (is_null($globalConfigOneDim[$configArea . $delimiter . $attrName]))
                                        ? 'NULL'
                                        : $globalConfigOneDim[$configArea . $delimiter . $attrName];
                                break;
                            case ConfigValidator::T_INTEGER:
                                $defaultValue =
                                    $globalConfigOneDim[$configArea . $delimiter . $attrName];
                                break;
                            case ConfigValidator::T_STRING:
                                $defaultValue =
                                    $globalConfigOneDim[$configArea . $delimiter . $attrName];
                                break;
                        }
                    }

                    $tmpFormData[$configArea][$attrName]['defaultValue'] = $defaultValue;
                }
                // --------------------

                // --- Setting up validation check array for Input
                $tmpFormData[$configArea][$attrName]['validation'] = [
                    'error' =>
                        (isset ($formValidationErrors[$configArea][$attrName]))
                        ? true : false,
                    'errorText' =>
                        (isset ($formValidationErrors[$configArea][$attrName]))
                        ? $formValidationErrors[$configArea][$attrName]['messages']
                        : '',
                    'post' => [
                        'isValid' =>
                            isset($formValidatedPost[$configArea][$attrName])
                            ? true : null,
                        'value' =>
                            (isset($formValidatedPost[$configArea][$attrName]))
                            ? $formValidatedPost[$configArea][$attrName]['value']
                            : null
                    ],
                ];
                // -----------------------

            }
        }
        
        $webData['formData']['form'] = $tmpFormData;

        return $webData;
    }

    /**
     * return meta config array for current config name ($this->configName)
     *
     * @return void
     * @author
     **/
    protected function getConfigMeta()
    {
        $fileConfigMeta = self::PATH_CONFIG . DS . $this->configName . '.meta.php';

        if (file_exists($fileConfigMeta)) {
            $configMeta = require($fileConfigMeta);

            if (is_array($configMeta)) {
                if (count($configMeta)) {
                    // Meta config is ok.
                    $this->configMeta = $configMeta;
                } else {
                    $this->validateErrorCodes['unvalidMetaConfig'] =
                        'Meta config file empty!';
                }
            } else {
                $this->validateErrorCodes['unvalidMetaConfig'] =
                    'Meta config file not an array()! Please provide array() for Meta config.';
            }
        } else {
            $this->validateErrorCodes['noMetaConfig'] =
                'Meta config file "' . $this->configName . '.php"  not found in "' . $fileConfigMeta . '" ';
        }
    }

    /**
     * return global config array for current config name ($this->configName)
     *
     * @return void
     * @author
     **/
    protected function getGlobalConfig()
    {
        $fileGlobalConfig = self::PATH_CONFIG . DS . $this->configName . '.php';

        if (file_exists($fileGlobalConfig)) {
            $globalConfig = require($fileGlobalConfig);

            if (is_array($globalConfig)) {
                if (count($globalConfig)) {
                    // Global config is ok.
                    $this->globalConfig = $globalConfig;
                } else {
                    $this->validateErrorCodes['unvalidGlobalConfig'] =
                        'Global config file empty!';
                }
            } else {
                $this->validateErrorCodes['unvalidGlobalConfig'] =
                    'Global config file not an array()! Please provide array() for Global config.';
            }
        } else {
            $this->validateErrorCodes['noGlobalConfig'] =
                'Global config file "' . $this->configName . '.php"  not found in "' . $fileGlobalConfig . '" ';
        }
    }

    /**
     * return local config array for current config name ($this->configName)
     *
     * @return void
     * @author
     **/
    protected function getLocalConfig()
    {
        $pathLocalConfig = self::PATH_CONFIG . DS . $this->configName . '.local.php';
        if (file_exists($pathLocalConfig)) {
            $localConfig = require($pathLocalConfig);

            if (is_array($localConfig)) {
                if (count($localConfig)) {
                    // Local config is ok.
                    $this->localConfig = $localConfig;
                } else {
                    $this->validateErrorCodes['unvalidLocalConfig'] =
                        'Local config file empty!';
                }
            } else {
                $this->validateErrorCodes['unvalidLocalConfig'] =
                    'Local config file not an array()! Please provide array() for Local config.';
            }
        } else {
            $this->validateErrorCodes['noLocalConfig'] =
                'Local config file "' . $this->configName . 'local.php"  not found in "' . $pathLocalConfig . '" ';
        }
    }

    /**
     * return Error codes array if $this->validate() fails.
     * possible error types: noGlobalConfig | noLocalConfig | noConfigMeta
     * | unvalidGlobalConfig |  unvalidLocalConfig | unvalidMeta
     * @return array
     * @author
     **/
    public function getErrors()
    {
        return $this->validateErrorCodes;
    }

}