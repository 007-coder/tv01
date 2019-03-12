<?php
$appConfig = $confFormViewData['appConfig'];
$formNav = $confFormViewData['formNav'];
$ConfMeta = $confFormViewData['configMeta'];
$coreLocalConfOneDim = MultiDimToOneDimArray('|',$coreLocalConf);
?>

<!DOCTYPE html>

<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="author" content="Yura Vakulenko | vakulenkoyura211@gmail.com | https://t.me/yura_v_007">
    <title>Application config form</title>

    <link href="<?php echo CSS_URL.'bootstrap.min.css' ?>" rel="stylesheet" />
    <link href="<?php echo CSS_URL.'style.css' ?>" rel="stylesheet" />

  </head>

  <body>

    <div class="container"> 

      <div class="row">
        <div class="col-sm-12 col-md-12">
          
          <h1 class="mb-5">Application config</h1>

          <?php if (count($appConfig)) { ?>

          <h4 class="text-center">Navigation</h4>

          <a name="navigation"></a>
          <ul class="nav justify-content-center">
            <?php foreach ($formNav as $navKey => $navValue) {
              $navItemName = (isset($navValue['name']) && $navValue['name'] !='' ) ? $navValue['name'] : $navKey;
            ?>
              <li class="nav-item">
                <a class="nav-link" href="<?php echo '#'.$navKey.'-options' ?>"><?php echo  $navItemName?></a>                
              </li>
            <?php } ?>            
          </ul>

          <form action="<?php echo BASE_URL ?>" name="appConfigForm" method="post"> 
            <?php 
            $readyFormData = readyFormData($appConfig, $ConfMeta);
            ?>

            <ul class="list-group list-group-flush">
              <?php foreach ($readyFormData['form'] as $configArea => $configAreaData) {

                $configAreaName = (isset($formNav[$configArea]['name']) && $formNav[$configArea]['name'] !='' ) ? $formNav[$configArea]['name'] : $configArea;
               ?>
                <li class="list-group-item">

                  <a name="<?php echo $configArea.'-options' ?>"></a>
                  <h3 class="configGroupeTitle">                    
                    <?php echo '<b>'.$configAreaName.'</b>'; ?>
                    <span class="countGroupeItems">
                      <?php echo '('.count($configAreaData).')'; ?>
                    </span>
                    <span class="to_top">
                      <a href="#navigation">to top</a>
                    </span>
                  </h3>

                  <div class="statistics row">
                    <?php 
                    if (isset($readyFormData['statistics'][$configArea])) {
                      foreach ($readyFormData['statistics'][$configArea] as $key => $value) { ?>
                        <p class="col-sm-2 col-md-2">
                          <?php foreach ($value as $k2 => $val2) {
                            echo $k2.': '.$val2.'<br>';
                          } ?> 
                        </p>                                                
                      <?php }
                    }
                    
                    ?>
                  </div>

                  <div class="input-group mb-3">
                    <?php if (empty($configAreaData)) {?>
                      <h2 class="text-center">no config available</h2>
                    <?php } else { ?>
                      <ul class="list-group data">
                      <?php 
                      foreach ($configAreaData as $attrName => $input) {
                      $classlocalConfig = (isset($coreLocalConfOneDim[$configArea.'|'.$attrName])) ? ' localConfig' : '';
                      ?>
                        <li class="list-group-item <?php echo $classlocalConfig ?>">
                          <?php echo buildInputHTML($configArea, $attrName, $input); ?>
                        </li>                         
                      <?php }?>
                    </ul>
                      

                    <?php } ?>
                    

                  </div>



                  
                </li>
              <?php } ?>

            </ul> 


           
            <div class="submitWrap">
              <div class="text-center">
                <button type="submit" class="btn btn-success btn-lg">Submit</button>                
              </div>
            </div>            
          </form>

          <h4 class="text-center"><a href="#navigation">to top</a></h4>

           

          <?php } else { ?>
            <h2>Empty application config data.</h2>
          <?php } ?>

        </div>        
      </div>

      
      
    </div>

    

    


    <script src="<?php echo JS_URL.'jquery3.3.1.slim.min.js'?>"></script>
    <script src="<?php echo JS_URL.'bootstrap.min.js'?>"></script>
    <script src="<?php echo JS_URL.'main.js' ?>"></script>
   

  </body>
</html>