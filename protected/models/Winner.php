<?php

/**
 * This is the model class for table "winner".
 *
 * The followings are the available columns in table 'winner':
 * @property integer $id
 * @property integer $leasing_id
 * @property integer $prospect_id
 * @property integer $send_email_id
 * @property string $email
 * @property string $cc_email
 * @property integer $status
 * @property string $message
 * @property string $created_by
 * @property string $updated_by
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */
class Winner extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'winner';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('leasing_id, prospect_id, send_email_id, status', 'numerical', 'integerOnly'=>true),
			array('email, cc_email, created_by, updated_by', 'length', 'max'=>255),
			array('message, created_at, updated_at, deleted_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('status_echo, id, leasing_id, prospect_id, send_email_id, email, cc_email, status, message, created_by, updated_by, created_at, updated_at, deleted_at, winner_confirm', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'leasing_id' => 'Leasing',
			'prospect_id' => 'Prospect',
			'send_email_id' => 'Send Email',
			'email' => 'Email',
			'cc_email' => 'Cc Email',
			'status' => 'Status',
			'message' => 'Message',
			'created_by' => 'Created By',
			'updated_by' => 'Updated By',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'deleted_at' => 'Deleted At',
			'winner_confirm' => 'Winner Confirm',
			'status_echo' => 'status_echo',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('leasing_id',$this->leasing_id);
		$criteria->compare('prospect_id',$this->prospect_id);
		$criteria->compare('send_email_id',$this->send_email_id);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('cc_email',$this->cc_email,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('message',$this->message,true);
		$criteria->compare('created_by',$this->created_by,true);
		$criteria->compare('updated_by',$this->updated_by,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('deleted_at',$this->deleted_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Winner the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
