<?php ?>
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="author"
          content="Yura Vakulenko | vakulenkoyura211@gmail.com | https://t.me/yura_v_007">
    <title>Application config form</title>

    <link href="<?php echo $tmplData['assetsUrl'] . 'css/bootstrap.min.css' ?>"
          rel="stylesheet"/>
    <link href="<?php echo $tmplData['assetsUrl'] . 'css/style.css' ?>"
          rel="stylesheet"/>

</head>

<body>
<div class="container">
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <h1 class="mb-5">
                Application config for
                <span class="badge badge-success"><?php echo $tmplData['configName'] ?></span>
                <span class="h3">config name.</span>
            </h1>

            <?php if (count($tmplData['formData']['form'])) { ?>
                <h4 class="text-center">Navigation</h4>

                <a name="navigation"></a>
                <ul class="nav justify-content-center">
                    <?php foreach ($tmplData['formNav'] as $navValue) { ?>
                        <li class="nav-item">
                            <a class="nav-link"
                               href="<?php echo $navValue['urlHash'] . '-options' ?>"><?php echo $navValue['name'] ?></a>
                        </li>
                    <?php } ?>
                </ul>


                <?php
                // Display list of Local config options
                if (count($tmplData['formData']['localConfigSettings'])) { ?>
                    <div class="localValuesWrap mb-3">
                        <h4 class="mt-3 mb-2">Local config values
                            (<?php echo count($tmplData['formData']['localConfigSettings']) ?>)
                        </h4>

                        <?php foreach ($tmplData['formData']['localConfigSettings'] as $localConfigValue) {
                            ?>
                            <div class="mb-0 valueWrap">
                                <p class="mb-0 d-lg-inline-block">
                                    <a href="<?php echo $localConfigValue['urlHash'] ?>"
                                       class="badge badge-info localConfig-badge">
                                        <?php echo $localConfigValue['title'] ?>
                                    </a>,
                                    <span class="badge badge-success ml-2">
                                        <?php echo $localConfigValue['data']['value'] ?>
                                    </span>,
                                    <span class="badge badge-light ml-2">
                                        <?php echo $localConfigValue['data']['type'] ?>
                                    </span>
                                </p>
                                <?php echo
                                    (isset($localConfigValue['deleteHTML'])
                                    && !empty($localConfigValue['deleteHTML']))
                                    ? $localConfigValue['deleteHTML']
                                    : '';?>
                            </div>


                        <?php } ?>
                    </div>
                <?php } ?>


                <?php
                // Display render errors
                if (isset($tmplData['renderErrors']) && count($tmplData['renderErrors'])) {
                    foreach ($tmplData['renderErrors'] as $errorText) { ?>
                        <div class="row justify-content-center mb-3">
                            <div class="col-md-7 col-sm-7">
                                <div class="alert alert-danger alert-dismissible fade show"
                                     role="alert">
                                    <?php echo $errorText ?>
                                    <button type="button" class="close"
                                            data-dismiss="alert"
                                            aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php }
                }

                // Display config updation status message
                if (isset($tmplData['configUpdated'])) {
                    $alertClass = ($tmplData['configUpdated'])
                        ? ' alert-success' : ' alert-danger';
                    $errorText = ($tmplData['configUpdated'])
                        ? 'Configuration updated successfully!'
                        : 'Configuration was not updated!'
                    ?>
                    <div class="row justify-content-center mb-3 mt-3">
                        <div class="col-md-8 col-sm-5">
                            <div class="alert <?php echo $alertClass; ?> alert-dismissible fade show"
                                 role="alert">

                                <h4 class="text-center"><?php echo $errorText ?></h4>

                                <?php if (count($tmplData['formData']['validationErrors'])) { ?>
                                    <div class="validationErrorsWrap">
                                        <p class="mb-2">You made mistakes for
                                            the following
                                            values:</p>
                                        <?php foreach ($tmplData['formData']['validationErrors'] as $validationError) { ?>
                                            <p class="mb-1">
                                                <a href="<?php echo $validationError['urlHash'] ?>"
                                                   class="badge badge-danger">
                                                    <?php echo $validationError['title'] ?>
                                                </a>
                                            </p>
                                        <?php } ?>
                                    </div>

                                    <div class="validationValidWrap mt-3">
                                        <p class="mb-2">The following values are
                                            valid: </p>
                                        <?php
                                        if (count($tmplData['formData']['validationValid'])) {
                                            foreach ($tmplData['formData']['validationValid'] as $validationValid) { ?>
                                                <p class="mb-1">
                                                    <a href="<?php echo $validationValid['urlHash'] ?>"
                                                       class="badge badge-success">
                                                        <?php echo $validationValid['title'] ?>
                                                    </a>
                                                </p>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <h5 class="mb-2 text-center"><b>No
                                                    valid values!</b></h5>
                                        <?php } ?>
                                    </div>
                                <?php } ?>

                                <button type="button" class="close"
                                        data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php } ?>


                <form action="<?php echo $tmplData['formActionUrl'] ?>"
                      name="appConfigForm" method="post">

                    <ul class="list-group list-group-flush">
                        <?php foreach ($tmplData['formData']['form'] as $configArea => $configAreaData) {
                            $configAreaName = (isset($tmplData['formNav'][$configArea]['name']) && $tmplData['formNav'][$configArea]['name'] != '')
                                ? $tmplData['formNav'][$configArea]['name']
                                : $configArea;
                            ?>
                            <li class="list-group-item">

                                <?php //Config groupe title ?>
                                <a name="<?php echo $configArea . '-options' ?>"></a>
                                <h3 class="configGroupeTitle">
                                    <?php echo '<b>' . $configAreaName . '</b>'; ?>
                                    <span class="countGroupeItems">
                                        <?php echo '(' . count($configAreaData) . ')'; ?>
                                    </span>
                                    <span class="to_top">
                                        <a href="#navigation">to top</a>
                                    </span>
                                </h3>
                                <?php //Config groupe title end ?>

                                <?php //Config Area statistics ?>
                                <div class="statistics row mb-lg-4 ">
                                    <?php
                                    if (isset($tmplData['formData']['statistics'][$configArea])) {
                                        foreach ($tmplData['formData']['statistics'][$configArea] as $key => $value) {
                                            $statFirstVal = $statSecondVal = $statTitle = '';
                                            switch ($key) {
                                                case "visability":
                                                    $statTitle = 'Visibility';
                                                    $statFirstVal = $value['visible'];
                                                    $statSecondVal = $value['hidden'];
                                                    $statCollClass = 'col-sm-2 col-md-2';
                                                    break;
                                                case "allowForEdit":
                                                    $statTitle = 'Allowed for edit';
                                                    $statFirstVal = $value['allowed'];
                                                    $statSecondVal = $value['disabled'];
                                                    $statCollClass = 'col-sm-4 col-md-4';
                                                break;
                                            }
                                        ?>
                                            <div class="<?php echo $statCollClass ?>">
                                                <h5 class="d-lg-inline-block mr-2"><?php echo $statTitle.': ' ?></h5>
                                                <span class="badge badge-success">
                                                    <?php echo $statFirstVal?>
                                                </span> /
                                                <span class="badge badge-secondary">
                                                    <?php echo $statSecondVal?>
                                                </span>

                                            </div>
                                        <?php }
                                    } ?>
                                </div>
                                <?php //Config Area statistics end ?>

                                <?php //Main data start ?>
                                <?php $delimiter = '.'; ?>
                                <div class="input-group mb-3">
                                    <?php if (empty($configAreaData)) { ?>
                                        <h2 class="text-center">no config
                                            available</h2>
                                    <?php } else { ?>
                                        <ul class="list-group data">
                                            <?php
                                            foreach ($configAreaData as $attrName => $input) {
                                                $classLocalConfig = ($input['fromLocalConfig'])
                                                    ? ' localConfig' : '';
                                                $errorClass = ($input['validation']['error'])
                                                    ? ' validationError' : '';
                                                $validClass = isset($input['validation']['post']['value'])
                                                    ? ' validationValid' : '';
                                                ?>
                                                <li class="list-group-item <?php echo $classLocalConfig . $errorClass . $validClass ?>">
                                                    <a name="<?php echo 'option-' . $configArea . '-' . $attrName; ?>"></a>
                                                    <?php echo buildInputHTML($configArea, $attrName, $input, $delimiter); ?>
                                                </li>
                                            <?php } ?>
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
                            <button type="submit"
                                    class="btn btn-success btn-lg">Submit
                            </button>
                        </div>
                    </div>
                    <?php // Submit button end ?>

                    <input type="hidden" name="configName"
                           value="<?php echo $tmplData['configName'] ?>">
                </form>


                <h4 class="text-center"><a href="#navigation">to top</a></h4>


            <?php } else { ?>
                <h2>Empty application config data.</h2>
            <?php } ?>
        </div>
    </div>
</div>


<script src="<?php echo $tmplData['assetsUrl'] . 'js/jquery3.3.1.slim.min.js' ?>"></script>
<script src="<?php echo $tmplData['assetsUrl'] . 'js/bootstrap.min.js' ?>"></script>
<script src="<?php echo $tmplData['assetsUrl'] . 'js/main.js' ?>"></script>

</body>
</html>