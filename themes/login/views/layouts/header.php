<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="language" content="en">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection">
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print">

    <?php  
      $baseUrl = Yii::app()->baseUrl; 
      $cs = Yii::app()->getClientScript();
      $cs->registerScriptFile($baseUrl.'/ext/adminlte/bootstrap/js/bootstrap.min.js');

      
      $cs->registerCssFile($baseUrl.'/ext/adminlte/dist/css/AdminLTE.min.css');
      $cs->registerCssFile($baseUrl.'/ext/adminlte/dist/css/skins/_all-skins.min.css');
      $cs->registerCssFile($baseUrl.'/ext/adminlte/bootstrap/css/bootstrap.min.css');
      $cs->registerCssFile($baseUrl.'/css/main.css');
      $cs->registerCssFile($baseUrl.'/css/form.css');
      $cs->registerCssFile($baseUrl.'/css/custom.css');
      $cs->registerCssFile($baseUrl.'/css/login-custom.css');
    ?>

	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection">
	<![endif]-->

	
  <style>
    html, body{
      height: 100%;
    }
    .login-box-body-header{
      background: #1d2832; 
      padding: 10px;
    }
  </style>

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body class="login-page">
     <div></div>