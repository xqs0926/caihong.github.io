<?php
if(!defined('IN_CRONLITE'))exit();
if($islogin2==1){
	$cookiesid = $userrow['zid'];
}

function display_zt2($zt){
	if($zt==1)
		return '<font color=green>已完成</font>';
	elseif($zt==2)
		return '<font color=orange>正在处理</font>';
	elseif($zt==3)
		return '<font color=red>异常</font>';
	elseif($zt==4)
		return '<font color=grey>已退单</font>';
	else
		return '<font color=blue>待处理</font>';
}

$data=trim(daddslashes($_GET['data']));
$page=isset($_GET['page'])?intval($_GET['page']):1;
if(!empty($data)){
	if(strlen($data)==17 && is_numeric($data))$sql=" A.`tradeno`='{$data}'";
	else $sql=" A.`input`='{$data}'";
	if($conf['queryorderlimit']==1)$sql.=" AND A.`userid`='$cookiesid'";
	$link='&data='.$data;
}
else $sql=" A.`userid`='{$cookiesid}'";
$limit = 10;
$start = $limit * ($page-1);

$total = $DB->getColumn("SELECT count(*) FROM `pre_orders` A WHERE{$sql} ");
$total_page = ceil($total/$limit);
$sql = "SELECT A.*,B.`name` FROM `pre_orders` A LEFT JOIN `pre_tools` B ON A.`tid`=B.`tid` WHERE{$sql} ORDER BY A.`id` DESC LIMIT {$start},{$limit}";
$rs=$DB->query($sql);
$record=array();
while($res = $rs->fetch()){
	$record[]=array('id'=>$res['id'],'tid'=>$res['tid'],'input'=>$res['input'],'money'=>$res['money'],'name'=>$res['name'],'value'=>$res['value'],'addtime'=>$res['addtime'],'endtime'=>$res['endtime'],'result'=>$res['result'],'status'=>$res['status'],'djzt'=>$res['djzt'],'skey'=>md5($res['id'].SYS_KEY.$res['id']));
}

$cssadd = '<link rel="stylesheet" href="'.$cdnserver.'assets/faka/css/user.css" />';
include_once TEMPLATE_ROOT.'faka/head2.php';
?>
<style>
td.wbreak{max-width:420px;word-break:break-all;}
#orderItem .orderTitle{word-break:keep-all;}
#orderItem .orderContent{word-break:break-all;}
#orderItem .orderContent img{max-width:100%}
td{padding: 5px;border-top: 1px solid #ddd;}
.table{width: 100%;max-width: 100%;margin-bottom: 20px;}
.table>tbody>tr>td.info{background-color: #d9edf7;}
.pagination li a{width: auto;line-height: unset;font-size: 14px;float: none;}
</style>
<div class="search w">
<form action="?" method="get"><input type="hidden" name="mod" value="query"/>
	<input name="data" type="text" class="search_input" placeholder="请输入订单号或填写的联系方式查询"><input name="submit" type="submit" class="search_submit" value="查询">
</form>
</div>
<div class="menux"><div align="center">订单管理</div></div>

<?php
foreach($record as $row){
	echo '<div class="msg w"><div class="msg_title">&nbsp;<font class="bl_type" style="background-color:#019bbc">自动发货</font> ID:'.$row['id'].' <font size="2" color="#cbcbcb">'.$row['addtime'].'</font></div><div class="msg_title2"><h1>商品：<a href="./?mod=buy&tid='.$row['tid'].'">'.$row['name'].'</a></h1><span></span></div><div class="msg_content">总价'.$row['money'].'元 数量'.$row['value'].'个 '.display_zt2($row['status']).' '.(($row['djzt']==3||$row['djzt']==4)?'<a href="./?mod=faka&id='.$row['id'].'&skey='.$row['skey'].'" title="点击查看卡密"><span class="bl_type" style="background-color:#c500e8;">提取卡密</span></a>':'<a href="javascript:showOrder('.$row['id'].',\''.$row['skey'].'\')" title="点击查看订单详情"><span class="bl_type" style="background-color:#5aab03;">查看详情</span></a>').'</div></div>';
}
?>
<div class="bl_more"> <div class='text-center'><ul class='pagination'>
<?php if($page>1){?>
<li><a href="./?mod=query&page=<?php echo ($page-1).$link;?>">上一页</a></li>
<?php }?>
<li><span class="rows"><?php echo $page?> / <?php echo $total_page?></span></li>
<?php if($total_page!=$page){?>
<li><a href="./?mod=query&page=<?php echo ($page+1).$link;?>">下一页</a></li>
<?php }?>
</ul></div></div>

<div class="m_user w">
<a href="#">返回顶部</a>
</div>

<div class="copyright">Copyright &copy; <?php echo date("Y")?> <?php echo $conf['sitename']?></div>
</div>
<script src="<?php echo $cdnpublic?>jquery/1.12.4/jquery.min.js"></script>
<script src="<?php echo $cdnpublic?>layer/2.3/layer.js"></script>
<script src="<?php echo $cdnserver ?>assets/faka/js/query.js?ver=<?php echo VERSION ?>"></script>
</body>
</html>