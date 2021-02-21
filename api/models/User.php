<?php

namespace api\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\filters\RateLimitInterface;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string  $username
 * @property string  $password_hash
 * @property string  $password_reset_token
 * @property string  $email
 * @property string  $auth_key
 * @property int     $status
 * @property int     $created_at
 * @property int     $updated_at
 * @property string  $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface, RateLimitInterface
{
	const STATUS_DELETED = 0;
	const STATUS_ACTIVE = 10;


	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return '{{%user}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function behaviors()
	{
		return [
			TimestampBehavior::className(),
		];
	}

	// 明确列出每个字段，适用于你希望数据表或
	// 模型属性修改时不导致你的字段修改（保持后端API兼容性）
	public function fields()
	{
		// return [
		//     'id',
		//     // 字段名为"email", 对应的属性名为"email_address"
		//     'email' => 'email_address',
		//     'name' => function ($model) {
		//         return $model->first_name . ' ' . $model->last_name;
		//     }
		// ];
		// remove some fields import
		// $fields = parent::fields();
		// unset($fields['auth_key'], $fields['password_hash'], $fields['password_reset_token']);
		// return $fields;
		return ['id', 'email', 'status'];
	}

	public function extraFields()
	{
		return ['username', 'email'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			['status', 'default', 'value' => self::STATUS_ACTIVE],
			['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public static function findIdentity($id)
	{
		return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function findIdentityByAccessToken($token, $type = null)
	{
		return static::findOne(['access_token' => $token, 'status' => self::STATUS_ACTIVE]);
		// throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
	}

	/**
	 * Finds user by username
	 *
	 * @param  string  $username
	 *
	 * @return static|null
	 */
	public static function findByUsername($username)
	{
		return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
	}

	/**
	 * Finds user by password reset token
	 *
	 * @param  string  $token  password reset token
	 *
	 * @return static|null
	 */
	public static function findByPasswordResetToken($token)
	{
		if (!static::isPasswordResetTokenValid($token)) {
			return null;
		}

		return static::findOne([
			'password_reset_token' => $token,
			'status' => self::STATUS_ACTIVE,
		]);
	}

	/**
	 * Finds out if password reset token is valid
	 *
	 * @param  string  $token  password reset token
	 *
	 * @return bool
	 */
	public static function isPasswordResetTokenValid($token)
	{
		if (empty($token)) {
			return false;
		}

		$timestamp = (int) substr($token, strrpos($token, '_') + 1);
		$expire = Yii::$app->params['user.passwordResetTokenExpire'];
		return $timestamp + $expire >= time();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getId()
	{
		return $this->getPrimaryKey();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAuthKey()
	{
		return $this->auth_key;
	}

	/**
	 * {@inheritdoc}
	 */
	public function validateAuthKey($authKey)
	{
		return $this->getAuthKey() === $authKey;
	}

	/**
	 * Validates password
	 *
	 * @param  string  $password  password to validate
	 *
	 * @return bool if password provided is valid for current user
	 */
	public function validatePassword($password)
	{
		return Yii::$app->security->validatePassword($password, $this->password_hash);
	}

	/**
	 * Generates password hash from password and sets it to the model
	 *
	 * @param  string  $password
	 */
	public function setPassword($password)
	{
		$this->password_hash = Yii::$app->security->generatePasswordHash($password);
	}

	/**
	 * Generates "remember me" authentication key
	 */
	public function generateAuthKey()
	{
		$this->auth_key = Yii::$app->security->generateRandomString();
	}

	/**
	 * Generates new password reset token
	 */
	public function generatePasswordResetToken()
	{
		$this->password_reset_token = Yii::$app->security->generateRandomString().'_'.time();
	}

	/**
	 * Removes password reset token
	 */
	public function removePasswordResetToken()
	{
		$this->password_reset_token = null;
	}

	// 返回某一时间允许请求的最大数量，比如设置10秒内最多5次请求（小数量方便我们模拟测试）
	public function getRateLimit($request, $action)
	{
		return [2, 10];
	}

	// 回剩余的允许的请求和相应的UNIX时间戳数 当最后一次速率限制检查时
	public function loadAllowance($request, $action)
	{
		return [$this->allowance, $this->allowance_updated_at];
	}

	// 保存允许剩余的请求数和当前的UNIX时间戳
	public function saveAllowance($request, $action, $allowance, $timestamp)
	{
		$this->allowance = $allowance;
		$this->allowance_updated_at = $timestamp;
		$this->save();
	}
}
