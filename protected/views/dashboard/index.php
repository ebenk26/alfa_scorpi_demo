<script type="text/javascript">
	$(document).ready(function () {
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

	    $('li.myLegend2').click(function () {

	        var theSeries = $(this).attr('id');
	        theSeries = theSeries.substring(6);
	        
	        var chart = $('#yw1').highcharts();
	        var series = chart.series[theSeries];
	        if (series.visible) {
	            series.hide();
	            $(this).find('i').removeClass('fa fa-dot-circle-o').addClass('fa fa-circle-o');
	        } else {
	            series.show();
	            $(this).find('i').removeClass('fa fa-circle-o').addClass('fa fa-dot-circle-o');
	        }
	    });
	});
</script>
<?php 
	$colorstock = array('448ccb', 'fbaf5d', 'acd373', 'ed1c24', 'da06d8');
    $datacolor = array();
    foreach ($leasing_json_array as $key => $value) {
        $datacolor[] = '#' . $colorstock[$key];
        // echo(substr(str_shuffle('ABCDE0123456789'), 0, 6));
    }

    foreach ($dealer_json_array as $key => $value) {
        $datacolor[] = '#' . $colorstock[$key];
        // echo(substr(str_shuffle('ABCDE0123456789'), 0, 6));
    }

	
?>
<div class="content">
	<section class="content-header">
	    <h1>
	        DASHBOARD
	    </h1>
	    <ol class="breadcrumb">
	        <li><a href="#">Home</a></li>
	        <li class="active"><span class="fa fa-dashboard"></span> Dashboard</li>
	    </ol>

	</section>
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
	                <i class="fa fa-flag"></i><h3 class="box-title">Dealer</h3><span class="campaign-list-icon" style="font-size:11px;"></span>
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
	                            'categories' => $dealer_label
	                        ),
	                        'colors' => $datacolor,
	                        'chart' => array(
	                            'type'=>array('column')
	                        ),
	                        'title' => array(
	                            'text' => ''
	                        ),
	                        'series' => $dealer_json_array,
	                        'global' => array(
	                            'useUTC' => false
	                        ),
	                        'plotOptions' => array(
					            'column'=> array(
					                'stacking'=> 'normal'
					            )
					        ),
					        'tooltip'=> array(
					            'headerFormat'=> '<b>{point.x}</b><br/>',
					            'pointFormat'=> '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
					        ),
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
	                 <?php $i=0; foreach($dealer_json_array as $key): ?>
	                    <li class="campaign-list myLegend" id="legend<?php echo $i;?>"><i class="fa fa-dot-circle-o"></i> <span class="campaign-list-icon"><?php echo $key['name'];?></span><span class="fa fa-circle" style="color:<?php echo $datacolor[$i];?>"></span></li>
	                <?php $i++; endforeach; ?>
	                </ul>
	            </div>
	            </div><!-- /.box-body -->
	        </div>
	    </div>
	</div>

    <div class="row">
	    <div class="col-lg-9 col-xs-12">
	        <div class="box for-box">
	            <div class="box-header with-border">
	                <i class="fa fa-flag"></i><h3 class="box-title">Leasing</h3><span class="campaign-list-icon" style="font-size:11px;"></span>
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
	                            'categories' => $leasing_label
	                        ),
	                        'colors' => $datacolor,
	                        'chart' => array(
	                            'type'=>array('column')
	                        ),
	                        'title' => array(
	                            'text' => ''
	                        ),
	                        'series' => $leasing_json_array,
	                        'global' => array(
	                            'useUTC' => false
	                        ),
	                        'plotOptions' => array(
					            'column'=> array(
					                'stacking'=> 'normal'
					            )
					        ),
					        'tooltip'=> array(
					            'headerFormat'=> '<b>{point.x}</b><br/>',
					            'pointFormat'=> '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
					        ),
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
	                <?php $i=0; foreach($leasing_json_array as $key): ?>
	                    <li class="campaign-list myLegend2" id="legend<?php echo $i;?>"><i class="fa fa-dot-circle-o"></i> <span class="campaign-list-icon"><?php echo $key['name'];?></span><span class="fa fa-circle" style="color:<?php echo $datacolor[$i];?>"></span></li>
	                <?php $i++; endforeach; ?>
	                </ul>
	            </div>
	            </div><!-- /.box-body -->
	        </div>
	    </div>
	</div>
</div>
