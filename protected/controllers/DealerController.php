<?php
class DealerController extends Controller
{
	public function actionIndex()
	{
		$role_perm = array(1,3);
		if(Yii::app()->user->isGuest){
			$this->redirect(array('/site/login'));
		}else if( in_array(Yii::app()->session['roleid'], $role_perm) ){
			$connection=Yii::app()->db;

			// var_dump(isset(Yii::app()->request->cookies['fromdate']));
			$totConfirm = 0;
			$totNofeedback = 0;
			$totApprove = 0;
			$totReject = 0;
			$totCancel = 0;

			if(!empty($_POST))
			{
				// print_r($_POST);
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
				//$where = " where a.id is not null and a.region_id is not null and g.leasing_id in (".Yii::app()->session['emailleasingid'].") and g.bidding_time is not null  and c.leasing_id = ".Yii::app()->session['emailleasingid']."";
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

			/*if(isset(Yii::app()->request->cookies['filter_status']) && Yii::app()->request->cookies['filter_status'] != ''){
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


			}*/

			/*if(isset(Yii::app()->request->cookies['filter_leasing']) && Yii::app()->request->cookies['filter_leasing'] != ''){
				$filter_leasing = Yii::app()->request->cookies['filter_leasing'];
				$where = $where." AND d.id = '$filter_leasing'";
			}*/
			
			$sql = "SELECT COUNT(a.id) AS total_prospect, 
						date(a.created_at) as tanggal, 
						d.id, 
						CASE 
							WHEN d.dealer_name is null THEN d.nama
							ELSE d.dealer_name
						END as dealer_name
					FROM prospect a 
						 INNER JOIN `user` h ON a.from_email = h.email
						 INNER JOIN dealer d ON h.dealer_id = d.id
						 INNER JOIN user f on a.from_email = f.email
					".$where."
					GROUP BY date(a.created_at),d.id
					ORDER BY date(a.created_at) ASC, total_prospect DESC";

			$sql_ = "
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
			$sql2 = "SELECT wew.winner_confirm, COUNT(wew.winner_confirm) as tot_win FROM (".$sql_.") as wew GROUP BY wew.winner_confirm";

			$sql3 = "SELECT a.id, 
						a.nama as prospect, 
						a.alamat, 
						a.no_hp, 
						d.nama, 
						d.dealer_name, 
						l.nama leasing, 
						CASE 
							WHEN c.winner_confirm =1 THEN 'Confirmed'
							WHEN c.winner_confirm =2 THEN 'Approved'
							WHEN c.winner_confirm is null THEN 'No Feedback'
							WHEN c.winner_confirm =98 THEN 'No Feedback'
							WHEN c.winner_confirm =3 THEN 'Cancel'
							ELSE 'Rejected'
						END as winner_confirm,
						a.created_at
					FROM prospect a INNER JOIN `user` f ON a.from_email = f.email
						LEFT JOIN winner c ON c.prospect_id = a.id
						LEFT JOIN leasing l ON c.leasing_id = l.id
						 INNER JOIN dealer d ON f.dealer_id = d.id
					".$where."
						 ";

			$sql4 = "SELECT id, UCASE(name) as name FROM region";


		$command= $connection->createCommand($sql);
		$data_daily = $command->queryAll();

		$command2= $connection->createCommand($sql2);
		$tot_winner_confirm = $command2->queryAll();

		$command4= $connection->createCommand($sql4);
		$res_region = $command4->queryAll();

		/*$command3= $connection->createCommand($sql3);
		$dealer_prospect = $command3->queryAll();*/

		foreach ($res_region as $key => $value) {
			# code...
			$region[$key]['name'] = $value['name'];
			$region[$key]['id'] = $value['id'];
			$region_label[]=$value['name'];
		}

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


		// echo $sql;
		// print_r($data_daily);
		// print_r($dealer_prospect);

	    $aryRange=array();
	    $strDateFrom = $data_from_date;
	    $strDateTo = $data_to_date;
	    $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
	    $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

	    if ($iDateTo>=$iDateFrom)
	    {
	        array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
	        while ($iDateFrom<$iDateTo)
	        {
	            $iDateFrom+=86400; // add 24 hours
	            array_push($aryRange,date('Y-m-d',$iDateFrom));
	        }
	    }

	    $arr_dealer = array();
	    // $arr_dealer_label = array();
	    foreach ($data_daily as $key => $value) {
	    	# code...
	    	$arr_dealer[$value['id']] = $value['dealer_name'];
	    	// $arr_dealer_label[] = $value['dealer_name'];
	    }

	    $data_prepare = array();

	    foreach ($arr_dealer as $key => $value) {
			// if($value['winner_confirm'] == $value2['winner_confirm']){
				$data_prepare[$key]['name'] = $value;
			// }
		}


	    foreach ($data_daily as $value2) {
			// echo "2";
			
			foreach ($aryRange as $key => $value3) {
				# code...
				// echo $data_prepare[$value2['winner_confirm']]['data'][$key];
				// echo "<br>";
				if(!isset($data_prepare[$value2['id']]['data'][$key]) ){
					$data_prepare[$value2['id']]['data'][$key] = 0;
				}
				/*else{
					$data_prepare[$value2['winner_confirm']]['data'][$key] = 0;
				}*/
				// echo "3";
				
			}
			foreach ($aryRange as $key => $value3) {
				# code...
				if($value3 == $value2['tanggal'] && $value2['total_prospect'] != 0){
					$data_prepare[$value2['id']]['data'][$key] = intval($value2['total_prospect']);
				}
			}

		}



		$result_json = array();
		foreach ($data_prepare as $value) {
			# code...
			array_push($result_json, $value);
		}

		// print_r($data_prepare);
		// print_r($data_prepare);
		$count=Yii::app()->db->createCommand('SELECT count(*) from (('. $sql3 .') as alias)')->queryScalar();

		$dataProvider = new CSqlDataProvider($sql3, array(
			'totalItemCount' => $count,
			'sort' => array(
					'defaultOrder' => 'a.id DESC ',
				),
			'pagination' => array(
					// 'pageSize' => intval(Yii::app()->params["defaultPageSize"]),
					'pageSize' => 20,
				),
		));
		// exit;
	    // return $aryRange;


			$this->render('index',array(
				'dataProvider' => $dataProvider,
				'json_array'=>$result_json,
				'date_label'=>$aryRange,
				'region'=>$region,
				// 'region_label'=>$region_label,
				'date_range' => $date_range,
				'totConfirm' => $totConfirm,
				'totApprove' => $totApprove,
				'totNofeedback' => $totNofeedback,
				'totReject' => $totReject,
				'totCancel' => $totCancel,
				// 'winner_confirm'=>$winner_confirm
			));

		}
	}

	public function actionDownloadexcel(){
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Alfa Scorpii")
		->setLastModifiedBy("Alfa Scorpii")
		->setTitle("Download Data Prospect Dealer")
		->setSubject("Download Data Prospect Dealer")
		->setDescription("Download Data Prospect Dealer")
		->setKeywords("download,Download Data Prospect Dealer")
		->setCategory("Download");
		
		$objPHPExcel->getActiveSheet()->setTitle('DATA PROSPECt DEALER');
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

		/*if(isset(Yii::app()->request->cookies['filter_status']) && Yii::app()->request->cookies['filter_status'] != ''){
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


		}*/

		/*if(isset(Yii::app()->request->cookies['filter_leasing']) && Yii::app()->request->cookies['filter_leasing'] != ''){
			$filter_leasing = Yii::app()->request->cookies['filter_leasing'];
			$where = $where." AND d.id = '$filter_leasing'";
		}*/
		
		$query = "SELECT a.id, 
						a.nama as prospect, 
						a.alamat, 
						a.no_hp, 
						d.nama, 
						d.dealer_name, 
						a.created_at
					FROM prospect a INNER JOIN `user` f ON a.from_email = f.email
						 INNER JOIN dealer d ON f.dealer_id = d.id
					".$where."
						 ";

		$data = Yii::app()->db->createCommand($query)->queryAll();
		$xlFieldName = array(
				'NO',
				'PROPECT',
				'ALAMAT',
				'HP',
				'DEALER',
				'USERNAME',
				'TIME CREATED',
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
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['prospect']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['alamat']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, '\''.$value['no_hp']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['dealer_name']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['nama']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['created_at']);
				
		
			}
		
		}
		
		$objPHPExcel->setActiveSheetIndex(0);
		$filename = 'Data Prospect Dealer '.date("Ymdhis");
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


}

?>