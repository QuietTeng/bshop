<?php
/**
 * @copyright Copyright(c) 2016 aircheng.com
 * @file menuUcenter.php
 * @brief 用户中心菜单管理
 * @author nswe
 * @date 2016/3/8 9:33:25
 * @version 4.4
 */
class menuUcenter
{
    //菜单的配制数据
	public static $menu = array(
		"个人设置" => array(
			"/ucenter/info" => "账户信息",
			"/ucenter/password" => "修改密码",
			"/ucenter/address" => "地址簿",
		),
		"账户资金" => array(
			"/ucenter/account_log" => "我的余额",
			"/ucenter/integral" => "我的积分",
			"/ucenter/redpacket" => "我的优惠券",
	
		),
		"交易记录" => array(
			"/ucenter/order" => "我的订单",
			"/ucenter/refunds" => "退款申请",
			"/ucenter/favorite" => "收藏夹",
			"/ucenter/evaluation" => "商品评价",
			
		),

		"服务中心" => array(
			"/ucenter/online_recharge" => "在线充值",
			"/ucenter/consult" => "商品咨询",
			"/ucenter/message" => "短信息",
			
		),

		"应用" => array(
			"/ucenter/complain" => "站点建议",
		
		),

		
	);

    /**
     * @brief 根据权限初始化菜单
     * @param int $roleId 角色ID
     * @return array 菜单数组
     */
    public static function init($roleId = "")
    {
		//菜单创建事件触发
		plugin::trigger("onUcenterMenuCreate");
		return self::$menu;
    }
}