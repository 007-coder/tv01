<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="author"
          content="Yura Vakulenko | vakulenkoyura211@gmail.com | https://t.me/yura_v_007">
    <title>Error page. Application config form</title>

    <link href="<?php echo $tmplData['assetsUrl'] . 'css/bootstrap.min.css' ?>"
          rel="stylesheet"/>
    <link href="<?php echo $tmplData['assetsUrl'] . 'css/style.css' ?>"
          rel="stylesheet"/>

</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-content-center">
            <div class="col-sm-6 col-md-7 mt-lg-5">
                <div class="alert alert-danger" role="alert">
                    <h4 class="alert-heading mb-3">
                        Error!
                        <a href="<?php echo $tmplData['baseUrl'] ?>" class="ml-2 h6">To home page</a>
                    </h4>
                    <hr>

                    <?php
                    if (count($tmplData['renderErrors'])) {
                        foreach ($tmplData['renderErrors'] as $errorType => $errorMessage) {?>
                            <p class="mb-2">
                                <?php echo $errorMessage ?>
                            </p>
                        <?php } ?>
                    <?php } ?>
                </div>

            </div>
        </div>
    </div>



    <script src="<?php echo $tmplData['assetsUrl'] . 'js/jquery3.3.1.slim.min.js' ?>"></script>
    <script src="<?php echo $tmplData['assetsUrl'] . 'js/bootstrap.min.js' ?>"></script>
    <script src="<?php echo $tmplData['assetsUrl'] . 'js/main.js' ?>"></script>
</body>
</html>