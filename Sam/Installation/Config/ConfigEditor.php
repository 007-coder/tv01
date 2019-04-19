<?php

namespace Sam\Installation\Config;

/**
 * Class ConfigEditor
 * @package Sam\Installation\Config
 * @author Vakulenko Yura
 */
class ConfigEditor extends \CustomizableClass
{

    const PATH_CONFIG = BASE_DIR . DS . '_configuration';

    /**
     * Current config name
     * @var string
     */
    protected $configName = '';

    /**
     * Global config array from global config file
     * for current config name (e.g core.php)
     * @var array
     */
    protected $globalConfig = [];

    /**
     * Local config array from local config file
     * for current config name (e.g. core.local.php)
     * @var array
     */
    protected $localConfig = [];

    /**
     * Array with meta data for current config name with ['datatype'] and ['validate'] keys
     * @var array
     */
    protected $configMetaFull = [];

    /**
     * Contains array with list of valid config names
     * @var array
     */
    protected $validConfigNames = ['core', 'core2'];

    /**
     * Contains data from POST request
     * @var array
     */
    protected $postData = [];

    /**
     * Contains data ready for update local config from POST request
     * @var array
     */
    protected $updateLocalConfigData = [];

    /**
     * Contains array with POST data validation errors
     * @var array
     */
    protected $validationErrors = [];

    /**
     * Contains array with valid POST data, received after POST
     * validation in $this->validate()
     * @var array
     */
    protected $validatedPost = [];

    /**
     * Filtered and ready for publish POST, used in $this->updateConfig()
     * @var array
     */
    protected $readyForPublishPost = [];

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
     * Setting up config name, config meta and loading global config
     * @param string: $configName
     * @return void
     */
    public function setConfigName($configName)
    {
        $configName = (
            is_string($configName)
            && in_array($configName, $this->validConfigNames)
        ) ? $configName : 'core';

        $this->configName = $configName;

        $fileGlobalConfig = self::PATH_CONFIG . DS . $this->configName . '.php';
        if (file_exists($fileGlobalConfig)) {
            $this->globalConfig = require $fileGlobalConfig;
        }

        $fileConfigMeta = self::PATH_CONFIG . DS . $this->configName . '.meta.php';
        if (file_exists($fileConfigMeta)) {
            $configMeta = require $fileConfigMeta;
            $this->configMetaFull = buildConfigMetaFull($this->globalConfig, $configMeta, true);
        }
    }

    /**
     * Return valid config names array
     * @return array
     */
    public function getValidConfigNames()
    {
        return $this->validConfigNames;
    }

    /**
     * Set valid config names for $this->validConfigNames
     * @param $namesArr
     * @return void
     */
    public function setValidConfigNames(array $namesArr)
    {
        if (is_array($namesArr)) {
            if (count($namesArr)) {
                foreach ($namesArr as $item) {
                    $this->validConfigNames[] = (is_string($item)) ? $item : '';
                }
            }
        } else {
            throw new RuntimeException("Method argument must be an array!");
        }
    }

    /**
     * Setting up data from POST request.
     * @param $postData
     * @return void
     *
     */
    public function setPostData($postData)
    {
        $this->postData = (is_array($postData) && count($postData)) ? $postData
            : [];
    }

    /**
     * Validate data from $this->postData
     * @return boolean
     *
     */
    public function validate()
    {
        if (isset($this->postData['configName'])) {
            unset($this->postData['configName']);
            $this->setPostData($this->postData);
        }

        $isValid = false;

        $validator = new ConfigValidator;
        $delimiter = '.';

        $oneDimPost = laravelHelpersArrDot($this->postData, $delimiter);
        $oneDimMeta = MultiDimToOneDimArray($delimiter, $this->configMetaFull);

        $validatedKeys = [];
        $countErrors = 0;
        foreach ($oneDimPost as $configKey => $value) {
            $dataType = $oneDimMeta[$configKey]['dataType'];
            $validations = $oneDimMeta[$configKey]['validate'];

            //  ------ Prepare additional validation rules start --------
            $validationRulesForAdd =
                isset($validations['validationRules'])
                    ? $validations['validationRules']
                    : '';
            if (!empty($validationRulesForAdd)) {
                $tmpValidations = [];
                foreach (explode('|', $validationRulesForAdd) as $methodName) {
                    if (strpos($methodName, ':') !== false) {
                        $explRule = explode(':', $methodName);
                        $arguments = [];
                        if (!empty($explRule[1])) {
                            foreach (explode(',', $explRule[1]) as $args) {
                                $explArgs = explode('=', $args);
                                $arguments[$explArgs[0]] = $explArgs[1];
                            }
                        }
                        $tmpValidations[$explRule[0]] = $arguments;
                    } else {
                        $tmpValidations[$methodName] = [];
                    }
                }

                $validationRulesForAdd = $tmpValidations;
            }
            //  ------ Prepare additional validation rules end --------

            $errorMessages = [];
            $isCurrValidDataType = true;
            $isCurrValidAllAddValidators = true;

            // ------- Data type validation start ---------
            switch ($dataType) {
                case ConfigValidator::T_BOOL:
                    if (!$validator->isBoolean($value)) {
                        $isCurrValidDataType = false;
                    }
                    break;

                case ConfigValidator::T_INTEGER:
                    if (!$validator->isInteger($value)) {
                        $isCurrValidDataType = false;
                    }
                    break;

                case ConfigValidator::T_DOUBLE:
                    if (!$validator->isDouble($value)) {
                        $isCurrValidDataType = false;
                    }
                    break;

                case ConfigValidator::T_STRING:
                    if (!$validator->isString($value)) {
                        $isCurrValidDataType = false;
                    }
                    break;

                case ConfigValidator::T_NULL:
                    if (!$validator->isNULL($value)) {
                        $isCurrValidDataType = false;
                    }
                    break;
            }

            if ($isCurrValidDataType === false) {
                $errorMessages[] = $validator->getErrorMessage($dataType);
            }
            // ------- Data type validation end ---------

            // ------- Additional validations start ---------
            if (!empty($validationRulesForAdd) && count($validationRulesForAdd)) {
                $validCurrAddRules = [];

                foreach ($validationRulesForAdd as $methodName => $arguments) {
                    if (method_exists($validator, $methodName)) {
                        if (is_array($arguments) && count($arguments)) {
                            $validationPassed = $validator->$methodName($arguments);
                        } else {
                            $validationPassed = $validator->$methodName();
                        }
                        if ($validationPassed) {
                            $validCurrAddRules[] = $methodName;
                        } else {
                            $errorMessages[] = $validator->getErrorMessage($methodName, 'custom');
                        }
                    } else {
                        $errorMessages[] = 'Validation method "<u>' . $methodName . '</u>" does not exists!';
                    }
                }

                if (count($validCurrAddRules) != count($validationRulesForAdd)) {
                    $isCurrValidAllAddValidators = false;
                } else {
                    $isCurrValidAllAddValidators = true;
                }
            }
            // ------- Additional validations end ---------

            // if validation for dataType and for additional validations
            // has been passed
            $isCurrValid = ($isCurrValidDataType && $isCurrValidAllAddValidators)
                ? true : false;

            // if get validation error
            if ($isCurrValid === false) {
                $countErrors++;
                $errorValue = [
                    'inputId' => $configKey,
                    'messages' => $errorMessages,
                    'dimensionStop' => true,
                ];
                $currError = [];
                laravelHelpersArrSet(
                    $currError, $configKey, $errorValue, $delimiter
                );

                $this->validationErrors = array_merge_recursive(
                    $this->validationErrors,
                    $currError
                );
            } // if validation passed
            else {
                $validatedKeys[$configKey] = $value;
                $validValue = [
                    'value' => $value,
                    'dimensionStop' => true,
                ];
                $currValid = [];
                laravelHelpersArrSet(
                    $currValid, $configKey, $validValue, $delimiter
                );
                $this->validatedPost = array_merge_recursive(
                    $this->validatedPost,
                    $currValid
                );
            }
        }

        if (count($this->validationErrors)) {
            $this->validationErrors['countErrors'] = $countErrors;
        }

        if (count($validatedKeys) == count($oneDimPost)) {
            $isValid = true;

            // ------- setting up $this->readyForPublishPost values -------
            foreach ($validatedKeys as $configArea => $value) {
                $valueDataType = $oneDimMeta[$configArea]['dataType'];
                $value = $this->leadToTypeAndFilter($value, $valueDataType);

                $this->readyForPublishPost[$configArea] = $value;
            }
            // --------------------
        }

        //wrap_pre($oneDimPost, '$oneDimPost in ' . __METHOD__ . ' L: ' . __LINE__);
        //wrap_pre($validatedKeys, '$validatedKeys in ' . __METHOD__ . ' L: ' . __LINE__);
        //wrap_pre($this->validationErrors, '$this->validationErrors');

        return $isValid;
    }

    /**
     * @param $value
     * @param $type
     * @return bool|float|int|null
     */
    protected function leadToTypeAndFilter($value, $type)
    {
        $validTypes = [
            'boolean', 'integer', 'double',
            'string', 'array', 'object', 'NULL',
        ];

        if (in_array($type, $validTypes)) {
            switch ($type) {
                case 'NULL':
                    if (empty($value)) {
                        return null;
                    }
                case 'integer':
                    return (int)$value;

                case 'double':
                    return (float)$value;

                case 'boolean':
                    $value = (in_array(
                        $value,
                        ['1', 'true', true, 1, 'yes', 'on'],
                        true)
                    ) ? true : false;

                    return $value;
            }
        }
    }


    /**
     * Update content of local config file for current config name.
     * $this->configName.'local.php'
     * @return boolean
     */
    public function updateConfig()
    {
        $updated = false;
        $delimiter = '.';

        if (count($this->readyForPublishPost)) {
            $oneDimGlobalConfig = laravelHelpersArrDot($this->globalConfig);

            $readyForPublish = $excludeFromPublish = [];
            foreach ($this->readyForPublishPost as $configKey => $value) {
                // if POST and Global config values are different
                if (
                    isset($oneDimGlobalConfig[$configKey])
                    && gettype($oneDimGlobalConfig[$configKey]) == gettype($value)
                    && $oneDimGlobalConfig[$configKey] != $value
                ) {
                    // putting them to $readyForPublish array
                    $readyForPublish[$configKey] = $value;
                } // if POST and Global config values are same
                elseif (
                    isset($oneDimGlobalConfig[$configKey])
                    && gettype($oneDimGlobalConfig[$configKey]) == gettype($value)
                    && $oneDimGlobalConfig[$configKey] == $value
                ) {
                    // putting them to $excludeFromPublish array
                    $excludeFromPublish[$configKey] = $value;
                }
            }

            $publish = [];
            if (count($readyForPublish)) {

                // build multidimensional $publish array
                foreach ($readyForPublish as $configKey => $value) {
                    $currPublish = [];
                    laravelHelpersArrSet(
                        $currPublish, $configKey, $value, $delimiter
                    );
                    $publish = array_merge_recursive($publish, $currPublish);
                }

                // --- publish ready POST to local config file
                if (
                    file_put_contents(
                        self::PATH_CONFIG . DS . $this->configName . '.local.php',
                        '<?php return ' . var_export($publish, true) . ';'
                    ) !== false) {

                    $updated = true;
                }
                // ---------------

            } else {
                $updated = true;
            }
            /*wrap_pre(laravelHelpersArrDot($publish), 'laravelHelpersArrDot($publish) in ' . __FUNCTION__);
            wrap_pre($excludeFromPublish, '$excludeFromPublish in ' . __FUNCTION__);
            wrap_pre($this->readyForPublishPost, '$this->readyForPublishPost in ' . __FUNCTION__);*/
        }

        return $updated;
    }

    /**
     * returns POST data validation errors, which will be
     * generated by $this->validate() method and will be inserted to
     * ConfigCombiner::setEditorValidationErrors()
     * @return array
     *
     */
    public function getErrors()
    {
        return $this->validationErrors;
    }

    /**
     * @return array
     */
    public function getValidatedPost()
    {
        return $this->validatedPost;
    }

}