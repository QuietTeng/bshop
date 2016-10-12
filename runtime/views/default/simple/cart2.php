<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $this->_siteConfig->name;?></title>
	<link type="image/x-icon" href="<?php echo IUrl::creatUrl("")."favicon.ico";?>" rel="icon">
	<link rel="stylesheet" href="<?php echo $this->getWebSkinPath()."css/index.css";?>" />
	<script type="text/javascript" charset="UTF-8" src="/runtime/_systemjs/jquery/jquery-1.12.4.min.js"></script>
	<script type="text/javascript" charset="UTF-8" src="/runtime/_systemjs/autovalidate/validate.js"></script><link rel="stylesheet" type="text/css" href="/runtime/_systemjs/autovalidate/style.css" />
	<script type="text/javascript" charset="UTF-8" src="/runtime/_systemjs/form/form.js"></script>
	<script type="text/javascript" charset="UTF-8" src="/runtime/_systemjs/artdialog/artDialog.js"></script><script type="text/javascript" charset="UTF-8" src="/runtime/_systemjs/artdialog/plugins/iframeTools.js"></script><link rel="stylesheet" type="text/css" href="/runtime/_systemjs/artdialog/skins/aero.css" />
	<script type='text/javascript' src="<?php echo $this->getWebViewPath()."javascript/common.js";?>"></script>
	<script type='text/javascript' src='<?php echo $this->getWebViewPath()."javascript/site.js";?>'></script>
</head>
<body class="second">
	<div class="brand_list container_2">
		<div class="header">
			<h1 class="logo"><a title="<?php echo $this->_siteConfig->name;?>" style="background:url(<?php if($this->_siteConfig->logo){?><?php echo IUrl::creatUrl("")."".$this->_siteConfig->logo."";?><?php }else{?><?php echo $this->getWebSkinPath()."images/front/logo.gif";?><?php }?>) center no-repeat;background-size:contain;" href="<?php echo IUrl::creatUrl("");?>"><?php echo $this->_siteConfig->name;?></a></h1>
			<ul class="shortcut">
				<li class="first"><a href="<?php echo IUrl::creatUrl("/ucenter/index");?>">我的账户</a></li>
				<li><a href="<?php echo IUrl::creatUrl("/ucenter/order");?>">我的订单</a></li>
				<li><a href="<?php echo IUrl::creatUrl("/simple/seller");?>">申请开店</a></li>
				<li><a href="<?php echo IUrl::creatUrl("/seller/index");?>">商家管理</a></li>
		   		<li class='last'><a href="<?php echo IUrl::creatUrl("/site/help_list");?>">使用帮助</a></li>
			</ul>

			<p class="loginfo">
			<?php if($this->user){?>
			<?php echo isset($this->user['username'])?$this->user['username']:"";?>您好，欢迎您来到<?php echo $this->_siteConfig->name;?>购物！[<a href="<?php echo IUrl::creatUrl("/simple/logout");?>" class="reg">安全退出</a>]
			<?php }else{?>
			[<a href="<?php echo IUrl::creatUrl("/simple/login");?>">登录</a><a class="reg" href="<?php echo IUrl::creatUrl("/simple/reg");?>">免费注册</a>]
			<?php }?>
			</p>
		</div>
	    <script type="text/javascript" charset="UTF-8" src="/runtime/_systemjs/artTemplate/artTemplate.js"></script><script type="text/javascript" charset="UTF-8" src="/runtime/_systemjs/artTemplate/artTemplate-plugin.js"></script>
<script type='text/javascript' src='<?php echo $this->getWebViewPath()."javascript/orderFormClass.js";?>'></script>
<script type='text/javascript'>
//创建订单表单实例
orderFormInstance = new orderFormClass();

//DOM加载完毕
jQuery(function(){
	//商家信息
	orderFormInstance.seller = <?php echo JSON::encode($this->seller);?>;

	//商品价格
	orderFormInstance.goodsSum = "<?php echo $this->final_sum;?>";

	//配送方式初始化
	orderFormInstance.deliveryInit("<?php echo isset($this->custom['delivery'])?$this->custom['delivery']:"";?>");

	//收货地址数据
	orderFormInstance.addressInit();

	//支付方式
	orderFormInstance.paymentInit("<?php echo isset($this->custom['payment'])?$this->custom['payment']:"";?>");

	//免运费
	orderFormInstance.freeFreight = <?php echo JSON::encode($this->freeFreight);?>;
});
</script>

<div class="wrapper clearfix">
	<div class="position mt_10"><span>您当前的位置：</span> <a href="<?php echo IUrl::creatUrl("");?>"> 首页</a> » 填写核对订单信息</div>
	<div class="myshopping m_10">
		<ul class="order_step">
			<li class="current_prev"><span class="first"><a href='<?php if(IReq::get('id')){?>javascript:window.history.go(-1);<?php }else{?><?php echo IUrl::creatUrl("/simple/cart");?><?php }?>'>1、查看购物车</a></span></li>
			<li class="current"><span>2、填写核对订单信息</span></li>
			<li class="last"><span>3、成功提交订单</span></li>
		</ul>
	</div>

	<form action='<?php echo IUrl::creatUrl("/simple/cart3");?>' method='post' name='order_form' onsubmit='return orderFormInstance.isSubmit()'>

		<input type='hidden' name='direct_gid' value='<?php echo $this->gid;?>' />
		<input type='hidden' name='direct_type' value='<?php echo $this->type;?>' />
		<input type='hidden' name='direct_num' value='<?php echo $this->num;?>' />
		<input type='hidden' name='direct_promo' value='<?php echo $this->promo;?>' />
		<input type='hidden' name='direct_active_id' value='<?php echo $this->active_id;?>' />

		<div class="cart_box m_10">
			<div class="title">填写核对订单信息</div>
			<div class="cont">

				<!--地址管理 开始-->
				<div class="wrap_box">
					<h3>
						<span class="orange">收货人信息</span>
					</h3>

					<div class="prompt_4 m_10">
						<strong>常用收货地址</strong>
						<ul class="addr_list">
							<?php foreach($this->addressList as $key => $item){?>
							<li id="addressItem<?php echo isset($item['id'])?$item['id']:"";?>">
								<label><input class="radio" name="radio_address" type="radio" value="<?php echo isset($item['id'])?$item['id']:"";?>" onclick="orderFormInstance.getDelivery(<?php echo isset($item['province'])?$item['province']:"";?>);" /><?php echo isset($item['accept_name'])?$item['accept_name']:"";?>&nbsp;&nbsp;&nbsp;<?php echo isset($item['province_val'])?$item['province_val']:"";?> <?php echo isset($item['city_val'])?$item['city_val']:"";?> <?php echo isset($item['area_val'])?$item['area_val']:"";?> <?php echo isset($item['address'])?$item['address']:"";?></label> [<a href="javascript:orderFormInstance.addressEdit(<?php echo isset($item['id'])?$item['id']:"";?>);" style="color:#005ea7;">修改地址</a>] [<a href="javascript:orderFormInstance.addressDel(<?php echo isset($item['id'])?$item['id']:"";?>);" style="color:#005ea7">删除</a>]
							</li>
							<?php }?>
							<li>
								<label><a href="javascript:orderFormInstance.addressAdd();" style="color:#005ea7;">添加新地址</a></label>
							</li>
						</ul>

						<!--收货地址项模板-->
						<script type='text/html' id='addressLiTemplate'>
						<li id="addressItem<%=item['id']%>">
							<label><input class="radio" name="radio_address" type="radio" value="<%=item['id']%>" onclick="orderFormInstance.getDelivery(<%=item['province']%>);" /><%=item['accept_name']%>&nbsp;&nbsp;&nbsp;<%=item['province_val']%> <%=item['city_val']%> <%=item['area_val']%> <%=item['address']%></label> [<a href="javascript:orderFormInstance.addressEdit(<%=item['id']%>);" style="color:#005ea7;">修改地址</a>] [<a href="javascript:orderFormInstance.addressDel(<%=item['id']%>);" style="color:#005ea7">删除</a>]
						</li>
						</script>
					</div>
				</div>
				<!--地址管理 结束-->

				<!--配送方式 开始-->
				<div class="wrap_box">
					<h3>
						<span class="orange">配送方式</span>
					</h3>

					<table width="100%" class="border_table m_10">
						<colgroup>
							<col width="180px" />
							<col />
						</colgroup>

						<tbody>
							<?php foreach(apple::go('getDeliveryList') as $key => $item){?>
							<tr>
								<th><label><input type="radio" name="delivery_id" value="<?php echo isset($item['id'])?$item['id']:"";?>" paytype="<?php echo isset($item['type'])?$item['type']:"";?>" onclick='orderFormInstance.deliverySelected(<?php echo isset($item['id'])?$item['id']:"";?>);' /><?php echo isset($item['name'])?$item['name']:"";?></label></th>
								<td>
									<span id="deliveryShow<?php echo isset($item['id'])?$item['id']:"";?>"></span>
									<?php echo isset($item['description'])?$item['description']:"";?>
									<?php if($item['type'] == 2){?>
									<a href="javascript:orderFormInstance.selectTakeself(<?php echo isset($item['id'])?$item['id']:"";?>);"><span class="red">选择自提点</span></a>
									<span id="takeself<?php echo isset($item['id'])?$item['id']:"";?>"></span>
									<?php }?>
								</td>
							</tr>
							<?php }?>
						</tbody>

						<!--配送信息-->
						<script type='text/html' id='deliveryTemplate'>
							<span style="color:#e4393c">运费：￥<%=item['price']%></span>
							<%if(item['protect_price'] > 0){%>
							<span style="color:#e4393c">保价：￥<%=item['protect_price']%></span>
							<%}%>
						</script>

						<!--物流自提点-->
						<script type='text/html' id='takeselfTemplate'>
							[<%=item['address']%> <%=item['name']%> <%=item['phone']%> <%=item['mobile']%>]
						</script>

						<tfoot>
							<th>送货时间：</th>
							<td>
								<label class='attr'><input type='radio' name='accept_time' checked="checked" value='任意' />任意</label>
								<label class='attr'><input type='radio' name='accept_time' value='周一到周五' />周一到周五</label>
								<label class='attr'><input type='radio' name='accept_time' value='周末' />周末</label>
							</td>
						</tfoot>
					</table>
				</div>
				<!--配送方式 结束-->

				<!--支付方式 开始-->
				<div class="wrap_box" id="paymentBox">
					<h3>
						<span class="orange">支付方式</span>
					</h3>

					<table width="100%" class="border_table">
						<colgroup>
							<col width="200px" />
							<col />
						</colgroup>
						<?php foreach(apple::go('getPaymentList') as $key => $item){?>
						<?php $paymentPrice = CountSum::getGoodsPaymentPrice($item['id'],$this->sum);?>
						<tr>
							<th><label><input class="radio" name="payment" alt="<?php echo isset($paymentPrice)?$paymentPrice:"";?>" onclick='orderFormInstance.paymentSelected(<?php echo isset($item['id'])?$item['id']:"";?>);' title="<?php echo isset($item['name'])?$item['name']:"";?>" type="radio" value="<?php echo isset($item['id'])?$item['id']:"";?>" /><?php echo isset($item['name'])?$item['name']:"";?></label></th>
							<td><?php echo isset($item['note'])?$item['note']:"";?> <?php if($paymentPrice){?>支付手续费：￥<?php echo isset($paymentPrice)?$paymentPrice:"";?><?php }?></td>
						</tr>
						<?php }?>
					</table>
				</div>
				<!--支付方式 结束-->

				<!--订单留言 开始-->
				<div class="wrap_box">
					<h3>
						<span class="orange">订单附言</span>
					</h3>

					<table width="100%" class="form_table">
						<colgroup>
							<col width="120px" />
							<col />
						</colgroup>
						<tr>
							<th>订单附言：</th>
							<td><input class="normal" type="text" name='message' /></td>
						</tr>
					</table>
				</div>
				<!--订单留言 结束-->

				<!--购买清单 开始-->
				<div class="wrap_box">

					<h3><span class="orange">购买的商品</span></h3>

					<div class="cart_prompt f14 t_l m_10" <?php if(empty($this->promotion)){?>style="display:none"<?php }?>>
						<p class="m_10 gray"><b class="orange">恭喜，</b>您的订单已经满足了以下优惠活动！</p>
						<?php foreach($this->promotion as $key => $item){?>
						<p class="indent blue"><?php echo isset($item['plan'])?$item['plan']:"";?>，<?php echo isset($item['info'])?$item['info']:"";?></p>
						<?php }?>
					</div>

					<table width="100%" class="cart_table t_c">
						<colgroup>
							<col width="115px" />
							<col />
							<col width="80px" />
							<col width="80px" />
							<col width="80px" />
							<col width="80px" />
							<col width="80px" />
						</colgroup>

						<thead>
							<tr>
								<th>图片</th>
								<th>商品名称</th>
								<th>赠送积分</th>
								<th>单价</th>
								<th>优惠</th>
								<th>数量</th>
								<th class="last">小计</th>
							</tr>
						</thead>

						<tbody>
							<!-- 商品展示 开始-->
							<?php foreach($this->goodsList as $key => $item){?>
							<tr>
								<td><img src="<?php echo IUrl::creatUrl("/pic/thumb/img/".$item['img']."/w/66/h/66");?>" width="66px" height="66px" alt="<?php echo isset($item['name'])?$item['name']:"";?>" title="<?php echo isset($item['name'])?$item['name']:"";?>" /></td>
								<td class="t_l">
									<a href="<?php echo IUrl::creatUrl("/site/products/id/".$item['goods_id']."");?>" class="blue"><?php echo isset($item['name'])?$item['name']:"";?></a>
									<?php if(isset($item['spec_array'])){?>
									<p>
									<?php $spec_array=Block::show_spec($item['spec_array']);?>
									<?php foreach($spec_array as $specName => $specValue){?>
										<?php echo isset($specName)?$specName:"";?>：<?php echo isset($specValue)?$specValue:"";?> &nbsp&nbsp
									<?php }?>
									</p>
									<?php }?>
								</td>
								<td><?php echo isset($item['point'])?$item['point']:"";?></td>
								<td><b>￥<?php echo isset($item['sell_price'])?$item['sell_price']:"";?></b></td>
								<td>减￥<?php echo isset($item['reduce'])?$item['reduce']:"";?></td>
								<td><?php echo isset($item['count'])?$item['count']:"";?></td>
								<td id="deliveryFeeBox_<?php echo isset($item['goods_id'])?$item['goods_id']:"";?>_<?php echo isset($item['product_id'])?$item['product_id']:"";?>_<?php echo isset($item['count'])?$item['count']:"";?>"><b class="red2">￥<?php echo isset($item['sum'])?$item['sum']:"";?></b></td>
							</tr>
							<?php }?>
							<!-- 商品展示 结束-->
						</tbody>
					</table>
				</div>
				<!--购买清单 结束-->

			</div>
		</div>

		<!--金额结算-->
		<div class="cart_box">
			<div class="cont_2">
				<strong>结算信息</strong>
				<div class="pink_box">
					<p class="f14 t_l"><?php if($this->final_sum != $this->sum){?>优惠后总金额<?php }else{?>商品总金额<?php }?>：<b><?php echo $this->final_sum;?></b> - 代金券：<b name='ticket_value'>0</b> + 税金：<b id='tax_fee'>0</b> + 运费总计：<b id='delivery_fee_show'>0</b> + 保价：<b id='protect_price_value'>0</b> + 支付手续费：<b id='payment_value'>0</b></p>

					<a class="fold" href='javascript:orderFormInstance.ticketShow();'>
						<b class="orange">使用代金券</b>
					</a>
				</div>

				<hr class="dashed" />
				<div class="pink_box gray m_10">
					<table width="100%" class="form_table t_l">
						<colgroup>
							<col width="220px" />
							<col />
							<col width="250px" />
						</colgroup>

						<tr>
							<td>是否需要发票？(税金:￥<?php echo $this->goodsTax;?>) <input class="radio" onclick="orderFormInstance.doAccount();$('#tax_title').toggle();" name="taxes" type="checkbox" value="<?php echo $this->goodsTax;?>" /></td>
							<td><label id="tax_title" class='attr' style='display:none'>发票抬头：<input type='text' class='normal' name='tax_title' /></label></td>
							<td class="t_r"><b class="price f14">应付总额：<span class="red2">￥<b id='final_sum'><?php echo $this->final_sum;?></b></span>元</b></td>
						</tr>
					</table>
				</div>
				<p class="m_10 t_r"><input type="submit" class="submit_order" /></p>
			</div>
		</div>
	</form>
</div>
		<?php echo IFilter::stripSlash($this->_siteConfig->site_footer_code);?>
	</div>
</body>
</html>
