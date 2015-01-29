<?php /* Smarty version 2.6.18, created on 2014-11-12 11:05:29
         compiled from userEvent/event_edit.tpl */ ?>
<script type="text/javascript" src="<?php echo @SITE; ?>
resource/js/public_add.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">活动编辑</div>
         <p style="color:red;font-size:14px;text-align:left;padding-left:20px;"><?php echo $this->_tpl_vars['msg']; ?>
</p>
         <form action="" method="post" enctype="multipart/form-data" onsubmit="return checkFrom();">
         <input type="hidden" name="act" value="edit" />
         <input type="hidden" name="id" value="<?php echo $this->_tpl_vars['data']['id']; ?>
" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">活动编辑</td>
             </tr>
             <tr>
                 <td style="text-align:center;">活动标题</td>
                 <td><input name="title" type="text" value="<?php echo $this->_tpl_vars['data']['title']; ?>
" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">约会对象</td>
                 <td><input name="title" type="text" value="<?php echo $this->_tpl_vars['data']['dating']; ?>
" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">活动时间</td>
                 <td><input name="datetime" type="text" value="<?php echo $this->_tpl_vars['data']['datetime']; ?>
" style="width:140px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">活动地址</td>
                 <td><input name="address" type="text" value="<?php echo $this->_tpl_vars['data']['address']; ?>
" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">经度</td>
                 <td><input name="lng" type="text" value="<?php echo $this->_tpl_vars['data']['lng']; ?>
" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">纬度</td>
                 <td><input name="lat" type="text" value="<?php echo $this->_tpl_vars['data']['lat']; ?>
" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">活动内容</td>
                 <td><textarea name="content" style="width:540px;height:80px;"><?php echo $this->_tpl_vars['data']['content']; ?>
</textarea></td>
             </tr>
             <tr>
                 <td style="text-align:center;">(宽高640:310)<br/>图片</td>
                 <td>
                 	<input name="imgIndex" type="file" style="width:240px;"><?php if ($this->_tpl_vars['data']['img'] != ''): ?><br><img src="<?php echo $this->_tpl_vars['data']['img']; ?>
" /><?php endif; ?>
                 	<input name="img" type="hidden" value="<?php echo $this->_tpl_vars['data']['img']; ?>
" />
                 </td>
             </tr>
         	<tr>
                 <td style="text-align:center;">是否允许发布</td>
                 <td>
                 	<label><input name="ispublic" type="radio" value="1" checked="checked">允许</label>
                 	<label><input name="ispublic" type="radio" value="2" <?php if ($this->_tpl_vars['data']['allow'] == 2): ?>checked="checked"<?php endif; ?> >不允许</label>
                 </td>
             </tr>
         </table>
         <p class="btn"><input type="submit" value=" 确定修改 "></p>
         </form>
 	</div>       
 </td>