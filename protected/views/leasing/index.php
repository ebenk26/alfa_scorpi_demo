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

	    /*
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
	    });*/

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
		                    $('#data-table-grid').yiiGridView('update');
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
<?php 

	$colorstock = array('448ccb', 'fbaf5d', 'acd373', 'ed1c24', 'da06d8');
    $datacolor = array();
    foreach ($win_result_array as $key => $value) {
        $datacolor[] = '#' . substr(str_shuffle('ABCDE0123456789'), 0, 6);
        // echo(substr(str_shuffle('ABCDE0123456789'), 0, 6));
    }
    /*foreach ($leasing_json_array as $key => $value) {
        $datacolor[] = '#' . $colorstock[$key];
        // echo(substr(str_shuffle('ABCDE0123456789'), 0, 6));
    }

    foreach ($dealer_json_array as $key => $value) {
        $datacolor[] = '#' . $colorstock[$key];
        // echo(substr(str_shuffle('ABCDE0123456789'), 0, 6));
    }*/

	$this->beginWidget('zii.widgets.jui.CJuiDialog',array(
	    'id'=>'mydialog',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>'Detail Data Winning',
	        'autoOpen'=>false,
			'width'=> '50%',
			'height' => '600',
	    ),
	));
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>


<div class="content">
	<section class="content-header">
	    <h1>
	        LEASING
	    </h1>
	    <ol class="breadcrumb">
	        <li><a href="#">Home</a></li>
	        <li class="active"><span class="fa fa-dashboard"></span> Leasing</li>
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
	    <div class="col-lg-12 col-xs-12">
	        <div class="box for-box">
	            <div class="box-header with-border">
	                <i class="fa fa-flag"></i><h3 class="box-title">Leasing Teraktif</h3><span class="campaign-list-icon" style="font-size:11px;"></span>
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
	                        'plotOptions' => array(
					            'column'=> array(
					                'stacking'=> 'normal'
					            )
					        ),
	                        'series' => $win_result_array,
	                        'global' => array(
	                            'useUTC' => false
	                        )
	                    ),
	                    
	                    
	                ));
	            // }
	            ?>
	        </div>
	    </div>
	    
	</div>
	<div class="row">
	    <div class="col-lg-12 col-xs-12">
	        <div class="box for-box">
	            <div class="box-header with-border">
	                <i class="fa fa-clock"></i><h3 class="box-title">DURASI SURVEY LEASING</h3><span class="campaign-list-icon" style="font-size:11px;"></span>
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
	                    	'legend' => array(
	                    		'labelFormat' => 'Durasi rata-rata'
	                    		),
	                        'xAxis' => array(
	                            'categories' => $leasing_dur_label
	                        ),
	                        yAxis => array( //--- Primary yAxis
							    title => array (
							        text => 'Minutes'
							    )
							),
	                        // 'colors' => $datacolor,
	                        'chart' => array(
	                            'type'=>array('column')
	                        ),
	                        'title' => array(
	                            'text' => ''
	                        ),
	                        
	                        'series' => array(array(
	                        	'data' => $leasing_val_array
	                        )),
	                        'global' => array(
	                            'useUTC' => false
	                        ),
	                        'tooltip'=> array(
					            'headerFormat'=> '<b>{point.x}</b><br/>',
					            'pointFormat'=> 'Durasi rata-rata: {point.y} minutes<br/>Total prospect : {point.totPros}'
					        ),
	                    ),
	                    
	                    
	                ));
	            // }
	            ?>
	        </div>
	    </div>
	    
	</div>
	<div class="row">
	    <div class="col-md-6">
		    <div class="form-group">
		        <select id="filter_region" name="filter_region" class="form-control select2" style="width: 30%;">
		          <option value="">Region</option>
		          <?php 
		          foreach ($region as $value) {
		            echo "<option value='".$value['id']."'>".$value['name']."</option>";
		          }
		          ?>
		        </select>
		        <select id="filter_leasing" name="filter_leasing" class="form-control " style="width: 30%;">
		          <option value="">Nama Leasing</option>
		        </select>
		        <button class="btn btn-default btn-clear">Clear</button>
        		<button class="btn btn-primary btn-filter">Filter</button>
	    	</div>
		</div>
		<div class="col-md-6">
		    <?php echo CHtml::link('<span class="fa fa-download"></span> Excell',array("downloadleasingstat"),array('class'=>'btn pull-right btn-success btn-excell',)); ?>
		  </div>
	</div>
	<div class="row">
		<?php 
			$this->widget('zii.widgets.grid.CGridView', array(
				'id' => 'data-table-grid',
			    'dataProvider'=>$dataProvider,
			    'ajaxUpdate' => true,
				'columns'=>array(
					array(
						'header'=> 'No.',
						'value' => '$this->grid->dataProvider->pagination->currentPage * $this->grid->dataProvider->pagination->pageSize + ($row+1)',
						'htmlOptions'=>array('style'=>'text-align: center;'),
					),
					'leasing_name' => array(
	        			'name' => 'leasing_name',
	        			'header' => 'Leasing',
	        			'value' => '$data["leasing_name"]',
	        			'type' => 'raw',
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
	        		'total_prospect' => array(
	        			'name' => 'total_prospect',
	        			'header' => 'Total Prospect',
	        			'value' => '$data["total_prospect"]',
	        			'type' => 'raw',
						'headerHtmlOptions' => array(
								'style' => 'vertical-align:middle;',
							),	
	        		),
	        		'terlibat' => array(
	        			'name' => 'terlibat',
	        			'header' => 'Terlibat',
	        			'value' => '$data["terlibat"]',
	        			'type' => 'raw',
						'headerHtmlOptions' => array(
								'style' => 'vertical-align:middle;',
							),	
	        		),
	        		'menang' => array(
	        			'name' => 'menang',
	        			'header' => 'Menang',
	        			// 'value' => '$data["menang"]',
	        			'value' => '
								CHtml::link($data["menang"],"javascript:void(0)", array("onClick"=>"$.ajax({
							type: `POST`,
							url: `".Yii::app()->request->baseUrl."/index.php/leasing/viewDataWinning?id=`+`$data[leasing_id]`,
							success: function(html){ var obj = jQuery.parseJSON( html );$(`#mydialog`).dialog(`open`).html(obj.content);return false; },
						})"))
								',
	        			'type' => 'raw',
						'headerHtmlOptions' => array(
								'style' => 'vertical-align:middle;',
							),	
	        		),
	        		'kalah' => array(
	        			'name' => 'kalah',
	        			'header' => 'Kalah',
	        			// 'value' => '$data["kalah"]',
	        			'value' => '
								CHtml::link($data["kalah"],"javascript:void(0)", array("onClick"=>"$.ajax({
							type: `POST`,
							url: `".Yii::app()->request->baseUrl."/index.php/leasing/viewDataLose?id=`+`$data[leasing_id]`,
							success: function(html){ var obj = jQuery.parseJSON( html );$(`#mydialog`).dialog(`open`).html(obj.content);return false; },
						})"))
								',
	        			'type' => 'raw',
						'headerHtmlOptions' => array(
								'style' => 'vertical-align:middle;',
							),	
	        		),
	        		'sisa_token' => array(
	        			'name' => 'sisa_token',
	        			'header' => 'Sisa Token',
	        			'value' => '$data["sisa_token"]',
	        			'type' => 'raw',
						'headerHtmlOptions' => array(
								'style' => 'vertical-align:middle;',
							),	
	        		),
	        	)
			));
		?>
	</div>
	
</div>