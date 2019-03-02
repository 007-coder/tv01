<?php 
$diffConfig = arrayRecursiveDiff($_POST, $coreConf);

if ($diffConfig) {
  file_put_contents(
    DIR_CONFIG.DS.'core.local.php',
  '<?php return ' . var_export($diffConfig, true) . ';');
}

