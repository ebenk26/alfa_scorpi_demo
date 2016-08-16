<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="language" content="en">

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection">
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print">
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection">
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css">
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css">
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap-datetimepicker.min.css">


	<?php  
      $baseUrl = Yii::app()->baseUrl; 
      $cs = Yii::app()->getClientScript();
      
      
      $cs->registerCssFile($baseUrl.'/ext/adminlte/bootstrap/css/bootstrap.min.css');
      $cs->registerCssFile($baseUrl.'/ext/adminlte/dist/css/AdminLTE.min.css');
      $cs->registerCssFile($baseUrl.'/css/ionicons.min.css');
      $cs->registerCssFile($baseUrl.'/ext/font-awesome-4.4.0/css/font-awesome.min.css');
      $cs->registerCssFile($baseUrl.'/ext/adminlte/dist/css/skins/_all-skins.min.css');
      $cs->registerCssFile($baseUrl.'/ext/adminlte/plugins/iCheck/flat/blue.css');
      $cs->registerCssFile($baseUrl.'/ext/adminlte/plugins/morris/morris.css');
      $cs->registerCssFile($baseUrl.'/ext/adminlte/plugins/jvectormap/jquery-jvectormap-1.2.2.css');
      $cs->registerCssFile($baseUrl.'/ext/adminlte/plugins/datepicker/datepicker3.css');
      $cs->registerCssFile($baseUrl.'/ext/adminlte/plugins/daterangepicker/daterangepicker-bs3.css');
      $cs->registerCssFile($baseUrl.'/ext/adminlte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css');
      $cs->registerCssFile($baseUrl.'/ext/swiper/dist/css/swiper.min.css');
      $cs->registerCssFile($baseUrl.'/ext/daterangepicker/daterangepicker.css');
      $cs->registerCssFile($baseUrl.'/ext/adminlte/plugins/select2/select2.min.css');
      $cs->registerCssFile($baseUrl.'/css/custom.css');

      $cs->registerCoreScript('jquery');
    ?>


	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	
</head>

<body class="hold-transition skin-blue fixed sidebar-mini">

<div class="wrapper">

	<!-- <div id="header">
		<div id="logo"><img src="/alfa_scorpi/images/header.png"></div>
	</div> header -->
	<header class="main-header">
        <!-- Logo -->
        <a href="<?php echo Yii::app()->createUrl('dashboard/dashboard'); ?>" class="logo">
          <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/logo-alfascorpi.png">
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
          </a>
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
              <!-- User Account: style can be found in dropdown.less -->
              <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <img src="<?php echo Yii::app()->baseUrl; ?>/ext/adminlte/dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
                  <!-- <span class="hidden-xs">Alexander Pierce</span> -->
                  <span class="hidden-xs">
                    <?php 
                    	echo Yii::app()->user->name;
                    ?>
                  </span>
                </a>
                <ul class="dropdown-menu">
                  <!-- User image -->
                  <li class="li-li"><a href="<?php echo Yii::app()->createUrl('databidding'); ?>" class="btn-block btn-flat"><img src="<?php echo Yii::app()->baseUrl."/images/myprofile.png"; ?>" class="user-li">My Account</a>
                  </li>
                  <li class="li-li-notop"><a href="<?php echo Yii::app()->createUrl('site/logout'); ?>" class="btn-block btn-flat"><img src="<?php echo Yii::app()->baseUrl."/images/logout.png"; ?>" class="user-li">Logout</a>
                  </li>
                </ul>
              </li>
              <!-- Control Sidebar Toggle Button -->
              <!-- <li>
                <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
              </li> -->
            </ul>
          </div>
        </nav>
      </header>
       <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">
	        <!-- sidebar: style can be found in sidebar.less -->

	        <section class="sidebar">
	          <!-- Sidebar user panel -->
	          <div class="user-panel">
	            <div class="pull-left image">
	              <img src="<?php echo Yii::app()->baseUrl; ?>/ext/adminlte/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
	            </div>
	            <div class="pull-left info">
	              <p><a href="<?php echo Yii::app()->createUrl('/site/profile'); ?>"><?php echo Yii::app()->user->name; ?></a></p>
	              <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
	            </div>
	          </div>

	          <ul class="sidebar-menu">
	            <li class="header">MAIN NAVIGATION</li>

	            <li class="treeview">    
	                <a href="<?php echo Yii::app()->createUrl('/dashboard'); ?>">
	                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
	                </a>
	            </li>
	            <li class="treeview">    
	                <a href="javascript:void(0)">
	                    <i class="fa fa-envelope"></i> <span>All</span>
	                </a>
	                <ul class="nav treeview-menu" style="display: none;">
	                    <li>
	                    	<a href="<?php echo Yii::app()->createUrl('/databidding'); ?>">
		                      <i class="margin-treeview fa fa-circle"></i> <span>Bidding</span> <i class="fa"></i>
		                    </a>
		                </li>
		                <li>
	                    	<a href="<?php echo Yii::app()->createUrl('/leasing'); ?>">
		                      <i class="margin-treeview fa fa-circle"></i> <span>Leasing</span> <i class="fa"></i>
		                    </a>
		                </li>
		                <?php if(Yii::app()->session['roleid'] != 2){?>
		                <li>
	                    	<a href="<?php echo Yii::app()->createUrl('/dealer'); ?>">
		                      <i class="margin-treeview fa fa-circle"></i> <span>Dealer</span> <i class="fa"></i>
		                    </a>
		                </li>
		                <?php }?>
		                <?php if(Yii::app()->session['roleid'] != 2){?>
		                <li>
	                    	<a href="<?php echo Yii::app()->createUrl('/salesman'); ?>">
		                      <i class="margin-treeview fa fa-circle"></i> <span>Salesman</span> <i class="fa"></i>
		                    </a>
		                </li>
		                <?php }?>
		                <?php if(Yii::app()->session['roleid'] == 1){?>
		                <li>
	                    	<a href="<?php echo Yii::app()->createUrl('/user'); ?>">
		                      <i class="margin-treeview fa fa-circle"></i> <span>Users</span> <i class="fa"></i>
		                    </a>
		                </li>
		                <?php }?>
	                </ul>
	            </li>
	           </ul>
	      </section>
	  </aside>

	<<!-- div id="mainmenu">
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'Home', 'url'=>array('/site/index'),'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Data Bidding', 'url'=>array('/databidding'),'visible'=>!Yii::app()->user->isGuest),
				// array('label'=>'About', 'url'=>array('/site/page', 'view'=>'about')),
				// array('label'=>'Contact', 'url'=>array('/site/contact')),
				array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
				array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest)
			),
		)); ?>
	</div>mainmenu -->
	<?php if(!isset($this->breadcrumbs)):?>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif?>

	<div class="content-wrapper">
		<?php echo $content; ?>
	</div>

	<div class="clear"></div>

	<div id="footer">
		Copyright &copy; <?php echo date('Y'); ?> by This My Company.<br/>
		All Rights Reserved.<br/>
		<?php echo Yii::powered(); ?>
	</div><!-- footer -->

</div><!-- page -->
	
	<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap-datetimepicker.js" type="text/javascript"></script>
	<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/main.js" type="text/javascript"></script>
	
      <script src="<?php echo Yii::app()->request->baseUrl; ?>/ext/adminlte/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
      <script src="<?php echo Yii::app()->request->baseUrl; ?>/ext/adminlte/plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>
      <script src="<?php echo Yii::app()->request->baseUrl; ?>/ext/adminlte/plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
      <script src="<?php echo Yii::app()->request->baseUrl; ?>/ext/adminlte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
      <script src="<?php echo Yii::app()->request->baseUrl; ?>/ext/adminlte/plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
      <script src="<?php echo Yii::app()->request->baseUrl; ?>/ext/adminlte/plugins/fastclick/fastclick.min.js" type="text/javascript"></script>
      <script src="<?php echo Yii::app()->request->baseUrl; ?>/ext/adminlte/plugins/morris/morris.min.js" type="text/javascript"></script>
      <script src="<?php echo Yii::app()->request->baseUrl; ?>/ext/adminlte/plugins/sparkline/jquery.sparkline.min.js" type="text/javascript"></script>
      <script src="<?php echo Yii::app()->request->baseUrl; ?>/ext/adminlte/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js" type="text/javascript"></script>
      <script src="<?php echo Yii::app()->request->baseUrl; ?>/ext/adminlte/plugins/jvectormap/jquery-jvectormap-world-mill-en.js" type="text/javascript"></script>
      <script src="<?php echo Yii::app()->request->baseUrl; ?>/ext/adminlte/plugins/knob/jquery.knob.js" type="text/javascript"></script>
      <script src="<?php echo Yii::app()->request->baseUrl; ?>/ext/adminlte/dist/js/app.min.js" type="text/javascript"></script>
      <script src="<?php echo Yii::app()->request->baseUrl; ?>/ext/swiper/dist/js/swiper.min.js" type="text/javascript"></script>
      <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/moment.min.js" type="text/javascript"></script>
      <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/raphael-min.js" type="text/javascript"></script>
      <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.confirm.js" type="text/javascript"></script>
      <script src="<?php echo Yii::app()->request->baseUrl; ?>/ext/daterangepicker/daterangepicker.js" type="text/javascript"></script>
      <script src="<?php echo Yii::app()->request->baseUrl; ?>/ext/adminlte/plugins/select2/select2.full.min.js" type="text/javascript"></script>

</body>
</html>
