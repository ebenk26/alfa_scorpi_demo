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
				SELECT c.id
				FROM winner c
				left join prospect i on c.prospect_id = i.id
				left join leasing b on c.leasing_id = b.id
				left join region s on b.region_id = s.id
				WHERE c.leasing_id = '".$getdataleasing."'
				order by c.created_at asc
		");
		$rows2 = $command2->queryAll();
		$totalData = count($rows2);
		$sql = "
				SELECT c.leasing_id , i.nama , b.region_id, c.created_at as time,
				(SELECT GROUP_CONCAT( t2.nama ) 
				FROM send_mail t1
				LEFT JOIN leasing t2 ON t2.id = t1.leasing_id
				WHERE t1.prospect_id = c.prospect_id
				AND t1.bidding_token IS NOT NULL
				GROUP BY t1.prospect_id
				) AS leasing, 
				CASE 
					WHEN c.winner_confirm =1 THEN 'Confirmed'
					WHEN c.winner_confirm =2 THEN 'Approved'
					WHEN c.winner_confirm is null THEN 'No Feedback'
					WHEN c.winner_confirm =98 THEN 'No Feedback'
					WHEN c.winner_confirm =3 THEN 'Cancel'
				ELSE 'Rejected'
				END as winner_confirm
				FROM winner c
				left join prospect i on c.prospect_id = i.id
				left join leasing b on c.leasing_id = b.id
				left join region s on b.region_id = s.id
				WHERE c.leasing_id = '".$getdataleasing."'
				order by c.created_at asc
				";
		// echo $sql;
		// exit;
		$dataProvider = new CSqlDataProvider($sql, array(
			'totalItemCount' => $totalData,
			'sort' => array(
					'defaultOrder' => 'c.created_at DESC ',
				),
			'pagination' => array(
					'pageSize' => 20,

				),
		));
	$leasing = Leasing::model()->findByPk($getdataleasing);

	$nama_leasing = $leasing->nama;
	echo 'Nama Leasing : <b>'.$nama_leasing.'<b><br><br>';
	$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'data-winning-grid',
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