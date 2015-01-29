<script type="text/javascript" src="{$smarty.const.SITE}resource/js/public_add.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">活动编辑</div>
         <p style="color:red;font-size:14px;text-align:left;padding-left:20px;">{$msg}</p>
         <form action="" method="post" enctype="multipart/form-data" onsubmit="return checkFrom();">
         <input type="hidden" name="act" value="edit" />
         <input type="hidden" name="id" value="{$data.id}" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">活动编辑</td>
             </tr>
             <tr>
                 <td style="text-align:center;">排序</td>
                 <td><input name="num" type="text" value="{$data.num}" style="width:40px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">活动标题</td>
                 <td><input name="title" type="text" value="{$data.title}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">活动地址</td>
                 <td><input name="address" type="text" value="{$data.address}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">经度</td>
                 <td><input name="lng" type="text" value="{$data.lng}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">纬度</td>
                 <td><input name="lat" type="text" value="{$data.lat}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">价格费用</td>
                 <td><input name="price" type="text" value="{$data.price}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">活动时间</td>
                 <td><input name="datetime" type="text" value="{$data.datetime}" style="width:140px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">活动内容</td>
                 <td><textarea name="content" style="width:540px;height:80px;">{$data.content}</textarea></td>
             </tr>
             <tr>
                 <td style="text-align:center;">(宽高640:310)<br/>首页图片</td>
                 <td>
                 	<input name="imgIndex" type="file" style="width:240px;">{if $data.img neq ''}<br><img src="{$data.img}" />{/if}
                 	<input name="img" type="hidden" value="{$data.img}" />
                 </td>
             </tr>
             {section name=sec loop=$photo}
             <tr>
                 <td style="text-align:center;word-break:keep-all;">(宽高160:220)<br/>海报图片</td>
                 <td>
                 	<img src="{$photo[sec].img}"><a class="delImg" rel="{$photo[sec].id}" href="{url controller=PublicEvent action=DelPhoto}">删 除</a>
                 	<input name="public_photos[]" type="hidden" value="{$photo[sec].img}" />
                 </td>
             </tr>
             {/section}
             <tr>
                 <td style="text-align:center;word-break:keep-all;">(宽高160:220)<br/>海报图片</td>
                 <td><input name="photos[]" type="file" style="width:240px;"></td>
             </tr>
             <tr id="photo_add"><td colspan="2" ><a style="margin-left:30px;color:#f00;" href="javascript:void(0)">添加海报</a></td></tr>
             <tr>
                 <td style="text-align:center;">是否发布</td>
                 <td>
                 	<label><input name="ispublic" type="radio" value="1" checked="checked">发布中</label>
                 	<label><input name="ispublic" type="radio" value="2" {if $data.ispublic eq 2}checked="checked"{/if} >不发布</label>
                 </td>
             </tr>
         </table>
         <p class="btn"><input type="submit" value=" 确定修改 "></p>
         </form>
 	</div>       
 </td>