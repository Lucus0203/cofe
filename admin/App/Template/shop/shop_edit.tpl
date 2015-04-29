<script type="text/javascript" src="{$smarty.const.SITE}resource/js/jquery.cropit.js"></script>
<script type="text/javascript" src="{$smarty.const.SITE}resource/js/lightbox.min.js"></script>
<script type="text/javascript" src="{$smarty.const.SITE}resource/js/shop_add.js"></script>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=ho6LXkYw6eWBzWFlPvcMpLhR"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <input type="hidden" id="provinceApiURL" value="{url controller=Api action=GetCityByProvince}" />
         <input type="hidden" id="cityApiURL" value="{url controller=Api action=GetTownByCity}" />
         <div class="hd_t">店铺编辑</div>
         <p style="color:red;font-size:14px;text-align:left;padding-left:20px;">{$msg}</p>
         <form action="" method="post" enctype="multipart/form-data" onsubmit="return checkFrom();">
         <input type="hidden" name="act" value="edit" />
         <input type="hidden" name="id" value="{$data.id}" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">店铺编辑</td>
             </tr>
             <tr>
                 <td style="text-align:center;">店铺名称</td>
                 <td><input name="title" type="text" value="{$data.title}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">别名</td>
                 <td><input name="subtitle" type="text" value="{$data.subtitle}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;word-break:keep-all;">上传店铺图片<br/>(图片大小640x480)</td>
                 <td style="padding-left:30px;">
                 	<a id="shopimgtool" href="javascript:void(0);">显示上传工具</a>
                 	<div id="shopimgBox" style="display: none;">
	                 	<div class="image-shoper">
		                    <input name="file" type="file" class="cropit-image-input" />
		                    <div class="cropit-image-preview-container">
							    <div class="cropit-image-preview"></div>
							  </div>
							<div class="slider-wrapper"><span class="icon icon-image small-image"></span><input type="range" class="cropit-image-zoom-input" min="0" max="1" step="0.01"><span class="icon icon-image large-image"></span></div>
					    </div>
	                 	<input type="button" value="上传裁剪图片" id="shopImg_add" />
	                 	<input type="button" value="上传原始图片" id="shopImg_add_nocut" />
                 	</div>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">店铺图片</td>
                 <td>
	                 <ul  id="shopimgs">
             			{section name=spi loop=$shopimg}
	                 		<li>
	                 			<a href="{$shopimg[spi].img}" data-lightbox="roadtrip"><img src="{$shopimg[spi].img}"></a><a class="delShopImg" rel="{$shopimg[spi].id}" href="javascript:void(0)">删 除</a>
	                 			<label><input type="radio" name="img" value="{$shopimg[spi].img}" {if $data.img eq $shopimg[spi].img} checked {/if} />作为主图</label>
	                 		</li>
             			{/section}
	             	</ul>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">营业时间</td>
                 <td><input name="hours" type="text" value="{$data.hours}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">电话</td>
                 <td><input name="tel" type="text" value="{$data.tel}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">城市区域</td>
                 <td>
	                <select name="province_id" class="province_id">
		                {section name=sec loop=$provinces}
							<option value="{$provinces[sec].id}" {if $data.province_id eq $provinces[sec].id}selected{/if}>{$provinces[sec].name}</option>
						{/section}
					</select>
	                <select name="city_id" class="city_id">
					<option value="">不限</option>
						{section name=sec loop=$city}
							<option value="{$city[sec].id}" {if $data.city_id eq $city[sec].id}selected{/if}>{$city[sec].name}</option>
						{/section}
					</select>
					<select name="town_id" class="town_id">
						{section name=sec loop=$towns}
							<option value="{$towns[sec].id}" {if $data.town_id eq $towns[sec].id}selected{/if}>{$towns[sec].name}</option>
						{/section}
					</select>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">地址</td>
                 <td><input id="address" name="address" type="text" value="{$data.address}" style="width:600px;">
                 	<div id="allmap"></div>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">经度</td>
                 <td><input id="lng" name="lng" type="text" value="{$data.lng}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">纬度</td>
                 <td><input id="lat" name="lat" type="text" value="{$data.lat}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">特色</td>
                 <td>
                 	{section name=t loop=$tags}
                 	<label><input name="features[]" type="checkbox" {$tags[t].checked} value="{$tags[t].tag}">{$tags[t].tag}</label>&nbsp;
                 	{/section}
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">简介</td>
                 <td><textarea name="introduction" style="width:540px;height:80px;">{$data.introduction}</textarea></td>
             </tr>
             <tr>
                 <td style="text-align:center;word-break:keep-all;">上传菜品<br>(图片大小292x233)</td>
                 <td style="padding-left:30px;">
                 	<a id="menuimgtool" href="javascript:void(0);">显示上传工具</a>
                 	<div id="menuimgBox" style="display: none;">
	                 	<div class="image-menuer">
		                    <input name="file" type="file" class="cropit-image-input" />
		                    <div class="cropit-image-preview-container">
							    <div class="cropit-image-preview"></div>
							  </div>
							<div class="slider-wrapper"><span class="icon icon-image small-image"></span><input type="range" class="cropit-image-zoom-input" min="0" max="1" step="0.01"><span class="icon icon-image large-image"></span></div>
					    </div>
	                 	菜品名称：<input type="text" id="menuTitle" style="margin-bottom: 20px;"/><br/>
	                 	<input type="button" value="上传裁剪图片" id="menuImg_add" />
	                 	<input type="button" value="上传原始图片" id="menuImg_add_nocut" />
                 	</div>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">菜品</td>
                 <td>
	                 <ul  id="menuimgs">
           				{section name=sec loop=$menu}
	                 		<li>
	                 			<a href="{$menu[sec].img}" data-lightbox="menu-group"><img src="{$menu[sec].img}"></a><a class="delMenuImg" rel="{$menu[sec].id}" href="javascript:void(0)">删 除</a>
	                 			<label>{$menu[sec].title}</label>
	                 		</li>
             			{/section}
	             	</ul>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">是否发布</td>
                 <td>
                 	<label><input name="status" type="radio" value="1" checked="checked">准备中</label>
                 	<label><input name="status" type="radio" value="2" {if $data.status eq 2}checked="checked"{/if} >发布中</label>
                 </td>
             </tr>
         </table>
         <p class="btn"><input type="submit" value=" 确定修改 "></p>
         </form>
 	</div>       
 </td>