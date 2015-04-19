<script type="text/javascript" src="{$smarty.const.SITE}resource/js/jquery.cropit.js"></script>
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
                 <td style="text-align:center;">(宽高640:345)<br/>店面图片</td>
                 <td><input name="file" type="file" style="width:240px;">{if $data.img neq ''}<br><img src="{$data.img}" />{/if}
                 	<input name="img" type="hidden" value="{$data.img}" /></td>
             </tr>
             {section name=spi loop=$shopimg}
             <tr>
                 <td style="text-align:center;word-break:keep-all;">更多店铺图片</td>
                 <td>
                 	<img src="{$shopimg[spi].img}"><a class="delShopImg" rel="{$shopimg[spi].id}" href="{url controller=Shop action=DelShopImg}">删 除</a>
                 	<input name="shop_oldimg[]" type="hidden" value="{$shopimg[spi].img}" />
                 </td>
             </tr>
             {/section}
             <tr>
                 <td style="text-align:center;">更多店铺图片</td>
                 <td><input name="shop_img[]" type="file" style="width:240px;"></td>
             </tr>
             <tr id="shopImg_add"><td colspan="2" ><a style="margin-left:30px;" href="javascript:void(0)">添加图片</a></td></tr>
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
             {section name=sec loop=$menu}
             <tr>
                 <td style="text-align:center;word-break:keep-all;">(宽高292:233)<br/>菜品</td>
                 <td><input style="margin-bottom:10px" type="text" name="menu_oldtitle[]" value="{$menu[sec].title}" ><br/>
                 	<img src="{$menu[sec].img}"><a class="delImg" rel="{$menu[sec].id}" href="{url controller=Shop action=DelMenu}">删 除</a>
                 	<input name="menu_oldimg[]" type="hidden" value="{$menu[sec].img}" />
                 </td>
             </tr>
             {/section}
             <tr>
                 <td style="text-align:center;word-break:keep-all;">(宽高292:233)<br/>菜品</td>
                 <td><input type="text" name="menu_title[]" ><input name="menu_img[]" type="file" style="width:240px;"></td>
             </tr>
             <tr id="photo_add"><td colspan="2" ><a style="margin-left:30px;color:#f00;" href="javascript:void(0)">添加菜品</a></td></tr>
             <tr>
                 <td style="text-align:center;">是否发布</td>
                 <td>
                 	<label><input name="status" type="radio" value="1" checked="checked">准备中</label>
                 	<label><input name="status" type="radio" value="2" {if $data.status eq 2}checked="checked"{/if} >发布中</label>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">是否要水印</td>
                 <td>
                 	<label><input name="iswatermark" type="checkbox" value="1" checked="checked">有水印</label>
                 </td>
             </tr>
         </table>
         <p class="btn"><input type="submit" value=" 确定修改 "></p>
         </form>
 	</div>       
 </td>