<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property int $user_id
 * @property float $sum
 * @property string $date
 * @property int $status_id
 * @property int $total_quantity
 *
 * @property BlockInfo[] $blockInfos
 * @property OrderComposition[] $orderCompositions
 * @property Status $status
 * @property User $user
 */
class Order extends \yii\db\ActiveRecord
{
    public $user_email = '';
    public $status = '';
    public $created_at = '';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'sum', 'status_id', 'total_quantity'], 'required'],
            [['user_id', 'status_id', 'total_quantity'], 'integer'],
            [['sum'], 'number'],
            [['date'], 'safe'],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => Status::class, 'targetAttribute' => ['status_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'sum' => 'Sum',
            'date' => 'Date',
            'status_id' => 'Status ID',
            'total_quantity' => 'Total Quantity',
        ];
    }

    /**
     * Gets query for [[BlockInfos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBlockInfos()
    {
        return $this->hasMany(BlockInfo::class, ['order_id' => 'id']);
    }

    /**
     * Gets query for [[OrderCompositions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderCompositions()
    {
        return $this->hasMany(OrderComposition::class, ['order_id' => 'id']);
    }

    /**
     * Gets query for [[Status]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(Status::class, ['id' => 'status_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
