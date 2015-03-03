<script type="text/javascript" src="{$smarty.const.SITE}resource/js/shop_add.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <input type="hidden" id="provinceApiURL" value="{url controller=Api action=GetCityByProvince}" />
         <input type="hidden" id="cityApiURL" value="{url controller=Api action=GetTownByCity}" />
         <div class="hd_t">添加店铺</div>
         <form action="" method="post" enctype="multipart/form-data" onsubmit="return checkFrom();">
         <input type="hidden" name="act" value="add" />
         <input type="hidden" id="checkShopRepeat" value="{url controller=Shop action=CheckShopRepeat}" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">添加店铺</td>
             </tr>
             <tr>
                 <td style="text-align:center;">店铺名称</td>
                 <td><input name="title" type="text" value="" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">别名</td>
                 <td><input name="subtitle" type="text" value="" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">(宽高640:310)<br/>店面图片</td>
                 <td><input name="img" type="file" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">更多店铺图片</td>
                 <td><input name="shop_img[]" type="file" style="width:240px;"></td>
             </tr>
             <tr id="shopImg_add"><td colspan="2" ><a style="margin-left:30px;" href="javascript:void(0)">添加图片</a></td></tr>
             <tr>
                 <td style="text-align:center;">营业时间</td>
                 <td><input name="hours" type="text" value="" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">电话</td>
                 <td><input name="tel" type="text" value="" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">城市区域</td>
                 <td>
	                <select name="province_id" class="province_id">
		                {section name=sec loop=$provinces}
							<option value="{$provinces[sec].id}" {if 1 eq $provinces[sec].id}selected{/if}>{$provinces[sec].name}</option>
						{/section}
					</select>
	                <select name="city_id" class="city_id">
					<option value="">不限</option>
						{section name=sec loop=$city}
							<option value="{$city[sec].id}" {if 1 eq $city[sec].id}selected{/if}>{$city[sec].name}</option>
						{/section}
					</select>
					<select name="town_id" class="town_id">
						{section name=sec loop=$towns}
							<option value="{$towns[sec].id}">{$towns[sec].name}</option>
						{/section}
					</select>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">地址</td>
                 <td><input name="address" type="text" value="" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">经度</td>
                 <td><input name="lng" type="text" value="" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">纬度</td>
                 <td><input name="lat" type="text" value="" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">特色</td>
                 <td>
                 	{section name=tag loop=$tags}
                 	<label><input name="features[]" type="checkbox" value="{$tags[tag]}" {if $tags[tag] eq '休闲小憩' or $tags[tag] eq '情侣约会' or $tags[tag] eq '随便吃吃'}checked{/if}>{$tags[tag]}</label>&nbsp;
                 	{/section}
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">简介</td>
                 <td><textarea name="introduction" style="width:540px;height:80px;"></textarea></td>
             </tr>
             <tr>
                 <td style="text-align:center;word-break:keep-all;">(宽高200:280)<br/>菜品</td>
                 <td><input name="menu_title[]" type="text" value="" /><input name="menu_img[]" type="file" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">菜品</td>
                 <td><input name="menu_title[]" type="text" value="" /><input name="menu_img[]" type="file" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">菜品</td>
                 <td><input name="menu_title[]" type="text" value="" /><input name="menu_img[]" type="file" style="width:240px;"></td>
             </tr>
             <tr id="photo_add"><td colspan="2" ><a style="margin-left:30px;" href="javascript:void(0)">添加菜品</a></td></tr>
             <tr>
                 <td style="text-align:center;">是否发布</td>
                 <td>
                 	<label><input name="status" type="radio" value="1">准备中</label>
                 	<label><input name="status" type="radio" value="2" checked="checked">发布中</label>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">是否要水印</td>
                 <td>
                 	<label><input name="iswatermark" type="checkbox" value="1" checked="checked">有水印</label>
                 </td>
             </tr>
         </table>
         <p class="btn"><input type="submit" value=" 确定添加 "></p>
         </form>
 	</div>       
 </td>