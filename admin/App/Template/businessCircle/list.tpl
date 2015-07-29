<script type="text/javascript" src="{$smarty.const.SITE}resource/js/business_circle.js"></script>
<td valign="top" align="center">
         <input type="hidden" id="provinceApiURL" value="{url controller=Api action=GetShopCityByProvince}" />
         <input type="hidden" id="cityApiURL" value="{url controller=Api action=GetShopAreaByCity}" />
 	<div class="main_ta_box">
         <div class="hd_t">城市商圈</div>
         <form action="{url controller=BusinessCircle action=AddCity}" method="post">
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center" style="margin-bottom:30px;">
                <colgroup>
                        <col width="45%">
                        <col width="45%">
                        <col width="15%">
                </colgroup>
         	<tr>
                    <td>
                    <select name="province_id">
                        <option value="">选择</option>
                        {section name=sec loop=$provinces}
                        <option value="{$provinces[sec].id}" {if $province_id eq $provinces[sec].id}selected{/if}>{$provinces[sec].name}</option>
                        {/section}
                    </select>
                    </td>
                <td>
                        <input name="name" value="">
                </td>
                <td><input class="cz_btn" type="submit" value="添加城市"></td></table>
         </form>
         <form action="{url controller=BusinessCircle action=AddArea}" method="post">
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center" style="margin-bottom:30px;">
                <colgroup>
                        <col width="45%">
                        <col width="45%">
                        <col width="15%">
                </colgroup>
         	<tr>
                    <td>
                    <select class="province_id">
                            <option value="">选择</option>
                            {section name=sec loop=$provinces}
                            <option value="{$provinces[sec].id}" {if $province_id eq $provinces[sec].id}selected{/if}>{$provinces[sec].name}</option>
                            {/section}
                    </select>
                    <select name="city_id" class="city_id">
                            <option value="">选择</option>
                    </select>
                    </td>
                    <td>
                            <input name="name" value="">
                    </td>
                    <td>
                    <input class="cz_btn" type="submit" value="添加区域"></td></table>
         </form>
         <form action="{url controller=BusinessCircle action=AddCircle}" method="post">
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center" style="margin-bottom:30px;">
                <colgroup>
                        <col width="45%">
                        <col width="45%">
                        <col width="15%">
                </colgroup>
         	<tr>
                    <td>
                    <select class="province_id">
                            <option value="">选择</option>
                            {section name=sec loop=$provinces}
                            <option value="{$provinces[sec].id}" {if $province_id eq $provinces[sec].id}selected{/if}>{$provinces[sec].name}</option>
                            {/section}
                    </select>
                    <select name="city_id" class="city_id">
                            <option value="">选择</option>
                    </select>
                    <select name="area_id" class="area_id">
                            <option value="">选择</option>
                    </select>
                    </td>
                    <td>
                            <input name="name" value="">
                    </td>
                    <td>
                    <input class="cz_btn" type="submit" value="添加商圈"></td></table>
         </form>
         
         <form action="" method="get">
         <input type="hidden" name="controller" value="BusinessCircle" />
         <input type="hidden" name="action" value="Index" />
         <div class="hd_t1">
         		省份
				<select name="province_id" class="province_id">
					<option value="">选择</option>
					{section name=sec loop=$provinces}
					<option value="{$provinces[sec].id}" {if $province_id eq $provinces[sec].id}selected{/if}>{$provinces[sec].name}</option>
					{/section}
				</select>
				城市
				<select name="city_id" class="city_id">
					<option value="">选择</option>
				</select>
                                区域
				<select name="area_id" class="area_id">
					<option value="">选择</option>
				</select>
				<input class="cz_btn" type="submit" value="查找"></div>
         </form>
         
         <table class="hd_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
			<colgroup>
				<col width="15%">
				<col width="15%">
				<col width="15%">
				<col width="15%">
			</colgroup>
             <tr>
                 <th>省份</th>
                 <th>城市</th>
                 <th>商圈</th>
                 <th>操作</th>
             </tr>
             {section name=sec loop=$list}
             <tr>
                 <td>{$list[sec].province}</td>
                 <td>{$list[sec].city}</td>
                 <td class="hd_td_l">{$list[sec].name}</td>
                 <td style="word-break:keep-all;">
                 	<a href="{url controller=BusinessCircle action=Edit id=$list[sec].id}">编辑</a><a class="delBtn" href="{url controller=BusinessCircle action=Del id=$list[sec].id}">删除</a>
                 </td>
             </tr>
             {/section}
         </table>
         {$page}
     </div>
 </td>