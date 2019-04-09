<?php
function wrap_pre($data, $title = '')
{
    $countData = (is_array($data) || is_object($data)) ? ' (' . count($data) . ') ' : '';
    $readyTitle = $title;
    /*if (!empty(__FUNCTION__)) {
        $readyTitle .= ' | func: '.__FUNCTION__;
    } else if (!empty(__METHOD__)) {
        $readyTitle .= ' | method: '.__METHOD__;
    }*/
    echo '<pre><h4>' . $readyTitle . $countData . ' </h4>' . print_r($data, true) . '</pre>';
}

function prepareInput($meta = [], $in, $out = [], $prefix = '')
{

    foreach ($in as $key => $value) {
        $currMeta = (isset($meta[$key]) && count($meta[$key])) ? $meta[$key] : [];

        if (is_array($value)) {
            $out = array_merge($out, prepareInput($currMeta, $value, $out, $prefix . $key . '|'));
        } else {
            $out["{$prefix}{$key}"] = [
                'val' => $value,
                'label' => $prefix . $key,
                'meta' => $currMeta,
            ];
        }
    }

    return $out;
}

function MultiDimToOneDimArray($kSepar = '|', $in, $excludeKeys = [], $out = [], $prefix = '')
{
    foreach ($in as $key => $value) {
        if (!in_array($key, $excludeKeys)) {
            if (is_array($value)) {
                if (isset($value['dimensionStop']) && $value['dimensionStop']) {
                    unset($value['dimensionStop']);
                    $out["{$prefix}{$key}"] = isset($value[0]) ? $value[0] : $value;
                } else {
                    $out = array_merge($out, MultiDimToOneDimArray($kSepar, $value, $excludeKeys, $out, $prefix . $key . $kSepar));
                }
            } else {
                /*if (in_array($key, $excludeKeys)) {
                    $key = '';
                    $prefix = substr($prefix, 0, strlen($prefix) - 1);
                }*/
                $out["{$prefix}{$key}"] = $value;
            }
        } else {
            $out = [];
        }
    }

    return $out;
}

function filterBool_NULL_Recursive($in, $out = [])
{
    foreach ($in as $key => $value) {
        if (is_array($value)) {
            $out[$key] = filterBool_NULL_Recursive($value);
        } else {
            if (in_array($value, ['true', 'false'])) {
                $out[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            } else if ($value == '::null::') {
                $out[$key] = null;
            } else {
                $out[$key] = $value;
            }
        }
    }

    return $out;
}

function readyFormData($appConfig, $ConfMeta)
{
    $readyData = [

        'form' => [],

        // массив в котором хранится инфо
        // сколько для данного раздела настроек скрытых и
        // не доступных для редактирования инпутов
        'statistics' => [],
    ];

    foreach ($appConfig as $сKey => $сValue) {
        $readyData['statistics'][$сKey] = [
            'visability' => [
                'visible' => 0,
                'hidden' => 0,
            ],
            'allowForEdit' => [
                'allowed' => 0,
                'disabled' => 0,
            ],
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
            } //
            else if (isset($inputData['meta']['visible']) && $inputData['meta']['visible'] === false) {
                $readyData['statistics'][$сKey]['visability']['hidden']++;
            }

            // Статистика для полей не доступных для редактирования
            if (
                !isset($inputData['meta']['editable']) ||
                (isset($inputData['meta']['editable']) && $inputData['meta']['editable'] === true)
            ) {
                $readyData['statistics'][$сKey]['allowForEdit']['allowed']++;
            } //
            else if (isset($inputData['meta']['editable']) && $inputData['meta']['editable'] === false) {
                $readyData['statistics'][$сKey]['allowForEdit']['disabled']++;
            }
        }
    }

    return $readyData;
}

function buildInputHTML($confArea, $attrName, $inputData = [])
{
    $html = $attrNameStr = $inputLabelText = '';
    $attrIdStr = $confArea;

    $explAttrName = explode('|', $attrName);
    foreach ($explAttrName as $nameVal) {
        $attrNameStr .= '[' . $nameVal . ']';
        $attrIdStr .= '_' . $nameVal;
    }
    $attrNameBase = $confArea . $attrNameStr;
    $attrNameStr = $attrNameBase/*.'[val]'*/
    ;
    $attrDataTypeStr = $attrNameBase/*.'[dataType]'*/
    ;

    $explInputLabel = explode('|', $inputData['label']);
    $inputLabelText = $confArea . '->';
    foreach ($explInputLabel as $k => $value) {
        $d = ($k + 1 == count($explInputLabel)) ? '' : '->';
        $inputLabelText .= ($k + 1 == count($explInputLabel) || $k + 1 == count($explInputLabel) - 1)
            ? '<b>' . $value . '</b>' . $d : $value . $d;
    }

    //wrap_pre($inputData, '$inputData|file: '.__FUNCTION__.'()|l:'.__LINE__);
    //wrap_pre($attrNameStr, '$attrNameStr|file: '.__FUNCTION__.'()|l:'.__LINE__);
    //wrap_pre($attrDataTypeStr, '$attrDataTypeStr|file: '.__FUNCTION__.'()|l:'.__LINE__);

    $defInputValue = (isset($inputData['defaultValue']) && !empty($inputData['defaultValue']))
        ? ', <a href="#" onclick="return false;" class="badge badge-secondary">' . $inputData['defaultValue'] . '</a> - default value'
        : '';
    $badgeDataTypeClasses = [
        'string' => ' badge-info',
        'boolean' => ' badge-warning',
        'integer' => ' badge-primary',
        'double' => ' badge-danger',
        'NULL' => ' badge-dark',
    ];
    $inputInfoCont =
        '<p>
            <a href="#" onclick="return false;" 
            data-toggle="tooltip" data-placement="top" title="Input data type"
            class="badge ' . $badgeDataTypeClasses[$inputData['inputDataType']] . '">'
        . $inputData['inputDataType'] .
        '</a>'
        . $defInputValue . '                       
        </p>';

    $inputErrorCont = '';
    if ($inputData['validation']['error']) {
        $inputErrorCont =
            '<p class="inputError">'
            . $inputData['validation']['errorText'] .
            '</p>';
    }

    $html .=
        '<div class="row align-items-start no-gutters">';
    // ------ prepare data for Checkbox column
    $htmlMarkFromLocalConfig = '';
    if ($inputData['fromLocalConfig']) {
        $htmlMarkFromLocalConfig .=
            '<p>
                <a href="#" class="badge badge-success mt-1" 
                onclick="return false;"
                data-toggle="tooltip" data-placement="top" 
                title="Setting from local config file.">Local</a>
            </p>';
    }
    $isSwitchOn =
        (
            isset($inputData['meta']['editable'])
            && $inputData['meta']['editable'] === true
        ) ? ' checked' : '';
    // ------ prepare data for Checkbox column

    // ------ Checkbox column
    $html .=
        '<div class="col-md-1 col-sm-1">
            <div class="custom-control custom-switch">
                <input type="checkbox" ' . $isSwitchOn . ' class="custom-control-input protectiusChbx" 
                id="chbx_' . $attrIdStr . '" data-input-id="' . $attrIdStr . '" data-input-type="' . $inputData['inputDataType'] . '">
                <label class="custom-control-label" for="chbx_' . $attrIdStr . '"></label>
            </div>
            ' . $htmlMarkFromLocalConfig . '           
        </div>';
    // ------ Checkbox column

    // ------ MAIN INPUT column
    $html .=
        '<div class="col-sm-11 col-md-11">';

    // if is String (numeric or null) Value input
    if (
        is_string($inputData['val'])
        || is_numeric($inputData['val'])
        || is_null($inputData['val'])
    ) {
        $readonly = (
            isset($inputData['meta']['editable'])
            && $inputData['meta']['editable'] === true
        ) ? '' : ' readonly disabled';

        $size = (strlen($inputData['val']) > 12) ? strlen($inputData['val']) + 3 : 12;

        $hiddenInputDataType = ''/*'<input type="hidden" class="iHidden '.$readonly.' " name="' . $attrDataTypeStr . '"
                    value="' . $inputData['inputDataType'] . '" id="' . $attrIdStr . '_hiddenDataType" 
                    ' . $readonly . '>'*/
        ;
        $html .=
            '<div class="form-group">';
        $html .=
            '<p><label for="' . $attrIdStr . '">' . $inputLabelText . '</label></p>';
        $html .= $inputInfoCont;
        $html .=
            '<p>
                    <input type="text" class="form-control ' . $readonly . ' " name="' . $attrNameStr . '" 
                    value="' . htmlentities($inputData['val']) . '" id="' . $attrIdStr . '" 
                    size="' . $size . '" ' . $readonly . ' maxlength="400">
                    ' . $hiddenInputDataType . '
                    
                  </p>';
        $html .= $inputErrorCont;
        $html .=
            '</div>';
    } // if is Boolean input
    else if (is_bool($inputData['val'])) {
        $disabled =
            (
                isset($inputData['meta']['editable'])
                && $inputData['meta']['editable'] === true
            ) ? '' : ' disabled';

        $html .=
            '<p>' . $inputLabelText . '</p>';
        $html .= $inputInfoCont;

        $hiddenInputDataType = ''/*'<input type="hidden" class="iHidden '.$disabled.' " name="' . $attrDataTypeStr . '"
                    value="' . $inputData['inputDataType'] . '" id="' . $attrIdStr . '_hiddenDataType" 
                    ' . $disabled . '>'*/
        ;

        foreach ([true, false] as $iVal) {
            $checked = (boolval($inputData['val']) == boolval($iVal)) ? ' checked' : '';
            $labelBoolTxt = (boolval($iVal)) ? 'true' : 'false';

            $html .=
                '<div class="custom-control custom-radio custom-control-inline">';
            $html .=
                '<input type="radio" class="form-check-input custom-control-input ' . $disabled . '" name="' . $attrNameStr . '" value="' . $labelBoolTxt . '" id="' . $attrIdStr . '_' . $labelBoolTxt . '" ' . $checked . $disabled . ' >';
            $html .=
                '<label class="custom-control-label" for="' . $attrIdStr . '_' . $labelBoolTxt . '">' . $labelBoolTxt . '</label>';
            $html .=
                '</div>';
        }

        $html .= $hiddenInputDataType;

        $html .= $inputErrorCont;
    }

    $html .=
        '</div>';
    // ------ MAIN INPUT column

    $html .=
        '</div>';

    return $html;
}

function arrayRecursiveDiff($aArray1, $aArray2)
{
    $aReturn = [];

    foreach ($aArray1 as $mKey => $mValue) {
        if (array_key_exists($mKey, $aArray2)) {
            if (is_array($mValue)) {
                $aRecursiveDiff = arrayRecursiveDiff($mValue, $aArray2[$mKey]);
                if (count($aRecursiveDiff)) {
                    $aReturn[$mKey] = $aRecursiveDiff;
                }
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

function buildConfigDataTypes($globalConfig, $configMeta = [], $out = [])
{

    foreach ($globalConfig as $key => $value) {
        $currMeta = (isset($configMeta[$key]) && count($configMeta[$key])) ? $configMeta[$key] : [];

        if (is_array($value)) {
            $out[$key] = buildConfigDataTypes($value, $currMeta);
        } else {
            $out[$key] =
                (isset($currMeta['inputDataType']) && !empty($currMeta['inputDataType']))
                    ? $currMeta['inputDataType']
                    : gettype($value);
        }
    }

    return $out;
}

;

function getValidationRules($in, $meta = [], $oneDimension = false, $out = [])
{

    foreach ($in as $key => $value) {
        $currMetaValidation = (isset($meta[$key]) && count($meta[$key])) ? $meta[$key] : [];

        if (is_array($value)) {
            $out[$key] = getValidationRules($value, $currMetaValidation, $oneDimension);
        } else {
            if ($oneDimension) {
                $out[$key][] =
                    (isset($currMetaValidation['validate']) && count($currMetaValidation['validate']))
                        ? $currMetaValidation['validate']
                        : [];
                $out[$key]['dimensionStop'] = true;
            } else {
                $out[$key] =
                    (isset($currMetaValidation['validate']) && count($currMetaValidation['validate']))
                        ? $currMetaValidation['validate']
                        : [];
            }
        }
    }

    return $out;
}

;

function buildConfigMetaFull($in, $meta = [], $oneDimension = false, $out = [])
{

    foreach ($in as $key => $value) {
        $currMeta = (isset($meta[$key]) && count($meta[$key])) ? $meta[$key] : [];

        if (is_array($value)) {
            $out[$key] = buildConfigMetaFull($value, $currMeta, $oneDimension);
        } else {
            if ($oneDimension) {
                $out[$key]['dataType'] =
                    (isset($currMeta['inputDataType']) && !empty($currMeta['inputDataType']))
                        ? $currMeta['inputDataType']
                        : gettype($value);
                $out[$key]['validate'] =
                    (isset($currMeta['validate']) && count($currMeta['validate']))
                        ? $currMeta['validate']
                        : [];
                $out[$key]['dimensionStop'] = true;
            } else {
                $out[$key]['dataType'] =
                    (isset($currMeta['inputDataType']) && !empty($currMeta['inputDataType']))
                        ? $currMeta['inputDataType']
                        : gettype($value);
                $out[$key]['validate'] =
                    (isset($currMeta['validate']) && count($currMeta['validate']))
                        ? $currMeta['validate']
                        : [];
            }
        }
    }

    return $out;
}

;

/**
 * Set an array item to a given value using "dot" notation.
 *
 * If no key is given to the method, the entire array will be replaced.
 *
 * @param  array $array
 * @param  string $key
 * @param  mixed $value
 * @param  string $delimiter
 * @return array
 */

function laravelHelpersArrSet(&$array, $key, $value, $delimiter = '.')
{
    if (is_null($key)) {
        return $array = $value;
    }

    $keys = explode($delimiter, $key);

    while (count($keys) > 1) {
        $key = array_shift($keys);

        // If the key doesn't exist at this depth, we will just create an empty array
        // to hold the next value, allowing us to create the arrays to hold final
        // values at the correct depth. Then we'll keep digging into the array.
        if (!isset($array[$key]) || !is_array($array[$key])) {
            $array[$key] = [];
        }

        $array = &$array[$key];
    }

    $array[array_shift($keys)] = $value;

    return $array;
}

/**
 * Flatten a multi-dimensional associative array with dots.
 *
 * @param  array $array
 * @param  string $prepend
 * @param  string $delimiter
 * @return array
 */
function laravelHelpersArrDot($array, $delimiter = '.', $prepend = '')
{
    $results = [];

    foreach ($array as $key => $value) {
        if (is_array($value) && !empty($value)) {
            $results = array_merge($results, laravelHelpersArrDot($value, $delimiter, $prepend . $key . $delimiter));
        } else {
            $results[$prepend . $key] = $value;
        }
    }

    return $results;
}

function buildValidationError($inputId, $strDelim = '->', $value = [])
{
    $result = [];
    $temp = &$result;
    foreach (explode($strDelim, $inputId) as $key) {
        $temp = &$temp[$key];
    }
    $temp = $value;

    return $result;
}
