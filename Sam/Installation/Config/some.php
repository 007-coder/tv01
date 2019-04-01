<?php
/**
 * Created by PhpStorm.
 * User: Yura
 * Date: 29.03.2019
 * Time: 17:53
 */

class HelpPlease {
    public $validationErrors = [];

  // примеры со входящими данными
  // для $this->postData
  public $postData = array (
      'account' =>  array (
          'page' => 'false',
          'thumbnailSize' => '6',
      ),

      'admin' =>  array (
          'dashboard' => array (
              'closedAuctions' => '10',
          ),
          'inventory' => array (
              'fieldConfig' =>  array (
                  'ItemNumber' => array (
                      'requirable' => 'false',
                  ),
                  'Category' => array (
                      'title' => 'Category89',
                  ),
                  'AuctionType' => array (
                      'title' => 'Auction type, Start/End date/time, timezone',
                  ),
                  'Warranty' => array (
                      'requirable' => 'false',
                  ),
              ),
          ),
      ),
  );

  // Это правила валидации
  // для $this->validationRules
  public $validationRules = array(
      'account' => array (
          'page' => array (
              'required' => true,
              'validationRules' =>  array (
                  0 => 'isInteger',
              ),
          ),
          'thumbnailSize' =>  array (
              'required' => true,
          ),
      ),

      'admin' => array (
          'dashboard' => array (
              'closedAuctions' => array (
                  'validationRules' =>  array (
                      0 => 'isTest',
                  ),
              ),
          ),
          'inventory' => array (
              'fieldConfig' => array (
                  'ItemNumber' =>  array (
                      'requirable' => array (
                          'validationRules' => array (
                              0 => 'isTest454',
                          ),
                      ),
                  )
              ),
          ),
      )
  );

  // для $this->configDataTypes
  // Это тот тип входящих данных которому должно соответвовать конечное значение из $this->postData
  public $configDataTypes = array(
    'account' => array (
      'page' => 'boolean',
      'thumbnailSize' => 'integer',
    ),
    'admin' => array (
      'dashboard' => [
        'closedAuctions' => 'integer'
      ],
      'inventory' => [
        'fieldConfig' => [
          'ItemNumber' => [
            'requirable' => 'boolean'
          ],
          'Category' => [
            'title' => 'string'
          ],
          'AuctionType' => [
            'title' => 'string'
          ],
          'Warranty' => [
            'requirable' => 'boolean'
          ]
        ]
      ]
    ),

  );


  //метод для валидации
  public function validate()
  {
      foreach ($this->postData as $key => $value) {
          $this->validatePostValue(
              $key,
              $value,
              // Массив с правилами для валидации
              $this->validationRules[$key],
              // Массив с типами данных которым должен соответвовать $value
              $this->configDataTypes[$key]
          );
      }
  }


  public function validatePostValue($key, $postValue, $validationRules = [], $dataType = [], $oSubPath = '', $errors = [])
  {
      $validator = new ValidatorClass();

      foreach ($postValue as $pKey => $item) {
          $tmpErrors = $errors;


          $subPath = $oSubPath;
          if (is_array($item)) {
              $subPath .= $pKey.'->';

              $this->validatePostValue(
                  $key,
                  $item,
                  $validationRules[$pKey],
                  $dataType[$pKey],
                  $subPath,
                  $tmpErrors
              );

          } else if (!is_object($item) || !is_resource($item)) {

              $inputId = $key.'->'.$subPath.$pKey;
              echo '<h2>'.$inputId .' = '.$item.'</h2>';

              switch ($dataType[$pKey]) {
                  case "boolean":
                      if ($validator->isBoolean($item)) {

                      } else {
                          $this->validationErrors[$key][$pKey] = [
                              'inputId' => $inputId,
                              'message' => $validator->getErrorMessage('boolean')
                          ];
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
          }
      }


  }



}


/**
 * Class ValidatorClass
 * Validate input POST data
 * @package Sam\Installation\Config
 */
class ValidatorClass
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

    protected $errorMessages = [
        'boolean' => '',
        'integer' => '',
        'double' => '',
        'string' => '',
        'array' => '',
        'object' => '',
        'resource' => '',
        'NULL' => '',
        'unknown type' => ''
    ];

    public function getErrorMessage($dataType) {
        return (isset($this->errorMessages[$dataType]))
            ? $this->errorMessages[$dataType]
            : '';
    }


    public function isBoolean($value) {
        if (is_bool($value)) {
            return true;
        } else if (in_array($value, ['true', 'false'])){
            return true;
        } else {
            return false;
        }
    }


}

