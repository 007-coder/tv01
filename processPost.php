<?php 
//wrap_pre($_POST, '$_POST');

$filtered_POST = filterBool_NULL_Recursive($_POST);

$diffConfig = arrayRecursiveDiff($filtered_POST, $coreConf);

/*wrap_pre($diffConfig, '$diffConfig');*/


if ($diffConfig) {

   $json = json_encode($diffConfig, JSON_PRETTY_PRINT);    
    file_put_contents( DIR_CONFIG.DS.'core.local.json', $json);

  file_put_contents(
    DIR_CONFIG.DS.'core.local.php',
  '<?php return ' . var_export($diffConfig, true) . ';');
}


      

