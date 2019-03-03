<?php 
function wrap_pre($data, $title='' ) {
  $countData  = (is_array($data) || is_object($data)) ? ' ('.count($data).') ' : '';
  echo '<pre><h4>'.$title.$countData.' </h4>'.print_r($data, true).'</pre>';
}

function prepareInput ($meta = [], $in, $out = [], $prefix = '') {
    foreach ($in as $key => $value) {
        $currMeta = (isset($meta[$key]) && count($meta[$key])) ? $meta[$key] : [];

        if (is_array($value)) {
            $out = array_merge($out, prepareInput($currMeta, $value, $out, $prefix . $key . '|'));
        }
        else {
            $out["{$prefix}{$key}"] = [
              'val'=> $value,
              'label' => $prefix.$key,
              'meta' => $currMeta              
            ];
        }
    }

    return $out;
}

function MultiDimToOneDimArray($kSepar = '|', $in, $out = [], $prefix = '') {
    foreach ($in as $key => $value) {
        if (is_array($value)) {
            $out = array_merge($out, MultiDimToOneDimArray($kSepar, $value, $out, $prefix . $key . $kSepar));
        }
        else {
            $out["{$prefix}{$key}"] = $value;
        }
    }

    return $out;
}

function filterBool_NULL_Recursive ($in, $out = []) {
    foreach ($in as $key => $value) {
        if (is_array($value)) {           
            $out[$key] = filterBool_NULL_Recursive($value);
        }
        else {
            if (in_array($value, ['true', 'false'])) {
              $out[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);  
            }
            else if ($value == '::null::') {
             $out[$key] = null; 
            } else {
              $out[$key] = $value;
            }            
        }
    }

    return $out;
}




function readyFormData($appConfig, $ConfMeta) { 
  $readyData = [

    'form'=>[],

    // массив в котором хранится инфо
    // сколько для данного раздела настроек скрытых и
    // не доступных для редактирования инпутов
    'statistics'=>[]
  ];

  foreach ($appConfig as $сKey => $сValue) {
    $readyData['statistics'][$сKey] = [
      'visability' => [
        'visible' => 0, 
        'hidden' => 0 
      ],
      'allowForEdit' => [
        'allowed' => 0, 
        'disabled' => 0 
      ]
    ];

    $inputGroupeMeta = (isset($ConfMeta[$сKey]) && count($ConfMeta[$сKey])) ? $ConfMeta[$сKey] : [];
    $prepareGroupeInputs = prepareInput($inputGroupeMeta, $сValue);

    foreach ($prepareGroupeInputs as $propName => $inputData) {               
      if (
        !isset($inputData['meta']['visible']) ||
        (isset($inputData['meta']['visible']) && $inputData['meta']['visible'] === true)
      ) {
        $readyData['form'][$сKey][$propName] = $inputData;
        $readyData['statistics'][$сKey]['visability']['visible']++;
      } 
      //
      else if (isset($inputData['meta']['visible']) && $inputData['meta']['visible'] === false) 
      {
        $readyData['statistics'][$сKey]['visability']['hidden']++;
      }


      // Статистика для полей не доступных для редактирования
      if (
        !isset($inputData['meta']['editable']) ||
        (isset($inputData['meta']['editable']) && $inputData['meta']['editable'] === true)
      ) {                      
        $readyData['statistics'][$сKey]['allowForEdit']['allowed']++;
      }
      //
      else if (isset($inputData['meta']['editable']) && $inputData['meta']['editable'] === false)
      {
        $readyData['statistics'][$сKey]['allowForEdit']['disabled']++;
      }

    }

  }


  return $readyData;

}

function buildInputHTML($confArea, $attrName, $inputData = []) {
  
  $html = $attrNameStr = $attrIdStr = $inputLabelText = '';  

  $classlocalConfig = '';


  $explAttrName = explode('|', $attrName);
  foreach ($explAttrName as $nameVal) {
    $attrNameStr .= '['.$nameVal.']';
    $attrIdStr .= '_'.$nameVal;
  }  
  $attrNameStr = $confArea.$attrNameStr;

  $explInputLabel = explode('|', $inputData['label']);
  $inputLabelText = $confArea . '->';
  foreach ($explInputLabel as $k => $value) {
    $d = ($k+1 == count($explInputLabel)) ? '' : '->';
    $inputLabelText .= ($k+1 == count($explInputLabel) || $k+1 == count($explInputLabel)-1) ?  '<b>'.$value.'</b>'.$d : $value.$d; 
  }


  
  if (is_string($inputData['val']) || is_numeric($inputData['val']) )
  {
    $readonly = (isset($inputData['meta']['editable']) && $inputData['meta']['editable'] === false ) ? ' readonly' : '';  

    $size = (strlen($inputData['val']) > 12) ? strlen($inputData['val'])+3 : 12;    

    $html .=
    '<div class="form-group '.$classlocalConfig.'">';
      $html .=
      '<p><label for="'.$attrIdStr.'">'.$inputLabelText .'</label></p>';    
      $html .=
      '<p><input type="text" class="form-control" name="'.$attrNameStr.'" value="'.htmlentities($inputData['val']).'" id="'.$attrIdStr.'" size="'.$size.'" '.$readonly.' maxlength="400"></p>';
    $html .=
    '</div>';    
  } 

  
  else if (is_bool($inputData['val']))
  {
    $disabled = (isset($inputData['meta']['editable']) && $inputData['meta']['editable'] === false ) ? ' disabled' : '';      

    $html .= 
    '<p>'.$inputLabelText.'</p>';

    foreach ([true, false] as $iVal) {
      $checked = (boolval($inputData['val']) == boolval($iVal)) ? ' checked' : $disabled;
      $labelBoolTxt = (boolval($iVal)) ? 'true' : 'false';
      $lnputBoolTxt = (boolval($iVal)) ? (bool)1 : false;

      $html .=
      '<div class="form-check form-check-inline '.$classlocalConfig.'">';
        $html .=
        '<input type="radio" class="form-check-input" name="'.$attrNameStr.'" value="'.$labelBoolTxt.'" id="'.$attrIdStr.'_'.$labelBoolTxt.'" '.$checked.' >';
        $html .=
        '<label class="form-check-label" for="'.$attrIdStr.'_'.$labelBoolTxt.'">'.$labelBoolTxt.'</label>';  
      $html .=
      '</div>';
    }
    

  } 
  else if (is_null($inputData['val'])) {
    $readonly = (isset($inputData['meta']['editable']) && $inputData['meta']['editable'] === false ) ? ' readonly' : '';  

    $size = 20;    

    $html .=
    '<div class="form-group '.$classlocalConfig.'">';
      $html .=
      '<p><label for="'.$attrIdStr.'">'.$inputLabelText .'</label></p>';    
      $html .=
      '<p><input type="text" class="form-control" name="'.$attrNameStr.'" value="::null::" id="'.$attrIdStr.'" size="'.$size.'" '.$readonly.' maxlength="400"></p>';
    $html .=
    '</div>';  
  }


  return $html;
}

function arrayRecursiveDiff($aArray1, $aArray2) {
  $aReturn = array();

  foreach ($aArray1 as $mKey => $mValue) {
    if (array_key_exists($mKey, $aArray2)) {
      if (is_array($mValue)) {
        $aRecursiveDiff = arrayRecursiveDiff($mValue, $aArray2[$mKey]);
        if (count($aRecursiveDiff)) { $aReturn[$mKey] = $aRecursiveDiff; }
      } else {
        if ($mValue != $aArray2[$mKey]) {
          $aReturn[$mKey] = $mValue;
        }
      }
    } else {
      $aReturn[$mKey] = $mValue;
    }
  }
  return $aReturn;
}
