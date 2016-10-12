<!DOCTYPE html>
<html lang="zh">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $this->_siteConfig->name;?></title>
	<meta name="keywords" content="B2B2C商城系统" />
	<meta name="description" content="这是有个颠覆淘宝的系统" />
	<meta name="author" content="songsang">
	<link type="image/x-icon" href="<?php echo IUrl::creatUrl("")."favicon.ico";?>" rel="icon">
	<link rel="stylesheet"  type="text/css" href="<?php echo $this->getWebSkinPath()."css/index.css";?>" />
	<script type="text/javascript" charset="UTF-8" src="/runtime/_systemjs/jquery/jquery-1.12.4.min.js"></script>
	<script type="text/javascript" charset="UTF-8" src="/runtime/_systemjs/form/form.js"></script>
	<script type="text/javascript" charset="UTF-8" src="/runtime/_systemjs/autovalidate/validate.js"></script><link rel="stylesheet" type="text/css" href="/runtime/_systemjs/autovalidate/style.css" />
	<script type="text/javascript" charset="UTF-8" src="/runtime/_systemjs/artdialog/artDialog.js"></script><script type="text/javascript" charset="UTF-8" src="/runtime/_systemjs/artdialog/plugins/iframeTools.js"></script><link rel="stylesheet" type="text/css" href="/runtime/_systemjs/artdialog/skins/aero.css" />
	<script type="text/javascript" charset="UTF-8" src="/runtime/_systemjs/artTemplate/artTemplate.js"></script><script type="text/javascript" charset="UTF-8" src="/runtime/_systemjs/artTemplate/artTemplate-plugin.js"></script>
	<script type='text/javascript' src="<?php echo $this->getWebViewPath()."javascript/common.js";?>"></script>
	<script type='text/javascript' src='<?php echo $this->getWebViewPath()."javascript/site.js";?>'></script>
</head>
<body class="index">
<div class="container">
	<div class="header">
		<h1 class="logo"><a title="<?php echo isset($sellerRow['true_name'])?$sellerRow['true_name']:"";?>" style="background:url(<?php if($sellerRow['logo']){?><?php echo IUrl::creatUrl("")."".$sellerRow['logo']."";?><?php }else{?><?php echo IUrl::creatUrl("")."".$this->_siteConfig->logo."";?><?php }?>) center no-repeat;background-size:contain;" href=""><?php echo isset($sellerRow['true_name'])?$sellerRow['true_name']:"";?></a></h1>

		<ul class="shortcut">
			<li class="first"><a href="<?php echo IUrl::creatUrl("/ucenter/index");?>">我的账户</a></li>
			<li><a href="<?php echo IUrl::creatUrl("/ucenter/order");?>">我的订单</a></li>
			<li><a href="<?php echo IUrl::creatUrl("/simple/seller");?>">申请开店</a></li>
			<li class='last'><a href="<?php echo IUrl::creatUrl("/site/help_list");?>">使用帮助</a></li>
		</ul>
		<p class="loginfo">
			<?php if($this->user){?>欢迎你：
			<?php echo $this->user['username'];?>&nbsp[<a href="<?php echo IUrl::creatUrl("/simple/logout");?>" class="reg">安全退出</a>]
			<?php }else{?>
			[<a href="<?php echo IUrl::creatUrl("/simple/login");?>">登录</a><a class="reg" href="<?php echo IUrl::creatUrl("/simple/reg");?>">免费注册</a>]
			<?php }?>
		</p>
	</div>
	<div class="navbar">
		<ul>
			<li><a href="<?php echo IUrl::creatUrl("/site/index");?>">首页</a></li>
			<?php foreach(apple::go('getGuideList') as $key => $item){?>
			<li><a href="<?php echo IUrl::creatUrl("".$item['link']."");?>"><?php echo isset($item['name'])?$item['name']:"";?><span> </span></a></li>
			<?php }?>
		</ul>

		<div class="mycart" name="mycart">
			<dl>
				<dt><a href="<?php echo IUrl::creatUrl("/simple/cart");?>">购物车<b name="mycart_count">0</b>件</a></dt>
				<dd><a href="<?php echo IUrl::creatUrl("/simple/cart");?>">去结算</a></dd>
			</dl>

			<!--购物车浮动div 开始-->
			<div class="shopping" id='div_mycart' style='display:none;'></div>
			<!--购物车浮动div 结束-->

			<!--购物车模板 开始-->
			<script type='text/html' id='cartTemplete'>
			<dl class="cartlist">
				<%for(var item in goodsData){%>
				<%var data = goodsData[item]%>
				<dd id="site_cart_dd_<%=item%>">
					<div class="pic f_l"><img width="55px" height="55px" src="<?php echo IUrl::creatUrl("")."<%=data['img']%>";?>"></div>
					<h3 class="title f_l"><a href="<?php echo IUrl::creatUrl("/site/products/id/<%=data['goods_id']%>");?>"><%=data['name']%></a></h3>
					<div class="price f_r t_r">
						<b class="block">￥<%=data['sell_price']%> x <%=data['count']%></b>
						<input class="del" type="button" value="删除" onclick="removeCart('<%=data['id']%>','<%=data['type']%>');$('#site_cart_dd_<%=item%>').hide('slow');" />
					</div>
				</dd>
				<%}%>

				<dd class="static"><span>共<b name="mycart_count"><%=goodsCount%></b>件商品</span>金额总计：<b name="mycart_sum">￥<%=goodsSum%></b></dd>

				<%if(goodsData){%>
				<dd class="static">
					<label class="btn_orange"><input type="button" value="去购物车结算" onclick="window.location.href='<?php echo IUrl::creatUrl("/simple/cart");?>';" /></label>
				</dd>
				<%}%>
			</dl>
			</script>
			<!--购物车模板 结束-->
		</div>
	</div>

	<div class="searchbar">
		<div class="allsort">
			<a href="javascript:void(0);">商品分类</a>

			<!--总的商品分类-开始-->
			<ul class="sortlist" id='div_allsort' style='display:none'>
				<?php foreach(apple::go('getCategoryListTop',$seller_id) as $key => $first){?>
				<li>
					<h2><a href="<?php echo IUrl::creatUrl("/site/pro_list/cat/".$first['id']."");?>"><?php echo isset($first['name'])?$first['name']:"";?></a></h2>

					<!--商品分类 浮动div 开始-->
					<div class="sublist" style='display:none'>
						<div class="items">
							<strong>选择分类</strong>
							<?php foreach(apple::go('getCategoryByParentid',array('#parent_id#',$first['id'])) as $key => $second){?>
							<dl class="category selected">
								<dt>
									<a href="<?php echo IUrl::creatUrl("/site/pro_list/cat/".$second['id']."");?>"><?php echo isset($second['name'])?$second['name']:"";?></a>
								</dt>

								<dd>
									<?php foreach(apple::go('getCategoryByParentid',array('#parent_id#',$second['id'])) as $key => $third){?>
									<a href="<?php echo IUrl::creatUrl("/site/pro_list/cat/".$third['id']."");?>"><?php echo isset($third['name'])?$third['name']:"";?></a>|
									<?php }?>
								</dd>
							</dl>
							<?php }?>
						</div>
					</div>
					<!--商品分类 浮动div 结束-->
				</li>
				<?php }?>
			</ul>
			<!--总的商品分类-结束-->
		</div>

		<div class="searchbox">
			<form method='get' onkeydown="if(event.keyCode==13)return false;">
				<input class="text" type="text" name='word' autocomplete="off" value="" placeholder="请输入关键词..."  />
				<input class="btn" type="button" value="搜索本店" onclick="search_ca(this.form)" />
			</form>
		</div>

		<div class="hotwords">热门搜索：
			<?php foreach(apple::go('getKeywordList') as $key => $item){?>
			<?php $tmpWord = urlencode($item['word']);?>
			<a href="<?php echo IUrl::creatUrl("/site/search_list/word/".$tmpWord."");?>"><?php echo isset($item['word'])?$item['word']:"";?></a>
			<?php }?>
		</div>
	</div>
	<?php echo Ad::show(1);?>

	<div class="position"> <span>您当前的位置：</span> <a href="<?php echo IUrl::creatUrl("");?>"> 首页</a> » 商户主页 </div>
<div class="wrapper clearfix container_2">

	<div class="sidebar f_l">
		<div class="box m_10">
			<div class="title">商户信息</div>
			<div class="content">
				<div class="c_box">
					<dl class="clearfix">
						<dt><strong style="margin-left:15px"><?php echo isset($sellerRow['true_name'])?$sellerRow['true_name']:"";?></strong><a class="favorite" onclick="favorite_add_ajax('',<?php echo isset($sellerRow['id'])?$sellerRow['id']:"";?>);" href="javascript:void(0)">收藏店铺</a></dt>

					</dl>
					<p> 评分：<span class="grade-star g-star<?php echo statistics::gradeSeller($sellerRow['id']);?>"></span></p>
					<p> 销量：<?php echo statistics::sellCountSeller($sellerRow['id']);?> 件</p>
					<p> 官网：<a href="<?php echo isset($sellerRow['home_url'])?$sellerRow['home_url']:"";?>" target="_blank" class="orange">点击进入</a></p>
					<p> 联系电话：<?php echo isset($sellerRow['phone'])?$sellerRow['phone']:"";?></p>
					<p> 所在地：<?php echo join(' ',area::name($sellerRow['province'],$sellerRow['city'],$sellerRow['area']));?> <?php echo isset($sellerRow['address'])?$sellerRow['address']:"";?></p>
					<!-- <p> <?php plugin::trigger("onServiceButton",$sellerRow['id'])?></p> -->
				</div>
			</div>
		</div>

		<!--促销规则-->
		<div class="box m_10">
			<div class="title">促销活动</div>
			<div class="cont">
				<ul class="list">
				<?php foreach(apple::go('getProrule',$seller_id) as $key => $item){?>
					<li><?php echo isset($item['info'])?$item['info']:"";?></li>
				<?php }?>
				</ul>
			</div>
		</div>
		<!--促销规则-->

		<div class="box m_10">
			<div class="title">销售排行榜</div>
			<div class="content">
				<ul class="ranklist" id="ranklist">
					<?php foreach(apple::go('getGoodsListBySellerid',array('#seller_id#',$seller_id),10) as $key => $item){?>
					<li><span><?php echo intval($key+1);?></span><a href="<?php echo IUrl::creatUrl("/site/products/id/".$item['id']."");?>"><img src="<?php echo IUrl::creatUrl("/pic/thumb/img/".$item['img']."/w/58/h/58");?>" width="58px" height="58px" /></a><a href="<?php echo IUrl::creatUrl("/site/products/id/".$item['id']."");?>"><?php echo isset($item['name'])?$item['name']:"";?></a><b>￥<?php echo isset($item['sell_price'])?$item['sell_price']:"";?></b></li>
					<?php }?>
				</ul>
			</div>
		</div>

		<div class="box m_10">
			<div class="title">商家推荐</div>
			<div class="cont">
				<ul class="ranklist">
					<?php foreach(apple::go('getGoodsListBySelleridList',array('#seller_id#',$seller_id),10) as $key => $item){?>
					<li class='current'><a href="<?php echo IUrl::creatUrl("/site/products/id/".$item['id']."");?>"><img src="<?php echo IUrl::creatUrl("/pic/thumb/img/".$item['img']."/w/58/h/58");?>" width="58" height="58" /></a><a href="<?php echo IUrl::creatUrl("/site/products/id/".$item['id']."");?>"><?php echo isset($item['name'])?$item['name']:"";?></a><b>￥<?php echo isset($item['sell_price'])?$item['sell_price']:"";?></b></li>
					<?php }?>
				</ul>
			</div>
		</div>

	</div>

	<div class="main f_r">
		<?php $goodsObj = search_goods::find(array('go.seller_id' => $seller_id,'search'=>IReq::get('search')));
		$resultData = $goodsObj->find();?>

		<!--商品条件筛选-->
		<?php require(ITag::createRuntime("_goods_list_filter"));?>

		<?php if($resultData){?>
		<ul class="display_list clearfix m_10">
			<?php foreach($resultData as $key => $item){?>
			<li name="goodsItems" class="clearfix win">
				<div class="pic">
					<a href="<?php echo IUrl::creatUrl("/site/products/id/".$item['id']."");?>"><img src="<?php echo IUrl::creatUrl("/pic/thumb/img/".$item['img']."/w/200/h/200");?>" width="200px" height="200px" alt="<?php echo isset($item['name'])?$item['name']:"";?>" title="<?php echo isset($item['name'])?$item['name']:"";?>" name="goodsImage" /></a>
				</div>
				<h3 class="title"><a title="<?php echo isset($item['name'])?$item['name']:"";?>" class="p_name" href="<?php echo IUrl::creatUrl("/site/products/id/".$item['id']."");?>"><?php echo isset($item['name'])?$item['name']:"";?></a><span>总销量：<?php echo isset($item['sale'])?$item['sale']:"";?><a class="blue" href="<?php echo IUrl::creatUrl("/site/comments_list/id/".$item['id']."");?>">( <?php echo isset($item['comments'])?$item['comments']:"";?>人评论 )</a></span><span class="grade-star g-star<?php echo Common::gradeWidth($item['grade'],$item['comments']);?>"></span></h3>
				<div class="handle">
					<label class="btn_gray_m"><img src="<?php echo $this->getWebSkinPath()."images/front/ucenter/shopping.gif";?>" width="15" height="15" /><input type="button" value="加入购物车" onclick="joinCart_list(<?php echo isset($item['id'])?$item['id']:"";?>);"></label>
					<label class="btn_gray_m"><img src="<?php echo $this->getWebSkinPath()."images/front/ucenter/favorites.gif";?>" width="15" height="14" /><input type="button" value="收藏" onclick="favorite_add_ajax('<?php echo isset($item['id'])?$item['id']:"";?>',this);"></label>
				</div>
				<div class="price">￥<?php echo isset($item['sell_price'])?$item['sell_price']:"";?><s>￥<?php echo isset($item['market_price'])?$item['market_price']:"";?></s></div>
			</li>
			<?php }?>
		</ul>
		<?php echo $goodsObj->getPageBar();?>

		<?php }else{?>
		<p class="display_list mt_10" style='margin-top:50px;margin-bottom:50px'>
			<strong class="gray f14">对不起，没有找到相关商品</strong>
		</p>
		<?php }?>
	</div>
</div>

	<div class="help m_10">
		<div class="cont clearfix">
			<?php foreach(apple::go('getHelpCategoryFoot') as $key => $helpCat){?>
			<dl>
				<dt><a href="<?php echo IUrl::creatUrl("/site/help_list/id/".$helpCat['id']."");?>"><?php echo isset($helpCat['name'])?$helpCat['name']:"";?></a></dt>
				<?php foreach(apple::go('getHelpListByCatidAll',array('#cat_id#',$helpCat['id'])) as $key => $item){?>
					<dd><a href="<?php echo IUrl::creatUrl("/site/help/id/".$item['id']."");?>"><?php echo isset($item['name'])?$item['name']:"";?></a></dd>
				<?php }?>
			</dl>
			<?php }?>
		</div>
	</div>
	<?php echo IFilter::stripSlash($this->_siteConfig->site_footer_code);?>
</div>

<script type='text/javascript'>
$(function()
{
	//搜索框填充默认数据
	$('input:text[name="word"]').val("<?php echo $this->word;?>");

	//定时刷新
	var allsortLateCall = new lateCall(200,function(){$('#div_allsort').show();});

	//商品分类
	$('.allsort').hover(
		function(){
			allsortLateCall.start();
		},
		function(){
			allsortLateCall.stop();
			$('#div_allsort').hide();
		}
	);
	$('.sortlist li').each(
		function(i)
		{
			$(this).hover(
				function(){
					$(this).addClass('hover');
					$('.sublist:eq('+i+')').show();
				},
				function(){
					$(this).removeClass('hover');
					$('.sublist:eq('+i+')').hide();
				}
			);
		}
	);

	//排行,浏览记录的图片
	$('#ranklist li').hover(
		function(){
			$(this).addClass('current');
		},
		function(){
			$(this).removeClass('current');
		}
	);
});
function search_ca(myform){
	var searchWord = myform.word.value;
	if(searchWord!="")
	{
		window.location.href= "/site/home/id/<?php echo isset($seller_id)?$seller_id:"";?>/search/"+searchWord;
	}else{
		alert('请输入关键词');
	}
}
</script>
</body>
</html>
