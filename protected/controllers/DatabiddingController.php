<?php

class DatabiddingController extends Controller
{
	public function actionIndex()
	{
		if(Yii::app()->user->isGuest){
			$this->redirect(array('/site/login'));
		}else{
			$connection=Yii::app()->db;

			// var_dump(isset(Yii::app()->request->cookies['fromdate']));
			$totConfirm = 0;
			$totNofeedback = 0;
			$totApprove = 0;
			$totReject = 0;
			$totCancel = 0;

			if(!empty($_POST))
			{

				Yii::app()->request->cookies['fromdate'] = new CHttpCookie('fromdate', $_POST['fromdate']);
				Yii::app()->request->cookies['todate'] = new CHttpCookie('todate', $_POST['todate']);
				Yii::app()->request->cookies['filter_region'] = new CHttpCookie('filter_region', $_POST['filter_region']);
				Yii::app()->request->cookies['filter_status'] = new CHttpCookie('filter_status', $_POST['filter_status']);
				Yii::app()->request->cookies['filter_leasing'] = new CHttpCookie('filter_leasing', $_POST['filter_leasing']);
			    
				exit();
			}else{
				// print_r($_POST);
				if(empty($_GET)){
					unset(Yii::app()->request->cookies['filter_region']);
					unset(Yii::app()->request->cookies['filter_status']);
					unset(Yii::app()->request->cookies['filter_leasing']);
				}
			}

			if(Yii::app()->session['roleid'] == 2){
				$where = " where a.id is not null and a.region_id is not null and g.leasing_id in (".Yii::app()->session['emailleasingid'].") and g.bidding_time is not null  and c.leasing_id = ".Yii::app()->session['emailleasingid']."";
			}else if(Yii::app()->session['roleid'] == 3){
				$where = " where a.id is not null and a.region_id is not null and f.dealer_id in ('".Yii::app()->session['dealerid']."')";
			}else{
				$where = " where a.id is not null and a.region_id is not null ";
			}
			
			if(empty($_GET) && !isset(Yii::app()->request->cookies['fromdate']) && empty(Yii::app()->request->cookies['fromdate'])){
				// echo "as";
				$date_from = date_create()->modify('-29 day');
				$data_from_date = date_format($date_from, 'Y-m-d');
				// echo $data_from_date;

				$to_date = date_create();
				// $to_date->modify('+1 day');
				$data_to_date = date_format($to_date, 'Y-m-d');
				// echo $data_to_date;

				Yii::app()->request->cookies['fromdate'] = new CHttpCookie('fromdate', $data_from_date);
				Yii::app()->request->cookies['todate'] = new CHttpCookie('todate', $data_to_date);
			}else{
				// echo "da";

				$date_from = date_create(Yii::app()->request->cookies['fromdate']);
				$data_from_date = date_format($date_from, 'Y-m-d');

				$to_date = date_create(Yii::app()->request->cookies['todate']);
				// $to_date->modify('+1 day');
				$data_to_date = date_format($to_date, 'Y-m-d');
			}
			$date_range['from'] = $data_from_date;
			$date_range['to'] = $data_to_date;
			$where = $where." AND a.created_at BETWEEN '".$data_from_date." 00:00:00' AND '".$data_to_date." 23:59:59'";

			if(isset(Yii::app()->request->cookies['filter_region']) && Yii::app()->request->cookies['filter_region'] != ''){
				$filter_region = Yii::app()->request->cookies['filter_region'];
				$where = $where." AND a.region_id = '$filter_region'";
			}

			if(isset(Yii::app()->request->cookies['filter_status']) && Yii::app()->request->cookies['filter_status'] != ''){
				$filter_status = Yii::app()->request->cookies['filter_status'];
				if($filter_status == "Confirmed"){
					$filter_status_ids = '1';
					$where = $where." AND c.winner_confirm = '$filter_status_ids'";
				}
				if($filter_status == "No feedback"){
					$filter_status_ids = '98';
					$where = $where." AND (c.winner_confirm = '$filter_status_ids' OR c.winner_confirm IS NULL)";
				}
				if($filter_status == "Approved"){
					$filter_status_ids = '2';
					$where = $where." AND c.winner_confirm = '$filter_status_ids'";
				}
				if($filter_status == "Rejected"){
					$filter_status_ids = '1,2,3,98';
					$where = $where." AND c.winner_confirm NOT IN ($filter_status_ids) AND c.winner_confirm IS NOT NULL";
				}
				if($filter_status == "Cancel"){
					$filter_status_ids = '3';
					$where = $where." AND c.winner_confirm = '$filter_status_ids'";
				}


			}

			if(isset(Yii::app()->request->cookies['filter_leasing']) && Yii::app()->request->cookies['filter_leasing'] != ''){
				$filter_leasing = Yii::app()->request->cookies['filter_leasing'];
				$where = $where." AND d.id = '$filter_leasing'";
			}
			/*$connection=Yii::app()->db;
			$command2= $connection->createCommand("
					select a.id as id,a.nama as prospect,UCASE(e.name) as region,a.region_id as leasing_terlibat,c.created_at as time,
					a.time_confirm,a.time_approve,c.time_reject,timediff(a.time_approve,a.time_confirm)as durasi,a.last_case_id,a.profil_konsumen,a.nama_salesman,i.name as role_name,
					CASE 
						WHEN c.winner_confirm =1 THEN 'Confirmed'
						WHEN c.winner_confirm =2 THEN 'Approved'
						WHEN c.winner_confirm is null THEN 'No Feedback'
						WHEN c.winner_confirm =98 THEN 'No Feedback'
						WHEN c.winner_confirm =3 THEN 'Cancel'
						ELSE 'Rejected'
					END as winner_confirm,
					CASE 
						WHEN d.leasing_name is null THEN d.nama
						ELSE d.leasing_name
					END as pemenang
					from prospect a
					left join send_mail g on a.id = g.prospect_id
					left join leasing b on a.region_id = b.region_id
					left join winner c on a.id = c.prospect_id
					left join leasing d on c.leasing_id = d.id
					left join region e on a.region_id = e.id
					left join user f on a.from_email = f.email
					left join user h on a.last_comment_user = h.id
					left join role i on h.role_id = i.id
					".$where."
					group by a.id
			");
			$rows2 = $command2->queryAll();
			// $model=$command2->query();
			// print_r($rows2);exit();
			// $model->unsetAttributes();
			$totalData = count($rows2);*/
			$sql = "
					select a.id as id,a.nama as prospect,UCASE(e.name) as region,a.region_id as leasing_terlibat,f.dealer_name as sumber_order,a.created_at as time_sent_order,c.created_at as time,
					a.time_confirm,a.time_approve,c.time_reject,timediff(a.time_approve,a.time_confirm)as durasi,a.last_case_id,a.profil_konsumen,a.nama_salesman,i.name as role_name,
					CASE 
						WHEN c.winner_confirm =1 THEN 'Confirmed'
						WHEN c.winner_confirm =2 THEN 'Approved'
						WHEN c.winner_confirm is null THEN 'No Feedback'
						WHEN c.winner_confirm =98 THEN 'No Feedback'
						WHEN c.winner_confirm =3 THEN 'Cancel'
						ELSE 'Rejected'
					END as winner_confirm,
					c.winner_confirm as winner_confirm_id,
					CASE 
						WHEN d.leasing_name is null THEN d.nama
						ELSE d.leasing_name
					END as pemenang
					from prospect a
					left join send_mail g on a.id = g.prospect_id
					left join leasing b on a.region_id = b.region_id
					left join winner c on a.id = c.prospect_id
					left join leasing d on c.leasing_id = d.id
					left join region e on a.region_id = e.id
					left join user f on a.from_email = f.email
					left join user h on a.last_comment_user = h.id
					left join role i on h.role_id = i.id
					".$where."
					group by a.id
					";

			$sql3 = "SELECT asd.winner_confirm FROM (SELECT 	
					CASE
						WHEN c.winner_confirm =1 THEN 'Confirmed'
						WHEN c.winner_confirm =2 THEN 'Approved'
						WHEN c.winner_confirm is null THEN 'No Feedback'
						WHEN c.winner_confirm =98 THEN 'No Feedback'
						WHEN c.winner_confirm =3 THEN 'Cancel'
						ELSE 'Rejected'
					END as winner_confirm
					FROM winner c GROUP BY winner_confirm)as asd GROUP BY asd.winner_confirm";


			$sql4 = "SELECT id, UCASE(name) as name FROM region";

			$sql5 = "SELECT wew.winner_confirm, COUNT(wew.winner_confirm) as tot_win FROM (".$sql.") as wew GROUP BY wew.winner_confirm";

			$command3= $connection->createCommand($sql3);
			$winner_confirm = $command3->queryAll();
			$command4= $connection->createCommand($sql4);
			$res_region = $command4->queryAll();

			$command5= $connection->createCommand($sql5);
			$tot_winner_confirm = $command5->queryAll();

			// print_r($tot_winner_confirm);
			foreach ($tot_winner_confirm as $value) {
				# code...
				if( strtolower($value['winner_confirm']) == strtolower('Confirmed') ){
					$totConfirm = $value['tot_win'];
				}
				if( strtolower($value['winner_confirm']) == strtolower('Approved') ){
					$totApprove = $value['tot_win'];
				}
				if( strtolower($value['winner_confirm']) == strtolower('No Feedback') ){
					$totNofeedback = $value['tot_win'];
				}
				if( strtolower($value['winner_confirm']) == strtolower('Rejected') ){
					$totReject = $value['tot_win'];
				}
				if( strtolower($value['winner_confirm']) == strtolower('Cancel') ){
					$totCancel = $value['tot_win'];
				}
			}

			foreach ($res_region as $key => $value) {
				# code...
				$region[$key]['name'] = $value['name'];
				$region[$key]['id'] = $value['id'];
				$region_label[]=$value['name'];
			}

			// $region = array('JAKARTA', 'SOLO', 'BANDUNG');
			// print_r($winner_confirm);
			// print_r($region);
			$sql2 = "SELECT COUNT(tbl1.winner_confirm) as tot_confirm, tbl1.region, tbl1.winner_confirm   FROM (".$sql.") as tbl1 GROUP BY tbl1.winner_confirm, tbl1.region   ";
			// echo($sql2);
			// $connection=Yii::app()->db;
			$command2= $connection->createCommand($sql2);
			$rows2 = $command2->queryAll();
			// $model=$command2->query();
			// print_r($rows2);
			$data_prepare = array();
			/*foreach ($winner_confirm as $value) {
				# code...
				$data = array();
				$data['name'] = $value['winner_confirm'];
				foreach ($rows2 as $value2) {
					# code...
					if($value['winner_confirm'] == $value2['winner_confirm']){
						$data['data'][] = $value2['region'];
						$data['data'][] = $value2['tot_confirm'];
					}
				}
				adata_prepare, $data);
			}*/
			foreach ($rows2 as $value2) {
				// echo "2";
				foreach ($winner_confirm as $value) {
					if($value['winner_confirm'] == $value2['winner_confirm']){
						$data_prepare[$value['winner_confirm']]['name'] = $value['winner_confirm'];
					}
				}
				foreach ($region as $key => $value3) {
					# code...
					// echo $data_prepare[$value2['winner_confirm']]['data'][$key];
					// echo "<br>";
					if(!isset($data_prepare[$value2['winner_confirm']]['data'][$key]) ){
						$data_prepare[$value2['winner_confirm']]['data'][$key] = 0;
					}
					/*else{
						$data_prepare[$value2['winner_confirm']]['data'][$key] = 0;
					}*/
					// echo "3";
					
				}
				foreach ($region as $key => $value3) {
					# code...
					if($value3['name'] == $value2['region'] && $value2['tot_confirm'] != 0){
						$data_prepare[$value2['winner_confirm']]['data'][$key] = intval($value2['tot_confirm']);
					}
				}

			}



			$result_json = array();
			foreach ($data_prepare as $value) {
				# code...
				array_push($result_json, $value);
			}
			// if (isset($_GET["DownloadExcel_x"])){
			
				// $this->downloadWebSurvey($sql);
			// }
			// echo $sql;
			$count=Yii::app()->db->createCommand('SELECT count(*) from (('. $sql .') as alias)')->queryScalar();

			$dataProvider = new CSqlDataProvider($sql, array(
				'totalItemCount' => $count,
				'sort' => array(
						'defaultOrder' => 'a.id DESC ',
					),
				'pagination' => array(
						// 'pageSize' => intval(Yii::app()->params["defaultPageSize"]),
						'pageSize' => 20,
					),
			));
			
			// print_r($region);
			// print_r($region_label);
			// print_r($data_prepare);
			// print_r($result_json);

			// exit();

			$this->render('index',array(
				'dataProvider' => $dataProvider,
				'json_array'=>$result_json,
				'region'=>$region,
				'region_label'=>$region_label,
				'date_range' => $date_range,
				'totConfirm' => $totConfirm,
				'totApprove' => $totApprove,
				'totNofeedback' => $totNofeedback,
				'totReject' => $totReject,
				'totCancel' => $totCancel,
				// 'winner_confirm'=>$winner_confirm
			));
			// $this->render('index');
		}
	}
	
	public function allowedActions()
	{
		return 'viewDatabidding';
		return 'viewComment';
	}
	
	public function actions(){
		return array(
			'viewDatabidding' => array(
		    		'class' => 'application.actions.ViewAction',
		    		'ajaxView' => '_view_databidding',
		    	),
			'viewComment' => array(
		    		'class' => 'application.actions.ViewAction',
		    		'ajaxView' => '_view_comment',
		    	),
		);
		// $this->render('_view_list_location_survey',array(
			// 'condition'=>'testparam',
		// ));
	}

	public function loadModel()
	{
	  
	}
	
	public function accessRules()
	{
		 return array(
			 array('allow',  // allow all users to perform 'index' and 'contact' actions
				  'actions'=>array('index','contact'),
				  'users'=>array('@'),
			 ),
			 array('allow', // allow authenticated user to perform 'delete' and 'update' actions
				  'actions'=>array('update','delete','downloadmonbrand'),
				  'users'=>array('@'),
			 ),
			 array('deny',  // deny all users
				   'users'=>array('*'),
			),
		 );
	}
	
	public function actionDownloadmonbrand(){
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Alfa Scorpii")
		->setLastModifiedBy("Alfa Scorpii")
		->setTitle("Download Data Bidding")
		->setSubject("Download Data Bidding")
		->setDescription("Download Data Bidding")
		->setKeywords("download,Download Data Bidding")
		->setCategory("Download");
		
		$objPHPExcel->getActiveSheet()->setTitle('DATA BIDDING');
		$worksheet = $objPHPExcel->getActiveSheet();
		
		$where = 'a.deleted_at is null';
		if(Yii::app()->session['roleid'] == 2){
			$where = " where a.id is not null and a.region_id is not null and g.leasing_id in (".Yii::app()->session['emailleasingid'].") and g.bidding_time is not null and c.leasing_id = ".Yii::app()->session['emailleasingid']."";
		}else if(Yii::app()->session['roleid'] == 3){
			$where = " where a.id is not null and a.region_id is not null and f.dealer_id in ('".Yii::app()->session['dealerid']."')";
		}else{
			$where = " where a.id is not null and a.region_id is not null ";
		}

		if(empty($_GET) && !isset(Yii::app()->request->cookies['fromdate']) && empty(Yii::app()->request->cookies['fromdate'])){
			// echo "as";
			$date_from = date_create()->modify('-29 day');
			$data_from_date = date_format($date_from, 'Y-m-d');
			// echo $data_from_date;

			$to_date = date_create();
			// $to_date->modify('+1 day');
			$data_to_date = date_format($to_date, 'Y-m-d');
			// echo $data_to_date;

			Yii::app()->request->cookies['fromdate'] = new CHttpCookie('fromdate', $data_from_date);
			Yii::app()->request->cookies['todate'] = new CHttpCookie('todate', $data_to_date);
		}else{
			// echo "da";

			$date_from = date_create(Yii::app()->request->cookies['fromdate']);
			$data_from_date = date_format($date_from, 'Y-m-d');

			$to_date = date_create(Yii::app()->request->cookies['todate']);
			// $to_date->modify('+1 day');
			$data_to_date = date_format($to_date, 'Y-m-d');
		}
		$date_range['from'] = $data_from_date;
		$date_range['to'] = $data_to_date;
		$where = $where." AND a.created_at BETWEEN '".$data_from_date." 00:00:00' AND '".$data_to_date." 23:59:59'";

		if(isset(Yii::app()->request->cookies['filter_region']) && Yii::app()->request->cookies['filter_region'] != ''){
			$filter_region = Yii::app()->request->cookies['filter_region'];
			$where = $where." AND a.region_id = '$filter_region'";
		}

		if(isset(Yii::app()->request->cookies['filter_status']) && Yii::app()->request->cookies['filter_status'] != ''){
			$filter_status = Yii::app()->request->cookies['filter_status'];
			if($filter_status == "Confirmed"){
				$filter_status_ids = '1';
				$where = $where." AND c.winner_confirm = '$filter_status_ids'";
			}
			if($filter_status == "No feedback"){
				$filter_status_ids = '98';
				$where = $where." AND (c.winner_confirm = '$filter_status_ids' OR c.winner_confirm IS NULL)";
			}
			if($filter_status == "Approved"){
				$filter_status_ids = '2';
				$where = $where." AND c.winner_confirm = '$filter_status_ids'";
			}
			if($filter_status == "Rejected"){
				$filter_status_ids = '1,2,3,98';
				$where = $where." AND c.winner_confirm NOT IN ($filter_status_ids) AND c.winner_confirm IS NOT NULL";
			}
			if($filter_status == "Cancel"){
				$filter_status_ids = '3';
				$where = $where." AND c.winner_confirm = '$filter_status_ids'";
			}


		}

		if(isset(Yii::app()->request->cookies['filter_leasing']) && Yii::app()->request->cookies['filter_leasing'] != ''){
			$filter_leasing = Yii::app()->request->cookies['filter_leasing'];
			$where = $where." AND d.id = '$filter_leasing'";
		}
		
		$query = "
					select a.id as id,a.nama as prospect,UCASE(e.name) as region,a.region_id as leasing_terlibat,f.dealer_name as sumber_order,a.created_at as time_sent_order,c.created_at as time,
					a.time_confirm,a.time_approve,c.time_reject,timediff(a.time_approve,a.time_confirm)as durasi,a.last_case_id,a.profil_konsumen,a.nama_salesman,i.name as role_name,
					CASE 
						WHEN c.winner_confirm =1 THEN 'Confirmed'
						WHEN c.winner_confirm =2 THEN 'Approved'
						WHEN c.winner_confirm is null THEN 'No Feedback'
						WHEN c.winner_confirm =98 THEN 'No Feedback'
						WHEN c.winner_confirm =3 THEN 'Cancel'
						ELSE 'Rejected'
					END as winner_confirm,
					CASE 
						WHEN d.leasing_name is null THEN d.nama
						ELSE d.leasing_name
					END as pemenang,
					a.nik,a.alamat,a.ttl,a.no_hp,a.keterangan,a.jam_survey,a.tipe,a.dp_approve,a.cicil_approve,a.profil_konsumen
					from prospect a
					left join send_mail g on a.id = g.prospect_id
					left join leasing b on a.region_id = b.region_id
					left join winner c on a.id = c.prospect_id
					left join leasing d on c.leasing_id = d.id
					left join region e on a.region_id = e.id
					left join user f on a.from_email = f.email
					left join user h on a.last_comment_user = h.id
					left join role i on h.role_id = i.id
					".$where."
					group by a.id
				";

		$data = Yii::app()->db->createCommand($query)->queryAll();
		$xlFieldName = array(
				'NO',
				'Case ID',
				'Last Case ID',
				'Prospect',
				//start detail prospect
				'NIK',
				'Alamat',
				'Tempat, Tanggal Lahir',
				'No. HP',
				'Keterangan',
				'Jam Survey',
				'Tipe Motor',
				'DP',
				'Cicilan',
				'Profil Konsumen',
				//start detail prospect
				'Region',
				'Leasing Terlibat',
				'Sumber Order',
				'Time Sent Order',
				'Pemenang',
				'Time Confirm',
				'Time Approve/Reject',
				'Durasi',
				'Winner Confirm',
				'Last Comment By',
		);
		
		$colId = 0;
		$rowId = 1;
		foreach ($xlFieldName as $key => $value) {
			$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value);
		}
		
		$urutan = 0;
		if (is_array($data)){
		
			foreach ($data as $value) {
		
				$colId = 0;
				$urutan++;
				$rowId++;
		
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $urutan );
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['id']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['last_case_id']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['prospect']);
				//start detail prospect
				if(isset($value['nik'])){
					$colIdnik = $colId++;
					$rowId = $rowId;
					$worksheet->setCellValueByColumnAndRow($colIdnik, $rowId, $value['nik']);
					$worksheet->getStyleByColumnAndRow($colIdnik, $rowId)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
				}
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['alamat']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['ttl']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['no_hp']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['keterangan']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['jam_survey']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['tipe']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['dp_approve']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['cicil_approve']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['profil_konsumen']);
				//start detail prospect
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['region']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['leasing_terlibat']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['sumber_order']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['time_sent_order']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['pemenang']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['time_confirm']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['time_approve']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['durasi']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['winner_confirm']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['role_name']);
		
			}
		
		}
		
		$objPHPExcel->setActiveSheetIndex(0);
		$filename = 'Databidding'.date("Ymdhis");
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		Yii::app()->end();
	}

	public function actionListLeasingAjax()
	{
		$keyword = $_POST['q'];
		// echo $keyword;
		$model = new Leasing('search');
		$criteria=new CDbCriteria;
		$criteria->condition = "leasing_name LIKE '%$keyword%'";
		$query = Leasing::model()->findAll($criteria);
		// print_r($query);
		foreach ($query as $list){
           $reusultados[] = array(
                        'id'=>$list->id,
                        'text'=>  $list->nama,
           ); 
        }
		echo CJSON::encode($reusultados);

		# code...
	}

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}