<?php

class DealerMdl extends Dealer
{
	public static function getOptions(){

		$criteria = new CDbCriteria();
		//$criteria->compare('is_active', 1);
		$arData = Dealer::model()->findAll($criteria);

		if (is_array($arData)){
			$data = array();
			foreach ($arData as $key => $value) {
				$data[$value->id] = $value->dealer_name;
			}

			return $data;
		}

		return array();
	}

}

?>