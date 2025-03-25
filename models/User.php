<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $token
 * @property int $role_id
 * @property float $cash
 * @property string $authKey
 *
 * @property Cart[] $carts
 * @property Order[] $orders
 * @property Role $role
 */
class User extends \yii\db\ActiveRecord  implements IdentityInterface
{
    const SCENARIO_REGISTER = 'register';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            ['name', 'required', 'on' => self::SCENARIO_REGISTER],
            ['email', 'email', 'on' => self::SCENARIO_REGISTER],
            ['email', 'unique', 'on' => self::SCENARIO_REGISTER],
            [['role_id'], 'integer'],
            [['cash'], 'number'],
            [['name', 'email', 'password', 'token', 'authKey'], 'string', 'max' => 255],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Role::class, 'targetAttribute' => ['role_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
            'token' => 'Token',
            'role_id' => 'Role ID',
            'cash' => 'Cash',
            'authKey' => 'authKey',
        ];
    }

    /**
     * Gets query for [[Carts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarts()
    {
        return $this->hasMany(Cart::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Role]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::class, ['id' => 'role_id']);
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['token' => $token]);
    }
    public static function findByUserName($username)
    {
        return static::findOne(['email' => $username]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    public function validatePassword(string $password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }
    public function getIsAdmin()
    {
        return $this->role_id = Role::getRoleId('admin');
    }

    public function setAuth($save = false)
    {
        $this->authKey = Yii::$app->security->generateRandomString();
        $save && $this->save(false);
    }
}
