<?php
$getdataleasing = intval($_GET["id"]);// echo $id;
if($getdataleasing != null && Yii::app()->session['id_leasing'] == null){
	Yii::app()->session['id_leasing'] = $getdataleasing;	
}elseif(Yii::app()->session['id_leasing'] != intval($_GET["id"])){
	$getdataleasing = intval($_GET["id"]);
	Yii::app()->session['id_leasing'] = $getdataleasing;
}
else{
	$getdataleasing = Yii::app()->session['id_leasing'];	
}

// echo $getdataleasing;
// echo "The time is " . date("h:i:sa");
$connection=Yii::app()->db;
		$command2= $connection->createCommand("
				SELECT p.id FROM (SELECT g.id, g.leasing_id, g.prospect_id
										                        FROM send_mail g
										                        WHERE g.bidding_token IS NOT NULL
				                                                AND g.leasing_id = '".$getdataleasing."'
				                                                 
										            ) as terlibat 
													LEFT JOIN prospect p ON p.id=terlibat.prospect_id
													LEFT JOIN winner w ON w.prospect_id=terlibat.prospect_id
													LEFT JOIN leasing l ON l.id = terlibat.leasing_id 
										            WHERE terlibat.id NOT IN (SELECT send_email_id FROM winner )  
				order by w.created_at asc
		");
		$rows2 = $command2->queryAll();
		$totalData = count($rows2);
		$sql = "
				SELECT terlibat.prospect_id, p.nama, l.region_id,
				(SELECT GROUP_CONCAT( t2.nama ) 
								FROM send_mail t1
								LEFT JOIN leasing t2 ON t2.id = t1.leasing_id
								WHERE t1.prospect_id = w.prospect_id
								AND t1.bidding_token IS NOT NULL
								GROUP BY t1.prospect_id
								) AS leasing,
				(SELECT nama FROM leasing WHERE leasing.id = w.leasing_id) as pemenang, w.created_at as time,
				CASE 
				    WHEN w.winner_confirm =1 THEN 'Confirmed'
				    WHEN w.winner_confirm =2 THEN 'Approved'
				    WHEN w.winner_confirm is null THEN 'No Feedback'
				    WHEN w.winner_confirm =98 THEN 'No Feedback'
				    WHEN w.winner_confirm =3 THEN 'Cancel'
				ELSE 'Rejected'
				END as winner_confirm,
				terlibat.leasing_id AS leasId FROM (SELECT g.id, g.leasing_id, g.prospect_id
										                        FROM send_mail g
										                        WHERE g.bidding_token IS NOT NULL
				                                                AND g.leasing_id = '".$getdataleasing."'
				                                                 
										            ) as terlibat 
													LEFT JOIN prospect p ON p.id=terlibat.prospect_id
													LEFT JOIN winner w ON w.prospect_id=terlibat.prospect_id
													LEFT JOIN leasing l ON l.id = terlibat.leasing_id 
										            WHERE terlibat.id NOT IN (SELECT send_email_id FROM winner )  
				order by w.created_at asc
				";
		// echo $sql;
		// exit;
		$dataProvider = new CSqlDataProvider($sql, array(
			'totalItemCount' => $totalData,
			'sort' => array(
					'defaultOrder' => 'w.created_at DESC ',
				),
			'pagination' => array(
					'pageSize' => 20,

				),
		));
	$leasing = Leasing::model()->findByPk($getdataleasing);

	$nama_leasing = $leasing->nama;
	echo 'Nama Leasing : <b>'.$nama_leasing.'<b><br><br>';
	$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'data-lose-grid',
		'dataProvider'=>$dataProvider,
		'ajaxUpdate' => true,
		// 'ajaxUrl' => '/alfa_scorpi/index.php/leasing/viewDataWinning?id='.$getdataleasing,
		'beforeAjaxUpdate' => 'function(id,options){
			// console.log(id);
			// console.log(jQuery("#data-winning-grid"));
		}',
		'afterAjaxUpdate' => 'function(id, options)
		{
			// console.log("asd "+id);
		}',
		'columns'=>array(
				array(
					'header'=> 'No.',
					'value' => '$this->grid->dataProvider->pagination->currentPage * $this->grid->dataProvider->pagination->pageSize + ($row+1)',
					'htmlOptions'=>array('style'=>'text-align: center;'),
				),
				'nama' => array(
					'name' => 'nama',
					'header' => 'Prospect',
					'value' => '$data["nama"]',
					'type' => 'raw',
					'headerHtmlOptions' => array(
							'style' => 'vertical-align:middle;',
						),	
				),
				'region_id' => array(
					'name' => 'region_id',
					'header' => 'Region',
					'value' => '$data["region_id"]',
					'type' => 'raw',
					'headerHtmlOptions' => array(
							'style' => 'vertical-align:middle;',
						),	
				),
				'leasing' => array(
					'name' => 'leasing',
					'header' => 'Leasing terlibat',
					'value' => '$data["leasing"]',
					'type' => 'raw',
					'headerHtmlOptions' => array(
							'style' => 'vertical-align:middle;',
						),	
				),

				'pemenang' => array(
					'name' => 'pemenang',
					'header' => 'Leasing pemenang',
					'value' => '$data["pemenang"]',
					'type' => 'raw',
					'headerHtmlOptions' => array(
							'style' => 'vertical-align:middle;',
						),	
				),
				
				'time' => array(
					'name' => 'time',
					'header' => 'Time',
					'value' => '$data["time"]',
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
			)
			
		)
	);

?>