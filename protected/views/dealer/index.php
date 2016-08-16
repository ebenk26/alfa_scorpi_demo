
<?php 
    $colorstock = array('448ccb', 'fbaf5d', 'acd373', 'ed1c24', 'da06d8');
    $datacolor = array();
    foreach ($json_array as $key => $value) {
        $datacolor[] = '#' . $colorstock[$key];
        $datacolor[] = '#' . (substr(str_shuffle('ABCDE0123456789'), 0, 6));;
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
                    $('#data-prospect-dealer-grid').yiiGridView('update');
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
                <i class="fa fa-flag"></i><h3 class="box-title">DEALER TERAKTIF</h3><span class="campaign-list-icon" style="font-size:11px;"></span>
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
                            'categories' => $date_label
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
        <!-- <select id="filter_leasing" name="filter_leasing" class="form-control " style="width: 25%;">
          <option value="">Nama Leasing</option>
          
        </select> -->
        <!-- <select id="filter_status" name="filter_status" class="form-control select2" style="width: 25%;">
          <option value="">Status Prospect</option>
          <option value="Confirmed">Confirmed</option>
          <option value="No feedback">No feedback</option>
          <option value="Approved">Approved</option>
          <option value="Rejected">Rejected</option>
          <option value="Cancel">Cancel</option>
        </select> -->
        <button class="btn btn-default btn-clear">Clear</button>
        <button class="btn btn-primary btn-filter">Filter</button>
      </div>
    </div>
    <div class="col-md-4">
        <?php echo CHtml::link('<span class="fa fa-download"></span> Excell',array("downloadexcel"),array('class'=>'btn pull-right btn-success btn-excell',)); ?>
    </div>
</div>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    	'id'=>'data-prospect-dealer-grid',
    	'dataProvider'=>$dataProvider,
    	 'afterAjaxUpdate'=>"function() {  
           // check_role_cond();
        }", 
    	// 'filter'=>$model,
    	'ajaxUpdate' => true,
        'columns'=>array(
            array(
                'header'=> 'No.',
                'value' => '$this->grid->dataProvider->pagination->currentPage * $this->grid->dataProvider->pagination->pageSize + ($row+1)',
                'htmlOptions'=>array('style'=>'text-align: center;'),
            ),
            'prospect' => array(
                'name' => 'prospect',
                'header' => 'Prospect',
                'value' => '$data["prospect"]',
                'type' => 'raw',
                'headerHtmlOptions' => array(
                        'style' => 'vertical-align:middle;',
                    ),  
            ),
            'alamat' => array(
                'name' => 'alamat',
                'header' => 'Alamat',
                'value' => '$data["alamat"]',
                'type' => 'raw',
                'headerHtmlOptions' => array(
                        'style' => 'vertical-align:middle;',
                    ),  
            ),
            'no_hp' => array(
                'name' => 'no_hp',
                'header' => 'Hp',
                'value' => '$data["no_hp"]',
                'type' => 'raw',
                'headerHtmlOptions' => array(
                        'style' => 'vertical-align:middle;',
                    ),  
            ),
            'nama' => array(
                'name' => 'nama',
                'header' => 'Nama Sales',
                'value' => '$data["nama"]',
                'type' => 'raw',
                'headerHtmlOptions' => array(
                        'style' => 'vertical-align:middle;',
                    ),  
            ),
            'dealer_name' => array(
                'name' => 'dealer_name',
                'header' => 'Dealer',
                'value' => '$data["dealer_name"]',
                'type' => 'raw',
                'headerHtmlOptions' => array(
                        'style' => 'vertical-align:middle;',
                    ),  
            ),
            'leasing' => array(
                'name' => 'leasing',
                'header' => 'Leasing',
                'value' => '$data["leasing"]',
                'type' => 'raw',
                'headerHtmlOptions' => array(
                        'style' => 'vertical-align:middle;',
                    ),  
            ),
            'winner_confirm' => array(
                'name' => 'winner_confirm',
                'header' => 'Status',
                'value' => '$data["winner_confirm"]',
                'type' => 'raw',
                'headerHtmlOptions' => array(
                        'style' => 'vertical-align:middle;',
                    ),  
            ),
            'created_at' => array(
                'name' => 'created_at',
                'header' => 'Time Created',
                'value' => '$data["created_at"]',
                'type' => 'raw',
                'headerHtmlOptions' => array(
                        'style' => 'vertical-align:middle;',
                    ),  
            ),
        )

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

