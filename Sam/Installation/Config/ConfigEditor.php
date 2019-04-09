<?php

namespace Sam\Installation\Config;

use Sam\Installation\Config\ValidatorClass;

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
     * Contains array with POST data validation errors
     * @var array
     */
    protected $validationErrors = [];


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
            $this->globalConfig = require($fileGlobalConfig);
        }

        $fileConfigMeta = self::PATH_CONFIG . DS . $this->configName . '.meta.php';
        if (file_exists($fileConfigMeta)) {
            $configMeta = require($fileConfigMeta);
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
    public function setValidConfigNames($namesArr)
    {
        if (is_array($namesArr) && count ($namesArr)) {
            foreach ($namesArr as $item) {
               $this->validConfigNames[] = (is_string($item)) ? $item : '';
            }
        } else {
            throw new Exception("Method argument must be an array!");
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
        $this->postData = (is_array($postData) && count($postData)) ? $postData : [];
    }


    /**
     * Validate data from $this->postData
     * @return boolean
     *
     */
    public function validate()
    {
        $isValid = false;

        unset($this->postData['configName']);
        $this->setPostData($this->postData);
        $oneDimPost = laravelHelpersArrDot($this->postData);
        $oneDimMeta = MultiDimToOneDimArray('.', $this->configMetaFull);

        wrap_pre($oneDimPost, '$oneDimPost ');
        wrap_pre($oneDimMeta , '$oneDimMeta');


        /*$validationRules = getValidationRules($this->postData, $this->configMeta);
        // дописать!!!
        foreach ($this->postData as $key => $value) {
            $this->validatePostValue(
                $key, $value, $validationRules[$key], $this->configDataTypes[$key]
            );

        }
        wrap_pre($this->postData, '$this->postData in ' . __METHOD__);*/


        return $isValid;
    }


    /**
     * @param string $key
     * @param string array $value
     * @param array $validationRules
     * @param array $dataType
     * @return boolean
     */
    protected function validatePostValue($key, $postValue, $validationRules = [], $dataType = [], $oSubPath = '')
    {
        $validator = new ValidatorClass();
        $isValid = true;

        $strDelim = '->';

        foreach ($postValue as $pKey => $item) {
            $subPath = $oSubPath;
            if (is_array($item)) {
                $subPath .= $pKey.$strDelim;
                $this->validatePostValue(
                    $key,
                    $item,
                    $validationRules[$pKey],
                    $dataType[$pKey],
                    $subPath
                );

            } else if (!is_object($item) || !is_resource($item)) {
                $inputId = $key.$strDelim.$subPath.$pKey;
                echo '<b>'.$inputId . ' = ' . $item .'</b><br>';

                $errorValue = [
                    'inputId' => $inputId,
                    'message' => 'dfdsfdsf'
                ];
                $currError = buildValidationError($inputId, $strDelim, $errorValue);

                $this->validationErrors = array_merge_recursive(
                    $this->validationErrors,
                    $currError
                );


                /*switch ($dataType[$pKey]) {
                    case "boolean":
                        if ($validator->isBoolean($item)) {

                        } else {
                            $isValid = false;
                        }

                        break;
                    case "integer":

                        break;
                    case "double":

                        break;
                    case "string":

                        break;
                    case "NULL":

                        break;
                }*/

                /*if ($isValid === false) {


                    $this->validationErrors = array_merge(
                        $this->validationErrors,
                        $currentError
                    );
                }*/


            }
        }

    }


    /**
     * Lead $data to needed data type
     * @param string $data
     * @param string $toType
     * @return string
     */
    protected function leadToType($data = '', $toType = '')
    {
        $leadedData = '';

        return $leadedData;

    }


    /**
     * Update content of local config file for current config name.
     * $this->configName.'local.php'
     * return @void
     */
    public function updateConfig()
    {

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


}