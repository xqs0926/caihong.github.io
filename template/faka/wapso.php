<?php
if(!defined('IN_CRONLITE'))exit();

$cssadd = '<link rel="stylesheet" href="'.$cdnserver.'assets/faka/css/index.css?v=2" />';
include_once TEMPLATE_ROOT.'faka/head2.php';

if($islogin2==1){
	$price_obj = new \lib\Price($userrow['zid'],$userrow);
}elseif($is_fenzhan == true){
	$price_obj = new \lib\Price($siterow['zid'],$siterow);
}else{
	$price_obj = new \lib\Price(1);
}

$template_label_auto = $conf['template_label_auto']?$conf['template_label_auto']:'自动';
$template_label_manual = $conf['template_label_manual']?$conf['template_label_manual']:'手动';

$kw=trim(daddslashes($_GET['kw']));

$total = $DB->getColumn("SELECT count(*) FROM pre_tools WHERE name LIKE '%$kw%' and active=1");

?>
<div class="top w">
<div class="menux" style="background-color: #ffffff;"><div align="center" style="background-color: #ffffff;">
  <form action="?" method="get"><input type="hidden" name="mod" value="wapso"/>
    <input name="kw" type="text" class="search_input" placeholder="请输入您要查询的商品名称关键词" value="<?php echo htmlspecialchars($kw)?>" required>
    <input type="submit" class="search_submit" style="background-color: #f44530" value="商品搜索">
  </form>
</div></div>
</div>

<div class="baoliao w">
    <div class="panel-group" id="accordion">

<div class="panel">
<div class="menux">搜索结果</div></a>
  <div class="" style="margin:10px;color: #007b1c">
    <img src="assets/faka/images/dhfgf.gif"> 商品搜索关键词【<?php echo htmlspecialchars($kw)?>】共<b><?php echo $total?></b>条记录
  </div>
<?php
$rs=$DB->query("SELECT * FROM pre_tools WHERE name LIKE '%$kw%' and active=1 order by sort asc");
while($res = $rs->fetch()){
	if(isset($price_obj)){
		$price_obj->setToolInfo($res['tid'],$res);
		if($price_obj->getToolDel($res['tid'])==1)continue;
		$price=$price_obj->getToolPrice($res['tid']);
	}else $price=$res['price'];
	if($res['is_curl']==4){
		$count = $DB->getColumn("SELECT count(*) FROM pre_faka WHERE tid='{$res['tid']}' and orderid=0");
		if($count>0&&$conf['faka_showleft']==0)$status = '<span class="bl_type" style="background-color:#0086ee">库存'.$count.'个</span>';
		elseif($count>0)$status = '<span class="bl_type" style="background-color:#0086ee">充足</span>';
		else $status = '<span class="bl_type" style="background-color:#6E6E6E;">缺货</span>';
	}elseif($res['stock']!==null){
		$count = $res['stock'];
		if($count>0&&$conf['faka_showleft']==0)$status = '<span class="bl_type" style="background-color:#0086ee">库存'.$count.'个</span>';
		elseif($count>0)$status = '<span class="bl_type" style="background-color:#0086ee">充足</span>';
		else $status = '<span class="bl_type" style="background-color:#6E6E6E;">缺货</span>';
	}else{
		if($res['close']==1)$status = '<span class="bl_type" style="background-color:#6E6E6E;">已下架</span>';
		else $status = '<span class="bl_type" style="background-color:#0086ee">正常</span>';
	}
	if($res['is_curl']==1||$res['is_curl']==2||$res['is_curl']==4||$res['is_curl']==5){
		$isauto = true;
	}else{
		$isauto = false;
	}
	$count = $DB->getColumn("SELECT count(*) FROM pre_faka WHERE tid='{$res['tid']}' and orderid=0");
	echo '<a href="./?mod=buy&cid='.$cid.'&tid='.$res['tid'].'" class="cid'.$cid.'"><div class="baoliao_content"><div class="bl_img" style="position:relative"><img data-original="'.($res['shopimg']?$res['shopimg']:'assets/faka/images/default.jpg').'" alt="'.$res['name'].'" class="lazy"><div style="width:100px;position:absolute;z-indent:2;left:1px;top:59px;">'.($isauto?'<div class="index_bl_type" style="background-color:#fe5604;max-width:56px;border-radius: 0 0 5px 5px;">'.$template_label_auto.'</div>':'<div class="index_bl_type" style="background-color:#49b41a;max-width:56px;border-radius: 0 0 5px 5px;">'.$template_label_manual.'</div>').'</div></div><div class="bl_right"><div class="bl_title">'.$res['name'].'</div><div class="bl_tag"><div class="bl_price">'.($conf['template_showsales']==1?'<span class="bl_type" style="background-color:#B187C1;">销量'.$res['sales'].'</span> ':'').$status.'  售价￥<b>'.$price.'</b></div></div></div></div></a>';
}
?>
</div>
</div>
<div class="m_user w">
<a href="#">返回顶部</a>
</div>

<div class="copyright">Copyright &copy; <?php echo date("Y")?> <?php echo $conf['sitename']?></div>
</div>
<script src="<?php echo $cdnpublic?>jquery/1.12.4/jquery.min.js"></script>
<script src="<?php echo $cdnpublic?>layer/2.3/layer.js"></script>
<script src="<?php echo $cdnpublic?>jquery.lazyload/1.9.1/jquery.lazyload.min.js"></script>
<script src="<?php echo $cdnpublic?>jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script type="text/javascript">
$(function() {
	$("img.lazy").lazyload({effect: "fadeIn"});
	if($.cookie('sec_defend_time'))$.removeCookie('sec_defend_time', { path: '/' });
});
</script>
</body>
</html>