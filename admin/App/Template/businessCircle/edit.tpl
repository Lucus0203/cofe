<script type="text/javascript" src="{$smarty.const.SITE}resource/js/business_circle_list.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">商圈</div>
         <form action="{url controller=BusinessCircle action=Add}" method="post">
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center" style="margin-bottom:30px;">
         	<tr>
         		<td>
				<select name="province_id" class="province_id">
					<option value="">不限</option>
					{section name=sec loop=$provinces}
					<option value="{$provinces[sec].id}" {if $province_id eq $provinces[sec].id}selected{/if}>{$provinces[sec].name}</option>
					{/section}
				</select>
				</td>
				<td>
				<select name="city_id" class="city_id">
					<option value="">不限</option>
					{section name=sec loop=$city}
					<option value="{$city[sec].id}" {if $city_id eq $city[sec].id}selected{/if}>{$city[sec].name}</option>
					{/section}
				</select>
				</td>
				<td>
				<select name="town_id" class="town_id">
					<option value="">不限</option>
					{section name=sec loop=$towns}
					<option value="{$towns[sec].id}">{$towns[sec].name}</option>
					{/section}
				</select>
				</td>
				<td>
					名称<input name="name" value="">
				</td>
				<td>经度<input name="lng" value="">，维度<input name="lat" value="">
				</td>
				<td>
				<input class="cz_btn" type="submit" value="添加"></td></table>
         </form>
     </div>
 </td>