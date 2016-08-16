<?php

class DashboardController extends Controller
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
				/*Yii::app()->request->cookies['filter_region'] = new CHttpCookie('filter_region', $_POST['filter_region']);
				Yii::app()->request->cookies['filter_status'] = new CHttpCookie('filter_status', $_POST['filter_status']);
				Yii::app()->request->cookies['filter_leasing'] = new CHttpCookie('filter_leasing', $_POST['filter_leasing']);*/
			    
				exit();
			}else{
				// print_r($_POST);
				/*if(empty($_GET)){
					unset(Yii::app()->request->cookies['filter_region']);
					unset(Yii::app()->request->cookies['filter_status']);
					unset(Yii::app()->request->cookies['filter_leasing']);
				}*/
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

			$where = "where a.id is not null";
			$where .= "  and a.region_id is not null ";
			
			$date_range['from'] = $data_from_date;
			$date_range['to'] = $data_to_date;
			$where = $where." AND a.created_at BETWEEN '".$data_from_date." 00:00:00' AND '".$data_to_date." 23:59:59'";

			$sql = "
					select a.id as id,a.nama as prospect,UCASE(e.name) as region,a.region_id as leasing_terlibat,f.dealer_name as sumber_order, f.dealer_id, a.created_at as time_sent_order,c.created_at as time,
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

			$sql5 = "SELECT wew.winner_confirm, COUNT(wew.winner_confirm) as tot_win FROM (".$sql.") as wew GROUP BY wew.winner_confirm";

			$sql6 = "SELECT d.id, UCASE(e.name) as region_name,
					CASE 
						WHEN d.leasing_name is null THEN d.nama
						ELSE d.leasing_name
					END as pemenang 
					FROM winner c
				left join leasing d on c.leasing_id = d.id
				left join prospect a on a.id=c.prospect_id
				left join region e on d.region_id = e.id
				".$where."
				group by pemenang";

			$sql7 = "SELECT d.id, COUNT(c.id) as tot, 
					CASE 
						WHEN c.winner_confirm =1 THEN 'Confirmed'
						WHEN c.winner_confirm =2 THEN 'Approved'
						WHEN c.winner_confirm is null THEN 'No Feedback'
						WHEN c.winner_confirm =98 THEN 'No Feedback'
						WHEN c.winner_confirm =3 THEN 'Cancel'
						ELSE 'Rejected'
					END as winner_confirm2,
					CASE 
						WHEN d.leasing_name is null THEN d.nama
						ELSE d.leasing_name
					END as pemenang 
					FROM winner c
				left join leasing d on c.leasing_id = d.id
				left join prospect a on a.id = c.prospect_id
				".$where."
				group by winner_confirm2, c.leasing_id";

			$sql8 = "SELECT COUNT(tbl1.winner_confirm) as tot_confirm, tbl1.sumber_order, tbl1.dealer_id, tbl1.winner_confirm   FROM (".$sql.") as tbl1 GROUP BY tbl1.winner_confirm, tbl1.dealer_id  ";
			

			$sql9 = "SELECT CASE 
						WHEN f.dealer_name is null THEN f.name
						ELSE f.dealer_name
					END as sumber_order, f.dealer_id 
					FROM prospect a 
					left join user f on a.from_email = f.email
					 ".$where."
					GROUP BY f.dealer_id";

			$command5= $connection->createCommand($sql5);
			$tot_winner_confirm = $command5->queryAll();

			$command6= $connection->createCommand($sql6);
			$leasing_res = $command6->queryAll();

			$command7= $connection->createCommand($sql7);
			$grafik_res = $command7->queryAll();

			$command8= $connection->createCommand($sql8);
			$dealer_res = $command8->queryAll();

			$command9= $connection->createCommand($sql9);
			$sumber_order = $command9->queryAll();

			$leasing_label = array();
			$leasing_label2 = array();
			foreach ($leasing_res as $key => $value) {
				# code...
				// $region[$key]['pemenang'] = $value['pemenang'];
				// $region[$key]['id'] = $value['id'];
				$leasing_label[]=$value['pemenang'];
				$leasing_label2[]=$value['pemenang']." (".$value['region_name'].")";
			}

			$dealer_label = array();
			$dealer_label2 = array();
			foreach ($sumber_order as $key => $value) {
				$dealer_label[] = $value['sumber_order'];
				$dealer_label2[] = $value['sumber_order']."-".$value['dealer_id'];
			}

			// print_r($leasing_label);
			// print_r($grafik_res);
			$data_serial = array();

			foreach ($grafik_res as $key => $value) {
				# code...
				// echo($value['winner_confirm2']);
				foreach ($tot_winner_confirm as $key => $value2) {
					if($value['winner_confirm2'] == $value2['winner_confirm']){
						$data_serial[$value2['winner_confirm']]['name'] = $value2['winner_confirm'];
					}
				}

				foreach ($leasing_label as $key => $value3) {
					# code...
					if(!isset($data_serial[$value['winner_confirm2']]['data'][$key])){
						$data_serial[$value['winner_confirm2']]['data'][$key] = 0;
					}
				}

				foreach ($leasing_label as $key => $value4) {
					# code...
					// echo $value4."=".$value['pemenang'].'<br>';
					if($value4 == $value['pemenang'] && $value['tot'] != 0){
						$data_serial[$value['winner_confirm2']]['data'][$key] = intval($value['tot']);
					}
				}
				
			}

			$leasing_result_json = array();
			foreach ($data_serial as $value) {
				# code...
				array_push($leasing_result_json, $value);
			}

			

			$data_prepare = array();

			foreach ($dealer_res as $value2) {
				// echo "2";
				foreach ($tot_winner_confirm as $value) {
					if($value2['winner_confirm'] == $value['winner_confirm']){
						$data_prepare[$value['winner_confirm']]['name'] = $value['winner_confirm'];
					}
				}
				foreach ($sumber_order as $key => $value3) {
					# code...
					// echo $data_prepare[$value2['winner_confirm']]['data'][$key];
					// echo "<br>";
					if(!isset($data_prepare[$value2['winner_confirm']]['data'][$key]) ){
						$data_prepare[$value2['winner_confirm']]['data'][$key] = 0;
					}
	
					// echo "3";
					
				}
				foreach ($sumber_order as $key => $value3) {
					# code...
					if($value3['dealer_id'] == $value2['dealer_id'] && $value2['tot_confirm'] != 0){
						$data_prepare[$value2['winner_confirm']]['data'][$key] = intval($value2['tot_confirm']);
					}
				}

			}

			$dealer_result_json = array();
			foreach ($data_prepare as $value) {
				# code...
				array_push($dealer_result_json, $value);
			}

			// print_r($sumber_order);
			// print_r($dealer_result_json);

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

			// exit;
			$this->render('index',array(
				'leasing_json_array'=>$leasing_result_json,
				'dealer_json_array'=>$dealer_result_json,
				'leasing_label' => $leasing_label2,
				'dealer_label' => $dealer_label,
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
}

?>