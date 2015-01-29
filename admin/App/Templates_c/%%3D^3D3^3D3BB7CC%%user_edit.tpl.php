<?php /* Smarty version 2.6.18, created on 2014-11-26 11:21:43
         compiled from user/user_edit.tpl */ ?>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">用户编辑</div>
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
                 <td class="hd_ta_t" colspan="2">用户编辑</td>
             </tr>
             <tr>
                 <td style="text-align:center;">UUID</td>
                 <td><?php echo $this->_tpl_vars['data']['uuid']; ?>
</td>
             </tr>
             <tr>
                 <td style="text-align:center;">咖啡号</td>
                 <td><input name="user_name" type="text" value="<?php echo $this->_tpl_vars['data']['user_name']; ?>
" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">新密码</td>
                 <td>
                 	<input name="user_password" type="text" value="" style="width:240px;">(不填则不变更原来的密码)
                 	<input name="old_password" type="hidden" value="<?php echo $this->_tpl_vars['data']['user_password']; ?>
">
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">性别</td>
                 <td>
                 	<label><input name="sex" type="radio" value="1" <?php if ($this->_tpl_vars['data']['sex'] == 1): ?>checked<?php endif; ?> />男</label>
                 	<label><input name="sex" type="radio" value="2" <?php if ($this->_tpl_vars['data']['sex'] == 2): ?>checked<?php endif; ?> />女</label>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">职业</td>
                 <td><input name="career" type="text" value="<?php echo $this->_tpl_vars['data']['career']; ?>
" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">家乡</td>
                 <td><input name="home" type="text" value="<?php echo $this->_tpl_vars['data']['home']; ?>
" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">常住地址</td>
                 <td><input name="address" type="text" value="<?php echo $this->_tpl_vars['data']['address']; ?>
" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">星座</td>
                 <td><input name="constellation" type="text" value="<?php echo $this->_tpl_vars['data']['constellation']; ?>
" style="width:140px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">兴趣</td>
                 <td><input name="interest" type="text" value="<?php echo $this->_tpl_vars['data']['interest']; ?>
" style="width:140px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">说说</td>
                 <td><input name="talk" type="text" value="<?php echo $this->_tpl_vars['data']['talk']; ?>
" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">个性签名</td>
                 <td><input name="signature" type="text" value="<?php echo $this->_tpl_vars['data']['signature']; ?>
" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">手机</td>
                 <td><input name="mobile" type="text" value="<?php echo $this->_tpl_vars['data']['mobile']; ?>
" style="width:140px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">邮箱</td>
                 <td><input name="email" type="text" value="<?php echo $this->_tpl_vars['data']['email']; ?>
" style="width:240px;"></td>
             </tr>
              <tr>
                 <td style="text-align:center;">获取地址</td>
                 <td>
                 	<label><input name="allow_add" type="radio" value="1" checked="checked">允许</label>
                 	<label><input name="allow_add" type="radio" value="2" <?php if ($this->_tpl_vars['data']['allow_add'] == 2): ?>checked="checked"<?php endif; ?> >不允许</label>
                 </td>
             </tr>
              <tr>
                 <td style="text-align:center;">找到我</td>
                 <td>
                 	<label><input name="allow_find" type="radio" value="1" checked="checked">允许</label>
                 	<label><input name="allow_find" type="radio" value="2" <?php if ($this->_tpl_vars['data']['allow_find'] == 2): ?>checked="checked"<?php endif; ?> >不允许</label>
                 </td>
             </tr>
              <tr>
                 <td style="text-align:center;">关注我</td>
                 <td>
                 	<label><input name="allow_flow" type="radio" value="1" checked="checked">允许</label>
                 	<label><input name="allow_flow" type="radio" value="2" <?php if ($this->_tpl_vars['data']['allow_flow'] == 2): ?>checked="checked"<?php endif; ?> >不允许</label>
                 </td>
             </tr>
         </table>
         <p class="btn"><input type="submit" value=" 确定修改 "></p>
         </form>
 	</div>       
 </td>