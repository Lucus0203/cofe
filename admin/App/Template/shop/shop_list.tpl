<script type="text/javascript" src="{$smarty.const.SITE}resource/js/shop_list.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">咖啡店铺</div>
         <form action="" method="get">
         <input type="hidden" name="controller" value="Shop" />
         <input type="hidden" name="action" value="Index" />
         <div class="hd_t1">查找店铺<input class="cz_input" type="text" name="title" value="{$title}"><input class="cz_btn" type="submit" value="查找"></div>
         </form>
         <table class="hd_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
			<colgroup>
				<col width="15%">
				<col width="15%">
				<col width="15%">
				<col width="25%">
				<col width="7%">
				<col width="7%">
				<col width="7%">
				<col width="7%">
				<col width="7%">
			</colgroup>
             <tr>
                 <th>缩略图</th>
                 <th>店铺名</th>
                 <th>电话</th>
                 <th>地点</th>
                 <th>留言</th>
                 <th>坐标</th>
                 <th>地图</th>
                 <th>发布状态</th>
                 <th>操作</th>
             </tr>
             {section name=sec loop=$list}
             <tr>
                 <td>{if $list[sec].img neq ''}<img src="{$list[sec].img}">{else}<img src="{$smarty.const.SITE}resource/images/no_img.gif">{/if}</td>
                 <td class="hd_td_l">{$list[sec].title}</td>
                 <td>{$list[sec].tel}</td>
                 <td>{$list[sec].address}</td>
                 <td><a href="{url controller=Bbs action=Shop shopid=$list[sec].id}">查看</a></td>
                 <td>{$list[sec].lng},<br/>{$list[sec].lat}</td>
                 <td><a href="http://api.map.baidu.com/lbsapi/getpoint/index.html" target="_blank">查看</a></td>
                 <td>{if $list[sec].status eq '1'}准备中{else}发布中{/if}</td>
                 <td style="word-break:keep-all;">
                 	<a href="{url controller=Shop action=Edit id=$list[sec].id}">编辑</a><a class="delBtn" href="{url controller=Shop action=Del id=$list[sec].id}">删除</a><br/>
                 	{if $list[sec].status eq '2'}<a class="pubBtn" href="{url controller=Shop action=Public id=$list[sec].id}">准备</a>{else}<a class="depubBtn" href="{url controller=Shop action=DePublic id=$list[sec].id}">发布{/if}</a>
                 </td>
             </tr>
             {/section}
         </table>
         {$page}
     </div>
 </td>