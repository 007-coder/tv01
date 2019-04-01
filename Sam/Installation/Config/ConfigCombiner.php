<?php 
namespace Sam\Installation\Config;

if (!defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }

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
    protected $validConfigNames = ['core','core2'];
  protected $validateErrorCodes = [];

    /**
     * ConfigEditor::validationErrors generated during validation
     * POST data
     * @var array
     */
    protected $editorValidationErrors = [];
  protected $configMeta = [];
  protected $globalConfig = [];  
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
     * store ConfigEditor::validationErrors for using in this->buildWebData()
     * for building input validation error messages
     * @param array $editorValidationErrors
     */
    public function setEditorValidationErrors($editorValidationErrors = [])
  {
      $this->editorValidationErrors = (
          is_array($editorValidationErrors) && count($editorValidationErrors)
      ) ? $editorValidationErrors : [];
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
    $webData = [];
    $formNavNames = [];
    $webData['formData'] =
        (count($this->localConfig))
        ? array_replace_recursive($this->globalConfig, $this->localConfig)
        : $this->globalConfig;
    foreach ($webData['formData'] as $kc => $confVal) {
      $webData['formNav'][$kc] = [
        'name' => isset($formNavNames[$kc]) ? $formNavNames[$kc] : $kc,
        'urlHash' => '#' . $kc
      ];
    }

    $webData['formData'] = readyFormData($webData['formData'], $this->configMeta);

    $localConfigOneDim =
            count($this->localConfig)
            ?  MultiDimToOneDimArray('|',$this->localConfig)
            : [];
    $globalConfigOneDim = MultiDimToOneDimArray('|',$this->globalConfig);
    $configMetaOneDim = MultiDimToOneDimArray('|',$this->configMeta);

    //wrap_pre($configMetaOneDim, '$configMetaOneDim '.__METHOD__);

    $tmpFormData = [];
    foreach ($webData['formData']['form'] as $configArea => $configAreaData) {      
      foreach ($configAreaData as $attrName => $inputData) {
        $tmpFormData[$configArea][$attrName] = $inputData;
        $tmpFormData[$configArea][$attrName]['fromLocalConfig'] = 
        (isset($localConfigOneDim[$configArea.'|'.$attrName])) ? true : false;

        //setting up data type for input
        if (isset($configMetaOneDim[$configArea.'|'.$attrName.'|inputDataType'])) {
            $dataType = $configMetaOneDim[$configArea.'|'.$attrName.'|inputDataType'];
        } else {
            $dataType = gettype($globalConfigOneDim[$configArea.'|'.$attrName]);
        }

        $tmpFormData[$configArea][$attrName]['inputDataType'] = $dataType;

        //setting up default value for input
        if ($tmpFormData[$configArea][$attrName]['fromLocalConfig']) {            
            $defaultValue = '';
            if (isset($globalConfigOneDim[$configArea.'|'.$attrName])) {
                switch ($dataType) {
                    case "boolean":
                        $defaultValue =
                            ($globalConfigOneDim[$configArea.'|'.$attrName])
                            ? 'true' : 'false';
                        break;
                    case "NULL":
                        $defaultValue =
                            (is_null($globalConfigOneDim[$configArea.'|'.$attrName]))
                                ? 'NULL'
                                : $globalConfigOneDim[$configArea.'|'.$attrName];
                        break;
                    case "integer":
                        $defaultValue =
                            $globalConfigOneDim[$configArea.'|'.$attrName];
                        break;
                    case "string":
                        $defaultValue =
                            $globalConfigOneDim[$configArea.'|'.$attrName];
                        break;
                }
            }

            $tmpFormData[$configArea][$attrName]['defaultValue'] = $defaultValue;
        } 

        $tmpFormData[$configArea][$attrName]['validation'] = [
          'error'=> false,
          'errorText' => ''
        ];

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
    $fileConfigMeta = self::PATH_CONFIG . DS . $this->configName.'.meta.php';

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
          'Meta config file "'.$this->configName.'.php"  not found in "'.$fileConfigMeta.'" ';
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
    $fileGlobalConfig = self::PATH_CONFIG . DS . $this->configName.'.php';

    if (file_exists($fileGlobalConfig)) {
      $globalConfig = require($fileGlobalConfig);

      if (is_array($globalConfig)) {
        if (count($globalConfig)) {
          // Global config is ok.
          $this->globalConfig = $globalConfig;

        } else {
          $this->validateErrorCodes['unvalidGlobalConfig'] = 'Global config file empty!';
        }
      } else {
        $this->validateErrorCodes['unvalidGlobalConfig'] = 'Global config file not an array()! Please provide array() for Global config.';  
      }

    } else {
      $this->validateErrorCodes['noGlobalConfig'] = 'Global config file "'.$this->configName.'.php"  not found in "'.$fileGlobalConfig.'" ';
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
    $pathLocalConfig = self::PATH_CONFIG . DS . $this->configName.'.local.php';
    if (file_exists($pathLocalConfig)) {
      $localConfig = require($pathLocalConfig);

      if (is_array($localConfig)) {
        if (count($localConfig)) {
          // Local config is ok.
          $this->localConfig = $localConfig;

        } else {
          $this->validateErrorCodes['unvalidLocalConfig'] = 'Local config file empty!';
        }
      } else {
        $this->validateErrorCodes['unvalidLocalConfig'] = 'Local config file not an array()! Please provide array() for Local config.';  
      }

    } else {
      $this->validateErrorCodes['noLocalConfig'] = 'Local config file "'.$this->configName.'local.php"  not found in "'.$pathLocalConfig.'" ';
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