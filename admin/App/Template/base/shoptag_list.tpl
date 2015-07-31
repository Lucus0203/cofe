<script type="text/javascript" src="{$smarty.const.SITE}resource/js/business_circle.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">店铺标签</div>
         <form action="{url controller=Base action=AddShopTag}" method="post">
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center" style="margin-bottom:30px;">
                <colgroup>
                        <col width="45%">
                        <col width="45%">
                </colgroup>
                <td>
                        <input name="name" value="">
                </td>
                <td><input class="cz_btn" type="submit" value="添加标签"></td></table>
         </form>
         
         <table class="hd_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
            <colgroup>
                    <col width="15%">
                    <col width="15%">
            </colgroup>
             <tr>
                 <th>标签</th>
                 <th>操作</th>
             </tr>
             {section name=sec loop=$list}
             <tr>
                 <td class="hd_td_l">{$list[sec].name}</td>
                 <td style="word-break:keep-all;">
                 	<a href="{url controller=Base action=EditShopTag id=$list[sec].id}">编辑</a><a class="delBtn" href="{url controller=Base action=DelShopTag id=$list[sec].id}">删除</a>
                 </td>
             </tr>
             {/section}
         </table>
         {$page}
     </div>
 </td>