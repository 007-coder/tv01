<?php  ?>
<!DOCTYPE html>

<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="author" content="Yura Vakulenko | vakulenkoyura211@gmail.com | https://t.me/yura_v_007">
    <title>Application config form</title>

    <link href="<?php echo $tmplData['assetsUrl'].'css/bootstrap.min.css' ?>" rel="stylesheet" />
    <link href="<?php echo $tmplData['assetsUrl'].'css/style.css' ?>" rel="stylesheet" />

  </head>

  <body>
    <div class="container"> 
      <div class="row">
        <div class="col-sm-12 col-md-12">          
          <h1 class="mb-5">Application config</h1>

          <?php if (count($tmplData['formData']['form'])) { ?>
            <h4 class="text-center">Navigation</h4>

            <a name="navigation"></a>
            <ul class="nav justify-content-center">
              <?php foreach ($tmplData['formNav'] as $navValue) {?>
                <li class="nav-item">
                  <a class="nav-link" href="<?php echo $navValue['urlHash'].'-options' ?>"><?php echo  $navValue['name']?></a>                
                </li>
              <?php } ?>            
            </ul>            

            <form action="<?php echo $tmplData['formActionUrl'] ?>" name="appConfigForm" method="post">
              <ul class="list-group list-group-flush">
                <?php foreach ($tmplData['formData']['form'] as $configArea => $configAreaData) {
                  $configAreaName = (isset($tmplData['formNav'][$configArea]['name']) && $tmplData['formNav'][$configArea]['name'] !='' ) ? $tmplData['formNav'][$configArea]['name'] : $configArea;
                 ?>
                  <li class="list-group-item">
                    
                    <?php //Config groupe title ?>
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
                    <?php //Config groupe title end ?>
                    
                    <?php //Config Area statistics ?>
                    <div class="statistics row">
                      <?php 
                      if (isset($tmplData['formData']['statistics'][$configArea])) {
                        foreach ($tmplData['formData']['statistics'][$configArea] as $key => $value) { ?>
                          <p class="col-sm-2 col-md-2">
                            <?php foreach ($value as $k2 => $val2) {
                              echo $k2.': '.$val2.'<br>';
                            } ?> 
                          </p>                                          
                        <?php }
                      } ?>
                    </div>
                    <?php //Config Area statistics end ?>
                    
                    <?php //Main data start ?>
                    <div class="input-group mb-3">
                      <?php if (empty($configAreaData)) {?>
                        <h2 class="text-center">no config available</h2>
                      <?php } else { ?>
                        <ul class="list-group data">
                        <?php 
                        foreach ($configAreaData as $attrName => $input) {
                        $classlocalConfig = ($input['fromLocalConfig']) ? ' localConfig' : '';
                        ?>
                          <li class="list-group-item <?php echo $classlocalConfig ?>">
                            <?php echo buildInputHTML($configArea, $attrName, $input); ?>
                          </li>                         
                        <?php }?>
                      </ul>                        

                      <?php } ?>
                    </div>
                    <?php //Main data end ?>

                    
                  </li>
                <?php } ?>
              </ul> 

             <?php // Submit button ?>
              <div class="submitWrap">
                <div class="text-center">
                  <button type="submit" class="btn btn-success btn-lg">Submit</button>                
                </div>
              </div>
              <?php // Submit button end ?>            
            </form>


            <h4 class="text-center"><a href="#navigation">to top</a></h4>


            <?php //echo wrap_pre($tmplData, '$tmplData'); ?>


          <?php } else { ?>
            <h2>Empty application config data.</h2>
          <?php } ?>
        </div>
      </div>
    </div>

    

    <script src="<?php echo $tmplData['assetsUrl'].'js/jquery3.3.1.slim.min.js'?>"></script>
    <script src="<?php echo $tmplData['assetsUrl'].'js/bootstrap.min.js'?>"></script>
    <script src="<?php echo $tmplData['assetsUrl'].'js/main.js' ?>"></script>

  </body>
</html>