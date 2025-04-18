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

    public $newFiles = [];
    // public $files = '';
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
            ['files', 'file', 'extensions' => 'png, jpeg, jpg', 'maxFiles' => 3],
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
            // ->with('productCategories')
            // ->with('category')
            // ->innerJoin(['t1' => $query1], 't1.id = product.id')
        ;


        $query->filterWhere(['product.id' => $data['product_id'] ?? null]);

        if (isset($data['cart_id'])) {
            $query
                ->addSelect(['cart.id as cart_id', 'cart_composition.quantity as quantity_in_cart', 'cart_composition.poisition_sum as position_sum'])
                ->innerJoin('cart_composition', 'product.id = cart_composition.product_id')
                ->innerJoin('cart', 'cart.id = cart_composition.cart_id')
                ->where(['user_id' => Yii::$app->user->id])
            ;
        }
        if (isset($data['file_info'])) {
            $query
                ->with('files');
        }
        if (isset($data['sort_quantity'])) {
            $query
                ->andWhere('quantity > 1');
        }

        if (isset($data['order_id'])) {
            $query
                ->addSelect(['order.id as order_id', 'order_composition.quantity as quantity', 'order_composition.position_sum as position_sum'])
                ->innerJoin('order_composition', 'product.id = order_composition.product_id')
                ->innerJoin('order', 'order.id = order_composition.order_id')
                ->where(['user_id' => Yii::$app->user->id, 'order.id' => $data['order_id']])
            ;
        }
        if (isset($data['search'])) {
            // var_dump($data['search'] );die;
            foreach ($data['search'] as $val) {
                $query
                    ->andFilterWhere([$val['operator'],  $val['field'], $val['val']]);
            }
        }

        $query->asArray();

        if (isset($data['product_id'])) {
            return $query->one();
        } else {
            $res = $query->all();
            return array_map(function ($val) {
                $val['files'] = $val['files'] ? array_map(fn($val) => Yii::$app->request->getHostInfo() . '/uploads/' . $val['title'], $val['files']) : '';
                return $val;
            }, $res);
        }
    }
    
    
    public static function getPosSum($quantity, $price)
    {
        return $quantity * $price;
    }
    public function upload()
    {
        if (count($this->newFiles)) {
            foreach ($this->newFiles as $file) {

                $model = new File();
                $path = Yii::$app->security->generateRandomString() . ".{$file->extension}";
                $file->saveAs('uploads/' . $path);
                $model->title = $path;
                $model->prodcut_id = $this->id;
                $model->save(false);
            }
        }
    }
}
