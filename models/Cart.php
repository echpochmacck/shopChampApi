<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cart".
 *
 * @property int $id
 * @property int $user_id
 * @property float $cart_sum
 * @property float $total_quantity
 *
 * @property CartComposition[] $cartCompositions
 * @property User $user
 */
class Cart extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cart';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'cart_sum', 'total_quantity'], 'required'],
            [['user_id'], 'integer'],
            [['cart_sum', 'total_quantity'], 'number'],
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
            'cart_sum' => 'Cart Sum',
            'total_quantity' => 'Total Quantity',
        ];
    }

    /**
     * Gets query for [[CartCompositions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCartCompositions()
    {
        return $this->hasMany(CartComposition::class, ['cart_id' => 'id']);
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
