<?php 
namespace Sam\Installation\Config;

class ConfigEditor extends \CustomizableClass
{

  const T_BOOL = 'boolean';
  const T_INTEGER = 'integer';
  const T_DOUBLE = 'double';
  const T_STRING = 'string';
  const T_ARRAY = 'array';
  const T_OBJECT = 'object';
  const T_RESOURCE = 'resource';
  const T_NULL = 'NULL';
  const T_UNKNOWN_TYPE = 'unknown type';

  /** 
   * Class instantiation method
   * @return $this
   */
  public static function getInstance()
  {
      $instance = parent::_getInstance(__CLASS__);
      return $instance;
  }


  
}