
<?php 
    $colorstock = array('448ccb', 'fbaf5d', 'acd373', 'ed1c24', 'da06d8');
    $datacolor = array();
    foreach ($json_array as $key => $value) {
        $datacolor[] = '#' . $colorstock[$key];
        // echo(substr(str_shuffle('ABCDE0123456789'), 0, 6));
    }
    // print_r($dataProvider->getData());
    // print_r($json_array);
    $data_confirm = array();
    /*foreach ($dataProvider->getData() as $value) {
        # code...
        // echo($value['winner_confirm']);
        // echo($winner_confirm_item['winner_confirm']);
        // if($value['winner_confirm'] == $winner_confirm_item['winner_confirm']){
            // $i++;
            echo "<br>";
            echo  $value['winner_confirm'];
        // }
        // echo "cumi";
    }

    foreach ($winner_confirm as $winner_confirm_item) {
        $i = 0;
        $data_confirm[$winner_confirm_item['winner_confirm']] = 0;
        foreach ($dataProvider->getData() as $value) {
            # code...
            // echo($value['winner_confirm']);
            // echo($winner_confirm_item['winner_confirm']);
            if($value['winner_confirm'] == $winner_confirm_item['winner_confirm']){
                $i++;
                // echo "<br>";
                // echo 
                $data_confirm[$winner_confirm_item['winner_confirm']] = $i;
            }
            // echo "cumi";
        }
    }
    print_r($data_confirm);*/
?>
<script>
$(document).ready(function() {
    check_role_cond();

	function cb(start, end) {
        // console.log("New date range selected: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range:  )');
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        // $('#date-filter').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
    
    }
    // cb(moment().subtract(29, 'days'), moment());
    cb(moment('<?php echo $date_range["from"];?>'), moment('<?php echo $date_range["to"];?>'));

    // $('#reportrange span').html('<?php echo $date_range["from"];?> - <?php echo $date_range["to"];?>');

    $('#reportrange').daterangepicker({
        "showDropdowns": true,
        "autoApply": true,
        "ranges": {
            "Today": [
                moment(), moment()
            ],
            "Yesterday": [
                moment().subtract( 1, 'days' ), moment().subtract( 1, 'days' )
            ],
            "Last 7 Days": [
                moment().subtract( 6, 'days' ), moment()
            ],
            "Last 30 Days": [
                moment().subtract( 29, 'days' ), moment()
            ],
            "This Month": [
                moment().startOf('month'), moment().endOf('month')
            ],
            "Last Month": [
                moment().subtract( 1, 'month' ).startOf('month'), moment().subtract( 1, 'month' ).endOf('month')
            ]
        },
        /*"startDate": moment().subtract(29, 'days'),
        "endDate": moment(),*/
        "opens": "left"
    }, cb);
    $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
        var firstyear = picker.startDate.format('YYYY-MM-DD');
        var lastyear = picker.endDate.format('YYYY-MM-DD');
       
        $.ajax({
            type: 'POST',
            data: { 
                'fromdate': firstyear, 
                'todate': lastyear // <-- the $ sign in the parameter name seems unusual, I would avoid it
            },
            success: function(data) {
                // $.fn.yiiGridView.update("data-bidding-grid");   
                // $('#data-bidding-grid').yiiGridView('update');
                location.reload();  
                // console.log(data);
                // var chart = $ ( '#yw0' ). highcharts (); 
                // chart . series [ 0 ]. setData ( <?php echo $json_array;?> ); 
            },
            error: function (xhr, status) {
                alert("Sorry");
            },
        });
    });
    // console.log(moment());
    /*$.ajax({
        type: 'POST',
        data: { 
            'from_date': moment().subtract(29, 'days').format('YYYY-MM-DD'), 
            'to_date': moment().format('YYYY-MM-DD') 
        },
       
        success: function(data) {
            // $.fn.yiiGridView.update("data-bidding-grid");  
            // console.log(data);
            $('#data-bidding-grid').yiiGridView('update');
        },
        error: function (xhr, status) {
            alert("Sorry");
        },
    });*/

    $('li.myLegend').click(function () {

        var theSeries = $(this).attr('id');
        theSeries = theSeries.substring(6);
        
        var chart = $('#yw0').highcharts();
        var series = chart.series[theSeries];
        if (series.visible) {
            series.hide();
            $(this).find('i').removeClass('fa fa-dot-circle-o').addClass('fa fa-circle-o');
        } else {
            series.show();
            $(this).find('i').removeClass('fa fa-circle-o').addClass('fa fa-dot-circle-o');
        }
    });

    $(".select2").select2();
    $("#filter_leasing").select2({
        ajax: {
            url: "<?php echo Yii::app()->createUrl('/databidding/listLeasingAjax'); ?>",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: function (params) {
              return {
                q: params.term, // search term
                page: params.page
              };
            },
            processResults: function (data, params) {
              // parse the results into the format expected by Select2
              // since we are using custom formatting functions we do not need to
              // alter the remote JSON data, except to indicate that infinite
              // scrolling can be used
              params.page = params.page || 1;
              console.log(data);
              return {
                results: data,
                pagination: {
                  more: (params.page * 30) < data.total_count
                }
              };
            },
            cache: true
        },
        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 1,
        templateResult: formatState, // omitted for brevity, see the source of this page
        templateSelection: formatSelection // omitted for brevity, see the source of this page
    });

    $(".btn-filter").on("click", function (e) 
    {
        // console.log($('.select2').val()); 
        var region_val = $('#filter_region').val();
        var status_val = $('#filter_status').val();
        var leasing_val = $('#filter_leasing').val();
        // console.log(region_val);
        // console.log(status_val);
        // console.log(leasing_val);

        // if(region_val != '' || status_val!= ''){
            $.ajax({
                type: 'POST',
                data: { 
                    'filter_region': region_val, 
                    'filter_status': status_val, // <-- the $ sign in the parameter name seems unusual, I would avoid it
                    'filter_leasing': leasing_val, // <-- the $ sign in the parameter name seems unusual, I would avoid it
                    'fromdate': '<?php echo $date_range["from"];?>', 
                    'todate': '<?php echo $date_range["to"];?>'
                },
                success: function(data) {
                    // $.fn.yiiGridView.update("data-bidding-grid");   
                    $('#data-bidding-grid').yiiGridView('update');
                    // check_role_cond();
                    // location.reload();  
                    // console.log(data);
                    // var chart = $ ( '#yw0' ). highcharts (); 
                    // chart . series [ 0 ]. setData ( <?php echo $json_array;?> ); 
                },
                error: function (xhr, status) {
                    alert("Sorry something wrong.");
                },
            });
        // }
    });

    $('.btn-clear').click(function (argument) {
        // body...
        $(".select2, #filter_leasing").val(null).trigger("change");
        // $(".select2, #filter_leasing").trigger("select2:select");
    })



});

function formatState (state) {
    // console.log(state);
  if (!state.id) { return state.text; }
  var $state = $(
    '<span>' + state.text + '</span>'
  );
  return $state;
};

function formatSelection (repo) {
  return repo.text;
}
</script>
<div class="content">
<section class="content-header">
    <h1>
        BIDDING
    </h1>
    <ol class="breadcrumb">
        <li><a href="#">Home</a></li>
        <li class="active"><span class="fa fa-envelope"></span> Data Bidding</li>
    </ol>

</section>
<?php
/* @var $this DatabiddingController */

$this->breadcrumbs=array(
	'Databidding',
);

$this->beginWidget('zii.widgets.jui.CJuiDialog',array(
    'id'=>'mydialog',
    // additional javascript options for the dialog plugin
    'options'=>array(
        'title'=>'Detail Data Bidding',
        'autoOpen'=>false,
		'width'=> '50%',
		'height' => '600',
    ),
));
$this->endWidget('zii.widgets.jui.CJuiDialog');
$this->beginWidget('zii.widgets.jui.CJuiDialog',array(
    'id'=>'mydialog2',
    // additional javascript options for the dialog plugin
    'options'=>array(
        'title'=>'Detail Data Prospect',
        'autoOpen'=>false,
		'width'=> '50%',
		'height' => '600',
    ),
));
   // echo 'dialog content here';

$this->endWidget('zii.widgets.jui.CJuiDialog');
$this->beginWidget('zii.widgets.jui.CJuiDialog',array(
    'id'=>'mydialog3',
    // additional javascript options for the dialog plugin
    'options'=>array(
        'title'=>'Comment',
        'autoOpen'=>false,
		'width'=> '50%',
		'height' => '600',
    ),
));
$this->endWidget('zii.widgets.jui.CJuiDialog');

// the link that may open the dialog
/* echo CHtml::link('open dialog', '#', array(
   'onclick'=>'$("#mydialog").dialog("open"); return false;',
)); */
?>

<!-- Small boxes (Stat box) -->
<div class="row">
    <div class="col-md-3" style="float: right; margin-bottom:20px;">
        <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
            <span></span> <b class="caret"></b>
        </div>
        <!-- <form>
        <div class="input-group">
          <input type="text" class="form-control"  id="date-filter">
          <span class="input-group-addon" style="background-color: #cccccc"><span class="caret"></span></span>
        </div>
        </form> -->
    </div>
</div>

<div class="row">
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3><?php echo $totConfirm; ?></h3>
                    <p>Confirm</p>
                </div>
                <div class="icon">
                    <span class="fa fa-check"></span>
                </div>
                <a href="javascript:void(0);" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                <!-- <a href="" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
            </div>
        </div><!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green">
                <div class="inner">
                    <h3><?php echo $totNofeedback; ?></h3>
                    <p>No Feedback</p>
                </div>
                <div class="icon">
                    <span class="fa fa-times-circle-o"></span>
                </div>
                <a href="javascript:void(0);" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                <!-- <a href="" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-red">
                <div class="inner">
                    <h3><?php echo $totApprove; ?></h3>
                    <p>Approve</p>
                </div>
                <div class="icon">
                    <span class="fa fa-check-square-o"></span>
                </div>
                <a href="javascript:void(0);" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                <!-- <a href="" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
            </div>
        </div><!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-orange">
                <div class="inner">
                    <h3><?php echo ($totReject+$totCancel); ?></h3>
                    <p>Reject & Cancel</p>
                </div>
                <div class="icon">
                    <span class="fa fa-times"></span>
                </div>
                <a href="javascript:void(0);" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                <!-- <a href="" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
            </div>
        </div><!-- ./col -->
    </div><!-- /.row -->

<div class="row">
    <div class="col-lg-9 col-xs-12">
        <div class="box for-box">
            <div class="box-header with-border">
                <i class="fa fa-flag"></i><h3 class="box-title">Prospect</h3><span class="campaign-list-icon" style="font-size:11px;"></span>
            </div>
            <?php
            // $data2 = '[[1452229200000,12]';
            // $time = 1452229200000;
            // $min=1;
            // $max=2000;
            //     for ($i = 1; $i <= 1000; $i++) {
            //         $time = $time + 900000;
            //         $data2 .= ',['.$time.','.rand($min,$max).']';
            //     }
            // $data2 .= ']';
            // echo $data2;
            ?>

            <?php 
            // if(DashboardController::roles_superuser() != true) {
               
                //HighchartsWidget
                $this->Widget('ext.highcharts.highcharts.HighchartsWidget', array(
                    'options' => array(
                        'xAxis' => array(
                            'categories' => $region_label
                        ),
                        'colors' => $datacolor,
                        'chart' => array(
                            'type'=>array('column')
                        ),
                        'title' => array(
                            'text' => ''
                        ),
                        'series' => $json_array,
                        'global' => array(
                            'useUTC' => false
                        )
                    ),
                    
                    
                ));
            // }
            ?>
        </div>
    </div>
    <div class="col-lg-3 col-xs-12">
        <div class="box for-box">
            <div class="box-header with-border">
                <h3 class="box-title">Status</h3>
                <div class="box-tools">
                    
                </div>
            </div>
            <div class="box-body">
            <div class="direct-chat-messages">
                <ul class="nav nav-pills nav-stacked">
                 <?php $i=0; foreach($json_array as $key): ?>
                    <li class="campaign-list myLegend" id="legend<?php echo $i;?>"><i class="fa fa-dot-circle-o"></i> <span class="campaign-list-icon"><?php echo $key['name'];?></span><span class="fa fa-circle" style="color:<?php echo $datacolor[$i];?>"></span></li>
                <?php $i++; endforeach; ?>
                </ul>
            </div>
            </div><!-- /.box-body -->
        </div>
    </div>
</div>
<div class="row header-2">
    <div class="col-md-2"><h4>BIDDING</h4></div>
</div>
<div class="row">
    <div class="col-md-8">
      <div class="form-group">
        <select id="filter_region" name="filter_region" class="form-control select2" style="width: 25%;">
          <option value="">Region</option>
          <?php 
          foreach ($region as $value) {
            echo "<option value='".$value['id']."'>".$value['name']."</option>";
          }
          ?>
        </select>
        <select id="filter_leasing" name="filter_leasing" class="form-control " style="width: 25%;">
          <option value="">Nama Leasing</option>
          
        </select>
        <select id="filter_status" name="filter_status" class="form-control select2" style="width: 25%;">
          <option value="">Status Prospect</option>
          <option value="Confirmed">Confirmed</option>
          <option value="No feedback">No feedback</option>
          <option value="Approved">Approved</option>
          <option value="Rejected">Rejected</option>
          <option value="Cancel">Cancel</option>
        </select>
        <button class="btn btn-default btn-clear">Clear</button>
        <button class="btn btn-primary btn-filter">Filter</button>
      </div>
    </div>
    <div class="col-md-4">
        <?php echo CHtml::link('<span class="fa fa-download"></span> Excell',array("downloadmonbrand"),array('class'=>'btn pull-right btn-success btn-excell',)); ?>
    </div>
</div>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'data-bidding-grid',
	'dataProvider'=>$dataProvider,
	 'afterAjaxUpdate'=>"function() {  
       check_role_cond();
    }", 
	// 'filter'=>$model,
	'ajaxUpdate' => true,
	'columns'=>array(
		array(
			'header'=> 'No.',
			'value' => '$this->grid->dataProvider->pagination->currentPage * $this->grid->dataProvider->pagination->pageSize + ($row+1)',
			'htmlOptions'=>array('style'=>'text-align: center;'),
		),
		'id' => array(
        			'name' => 'id',
        			'header' => 'Case ID',
        			'value' => 'date("m")."-".$data["id"]',
        			'type' => 'raw',
					'headerHtmlOptions' => array(
							'style' => 'vertical-align:middle;',
						),	
        		),
		'last_case_id' => array(
        			'name' => 'last_case_id',
        			'header' => 'Last Case ID',
        			'value' => '$data["last_case_id"]',
        			'type' => 'raw',
					'headerHtmlOptions' => array(
							'style' => 'vertical-align:middle;',
						),	
        		),
		'prospect' => array(
        			'name' => 'prospect',
        			// 'header' => 'Prospect',
        			// 'value' => '$data["prospect"]',
        			// 'type' => 'raw',
					// 'headerHtmlOptions' => array(
							// 'style' => 'vertical-align:middle;',
						// ),	
					'value' => '
						CHtml::link("$data[prospect]","#", array("onClick"=>"$.ajax({
							type: `POST`,
							url: `/alfa_scorpi/index.php/viewprospect/viewDataprospect?id=`+`$data[id]`,
							success: function(html){ var obj = jQuery.parseJSON( html );jQuery(`#mydialog2`).dialog(`open`).html(obj.content);return false; },
						})"))
						',
					'type' => 'raw',
					'htmlOptions' => array(
							'style' => 'padding-left: 12px;',
						),
					'header' => 'Prospect',
					'headerHtmlOptions' => array(
								'style' => 'vertical-align:middle;',
							),
        		),
		'region' => array(
        			'name' => 'region',
        			'header' => 'Region',
        			'value' => '$data["region"]',
        			'type' => 'raw',
					'headerHtmlOptions' => array(
							'style' => 'vertical-align:middle;',
						),	
        		),
		'leasing_terlibat' => array(
        			'name' => 'leasing_terlibat',
        			'header' => 'Leasing Terlibat',
        			// 'value' => '$data["leasing_terlibat"]',
					'value'  => 'Common::model()->Getleasingterlibat($data["leasing_terlibat"])',
        			'type' => 'raw',
					'headerHtmlOptions' => array(
							'style' => 'vertical-align:middle;',
						'class' => 'visible',
						),	
					'htmlOptions' => array(
							'class' => 'visible',
						),
        		),
		'sumber_order' => array(
        			'name' => 'sumber_order',
        			'header' => 'Sumber Order',
        			'value' => '$data["sumber_order"]',
        			'type' => 'raw',
					'headerHtmlOptions' => array(
							'style' => 'vertical-align:middle;',
							'class' => 'visible',
						),	
					'htmlOptions' => array(
							'class' => 'visible',
						),
        		),
		'nama_salesman' => array(
        			'name' => 'nama_salesman',
        			'header' => 'Nama Salesman',
        			'value' => '$data["nama_salesman"]',
        			'type' => 'raw',
					'headerHtmlOptions' => array(
							'style' => 'vertical-align:middle;',
							'class' => 'visible2',
						),	
					'htmlOptions' => array(
							'class' => 'visible2',
						),
        		),
		'time_sent_order' => array(
        			'name' => 'time_sent_order',
        			'header' => 'Time Sent Order',
        			'value' => '$data["time_sent_order"]',
        			'type' => 'raw',
					'headerHtmlOptions' => array(
							'style' => 'vertical-align:middle;',
						),	
        		),
		'pemenang' => array(
        			'name' => 'pemenang',
        			'header' => 'Pemenang',
        			'value' => '$data["pemenang"]',
        			'type' => 'raw',
					'headerHtmlOptions' => array(
							'style' => 'vertical-align:middle;',
							'class' => 'visible',
						),	
					'htmlOptions' => array(
							'class' => 'visible',
						),
        		),
		/* 'time' => array(
        			'name' => 'time',
        			'header' => 'Time',
        			'value' => '$data["time"]',
        			'type' => 'raw',
					'headerHtmlOptions' => array(
							'style' => 'vertical-align:middle;',
						),	
        		), */
		'time_confirm' => array(
        			'name' => 'time_confirm',
        			'header' => 'Time Confirm',
        			'value' => '$data["time_confirm"]',
        			'type' => 'raw',
					'headerHtmlOptions' => array(
							'style' => 'vertical-align:middle;',
						),	
        		),
		'time_approve' => array(
        			'name' => 'time_approve',
        			'header' => 'Time Approve / Reject',
        			'value' => '$data["time_approve"]',
        			'type' => 'raw',
					'headerHtmlOptions' => array(
							'style' => 'vertical-align:middle;',
						),	
        		),
		'durasi' => array(
        			'name' => 'durasi',
        			'header' => 'Durasi',
        			'value' => '$data["durasi"]',
        			'type' => 'raw',
					'headerHtmlOptions' => array(
							'style' => 'vertical-align:middle;',
						),	
        		),
		'winner_confirm' => array(
        			'name' => 'winner_confirm',
        			'header' => 'Winner Confirm',
        			'value' => '$data["winner_confirm"]',
        			'type' => 'raw',
					'headerHtmlOptions' => array(
							'style' => 'vertical-align:middle;',
						),	
        		),
        /*'winner_confirm_id' => array(
                    'name' => 'winner_confirm_id',
                    'header' => 'Winner Confirm ID',
                    'value' => '$data["winner_confirm_id"]',
                    'type' => 'raw',
                    'headerHtmlOptions' => array(
                            'style' => 'vertical-align:middle;',
                        ),  
                ),*/
		'role_name' => array(
        			'name' => 'role_name',
        			'header' => 'Last Comment By',
        			'value' => '$data["role_name"]',
        			'type' => 'raw',
					'headerHtmlOptions' => array(
							'style' => 'vertical-align:middle;',
						),	
        		),
		'action' => array(
					// 'name' => 'Action',
					'value' => 'CHtml::link(CHtml::image("/alfa_scorpi/images/icon-view.png"),"#", array("onClick"=>"$.ajax({
                            type: `POST`,
                            url: `/alfa_scorpi/index.php/databidding/viewDatabidding?id=`+`$data[id]`,
                            success: function(html){ var obj = jQuery.parseJSON( html );jQuery(`#mydialog`).dialog(`open`).html(obj.content);return false; },
                        })"))',
					'type' => 'raw',
					'header' => 'Action',
					// 'type' => 'number',
					'htmlOptions' => array(
							// 'width' => '20',
							'style' => 'text-align:right;vertical-align:middle;',
							'class' => 'visible',
                            'colspan' => 2,
						),
					'headerHtmlOptions' => array(
							'colspan' => 2,
							'rowspan' => 2,
							'style' => 'padding:0px;',
							'class' => 'visible',
						),	

				),
		'comment' => array(
					'value' => '
								CHtml::link(CHtml::image("/alfa_scorpi/images/update.png"),"#", array("onClick"=>"$.ajax({
							type: `POST`,
							url: `/alfa_scorpi/index.php/databidding/viewComment?id=`+`$data[id]`,
							success: function(html){ var obj = jQuery.parseJSON( html );jQuery(`#mydialog3`).dialog(`open`).html(obj.content);return false; },
						})"))
								',
					'header' => false,
					'type' => 'raw',
					'htmlOptions' => array(
							'width' => '20',
							'style' => 'text-align:right;vertical-align:middle;',
							'class' => 'visible3',
                            // 'colspan' => 2,
						),
					'headerHtmlOptions' => array(
							'style' => "display:none;",
							'class' => 'visible3',
						),	
				),
	),
	)
);
?>
<script type="text/javascript">
function check_role_cond(){
        <?php if (Yii::app()->session['roleid'] == 3) { ?>

        	$('.visible').css('display','none');

        <?php }else{ ?>
        	
        		$('.visible2').css('display','none');
        	
        <?php	} 
        ?>
        <?php if (Yii::app()->session['roleid'] == 4 || Yii::app()->session['roleid'] == 1) { ?>

        	$('.visible3').css('display','none');

        <?php }
        ?>
        <?php if (Yii::app()->session['roleid'] == 4) { ?>

        	$('.btn-excell').css('display','none');

        <?php }
        ?>
}
</script>
</div>