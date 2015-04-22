<script type="text/javascript" src="{$smarty.const.SITE}resource/js/lightbox.min.js"></script>
<script type="text/javascript" src="{$smarty.const.SITE}resource/js/master_shopinfo.js"></script>
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
                 <td>{$data.title}</td>
             </tr>
             <tr>
                 <td style="text-align:center;">别名</td>
                 <td>{$data.subtitle}</td>
             </tr>
             <tr>
                 <td style="text-align:center;">店铺图片</td>
                 <td>
	                 <ul  id="shopimgs">
             			{section name=spi loop=$shopimg}
	                 		<li>
	                 			<a href="{$shopimg[spi].img}" data-lightbox="roadtrip"><img src="{$shopimg[spi].img}"></a>
	                 			<label><input type="radio" name="img" value="{$shopimg[spi].id}" {if $data.img eq $shopimg[spi].img} checked {/if} />作为主图</label>
	                 		</li>
             			{/section}
	             	</ul>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">营业时间</td>
                 <td>{$data.hours}</td>
             </tr>
             <tr>
                 <td style="text-align:center;">电话</td>
                 <td>{$data.tel}</td>
             </tr>
             <tr>
                 <td style="text-align:center;">城市区域</td>
                 <td>
                 	{$data.province}{$data.city}{$data.town}
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">地址</td>
                 <td>{$data.address}<input id="address" name="address" type="hidden" value="{$data.address}" style="width:600px;">
                 	<div id="allmap"></div>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">经度</td>
                 <td>{$data.lng}<input id="lng" name="lng" type="hidden" value="{$data.lng}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">纬度</td>
                 <td>{$data.lat}<input id="lat" name="lat" type="hidden" value="{$data.lat}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">特色</td>
                 <td>
                 	{section name=t loop=$tags}
                 	<label>{$tags[t].tag}</label>&nbsp;
                 	{/section}
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">简介</td>
                 <td>{$data.introduction}</td>
             </tr>
             <tr>
                 <td style="text-align:center;">菜品</td>
                 <td>
	                 <ul  id="menuimgs">
           				{section name=sec loop=$menu}
	                 		<li>
	                 			<a href="{$menu[sec].img}" data-lightbox="menu-group"><img src="{$menu[sec].img}"></a>
	                 			<label>{$menu[sec].title}</label>
	                 		</li>
             			{/section}
	             	</ul>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">审核状态</td>
                 <td>
                 	<label><input name="status" type="radio" value="1" checked="checked">再审核</label>
                 	<label><input name="status" type="radio" value="2" {if $data.status eq 2}checked="checked"{/if} >审核通过</label>
                 </td>
             </tr>
         </table>
         <p class="btn"><input type="submit" value=" 确  定 "></p>
         </form>
 	</div>       
 </td>