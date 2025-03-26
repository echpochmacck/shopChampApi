<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property float $price
 * @property int $quantity
 *
 * @property CartComposition[] $cartCompositions
 * @property File[] $files
 * @property OrderComposition[] $orderCompositions
 * @property ProductCategory[] $productCategories
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'description', 'price', 'quantity'], 'required'],
            [['price'], 'number'],
            [['quantity'], 'integer'],
            [['title', 'description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'price' => 'Price',
            'quantity' => 'Quantity',
        ];
    }

    /**
     * Gets query for [[CartCompositions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCartCompositions()
    {
        return $this->hasMany(CartComposition::class, ['product_id' => 'id']);
    }

    /**
     * Gets query for [[Files]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany(File::class, ['prodcut_id' => 'id']);
    }

    /**
     * Gets query for [[OrderCompositions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderCompositions()
    {
        return $this->hasMany(OrderComposition::class, ['product_id' => 'id']);
    }

    /**
     * Gets query for [[ProductCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductCategories()
    {
        return $this->hasMany(ProductCategory::class, ['product_id' => 'id']);
    }
    public static function getProduct($data = [])
    {
        $query1 = Product::find()
            ->select([
                'category.title as category_title',
                'product.id as id',
            ])
            ->innerJoin('product_category', 'product_category.product_id = product.id')
            ->innerJoin('category', 'product_category.category_id = category.id');
        $query = Product::find()
            ->select([
                'product.title',
                'product.id',
                'product.description',
                'product.price',
                'product.quantity as product_quantity',
                // 'category_title'
            ])
            // ->innerJoin(['t1' => $query1], 't1.id = product.id')
        ;


        $query->filterWhere(['product.id' => $data['product_id'] ?? null]);

        $query->asArray();
        if (isset($data['cart_id'])) {
            $query
                ->addSelect(['cart.id as cart_id', 'cart_composition.quantity as quantity_in_cart', 'cart_composition.poisition_sum as position_sum'])
                ->innerJoin('cart_composition', 'product.id = cart_composition.product_id')
                ->innerJoin('cart', 'cart.id = cart_composition.cart_id')
                ->where(['user_id' => Yii::$app->user->id])
            ;
        }
        if (isset($data['order_id'])) {
            $query
                ->addSelect(['order.id as order_id', 'order_composition.quantity as quantity', 'order_composition.position_sum as position_sum'])
                ->innerJoin('order_composition', 'product.id = order_composition.product_id')
                ->innerJoin('order', 'order.id = order_composition.order_id')
                ->where(['user_id' => Yii::$app->user->id])
            ;
        }
        if (isset($data['product_id'])) {
            return $query->one();
        } else {
            $res = $query->all();
            // if ($data['category_info']) {
            // return  array_map(function ($product) {
            //     $product['categories'][] = Category::find()->select('title')
            //         ->innerJoin('product_category', 'product_category.category_id = category.id')
            //         ->where(['product_id' => $product['id']])
            //         ->asArray()->all();
            //     return $product;
            // }, $res);


            return $res;
        }
    }
    public static function getPosSum($quantity, $price)
    {
        return $quantity * $price;
    }
}
