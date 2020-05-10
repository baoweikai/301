<?php
namespace traits;

/*
 * 搜索器(常用搜索字符安).
 *
 * @author Weikai
 * @link http://www.yiiframework.com/
 * @copyright 2008-2013 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

trait Search
{
    // 用户名
    public function searchUsernameAttr($query, $value, $data)
    {
        if(!empty($value)) {
            $query->where('username', 'like', $value . '%');
        }
    }
    // 手机号
    public function searchMobileAttr($query, $value, $data)
    {
        $query->where('mobile','like', $value . '%');
    }
    // 状态
    public function searchStateAttr($query, $value, $data){
        if($value !== '' && $value !== null){
            $query->where('state', intval($value));
        }
    }
    // 类型
    public function searchTypeAttr($query, $value, $data){
        if($value !== '' && $value !== null){
            $query->where('type', intval($value));
        }
    }
    // 用户
    public function searchUserIdAttr($query, $value, $data)
    {
        if(!empty($value)) {
            $query->where('user_id', $value);
        }
    }
    // 代理商
    public function searchAgentIdAttr($query, $value, $data)
    {
        if(!empty($value)) {
            $query->where('agent_id', intval($value));
        }
    }
    // 姓名
    public function searchNameAttr($query, $value, $data)
    {
        if(!empty($value)) {
            $query->where('name', 'like', $value . '%');
        }
    }
    // 标题
    public function searchTitleAttr($query, $value, $data)
    {
        if(!empty($value)){
            $query->where('title','like', $value . '%');
        }
    }
    // 创建时间
    public function searchCreateAtAttr($query, $value, $data)
    {
        if(!empty($value)) {
            if (!empty($value[0])) {
                $value[0] = str_replace('"','',$value[0]);
                $query->where('create_at', '>=', strtotime($value[0]));
            }
            if (!empty($value[1])) {
                $value[1] = str_replace('"','',$value[1]);
                $query->where('create_at', '<=', strtotime($value[1]));
            }
        }
    }
    // 发布时间
    public function searchPublishAtAttr($query, $value, $data)
    {
        if(!empty($value)) {
            if (!empty($value[0])) {
                $value[0] = str_replace('"','',$value[0]);
                $query->where('publish_at', '>=', strtotime($value[0]));
            }
            if (!empty($value[1])) {
                $value[1] = str_replace('"','',$value[1]);
                $query->where('publish_at', '<=', strtotime($value[1]));
            }
        }
    }
    // 支付时间
    public function searchPayAtAttr($query, $value, $data)
    {
        if(!empty($value[0])){
            $query->where('pay_at', '<=', strtotime($value[0] . ' 00:00:00'));
        }
        if(!empty($value[1])){
            $query->where('pay_at', '>=', strtotime($value[1] . ' 23:59:59'));
        }
    }
    // 积分
    public function searchScoreAttr($query, $value, $data)
    {
        if(!empty($value)) {
            $query->where('score', intval($value));
        }
    }
    // 订单编号
    public function searchOrderNoAttr($query, $value, $data){
        if(!empty($value)){
            $query->where('order_no', trim($value));
        }
    }
    // 订单状态
    public function searchOrderTypeAttr($query, $value, $data){
        if(!empty($value) || $value == '0'){
            $query->where('order_type', intval($value));
        }
    }
    // 商品名称
    public function searchProdNameAttr($query, $value, $data)
    {
        if(!empty($value)) {
            $query->where('prod_name', 'like', $value . '%');
        }
    }
		// 卡号编号
		public function searchCodeAttr($query, $value, $data){
			if(!empty($value)){
				$query->where('code', trim($value));
			}
		}

		// 支付方式
		public function searchPayTypeAttr($query, $value, $data){
			if(!empty($value) || $value == '0'){
				$query->where('pay_type', intval($value));
			}
		}

		// 昵称
		public function searchNicknameAttr($query, $value, $data)
		{
			if(!empty($value)) {
				$query->where('nickname', 'like', $value . '%');
			}
		}
}
