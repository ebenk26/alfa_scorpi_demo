<?php
/* @var $this UserController */
/* @var $model UserModel */

$this->breadcrumbs=array(
	'User Models'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List UserModel', 'url'=>array('index')),
	array('label'=>'Create UserModel', 'url'=>array('create')),
	array('label'=>'Update UserModel', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete UserModel', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage UserModel', 'url'=>array('admin')),
);
?>

<div id="sidebar">
<?php
	$this->beginWidget('zii.widgets.CPortlet', array(
		'title'=>'Operations',
	));
	$this->widget('zii.widgets.CMenu', array(
		'items'=>$this->menu,
		'htmlOptions'=>array('class'=>'operations'),
	));
	$this->endWidget();
?>
</div><!-- sidebar -->


<h1>View User #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'username',
		'email',
		'name',
		'dealer_name',
		'phone_number',
		'role_id',
		'region_id',
		'active',
		'dealer_id',
		'created_by',
		'updated_by',
		'created_at',
		'updated_at',
		'deleted_at',
	),
)); ?>
