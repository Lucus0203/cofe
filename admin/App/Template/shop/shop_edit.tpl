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
             	<td style="text-align:center;">菜单列表</td>
             	<td style="padding-left:30px;"><a href="{url controller=ShopMenu action=Index id = $data.id}">查看菜品</a></td>
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
                 <td style="text-align:center;word-break:keep-all;">上传店铺图片<br/>(最小尺寸750x500)</td>
                 <td style="padding-left:30px;">
                 	<a id="shopimgtool" href="javascript:void(0);">显示上传工具</a>
                 	<div id="shopimgBox" style="display: none;">
	                 	<div class="image-shoper">
		                    <input name="file" type="file" style="width:240px;" class="cropit-image-input" />
		                    <div class="cropit-image-preview-container">
							    <div class="cropit-image-preview"></div>
							  </div>
							<div class="slider-wrapper"><span class="icon icon-image small-image"></span><input type="range" class="cropit-image-zoom-input" min="0" max="1" step="0.01"><span class="icon icon-image large-image"></span></div>
					    	<div class="shopimgBoxResize"><span>图片高度</span><input type="range" step="1" max="500" min="0" class="cropit-image-resize" value="0"></div>
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
                 <td>
                 	<input name="hours" type="text" value="{$data.hours}" style="width:240px;">(旧版需要)<br/>
                 	<select name="hours1" >
                 		{section name=loop loop=24}
                 		{if $smarty.section.loop.index lt 10}
                 			{assign var="h" value='0'|cat:$smarty.section.loop.index}
                 		{else}
                 			{assign var="h" value=$smarty.section.loop.index}
                 		{/if}
                 		<option value="{$h}" {if $h eq $data.hours1} selected {/if} >{$h}</option>
                 		{/section}
                 	</select>
                 	:
                 	<select name="minutes1">
                 		<option value="00" {if '00' eq $data.minutes1} selected {/if} >00</option>
                 		<option value="30" {if '30' eq $data.minutes1} selected {/if} >30</option>
                 	</select>
                 	~
                 	<select name="hours2">
                 		{section name=loop loop=24}
                 		{if $smarty.section.loop.index lt 10}
                 			{assign var="h" value='0'|cat:$smarty.section.loop.index}
                 		{else}
                 			{assign var="h" value=$smarty.section.loop.index}
                 		{/if}
                 		<option value="{$h}" {if $h eq $data.hours2} selected {/if} >{$h}</option>
                 		{/section}
                 	</select>
                 	:
                 	<select name="minutes2">
                 		<option value="00" {if '00' eq $data.minutes2} selected {/if} >00</option>
                 		<option value="30" {if '30' eq $data.minutes2} selected {/if} >30</option>
                 	</select>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">休息日</td>
                 <td>
                 	<label><input type="radio" name="holidayflag" value="1" checked />无休</label><label><input type="radio" name="holidayflag" {if $data.holidayflag eq 2} checked {/if}  value="2" />休息日</label><label><input type="radio" name="holidayflag" {if $data.holidayflag eq 3} checked {/if} value="3" />休息日营业时间</label>
                 	<ul class="holidays" {if $data.holidayflag eq 2 or $data.holidayflag eq 3}style="display: block;"{/if} >
                 		<li><label><input name="holidays[]" type="checkbox" {if $data.holidays|strpos:"1" !== false}checked{/if} value="1">一</label></li>
                 		<li><label><input name="holidays[]" type="checkbox" {if $data.holidays|strpos:"2" !== false}checked{/if} value="2">二</label></li>
                 		<li><label><input name="holidays[]" type="checkbox" {if $data.holidays|strpos:"3" !== false}checked{/if} value="3">三</label></li>
                 		<li><label><input name="holidays[]" type="checkbox" {if $data.holidays|strpos:"4" !== false}checked{/if} value="4">四</label></li>
                 		<li><label><input name="holidays[]" type="checkbox" {if $data.holidays|strpos:"5" !== false}checked{/if} value="5">五</label></li>
                 		<li><label><input name="holidays[]" type="checkbox" {if $data.holidays|strpos:"6" !== false}checked{/if} value="6">六</label></li>
                 		<li><label><input name="holidays[]" type="checkbox" {if $data.holidays|strpos:"0" !== false}checked{/if} value="0">日</label></li>
                 	</ul>
                 	<div class="holidaytime" {if $data.holidayflag eq 3}style="display: block;"{/if} >
	                 	<select name="holidayhours1">
	                 		{section name=loop loop=24}
	                 		{if $smarty.section.loop.index lt 10}
	                 			{assign var="h" value='0'|cat:$smarty.section.loop.index}
	                 		{else}
	                 			{assign var="h" value=$smarty.section.loop.index}
	                 		{/if}
	                 		<option value="{$h}" {if $data.holidayhours1 eq $h}selected{/if} >{$h}</option>
	                 		{/section}
	                 	</select>
	                 	:
	                 	<select name="holidayminutes1">
	                 		<option value="00" {if $data.holidayminutes1 eq '00'}selected{/if} >00</option>
	                 		<option value="30" {if $data.holidayminutes1 eq '30'}selected{/if} >30</option>
	                 	</select>
	                 	~
	                 	<select name="holidayhours2">
	                 		{section name=loop loop=24}
	                 		{if $smarty.section.loop.index lt 10}
	                 			{assign var="h" value='0'|cat:$smarty.section.loop.index}
	                 		{else}
	                 			{assign var="h" value=$smarty.section.loop.index}
	                 		{/if}
	                 		<option value="{$h}" {if $data.holidayhours2 eq $h}selected{/if} >{$h}</option>
	                 		{/section}
	                 	</select>
	                 	:
	                 	<select name="holidayminutes2">
	                 		<option value="00" {if $data.holidayminutes2 eq '00'}selected{/if} >00</option>
	                 		<option value="30" {if $data.holidayminutes2 eq '30'}selected{/if} >30</option>
	                 	</select>
	                 </div>
                 </td>
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