<?php

class LeasingController extends Controller
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
				// Yii::app()->request->cookies['filter_status'] = new CHttpCookie('filter_status', $_POST['filter_status']);
				Yii::app()->request->cookies['filter_leasing'] = new CHttpCookie('filter_leasing', $_POST['filter_leasing']);
			    
				exit();
			}else{
				// print_r($_POST);
				if(empty($_GET)){
					unset(Yii::app()->request->cookies['filter_region']);
					// unset(Yii::app()->request->cookies['filter_status']);
					unset(Yii::app()->request->cookies['filter_leasing']);
				}
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
			// echo $data_to_date;

			$where = "where a.id is not null";

			if(Yii::app()->session['roleid'] == 2){
				$where .= " and a.region_id is not null and g.leasing_id in (".Yii::app()->session['emailleasingid'].") and g.bidding_time is not null and c.leasing_id = ".Yii::app()->session['emailleasingid']."";
			}else if(Yii::app()->session['roleid'] == 3){
				$where .= "  and a.region_id is not null and f.dealer_id in ('".Yii::app()->session['dealerid']."')";
			}else{
				$where .= "  and a.region_id is not null ";
			}

			$date_range['from'] = $data_from_date;
			$date_range['to'] = $data_to_date;
			$where = $where." AND a.created_at BETWEEN '".$data_from_date." 00:00:00' AND '".$data_to_date." 23:59:59'";


			$where_table = "";
			if(isset(Yii::app()->request->cookies['filter_region']) && Yii::app()->request->cookies['filter_region'] != ''){
				$filter_region = Yii::app()->request->cookies['filter_region'];
				$where_table = $where_table." AND a.region_id = '$filter_region'";
			}

			if(isset(Yii::app()->request->cookies['filter_leasing']) && Yii::app()->request->cookies['filter_leasing'] != ''){
				$filter_leasing = Yii::app()->request->cookies['filter_leasing'];
				$where_table = $where_table." AND d.id = '$filter_leasing'";
			}

			$sql = "
					select a.id as id,a.nama as prospect,a.region_id as leasing_terlibat,f.dealer_name as sumber_order, f.dealer_id, a.created_at as time_sent_order,c.created_at as time,
					a.time_confirm,a.time_approve,c.time_reject,timediff(a.time_approve,a.time_confirm)as durasi,a.last_case_id,a.profil_konsumen,a.nama_salesman,i.id as role_id, i.name as role_name, d.id as leasing_id,
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
					from winner c
					left join prospect a on a.id = c.prospect_id
					left join send_mail g on a.id = g.prospect_id
					left join leasing d on c.leasing_id = d.id
					left join user f on a.from_email = f.email
					left join role i on f.role_id = i.id
					".$where."
					group by a.id
					";

			$sql2 = "SELECT wew.winner_confirm, COUNT(wew.winner_confirm) as tot_win FROM (".$sql.") as wew GROUP BY wew.winner_confirm";

			$sql3 = "SELECT g.leasing_id, d.nama
					FROM send_mail g
					LEFT JOIN prospect a on a.id = g.prospect_id
					LEFT JOIN leasing d on g.leasing_id = d.id
					WHERE g.bidding_time is not null
					AND a.created_at BETWEEN '".$data_from_date." 00:00:00' AND '".$data_to_date." 23:59:59'
					GROUP BY g.leasing_id
					";

			$sql4 = "SELECT g.leasing_id, a.id as prospect_id,
					CASE 
						WHEN g.leasing_id != c.leasing_id THEN 'MENANG'
						WHEN g.leasing_id = c.leasing_id THEN 'KALAH'
						ELSE 'UNDEFINED'
					END as STATUS
					FROM send_mail g
					LEFT JOIN prospect a on a.id = g.prospect_id
					LEFT JOIN winner c on a.id = c.prospect_id
					WHERE g.bidding_time is not null
					AND a.created_at BETWEEN '".$data_from_date." 00:00:00' AND '".$data_to_date." 23:59:59'
					
					";

			$sql5 = "SELECT aw.leasing_id, aw.STATUS, COUNT(aw.STATUS) as total FROM (".$sql4.") as aw GROUP BY aw.STATUS, aw.leasing_id";

			$sql6 = "select a.id as id,a.nama as prospect,a.region_id as leasing_terlibat, a.created_at as time_sent_order,c.created_at as time,
					a.time_confirm,a.time_approve,c.time_reject, TIMESTAMPDIFF(SECOND, a.time_confirm, a.time_approve)as durasi,a.last_case_id,a.profil_konsumen,a.nama_salesman, d.id as leasing_id, d.nama as leasing_name,
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
					from winner c
					left join prospect a on a.id = c.prospect_id
					left join send_mail g on a.id = g.prospect_id
					left join leasing d on c.leasing_id = d.id
					left join user f on a.from_email = f.email
					WHERE a.created_at BETWEEN '".$data_from_date." 00:00:00' AND '".$data_to_date." 23:59:59' 
					AND a.time_confirm IS NOT NULL 
					AND a.time_approve IS NOT NULL
					AND a.time_confirm < a.time_approve
					group by a.id  ";

			$sql7 = "SELECT jo.leasing_name, jo.leasing_id, SUM(jo.durasi) as total, AVG(jo.durasi) as avg, COUNT(jo.id) as tot_prosp FROM (".$sql6.") as jo GROUP BY jo.leasing_id ";

			// $sql8 = "SELECT ib.leasing_id, COUNT(ib.durasi) as total FROM (".$sql6.") as ib GROUP BY ib.leasing_id ";

			$sql9 = "SELECT 

						d.nama as leasing_name,
						d.id as leasing_id,
						UCASE(s.name) as region,
						COALESCE(tot_pros.prosCount,0) AS total_prospect,
						COALESCE(terlibat.terlibatCount,0) AS terlibat,
						COALESCE(winn.winCount,0) AS menang,
						COALESCE(lose.loseCount,0) AS kalah,
						d.token as sisa_token

						FROM leasing as d

						LEFT JOIN (
						            SELECT COUNT(*) AS prosCount, leasing_id AS leasId 
						            FROM send_mail
						            GROUP BY leasing_id
						        ) AS tot_pros
							ON tot_pros.leasId=d.id

						LEFT JOIN (
						            SELECT COUNT(*) AS terlibatCount, leasing_id AS leasId 
						            FROM send_mail
						    		WHERE bidding_token IS NOT NULL
						            GROUP BY leasing_id
						        ) AS terlibat
							ON terlibat.leasId=d.id

						LEFT JOIN (
						            SELECT COUNT(*) AS winCount, leasing_id AS leasId 
						            FROM winner
						    		
						            GROUP BY leasing_id
						        ) AS winn
							ON winn.leasId=d.id


						LEFT JOIN (
						            SELECT COUNT(terlibat.leasing_id) as loseCount, terlibat.leasing_id AS leasId FROM (SELECT g.id, g.leasing_id, g.prospect_id
						                        FROM send_mail g
						                        WHERE g.bidding_token IS NOT NULL
						                        
						            ) as terlibat 
						            WHERE terlibat.id NOT IN (SELECT send_email_id FROM winner )  
						            GROUP BY terlibat.leasing_id
						        ) AS lose
							ON lose.leasId=d.id

						left join winner c on d.id = c.leasing_id
						left join prospect a on a.id = c.prospect_id
						left join send_mail g on a.id = g.prospect_id
						left join user f on a.from_email = f.email
						left join region s on s.id = d.region_id
					
					".$where."
					".$where_table."

					GROUP BY d.id
					";

			$sql10 = "SELECT 

						d.nama as leasing_name,
						d.id as leasing_id,
						UCASE(s.name) as region,
						COALESCE(tot_pros.prosCount,0) AS total_prospect,
						COALESCE(terlibat.terlibatCount,0) AS terlibat,
						COALESCE(winn.winCount,0) AS menang,
						COALESCE(lose.loseCount,0) AS kalah,
						d.token as sisa_token

						FROM leasing as d

						LEFT JOIN (
						            SELECT COUNT(*) AS prosCount, leasing_id AS leasId 
						            FROM send_mail
						            GROUP BY leasing_id
						        ) AS tot_pros
							ON tot_pros.leasId=d.id

						LEFT JOIN (
						            SELECT COUNT(*) AS terlibatCount, leasing_id AS leasId 
						            FROM send_mail
						    		WHERE bidding_token IS NOT NULL
						            GROUP BY leasing_id
						        ) AS terlibat
							ON terlibat.leasId=d.id

						LEFT JOIN (
						            SELECT COUNT(*) AS winCount, leasing_id AS leasId 
						            FROM winner
						    		
						            GROUP BY leasing_id
						        ) AS winn
							ON winn.leasId=d.id


						LEFT JOIN (
						            SELECT COUNT(terlibat.leasing_id) as loseCount, terlibat.leasing_id AS leasId FROM (SELECT g.id, g.leasing_id, g.prospect_id
						                        FROM send_mail g
						                        WHERE g.bidding_token IS NOT NULL
						                        
						            ) as terlibat 
						            WHERE terlibat.id NOT IN (SELECT send_email_id FROM winner )  
						            GROUP BY terlibat.leasing_id
						        ) AS lose
							ON lose.leasId=d.id

						left join winner c on d.id = c.leasing_id
						left join prospect a on a.id = c.prospect_id
						left join send_mail g on a.id = g.prospect_id
						left join user f on a.from_email = f.email
						left join region s on s.id = d.region_id
					
					WHERE a.created_at BETWEEN '".$data_from_date." 00:00:00' AND '".$data_to_date." 23:59:59'

					GROUP BY d.id
					";

			$sql11 = "SELECT id, UCASE(name) as name FROM region";


			$command= $connection->createCommand($sql);
			$data_source = $command->queryAll();

			$command2= $connection->createCommand($sql2);
			$tot_winner_confirm = $command2->queryAll();

			$command3= $connection->createCommand($sql3);
			$leasing_res = $command3->queryAll();

			$command4= $connection->createCommand($sql4);
			$status_res = $command4->queryAll();

			/*$command5= $connection->createCommand($sql5);
			$status_tot_res = $command5->queryAll();*/


			

			$command7= $connection->createCommand($sql7);
			$leasing_durasi_res = $command7->queryAll();

			/*$command8= $connection->createCommand($sql6);
			$durasi_res = $command8->queryAll();*/

			/*$command9= $connection->createCommand($sql9);
			$leasing_stat_res = $command9->queryAll();*/

			$command10= $connection->createCommand($sql10);
			$leasing_win_lose_res = $command10->queryAll();

			$command11= $connection->createCommand($sql11);
			$res_region = $command11->queryAll();

			// print_r($tot_winner_confirm);
			// print_r($data_source);
			// print_r($leasing_res);
			// print_r($status_res);
			// print_r($status_tot_res);
			// print_r($durasi_res);
			// print_r($leasing_durasi_res);
			// print_r($leasing_stat_res);

			$data_prepare = array();
			// $data_prepare
			$data_prepare['MENANG']['name'] = "MENANG";
			$data_prepare['KALAH']['name'] = "KALAH";

			/*foreach ($leasing_res as $key2 => $value2) {
				
					foreach ($status_tot_res as $key => $value) {
						if($value['STATUS'] == "MENANG" && $value2['leasing_id'] == $value['leasing_id']){
							$data_prepare['MENANG']['data'][$key2] = intval($value['total']);
						}elseif($value['STATUS'] == "KALAH" && $value2['leasing_id'] == $value['leasing_id']){
							$data_prepare['KALAH']['data'][$key2] = intval($value['total']);
						}
					}
			}*/
			foreach ($leasing_win_lose_res as $key2 => $value2) {
				$data_prepare['MENANG']['data'][$key2] = intval($value2['menang']);
				$data_prepare['KALAH']['data'][$key2] = intval($value2['kalah']);
			}
			$win_result_json = array();
			foreach ($data_prepare as $value) {
				# code...
				array_push($win_result_json, $value);
			}

			$leasing_label = array();
			foreach ($leasing_win_lose_res as $value) {
				# code...
				array_push($leasing_label, $value['leasing_name']);
			}
			/*$leasing_label = array();
			foreach ($leasing_res as $value) {
				# code...
				array_push($leasing_label, $value['nama']);
			}*/




			$leasing_dur_label = array();
			$leasing_dur_val = array();
			foreach ($leasing_durasi_res as $key => $value) {
				# code...
				array_push($leasing_dur_label, $value['leasing_name']);
				array_push($leasing_dur_val, array('y' => intval($value['avg']/60), 'totPros' => intval($value['tot_prosp'])));
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

			foreach ($res_region as $key => $value) {
				# code...
				$region[$key]['name'] = $value['name'];
				$region[$key]['id'] = $value['id'];
				$region_label[]=$value['name'];
			}

			// print_r($data_prepare);
			// print_r($win_result_json);
			// print_r($leasing_label);
			// print_r($leasing_dur_label);
			// print_r($leasing_dur_val);
			$count=Yii::app()->db->createCommand('SELECT count(*) from (('. $sql9 .') as alias)')->queryScalar();

			$dataProvider = new CSqlDataProvider($sql9, array(
				'totalItemCount' => $count,
				'sort' => array(
						'defaultOrder' => 'd.id DESC ',
					),
				'pagination' => array(
						// 'pageSize' => intval(Yii::app()->params["defaultPageSize"]),
						'pageSize' => 20,
					),
			));

			// exit;
			$this->render('index',array(
				'dataProvider' => $dataProvider,
				'region' => $region,
				'win_result_array'=>$win_result_json,
				'leasing_label' => $leasing_label,

				'leasing_dur_label' => $leasing_dur_label,
				'leasing_val_array' => $leasing_dur_val,
				/*'dealer_json_array'=>$dealer_result_json,
				'leasing_label' => $leasing_label2,
				'dealer_label' => $dealer_label,*/
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

	public function actionDownloadleasingstat(){
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Alfa Scorpii")
		->setLastModifiedBy("Alfa Scorpii")
		->setTitle("Download Data Leasing")
		->setSubject("Download Data Leasing")
		->setDescription("Download Data Leasing")
		->setKeywords("download,Download Data Leasing")
		->setCategory("Download");
		
		$objPHPExcel->getActiveSheet()->setTitle('DATA LEASING');
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


		if(isset(Yii::app()->request->cookies['filter_leasing']) && Yii::app()->request->cookies['filter_leasing'] != ''){
			$filter_leasing = Yii::app()->request->cookies['filter_leasing'];
			$where = $where." AND d.id = '$filter_leasing'";
		}
		
		$query = "SELECT 

						d.nama as leasing_name,
						d.id as leasing_id,
						UCASE(s.name) as region,
						COALESCE(tot_pros.prosCount,0) AS total_prospect,
						COALESCE(terlibat.terlibatCount,0) AS terlibat,
						COALESCE(winn.winCount,0) AS menang,
						COALESCE(lose.loseCount,0) AS kalah,
						d.token as sisa_token

						FROM leasing as d

						LEFT JOIN (
						            SELECT COUNT(*) AS prosCount, leasing_id AS leasId 
						            FROM send_mail
						            GROUP BY leasing_id
						        ) AS tot_pros
							ON tot_pros.leasId=d.id

						LEFT JOIN (
						            SELECT COUNT(*) AS terlibatCount, leasing_id AS leasId 
						            FROM send_mail
						    		WHERE bidding_token IS NOT NULL
						            GROUP BY leasing_id
						        ) AS terlibat
							ON terlibat.leasId=d.id

						LEFT JOIN (
						            SELECT COUNT(*) AS winCount, leasing_id AS leasId 
						            FROM winner
						    		
						            GROUP BY leasing_id
						        ) AS winn
							ON winn.leasId=d.id


						LEFT JOIN (
						            SELECT COUNT(terlibat.leasing_id) as loseCount, terlibat.leasing_id AS leasId FROM (SELECT g.id, g.leasing_id, g.prospect_id
						                        FROM send_mail g
						                        WHERE g.bidding_token IS NOT NULL
						                        
						            ) as terlibat 
						            WHERE terlibat.id NOT IN (SELECT send_email_id FROM winner )  
						            GROUP BY terlibat.leasing_id
						        ) AS lose
							ON lose.leasId=d.id

						left join winner c on d.id = c.leasing_id
						left join prospect a on a.id = c.prospect_id
						left join send_mail g on a.id = g.prospect_id
						left join user f on a.from_email = f.email
						left join region s on s.id = d.region_id
					
					".$where."

					GROUP BY d.id
					";

		$data = Yii::app()->db->createCommand($query)->queryAll();
		$xlFieldName = array(
				'No',
				'Leasing',
				'Region',
				'Total Prospect',
				//start detail prospect
				'Terlibat',
				'Menang',
				'Kalah',
				'Sisa Token'
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
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['leasing_name']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['region']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['total_prospect']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['terlibat']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['menang']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['kalah']);
				$worksheet->setCellValueByColumnAndRow($colId++, $rowId, $value['sisa_token']);
			}
		}
		
		$objPHPExcel->setActiveSheetIndex(0);
		$filename = 'Data Statistik Leasing'.date("Ymdhis");
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

	public function allowedActions()
	{
		return 'viewDataWinning';
		return 'viewDataLose';
	}

	public function actions(){
		return array(
			'viewDataWinning' => array(
		    		'class' => 'application.actions.ViewAction',
		    		'ajaxView' => '_view_datawinning',
		    		'view' => '_view_datawinning',
		    		'disableScripts' => array('jquery.yiigridview.js')
		    	),
			'viewDataLose' => array(
		    		'class' => 'application.actions.ViewAction',
		    		'ajaxView' => '_view_datalose',
		    		'view' => '_view_datalose',
		    		'disableScripts' => array('jquery.yiigridview.js')
		    	),

			'reportDealer' => array(
					'class' => 'application.controllers.DealerReportAction',
					
				),
		);
		// $this->render('_view_list_location_survey',array(
			// 'condition'=>'testparam',
		// ));
	}

	public function loadModel()
	{
	  
	}


}

?>