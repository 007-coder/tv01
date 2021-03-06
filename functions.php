<?php

use Sam\Installation\Config\ConfigValidator;


function wrap_pre($data, $title = '')
{
    $countData = (is_array($data) || is_object($data))
        ? ' (' . count($data) . ') ' : '';
    $readyTitle = $title;
    echo '<pre><h4>' . $readyTitle . $countData . ' </h4>' . print_r($data, true) . '</pre>';
}

function prepareInput(
    $in, $meta = [], $delimiter = '|', $out = [], $prefix =
''
) {

    foreach ($in as $key => $value) {
        $currMeta = (isset($meta[$key]) && count($meta[$key])) ? $meta[$key]
            : [];

        if (is_array($value)) {
            if( isset($currMeta['inputDataType'])
                && $currMeta['inputDataType'] == 'array'
            ) {
                $out["{$prefix}{$key}"] = [
                    'val' => $value,
                    'label' => $prefix . $key,
                    'meta' => $currMeta,
                ];
            } else {
                $out =
                array_merge(
                    $out,
                    prepareInput($value, $currMeta, $delimiter, $out,$prefix . $key . $delimiter)
                );
            }
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

function MultiDimToOneDimArray(
    $delimiter = '|', $in, $excludeKeys = [], $out = [], $prefix = ''
) {
    foreach ($in as $key => $value) {
        if (!in_array($key, $excludeKeys)) {
            if (is_array($value)) {
                if (array_key_exists('dimensionStop', $value)) {
                    unset($value['dimensionStop']);

                    if (array_key_exists(0, $value)) {
                        $out["{$prefix}{$key}"] = $value[0];
                    } else if (array_key_exists('values', $value)) {
                        $out["{$prefix}{$key}"] = $value['values'];
                    } else {
                        $out["{$prefix}{$key}"] = $value;
                    }

                } else {
                    $out = array_merge(
                        $out,
                        MultiDimToOneDimArray(
                            $delimiter, $value, $excludeKeys, $out,
                            $prefix . $key . $delimiter
                        )
                    );
                }
            } else {
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

function readyFormData($appConfig, $ConfMeta, $delimiter = '|')
{
    $readyData = [

        'form' => [],

        // массив в котором хранится инфо
        // сколько для данного раздела настроек скрытых и
        // не доступных для редактирования инпутов
        'statistics' => [],
    ];

    foreach ($appConfig as $cKey => $cValue) {
        $readyData['statistics'][$cKey] = [
            'visability' => [
                'visible' => 0,
                'hidden' => 0,
            ],
            'allowForEdit' => [
                'allowed' => 0,
                'disabled' => 0,
            ],
        ];

        $inputGroupeMeta = (isset($ConfMeta[$cKey]) && count($ConfMeta[$cKey]))
            ? $ConfMeta[$cKey]
            : [];

        $prepareGroupeInputs = prepareInput($cValue, $inputGroupeMeta, $delimiter);

        foreach ($prepareGroupeInputs as $propName => $inputData) {
            if (
                !isset($inputData['meta']['visible']) ||
                (isset($inputData['meta']['visible']) && $inputData['meta']['visible'] === true)
            ) {
                $readyData['form'][$cKey][$propName] = $inputData;
                $readyData['statistics'][$cKey]['visability']['visible']++;
            } //
            else if (isset($inputData['meta']['visible']) && $inputData['meta']['visible'] === false) {
                $readyData['statistics'][$cKey]['visability']['hidden']++;
            }

            // Статистика для полей не доступных для редактирования
            if (
                !isset($inputData['meta']['editable']) ||
                (isset($inputData['meta']['editable']) && $inputData['meta']['editable'] === true)
            ) {
                $readyData['statistics'][$cKey]['allowForEdit']['allowed']++;
            } //
            else if (isset($inputData['meta']['editable']) && $inputData['meta']['editable'] === false) {
                $readyData['statistics'][$cKey]['allowForEdit']['disabled']++;
            }
        }
    }

    return $readyData;
}

function readyFormValidationErrors($errors, $delimiter = '|')
{
    $readyErrors = [];
    unset($errors['countErrors']);
    foreach ($errors as $area => $error) {
        $tmp = [];
        array_push($tmp, MultiDimToOneDimArray($delimiter, $error));
        $readyErrors[$area] = $tmp[0];
    }

    return $readyErrors;
}

function readyFormValidatedPost($post, $delimiter = '|')
{
    $readyPost = [];
    foreach ($post as $area => $value) {
        $tmp = [];
        array_push($tmp, MultiDimToOneDimArray($delimiter, $value));
        $readyPost[$area] = $tmp[0];
    }
    return $readyPost;
}

/**
 * @param $confArea
 * @param $attrName
 * @param array $input
 * @param string $delimiter
 * @return string
 */
function buildInputHTML($confArea, $attrName, $input = [], $delimiter = '|')
{
    $html = $attrNameStr = $inputLabelText = '';
    $attrIdStr = $confArea;

    $explAttrName = explode($delimiter, $attrName);
    foreach ($explAttrName as $nameVal) {
        $attrNameStr .= '[' . $nameVal . ']';
        $attrIdStr .= '_' . $nameVal;
    }
    $attrNameBase = $confArea . $attrNameStr;
    $attrNameStr = $attrNameBase;
    $attrDataTypeStr = $attrNameBase;

    $explInputLabel = explode($delimiter, $input['label']);
    $inputLabelText = $confArea . '->';
    $toTopLink = '<span class="to_top ml-3"><a href="#navigation">to top</a></span>';

    foreach ($explInputLabel as $k => $value) {
        $d = ($k + 1 == count($explInputLabel)) ? '' : '->';
        $inputLabelText .=
            ($k + 1 == count($explInputLabel) || $k + 1 == count($explInputLabel) - 1)
                ? '<b>' . $value . '</b>' . $d
                : $value . $d;
    }
    $inputLabelText = $inputLabelText . $toTopLink;

    $defInputValue = (isset($input['defaultValue']) && !empty($input['defaultValue']))
        ? ', <a href="#" onclick="return false;" class="badge badge-secondary">' . $input['defaultValue'] . '</a> - default value'
        : '';
    $badgeDataTypeClasses = [
        'string' => ' badge-info',
        'array' => ' badge-secondary',
        'boolean' => ' badge-warning',
        'integer' => ' badge-primary',
        'double' => ' badge-danger',
        'NULL' => ' badge-dark',
    ];
    $inputInfoCont =
        '<p>
            <a href="#" onclick="return false;" 
            data-toggle="tooltip" data-placement="top" title="Input data type"
            class="badge ' . $badgeDataTypeClasses[$input['inputDataType']] . '">'
        . $input['inputDataType'] .
        '</a>'
        . $defInputValue . '                       
        </p>';

    $inputErrorCont = '';
    if ($input['validation']['error']) {
        $inputErrorText = '';
        if (count($input['validation']['errorText'])) {
            foreach ($input['validation']['errorText'] as $errorText) {
                $inputErrorText .=
                    '<div class="errorTextWrap">
                        <p class="badge badge-danger">' . $errorText . '</p>
                    </div>';
            }
        }
        $inputErrorCont =
            '<div class="inputError">' . $inputErrorText . '</div>';
    }

    $inputDescriptionCont = '';
    if (!empty($input['description'])) {
        $inputDescriptionCont =
            '<div class="inputDescr"><p>' . $input['description'] . '</p></div>';
    }

    $html .=
        '<div class="row align-items-start no-gutters">';
    // ------ prepare data for Checkbox column
    $htmlMarkFromLocalConfig = '';
    if ($input['fromLocalConfig']) {
        $htmlMarkFromLocalConfig .=
            '<p>
                <a href="#" class="badge badge-success mt-1" 
                onclick="return false;"
                data-toggle="tooltip" data-placement="top" 
                title="Setting from local config file.">Local</a>
            </p>';
    }

    // bootstrap Switcher which allow/disallow edit content
    // of input or radio buttons
    if (is_null($input['validation']['post']['value'])) {
        $isSwitchOn =
            ($input['fromLocalConfig']) ? ' checked' : '';
    } else {
        $isSwitchOn = ' checked';
    }

    // ------ Checkbox column
    if (isset($input['meta']['editable']) && $input['meta']['editable'] === false) {
        $checkboxHTML =  '<div class="col-md-1 col-sm-1"></div>';
    } else {
        $chbx_inputContainer = '';
        if ($input['inputDataType'] == 'array') {
            if (isset($input['meta']['inputType'])) {
                $chbx_inputContainer = in_array($input['meta']['inputType'],['textarea', 'input'])
                        ? $input['meta']['inputType'] : 'input';
            } else {
                $chbx_inputContainer = 'input';
            }
        }

        $checkboxHTML =
        '<div class="col-md-1 col-sm-1">
            <div class="custom-control custom-switch">
                <input type="checkbox" ' . $isSwitchOn . ' class="custom-control-input protectiusChbx" 
                id="chbx_' . $attrIdStr . '" data-input-id="' . $attrIdStr . '" 
                data-input-type="' . $input['inputDataType'] . '"
                data-input-container="'.$chbx_inputContainer.'"
                >
                <label class="custom-control-label" for="chbx_' . $attrIdStr . '"></label>
            </div>
            ' . $htmlMarkFromLocalConfig . '           
        </div>';
    }

    $html .= $checkboxHTML;
    // ------ Checkbox column



    // ------ MAIN INPUT column
    $html .=
        '<div class="col-sm-11 col-md-11">';

    // if is String (numeric or null) Value input
    if (
        is_string($input['val'])
        || is_numeric($input['val'])
        || is_null($input['val'])
    ) {
        if (is_null($input['validation']['post']['value'])) {
            if (isset($input['meta']['editable']) && $input['meta']['editable'] === false){
                $readonly = ' readonly disabled';
            } elseif ($input['fromLocalConfig']) {
                $readonly = '';
            } else {
                $readonly = ' readonly disabled';
            }
        } else {
            $readonly = '';
            $input['val'] = $input['validation']['post']['value'];
        }

        $size = (strlen($input['val']) > 12) ? strlen($input['val']) + 3 : 12;

        $html .=
            '<div class="form-group">';
        $html .=
            '<p><label for="' . $attrIdStr . '">' . $inputLabelText . '</label></p>';
        $html .= $inputDescriptionCont;
        $html .= $inputInfoCont;
        $html .= $inputErrorCont;
        $html .=
            '<p>
                <input type="text" class="form-control ' . $readonly . ' " name="' . $attrNameStr . '" 
                    value="' . htmlentities($input['val']) . '" id="' . $attrIdStr . '" 
                    size="' . $size . '" ' . $readonly . ' maxlength="400">                    
            </p>';
        $html .=
            '</div>';
    }
    // --------------

    // if is Boolean input
    else if (is_bool($input['val'])) {

        if (is_null($input['validation']['post']['value'])) {
            $disabled =
                ($input['fromLocalConfig']) ? '' : ' disabled';
        } else {
            $disabled = '';
            $postValue = $input['validation']['post']['value'];
            $booleans = ['1', 'true', true, 1, '0', 'false', false, 0, 'yes',
                'no', 'on', 'off'];
            $booleansTrue = ['1', 'true', true, 1, 'yes', 'on'];

            if (in_array($postValue, $booleans, true)) {
                $input['val'] = (in_array($postValue, $booleansTrue, true))
                    ? true : false;
            }
        }

        $html .=
            '<p>' . $inputLabelText . '</p>';
        $html .= $inputDescriptionCont;
        $html .= $inputInfoCont;
        $html .= $inputErrorCont;

        foreach ([true, false] as $iVal) {
            $checked = (boolval($input['val']) == boolval($iVal)) ? ' checked'
                : '';
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
    }
    // --------------


    // if is array input
    else if (is_array($input['val'])) {

        if (is_null($input['validation']['post']['value'])) {
            if (
                isset($input['meta']['editable'])
                && $input['meta']['editable'] === false
            ){
                $readonly = ' readonly disabled';
            } elseif ($input['fromLocalConfig']) {
                $readonly = '';
            } else {
                $readonly = ' readonly disabled';
            }
        } else {
            $readonly = '';
            $input['val'] = $input['validation']['post']['value'];
        }
        $valuesDelimiter =
            (
                isset($input['meta']['valuesDelimiter'])
                && !empty($input['meta']['valuesDelimiter'])
                )
            ? $input['meta']['valuesDelimiter']
            : ',';
        $inputValuesDelimiterHTML =
            '<p>
                Values delimiter: <span class="badge badge-success">
                    <b>'.$valuesDelimiter.'</b>
                </span>
            </p>';

        $sizeCounter = 0;
        $size = '';
        if (count($input['val'])) {
            foreach ($input['val'] as $inVal) {
                $sizeCounter += strlen($inVal);
            }
            $size = $sizeCounter+count($input['val'])+5;
        } else {
            $size = 12;
        }

        $inputHTML = '';
        if (
            isset($input['meta']['inputType'])
            && in_array($input['meta']['inputType'], ['input', 'textarea'])
        ) {
            if ($input['meta']['inputType'] == 'textarea') {
                $inputHTML .=
                    '<p>
                        <textarea class="form-control ' . $readonly . ' " 
                            name="' . $attrNameStr . '"
                            id="' . $attrIdStr . '" ' . $readonly . ' 
                            cols="95" rows="5" maxlength="1400">'
                            .trim(implode($valuesDelimiter,$input['val'])).
                        '</textarea>
                    </p>';
            } else {
                $inputHTML .=
                    '<p>
                        <input type="text" class="form-control ' . $readonly . ' " 
                        name="' . $attrNameStr . '" size="'.$size.'" 
                        value="' . trim(implode($valuesDelimiter,$input['val'])) . '" 
                        id="' . $attrIdStr . '" ' . $readonly . ' maxlength="400">
                    </p>';
            }
        } else {
            $inputHTML .=
                '<p>
                    <input type="text" class="form-control ' . $readonly . ' " 
                    name="' . $attrNameStr . '" size="'.$size.'"
                    value="'.trim(implode($valuesDelimiter,$input['val'])).'" 
                    id="' . $attrIdStr . '" ' . $readonly . ' maxlength="400">
                </p>';
        }

        $html .=
            '<div class="form-group">';
        $html .=
            '<p><label for="' . $attrIdStr . '">' . $inputLabelText . '</label></p>';
        $html .= $inputDescriptionCont;
        $html .= $inputValuesDelimiterHTML;
        $html .= $inputInfoCont;
        $html .= $inputErrorCont;
        $html .= $inputHTML;
        $html .=
            '</div>';
    }
    // --------------


    $html .=
        '</div>';
    // ------ MAIN INPUT column

    $html .=
        '</div>';

    return $html;
}



function buildInputDeleteHTML($confArea, $subArea, $submButtonSize = 'small', $delimiter = '.') {

    $html = '<form action="'.BASE_URL.'" method="post" class="configKey actionForm ml-2 d-lg-inline-block">';
        $html .= '<button type="submit" class="'.$submButtonSize.' btn btn-outline-danger btn-sm">Delete</button>';
        $html .= '<input type="hidden" value="'.$confArea.$delimiter.$subArea.'" name="configKey">';
        $html .= '<input type="hidden" value="delete" name="action">';
    $html .= '</form>';

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
        $currMeta = (isset($configMeta[$key]) && count($configMeta[$key]))
            ? $configMeta[$key] : [];

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



function getValidationRules($in, $meta = [], $oneDimension = false, $out = [])
{

    foreach ($in as $key => $value) {
        $currMetaValidation = (isset($meta[$key]) && count($meta[$key]))
            ? $meta[$key] : [];

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
        $currMeta = (isset($meta[$key]) && count($meta[$key])) ? $meta[$key]
            : [];

        if (is_array($value)) {
            if( isset($currMeta['inputDataType'])
                && $currMeta['inputDataType'] == 'array'
            ) {
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
            } else {
                $out[$key] = buildConfigMetaFull($value, $currMeta, $oneDimension);
            }

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

function buildConfigUsingMeta($globalConfig, $configMeta = [], $oneDimension = false, $out = [])
{
    foreach ($globalConfig as $key => $value) {
        $currMeta = (isset($configMeta[$key]) && count($configMeta[$key]))
            ? $configMeta[$key] : [];

        if (is_array($value)) {
            if( isset($currMeta['inputDataType'])
                && $currMeta['inputDataType'] == 'array'
            ) {
                if ($oneDimension) {
                    $out[$key] = [
                        'values' => $value,
                        'dimensionStop' => true
                    ];
                } else {
                    $out[$key] = $value;
                }
            } else {
                $out[$key] = buildConfigUsingMeta($value, $currMeta, $oneDimension);
            }
        } else {
            if ($oneDimension) {
                $out[$key] = [
                        'values' => $value,
                        'dimensionStop' => true
                    ];

            } else {
                $out[$key] = $value;
            }
        }
    }

    return $out;
}


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

function setInputValue($value, $dataType)
{
    switch ($dataType) {
        case ConfigValidator::T_BOOL:
            return ($value) ? 'true' : 'false';

        case ConfigValidator::T_NULL:
            return is_null($value) ? 'NULL' : $value;

        case ConfigValidator::T_ARRAY:
            return (!empty($value) && is_array($value)) ? implode(', ', $value) : '';
        case ConfigValidator::T_INTEGER:
            return (!empty($value) && is_numeric($value)) ? (int)$value : 'error: not a Numeric';

        case ConfigValidator::T_STRING:
            return (!empty($value) && is_string($value)) ? $value : 'error: not a String';

    }
}
