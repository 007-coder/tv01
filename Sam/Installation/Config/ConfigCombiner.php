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

    const PATH_CONFIG = BASE_DIR . DS . '_configuration';

    /**
     * Current config name
     * @var string
     */
    protected $configName = '';

    /**
     * Array with List of all valid config names
     * @var array
     */
    protected $validConfigNames = [];

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
     * Setting valid config names
     * @param array $validNames
     */
    public function setValidConfigNames(array $validNames)
    {
        $this->validConfigNames = empty($validNames) ? [] : $validNames;
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
        ) ? $configName : null;

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

        if (empty($this->validateErrorCodes)) {
            $this->getLocalConfig();
            $this->getConfigMeta();
            return true;
        } else {
            return false;
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
        if (!empty($this->configName)) {
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
        } else {
            $validConfigNames = implode(', ', $this->validConfigNames);
            $this->validateErrorCodes['isNullGlobalConfig'] =
                'Global config file not defined, because it not exist in valid 
                config names <b>' . $validConfigNames . '</b>';
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
        $pathLocalConfigDefaults = self::PATH_CONFIG . DS . $this->configName . '.local.defaults.php';

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
                'Local config file "' . $this->configName . '.local.php"  not found in "' . $pathLocalConfig . '" ';

            if (file_exists($pathLocalConfigDefaults)) {
                $localConfigDefaults = require($pathLocalConfigDefaults);
                if (is_array($localConfigDefaults)) {
                    if (count($localConfigDefaults)) {
                        // Local config defaults is ok.
                        $this->localConfig = $localConfigDefaults;
                    } else {
                        $this->validateErrorCodes['unvalidLocalConfigDefaults'] =
                            'Local config defaults file empty!';
                    }
                } else {
                    $this->validateErrorCodes['unvalidLocalConfig'] =
                        'Local config defaults file not an array()! Please provide array() for it.';
                }

                $this->validateErrorCodes['UseDefaultsConfig'] =
                'For Local config will be used defaults config values from 
                file "' . $this->configName . '.local.defaults.php"  in "'
                . $pathLocalConfigDefaults . '" ';

            } else {
                $this->validateErrorCodes['noLocalConfigDefaults'] =
                    'Defaults Local config file "' . $this->configName .
                    '.local.defaults.php"  not found in "' . $pathLocalConfigDefaults
                    . '" ';
            }
        }
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
                readyFormValidationErrors(
                    $this->editorValidationErrors,
                    $delimiter
                );
            foreach ($formValidationErrors as $areaError => $errorData) {
                foreach ($errorData as $subAreaError => $subErrorData) {
                    $webData['formData']['validationErrors'][] = [
                        'title' => $areaError . '->'
                            . str_replace($delimiter, "->", $subAreaError),
                        'urlHash' => '#option-' . $areaError . '-' . $subAreaError,
                    ];
                }
            }
        }

        // $formValidatedPost
        if (count($this->validatedPost)) {
            $formValidatedPost =
                readyFormValidatedPost($this->validatedPost, $delimiter);
            foreach ($formValidatedPost as $areaFVP => $dataFVP) {
                foreach ($dataFVP as $subAreaFVP => $valueFVP) {
                    $webData['formData']['validationValid'][] = [
                        'title' => $areaFVP . '->'
                            . str_replace($delimiter, "->", $subAreaFVP),
                        'urlHash' => '#option-' . $areaFVP . '-' . $subAreaFVP,
                    ];
                }
            }
        }

        $tmpFormData = $webData['formData']['localConfigSettings'] = [];
        foreach ($webData['formData']['form'] as $configArea => $configAreaData) {
            foreach ($configAreaData as $attrName => $inputData) {

                $tmpFormData[$configArea][$attrName] = $inputData;
                $tmpFormData[$configArea][$attrName]['fromLocalConfig'] =
                    (isset($localConfigOneDim[$configArea . $delimiter . $attrName]))
                        ? true : false;

                // --- setting up data type for input
                if (isset($configMetaOneDim[$configArea . $delimiter .
                    $attrName . $delimiter . 'inputDataType'])) {
                    $dataType = $configMetaOneDim[$configArea . $delimiter .
                    $attrName . $delimiter . 'inputDataType'];
                } else {
                    $dataType = gettype($globalConfigOneDim[$configArea . $delimiter . $attrName]);
                }
                $tmpFormData[$configArea][$attrName]['inputDataType'] = $dataType;
                // -----------------------

                $tmpFormData[$configArea][$attrName]['description'] =
                    (isset($configMetaOneDim[$configArea . $delimiter .
                    $attrName . $delimiter . 'description']))
                        ? $configMetaOneDim[$configArea . $delimiter .
                    $attrName . $delimiter . 'description']
                        : '';

                if ($tmpFormData[$configArea][$attrName]['fromLocalConfig']) {
                    // setting up list of all local config values for Config form view
                    $webData['formData']['localConfigSettings'][] = [
                        'title' => $configArea . '->'
                            . str_replace($delimiter, "->", $attrName),
                        'urlHash' => '#option-' . $configArea . '-' . $attrName,
                        'data' => [
                            'type' => $dataType,
                            'value' => setInputValue($inputData['val'], $dataType),
                        ],
                        'deleteHTML' => buildInputDeleteHTML($configArea, $attrName)
                    ];
                    // ------------------
                    $inputData['deleteHTML'] = buildInputDeleteHTML($configArea, $attrName, 'big');

                    // setting up default value for input
                    $defaultValue = '';
                    if (isset($globalConfigOneDim[$configArea . $delimiter . $attrName])) {
                        $defaultValue =
                            setInputValue(
                                $globalConfigOneDim[$configArea . $delimiter . $attrName],
                                $dataType
                            );
                    }
                    $tmpFormData[$configArea][$attrName]['defaultValue'] = $defaultValue;
                    // --------------------
                }

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
                                : null,
                    ],
                ];
                // -----------------------

                //wrap_pre($tmpFormData[$configArea][$attrName], '');

            }
        }

        $webData['formData']['form'] = $tmpFormData;

        return $webData;
    }

    /**
     * return Error codes array if $this->validate() fails.
     * possible error types: noGlobalConfig | noLocalConfig | noConfigMeta
     * | unvalidGlobalConfig |  unvalidLocalConfig | unvalidMeta
     * @return array
     **/
    public function getErrors()
    {
        return $this->validateErrorCodes;
    }

}