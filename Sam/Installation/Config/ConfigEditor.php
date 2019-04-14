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
     * Contains array with valid POST data, received during POST
     * validation in $this->validate()
     * @var array
     */
    protected $validatedPost = [];

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
    public function setValidConfigNames($namesArr)
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
        $this->postData = (is_array($postData) && count($postData)) ? $postData : [];
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
            //$validations = $oneDimMeta[$configKey]['validate'];

            $errorMessages = [];
            $isCurrValid = true;

            switch ($dataType) {
                case ConfigValidator::T_BOOL:
                    if ($validator->isBoolean($value)) {

                    } else {
                        $isCurrValid = false;
                        $errorMessages[] = $validator->getErrorMessage($dataType);
                    }
                    break;

                case ConfigValidator::T_INTEGER:
                    if ($validator->isInteger($value)) {

                    } else {
                        $isCurrValid = false;
                        $errorMessages[] = $validator->getErrorMessage($dataType);
                    }
                    break;

                case ConfigValidator::T_DOUBLE:
                    if ($validator->isDouble($value)) {

                    } else {
                        $isCurrValid = false;
                        $errorMessages[] = $validator->getErrorMessage($dataType);
                    }
                    break;

                case ConfigValidator::T_STRING:
                    if ($validator->isString($value)) {

                    } else {
                        $isCurrValid = false;
                       $errorMessages[] = $validator->getErrorMessage($dataType);
                    }
                    break;

                case ConfigValidator::T_NULL:
                    if ($validator->isNULL($value)) {

                    } else {
                        $isCurrValid = false;
                        $errorMessages[] = $validator->getErrorMessage($dataType);
                    }
                    break;
            }

            // if get validation error
            if ($isCurrValid === false) {
                $countErrors++;
                $errorValue = [
                    'inputId' => $configKey,
                    'messages' => $errorMessages,
                    'dimensionStop' => true
                ];
                $currError = [];
                laravelHelpersArrSet(
                    $currError, $configKey, $errorValue, $delimiter
                );

                $this->validationErrors = array_merge_recursive(
                    $this->validationErrors,
                    $currError
                );
            } else {
                $validatedKeys[] = $configKey;
                $validValue = [
                    'value' => $value,
                    'dimensionStop' => true
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
        }

        wrap_pre($oneDimPost, '$oneDimPost in '.__METHOD__.' L: '.__LINE__);
        wrap_pre($validatedKeys, '$validatedKeys in '.__METHOD__.' L: '.__LINE__);
        //wrap_pre($this->validationErrors, '$this->validationErrors');
        //wrap_pre($this->validatedPost, '$this->validatedPost');

        return $isValid;
    }

    /**
     * @param string $key
     * @param string array $value
     * @param array $validationRules
     * @param array $dataType
     * @return boolean
     */
    /*protected function validatePostValue($key, $postValue, $validationRules = [], $dataType = [], $oSubPath = '')
    {
        $validator = new ConfigValidator;
        $isValid = true;

        $strDelim = '->';

        foreach ($postValue as $pKey => $item) {
            $subPath = $oSubPath;
            if (is_array($item)) {
                $subPath .= $pKey . $strDelim;
                $this->validatePostValue(
                    $key,
                    $item,
                    $validationRules[$pKey],
                    $dataType[$pKey],
                    $subPath
                );
            } else if (!is_object($item) || !is_resource($item)) {
                $inputId = $key . $strDelim . $subPath . $pKey;
                echo '<b>' . $inputId . ' = ' . $item . '</b><br>';

                $errorValue = [
                    'inputId' => $inputId,
                    'message' => 'dfdsfdsf',
                ];
                $currError = buildValidationError($inputId, $strDelim, $errorValue);

                $this->validationErrors = array_merge_recursive(
                    $this->validationErrors,
                    $currError
                );

                switch ($dataType[$pKey]) {
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
                }
                if ($isValid === false) {


                    $this->validationErrors = array_merge(
                        $this->validationErrors,
                        $currentError
                    )
                }
            }
        }
    }*/

    /**
     * Lead $data to needed data type
     * @param string $data
     * @param string $toType
     * @return string
     */
    /*protected function leadToType($data = '', $toType = '')
    {
        $leadedData = '';

        return $leadedData;
    }*/



    /**
     * Update content of local config file for current config name.
     * $this->configName.'local.php'
     * @return boolean
     */
    public function updateConfig()
    {
        return false;

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