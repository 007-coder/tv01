<?php 
namespace Sam\Installation\Config;

if (!defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }

class ConfigCombiner extends \CustomizableClass
{ 

  protected $configName = '';  
  protected $validConfigNames = ['core','core2'];  
  protected $validateErrorCodes = [];
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
   *
   * @return void
   * @author 
   **/  
  public function setConfigName($configName)
  {    
    $configName = (is_string($configName) && in_array($configName, $this->validConfigNames)) ? $configName : 'core';

    $this->configName = $configName;     
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
    $webData['formData'] = (count($this->localConfig)) ? array_replace_recursive($this->globalConfig, $this->localConfig) : $this->globalConfig;
    foreach ($webData['formData'] as $kc => $confVal) {
      $webData['formNav'][$kc] = [
        'name' => isset($formNavNames[$kc]) ? $formNavNames[$kc] : $kc,
        'urlHash' => '#' . $kc
      ];
    }

    $webData['formData'] = readyFormData($webData['formData'], $this->configMeta);

    $localConfigOneDim = count($this->localConfig) ?  MultiDimToOneDimArray('|',$this->localConfig) : [];
    $globalConfigOneDim = MultiDimToOneDimArray('|',$this->globalConfig);

    $_tmpFormData = [];
    foreach ($webData['formData']['form'] as $configArea => $configAreaData) {      
      foreach ($configAreaData as $attrName => $inputData) {
        $_tmpFormData[$configArea][$attrName] = $inputData;
        $_tmpFormData[$configArea][$attrName]['fromLocalConfig'] = 
        (isset($localConfigOneDim[$configArea.'|'.$attrName])) ? true : false;  
        if ($_tmpFormData[$configArea][$attrName]['fromLocalConfig']) {
          $_tmpFormData[$configArea][$attrName]['defaultValue'] = 
          (isset($globalConfigOneDim[$configArea.'|'.$attrName])) ? $globalConfigOneDim[$configArea.'|'.$attrName] : '';
        } 

        $_tmpFormData[$configArea][$attrName]['validation'] = [
          'error'=> false,
          'errorText' => ''
        ];

      }
    }
    $webData['formData']['form'] = $_tmpFormData;
    $_tmpFormData = [];
    

    return $webData;

  }

  /**
   * return meta config array for current config name ($this->configName)
   *
   * @return array()
   * @author 
   **/
  protected function getConfigMeta()
  { 
    $pathMeta = self::PATH_CONFIG . DS . 'configMeta.php';

    if (file_exists($pathMeta)) {
      $MetaConfig = require_once($pathMeta);

      if (is_array($MetaConfig)) {
        if (count($MetaConfig)) {
          // Meta config is ok.
          $this->configMeta = $MetaConfig;

        } else {
          $this->validateErrorCodes['unvalidMetaConfig'] = 'Meta config file empty!';
        }
      } else {
        $this->validateErrorCodes['unvalidMetaConfig'] = 'Meta config file not an array()! Please provide array() for Meta config.';  
      }

    } else {
      $this->validateErrorCodes['noMetaConfig'] = 'Meta config file "'.$this->configName.'.php"  not found in "'.$pathMeta.'" ';
    }
  }

  /**
   * return global config array for current config name ($this->configName)
   *
   * @return array()
   * @author 
   **/
  protected function getGlobalConfig()
  { 
    $pathGlobalConfig = self::PATH_CONFIG . DS . $this->configName.'.php';
    if (file_exists($pathGlobalConfig)) {
      $globalConfig = require_once($pathGlobalConfig);

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
      $this->validateErrorCodes['noGlobalConfig'] = 'Global config file "'.$this->configName.'.php"  not found in "'.$pathGlobalConfig.'" ';
    }
  }

  /**
   * return local config array for current config name ($this->configName)
   *
   * @return array()
   * @author 
   **/
  protected function getLocalConfig()
  { 
    $pathLocalConfig = self::PATH_CONFIG . DS . $this->configName.'.local.php';
    if (file_exists($pathLocalConfig)) {
      $localConfig = require_once($pathLocalConfig);

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
   * @return string
   * @author 
   **/
  public function getErrors()
  {
    return $this->validateErrorCodes;
  }





}