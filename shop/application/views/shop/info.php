<script type="text/javascript" src="<?php echo base_url();?>js/shop_add.js"></script>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=ho6LXkYw6eWBzWFlPvcMpLhR"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">店铺内容</div>
         <?php if($msg!=''){?>
         <p style="color:red;font-size:14px;text-align:left;padding-left:20px;"><?php echo $msg; ?></p>
         <?php } ?>
         <form action="" method="post" enctype="multipart/form-data" onsubmit="return checkFrom();">
         <input type="hidden" name="act" value="edit" />
         <input type="hidden" name="id" value="<?php echo $data['id'] ?>" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">店铺内容</td>
             </tr>
             <tr>
                 <td style="text-align:center;">店铺名称</td>
                 <td><input name="title" type="text" value="<?php echo $data['title'] ?>" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">别名</td>
                 <td><input name="subtitle" type="text" value="<?php echo $data['subtitle']?>" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">(宽高640:345)<br/>店面图片</td>
                 <td><input name="file" type="file" style="width:240px;"><?php if ($data['img'] != ''){ ?> <br><img src="<?php echo base_url().$data['img'] ?>" /><?php } ?>
                 	<input name="img" type="hidden" value="<?php echo $data['img'] ?>" /></td>
             </tr>
             <?php foreach ($shopimg as $img){ ?>
             <tr>
                 <td style="text-align:center;word-break:keep-all;">更多店铺图片</td>
                 <td>
                 	<img src="<?php echo $img['img']?>"><a class="delShopImg" rel="<?php echo $img['id']?>" href="<?php echo base_url()?>shop/delshopimg">删 除</a>
                 	<input name="shop_oldimg[]" type="hidden" value="<?php echo $img['img']?>" />
                 </td>
             </tr>
             <?php } ?>
             <tr>
                 <td style="text-align:center;">更多店铺图片</td>
                 <td><input name="shop_img[]" type="file" style="width:240px;"></td>
             </tr>
             <tr id="shopImg_add"><td colspan="2" ><a style="margin-left:30px;" href="javascript:void(0)">添加图片</a></td></tr>
             <tr>
                 <td style="text-align:center;">营业时间</td>
                 <td><input name="hours" type="text" value="<?php echo $data['hours']?>" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">电话</td>
                 <td><input name="tel" type="text" value="<?php echo $data['tel']?>" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">城市区域</td>
                 <td>
	                <select name="province_id" class="province_id">
	                	<?php foreach ($provinces as $p){ ?>
							<option value="<?php echo $p['id'] ?>" <?php if($data['province_id']==$p['id']){?>selected<?php } ?> > <?php echo $p['name'] ?></option>
						<?php } ?>
					</select>
	                <select name="city_id" class="city_id">
					<option value="">不限</option>
	                	<?php foreach ($cities as $c){ ?>
							<option value="<?php echo $c['id'] ?>" <?php if ($data['city_id']==$c['id']){ ?>selected<?php } ?> > <?php echo $c['name'] ?></option>
						<?php } ?>
					</select>
					<select name="town_id" class="town_id">
					<option value="">不限</option>
	                	<?php foreach ($towns as $t){ ?>
						{section name=sec loop=$towns}
							<option value="<?php echo $t['id'] ?>" <?php if ($data['town_id']==$t['id']){ ?>selected<?php } ?> > <?php echo $t['name'] ?></option>
						{/section}
	                	<?php } ?>
					</select>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">地址</td>
                 <td><input id="address" name="address" type="text" value="<?php echo $data['address']?>" style="width:600px;">
                 	<div id="allmap"></div>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">经度</td>
                 <td><input id="lng" name="lng" type="text" value="<?php echo $data['lng']?>" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">纬度</td>
                 <td><input id="lat" name="lat" type="text" value="<?php echo $data['lat']?>" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">特色</td>
                 <td>
                 <?php foreach ($tags as $tag){ ?>
                 	<label><input name="features[]" type="checkbox" <?php echo $tag['checked'] ?> value="<?php echo $tag['tag'] ?>"><?php echo $tag['tag'] ?></label>&nbsp;
                 <?php } ?>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">简介</td>
                 <td><textarea name="introduction" style="width:540px;height:80px;"><?php echo $data['introduction']?></textarea></td>
             </tr>
             <?php foreach ($menu as $m){ ?>
             <tr>
                 <td style="text-align:center;word-break:keep-all;">(宽高292:233)<br/>菜品</td>
                 <td><input style="margin-bottom:10px" type="text" name="menu_oldtitle[]" value="<?php echo $m['title']?>" ><br/>
                 	<img src="<?php echo $m['img'] ?>"><a class="delImg" rel="<?php echo $m['id'] ?>" href="<?php echo base_url()?>shop/delmenu">删 除</a>
                 	<input name="menu_oldimg[]" type="hidden" value="<?php echo $m['img'] ?>" />
                 </td>
             </tr>
             <?php } ?>
             <tr>
                 <td style="text-align:center;word-break:keep-all;">(宽高292:233)<br/>菜品</td>
                 <td><input type="text" name="menu_title[]" ><input name="menu_img[]" type="file" style="width:240px;"></td>
             </tr>
             <tr id="photo_add"><td colspan="2" ><a style="margin-left:30px;color:#f00;" href="javascript:void(0)">添加菜品</a></td></tr>
             <tr>
                 <td style="text-align:center;">是否发布</td>
                 <td>
                 	<label><input name="status" type="radio" value="1" checked="checked">准备中</label>
                 	<label><input name="status" type="radio" value="2" <?php if ($data['status']==2){ ?>checked="checked" <?php } ?> >发布中</label>
                 </td>
             </tr>
         </table>
         <p class="btn"><input type="submit" value=" 确定修改 "></p>
         </form>
 	</div>       
 </td>