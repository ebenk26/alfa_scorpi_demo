<?php

class GrabmailController extends Controller
{
	public function actionIndex()
	{ // phpinfo();exit();
		 // Do stuff
	// echo 'masuk cron';
		$day = date('l');
		$criteria = new CDbCriteria;
		$criteria->addCondition("day = '".$day."'");
		$overtime = Overtime::model()->find($criteria);

		if($overtime){
			// if (date("l") != $overtime->day){
			// 	echo 'tidak masuk day';echo date('l');echo $overtime->day;exit();
			// }else{
				echo 'masuk day = ';echo date('l');
				// if(date('H:i:s') >= $overtime->from_time && date('H:i:s') <= $overtime->to_time){
					echo '<br>masuk time = '.date('H:i:s');


					$hostname = '{libra.gotnotice.com:143/imap/notls}INBOX';
					// $username = 'smsincoming@nadyne.com';
					// $password = 'iniSmsYamaha889';
					$username = 'rizky@nadyne.com';
					$password = 'rizky7889';
					$inbox = imap_open($hostname,$username,$password) or die('Cannot connect to server: ' . imap_last_error());
					$n_msgs = imap_num_msg($inbox);
					$emails = imap_search ( $inbox, "UNSEEN");
					if($emails) {
						$count = 1;

						/* put the newest emails on top */
						rsort($emails);

						/* for every email... */
						foreach($emails as $email_number)
						{
							#var_dump($emails);
							
							/* get information specific to this email */
							$overview = imap_fetch_overview($inbox, $email_number, 0);
							echo "<pre>";
							print_r($overview);
							echo "</pre>";
							
							/* get mail message */
							$message = imap_fetchbody($inbox,$email_number,1);
							
							/* get mail from */
							$header = imap_headerinfo($inbox,$email_number);
							$from = $header->from[0]->mailbox . "@" . $header->from[0]->host;
							
							/* get mail password from object */
							$password = md5($overview[0]->subject);
							$udate = $overview[0]->udate;
							
							/* search specific user */
							$criteria = new CDbCriteria;  
							$criteria->addCondition("email = '".$from."' and password = '".$password."'");
							$user = User::model()->find($criteria);
							
							/* search specific prospect */
							$criteria2 = new CDbCriteria;  
							$criteria2->addCondition("udate = '".$udate."'");
							$prospect = Prospect::model()->findAll($criteria2);
							$count = count($prospect);
							$user_id = '';
							
							/* load model prospect */
							$_model = new Prospect;
							
							/* if there is record in prospect */
							if($count > 0)
							{
								
							}
							/* else there is NO record in prospect */
							else
							{
								if($user)
								{
									$user_id = $user->id;
								}else
								{
									/* load model inbox */
									$_model = new inbox;
									$_model->message = $message;
								}
								$explode = explode('-----',$message);
								$res = explode('#',$explode[0]);
								$nik = '';
								$nama = '';
								$alamat = '';
								$ttl = '';
								$no_hp = '';
								$tipe = '';
								$dp = '';
								$jam_survey = '';
								$keterangan = '';
								$region = '';
								$profil_konsumen = '';
								
								if(isset($res[0]))
								{
									$nik = $res[0];
								}
								if(isset($res[1]))
								{
									$nama = $res[1];
								}
								if(isset($res[2]))
								{
									$alamat = $res[2];
								}
								if(isset($res[3]))
								{
									$ttl = $res[3];
								}
								if(isset($res[4]))
								{
									$no_hp = $res[4];
								}
								if(isset($res[5]))
								{
									$tipe = $res[5];
								}
								if(isset($res[6]))
								{
									$dp = $res[6];
								}
								if(isset($res[7]))
								{
									$jam_survey = $res[7];
								}
								if(isset($res[8]))
								{
									$keterangan = $res[8];
								}
								if(isset($res[9]))
								{
									// $region = $res[9];
									$region = $user->region_id;
								}
								if(isset($res[10]))
								{
									$profil_konsumen = $res[10];
								}
								if(isset($res[11]))
								{
									$nama_salesman = $res[11];

									$_model->nik = $nik;
									$_model->nama = $nama;
									$_model->alamat = $alamat;
									$_model->ttl = $ttl;
									$_model->no_hp = $no_hp;
									$_model->tipe = $tipe;
									$_model->dp = $dp;
									$_model->jam_survey = $jam_survey;
									$_model->keterangan = $keterangan;
									$_model->region_id = $region;
									$_model->profil_konsumen = $profil_konsumen;
									$_model->user_id = $user_id;
									$_model->udate = $udate;
									$_model->from_email = $from;
									$_model->nama_salesman = $nama_salesman;
									$_model->status = 0;
									$_model->created_by = 'SYSTEM';
									$_model->created_at = new CDbExpression('NOW()');
									$_model->updated_at = new CDbExpression('NOW()');

									$errormail = new ErrorGrabMail;
									$users = User::model()->findByPk($user->id);

									date_default_timezone_set('Asia/Jakarta');
									$time = date('H');
									
									if($time >= 6 && $time < 10)
									{
										$timemessage = 'Pagi';
									}else if($time >= 10 && $time < 15)
									{
										$timemessage = 'Siang';
									}else if($time >= 15 && $time <= 18)
									{
										$timemessage = 'Sore';
									}else
									{
										$timemessage = 'Malam';
									}
									$messages = "Dear ".$users->name."<br>
												Selamat ".$timemessage."<br>

												Data Prospect yang anda kirimkan salah :<br>
												".$message."<br><br>
												Pastikan format yang anda gunakan Benar.<br>
												Format benar :<br>
												NIK#Nama Prospect#Alamat#Tempat, tanggal lahir#No Telepon#Tipe Motor#DP yang disetujui# Tanggal & Jam survey#Keterangan#Region#Status Konsumen#Nama Salesman<br>
												Pastikan tidak menggunakan enter di setiap hastag (#) dan format dalam penulisan jam survey tidak salah, yaitu :<br>
												dd-mm-yyyy hh:mm<br><br>
												Terimakasih,<br>
												Best regards,<br>
												Admin
												";
									$errormail->message = $messages;
									$errormail->email = $from;
									$errormail->created_by = 'SYSTEM';
									$errormail->created_at = new CDbExpression('NOW()');
									$errormail->cc_email = 'ebenk.rzq2@gmail.com';
																	
									$array = preg_split("/[[:space:]]+/",$jam_survey);
									if($array[0] && isset($array[1])){
										$return = 'array bener';
										$strexplode2 = explode('-', $array[0]);
										$strexplode3 = explode(':', $array[1]);
										if (array($strexplode2) && array($strexplode3)) {
											if(isset($strexplode2[2]) && isset($strexplode2[1]) && isset($strexplode2[0]) && isset($strexplode3[1]) && isset($strexplode3[0])){
												if ((is_numeric($strexplode2[2]) && strlen($strexplode2[2]) == 4) && (is_numeric($strexplode2[1]) && strlen($strexplode2[1]) == 2) && (is_numeric($strexplode2[0]) && strlen($strexplode2[0]) == 2) && (is_numeric($strexplode3[1]) && strlen($strexplode3[1]) == 2) && (is_numeric($strexplode3[0]) && strlen($strexplode3[0]) == 2)) {
													$return .= "true1";
													echo "true1";
													$_model->save();
												}else{
													$return .= "false1";
													echo "false1";
													$errormail->reason = $return;
													$errormail->save();
												}
											}else{
												$return .= "false2";
												echo "false2";
												$errormail->reason = $return;
												$errormail->save();
											}
										}else{
											$return .= "false3";
											echo "false3";
											$errormail->reason = $return;
											$errormail->save();
										}
										// exit();
									}else{
										$return .= "false4";
										echo "false4";
										$errormail->save();
										// exit();
									}
								}else{
									date_default_timezone_set('Asia/Jakarta');
									$time = date('H');
									
									if($time >= 6 && $time < 10)
									{
										$timemessage = 'Pagi';
									}else if($time >= 10 && $time < 15)
									{
										$timemessage = 'Siang';
									}else if($time >= 15 && $time <= 18)
									{
										$timemessage = 'Sore';
									}else
									{
										$timemessage = 'Malam';
									}
									$errormail = new ErrorGrabMail;
									$criteria = new CDbCriteria;  
									$criteria->addCondition("email = '".$from."' and password = '".$password."'");
									$user = User::model()->find($criteria);
									if ($user) {
										$messages = "Dear ".$user->name."<br>
													Selamat ".$timemessage."<br>

													Data Prospect yang anda kirimkan salah :<br>
													".$message."<br><br>
													Pastikan format yang anda gunakan Benar.<br>
													Format benar :<br>
													NIK#Nama Prospect#Alamat#Tempat, tanggal lahir#No Telepon#Tipe Motor#DP yang disetujui# Tanggal & Jam survey#Keterangan#Region#Status Konsumen#Nama Salesman<br>
													Pastikan tidak menggunakan enter di setiap hastag (#) dan format dalam penulisan jam survey tidak salah, yaitu :<br>
													dd-mm-yyyy hh:mm<br><br>
													Terimakasih,<br>
													Best regards,<br>
													Admin
													";
										$errormail->message = $messages;
										$errormail->email = $from;
										$errormail->created_by = 'SYSTEM';
										$errormail->created_at = new CDbExpression('NOW()');
										$errormail->cc_email = 'ebenk.rzq2@gmail.com';
										$errormail->reason = 'error no 11 #';
										$errormail->save();
										}
								}
							}
								$status = imap_setflag_full($inbox, $email_number, "\\Seen", ST_UID);
								echo gettype($status) . "\n";
								echo $status . "\n";
								if($count++ >= $email_number) break;
						}//foreach

						/* close the connection */
						imap_close($inbox);
					}//if emails
					else{
						echo 'not ok';
					}
				// }else{
				// 		echo '<br>tidak masuk time = '.date('H:i:s');exit();
				// }
			// }
		}
		// $this->render('index');
	}
	public function validateDate($date, $format = 'd-m-Y H:i')
	{
	    $d = DateTime::createFromFormat($format, $date);
	    return $d && $d->format($format) == $date;
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