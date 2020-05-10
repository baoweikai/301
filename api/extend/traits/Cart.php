<?php
namespace traits;

/*
 * This is the model class for table "cart".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $session_id
 * @property integer $item_id
 * @property string $sku
 * @property string $name
 * @property integer $number
 * @property string $market_price
 * @property string $price
 * @property string $thumb
 * @property integer $type
 */
trait Cart
{
    protected $items;  // 所有商品
    protected $total;     // 商品数量
    protected $amount;    // 商品总价
    /*
     * 购物车商品
     */
    public function items(){
        if($this->items !== null){
            return $this->items;
        }
        $items = self::where(['or', 'session_id = "' . session_id() . '"', 'user_id = ' . (session('user.id') ? -1: session('user.id'))])->all();
        return $this->items = $items;
    }
    /*
     * 总额
     */
    public function amount(){
        if($this->amount !== null){
            return $this->amount;
        }
        $amount = 0;
        if($this->items !== null){
            foreach($this->items as $item) {
                $amount += $item->price * $item->number;
            }
        }else{
            $amount = self::where(['or', 'session_id = "' . session_id() . '"', 'user_id = ' . intval(session('user.id'))])->sum('number * price');
        }
        return $this->amount = floatval($amount);
    }
    /*
     * 全部商品数量
     */
    public function total(){
        if($this->total !== null){
            return $this->total;
        }

        if($this->items !== null){
            $total = 0;
            foreach($this->items as $item) {
                $total += $item->number;
            }
        }else{
            $total = self::where(['or', 'session_id = "' . session_id() . '"', 'user_id = ' . session('UserID')])->sum('number');
        }
        return $this->total = intval($total);
    }
}
