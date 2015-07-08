<script type="text/javascript" src="{$smarty.const.SITE}resource/js/jquery.cropit.js"></script>
<script type="text/javascript" src="{$smarty.const.SITE}resource/js/shop_add.js"></script>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=ho6LXkYw6eWBzWFlPvcMpLhR"></script>
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
                 <td style="text-align:center;">营业时间</td>
                 <td>
                 	<input name="hours" type="text" value="" style="width:240px;">(旧版需要)<br/>
                 	<select name="hours1" >
                 		{section name=loop loop=24}
                 		{if $smarty.section.loop.index lt 10}
                 			{assign var="h" value='0'|cat:$smarty.section.loop.index}
                 		{else}
                 			{assign var="h" value=$smarty.section.loop.index}
                 		{/if}
                 		<option value="{$h}" >{$h}</option>
                 		{/section}
                 	</select>
                 	:
                 	<select name="minutes1">
                 		<option value="00" >00</option>
                 		<option value="30" >30</option>
                 	</select>
                 	~
                 	<select name="hours2">
                 		{section name=loop loop=24}
                 		{if $smarty.section.loop.index lt 10}
                 			{assign var="h" value='0'|cat:$smarty.section.loop.index}
                 		{else}
                 			{assign var="h" value=$smarty.section.loop.index}
                 		{/if}
                 		<option value="{$h}" >{$h}</option>
                 		{/section}
                 	</select>
                 	:
                 	<select name="minutes2">
                 		<option value="00" >00</option>
                 		<option value="30" >30</option>
                 	</select>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">休息日</td>
                 <td>
                 	<label><input type="radio" name="holidayflag" value="1" checked />无休</label><label><input type="radio" name="holidayflag" value="2" />休息日</label><label><input type="radio" name="holidayflag" value="3" />休息日营业时间</label>
                 	<ul class="holidays" >
                 		<li><label><input name="holidays[]" type="checkbox" value="1">一</label></li>
                 		<li><label><input name="holidays[]" type="checkbox" value="2">二</label></li>
                 		<li><label><input name="holidays[]" type="checkbox" value="3">三</label></li>
                 		<li><label><input name="holidays[]" type="checkbox" value="4">四</label></li>
                 		<li><label><input name="holidays[]" type="checkbox" value="5">五</label></li>
                 		<li><label><input name="holidays[]" type="checkbox" value="6">六</label></li>
                 		<li><label><input name="holidays[]" type="checkbox" value="0">日</label></li>
                 	</ul>
                 	<div class="holidaytime" {if $data.holidayflag eq 3}style="display: block;"{/if} >
	                 	<select name="holidayhours1">
	                 		{section name=loop loop=24}
	                 		{if $smarty.section.loop.index lt 10}
	                 			{assign var="h" value='0'|cat:$smarty.section.loop.index}
	                 		{else}
	                 			{assign var="h" value=$smarty.section.loop.index}
	                 		{/if}
	                 		<option value="{$h}" >{$h}</option>
	                 		{/section}
	                 	</select>
	                 	:
	                 	<select name="holidayminutes1">
	                 		<option value="00" >00</option>
	                 		<option value="30" >30</option>
	                 	</select>
	                 	~
	                 	<select name="holidayhours2">
	                 		{section name=loop loop=24}
	                 		{if $smarty.section.loop.index lt 10}
	                 			{assign var="h" value='0'|cat:$smarty.section.loop.index}
	                 		{else}
	                 			{assign var="h" value=$smarty.section.loop.index}
	                 		{/if}
	                 		<option value="{$h}" >{$h}</option>
	                 		{/section}
	                 	</select>
	                 	:
	                 	<select name="holidayminutes2">
	                 		<option value="00" >00</option>
	                 		<option value="30" >30</option>
	                 	</select>
	                 </div>
                 </td>
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
							<option value="{$provinces[sec].id}" {if 11 eq $provinces[sec].id}selected{/if}>{$provinces[sec].name}</option>
						{/section}
					</select>
	                <select name="city_id" class="city_id">
					<option value="">不限</option>
						{section name=sec loop=$city}
							<option value="{$city[sec].id}" {if 91 eq $city[sec].id}selected{/if}>{$city[sec].name}</option>
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
                 <td><input id="address" name="address" type="text" value="" style="width:600px;">
                 	<div id="allmap"></div>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">经度</td>
                 <td><input id="lng" name="lng" type="text" value="" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">纬度</td>
                 <td><input id="lat" name="lat" type="text" value="" style="width:240px;"></td>
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
                 <td style="text-align:center;">是否发布</td>
                 <td>
                 	<label><input name="status" type="radio" value="1">准备中</label>
                 	<label><input name="status" type="radio" value="2" checked="checked">发布中</label>
                 </td>
             </tr>
         </table>
         <p class="btn"><input type="submit" value=" 确定添加 "></p>
         </form>
 	</div>       
 </td>