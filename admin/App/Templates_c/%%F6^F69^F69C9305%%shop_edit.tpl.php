<?php /* Smarty version 2.6.18, created on 2014-11-04 16:57:32
         compiled from shop/shop_edit.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'url', 'shop/shop_edit.tpl', 33, false),)), $this); ?>
<script type="text/javascript" src="<?php echo @SITE; ?>
resource/js/shop_add.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">店铺编辑</div>
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
                 <td class="hd_ta_t" colspan="2">店铺编辑</td>
             </tr>
             <tr>
                 <td style="text-align:center;">店铺名称</td>
                 <td><input name="title" type="text" value="<?php echo $this->_tpl_vars['data']['title']; ?>
" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">别名</td>
                 <td><input name="subtitle" type="text" value="<?php echo $this->_tpl_vars['data']['subtitle']; ?>
" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">(宽高640:310)<br/>店面图片</td>
                 <td><input name="file" type="file" style="width:240px;"><?php if ($this->_tpl_vars['data']['img'] != ''): ?><br><img src="<?php echo $this->_tpl_vars['data']['img']; ?>
" /><?php endif; ?>
                 	<input name="img" type="hidden" value="<?php echo $this->_tpl_vars['data']['img']; ?>
" /></td>
             </tr>
             <?php unset($this->_sections['spi']);
$this->_sections['spi']['name'] = 'spi';
$this->_sections['spi']['loop'] = is_array($_loop=$this->_tpl_vars['shopimg']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['spi']['show'] = true;
$this->_sections['spi']['max'] = $this->_sections['spi']['loop'];
$this->_sections['spi']['step'] = 1;
$this->_sections['spi']['start'] = $this->_sections['spi']['step'] > 0 ? 0 : $this->_sections['spi']['loop']-1;
if ($this->_sections['spi']['show']) {
    $this->_sections['spi']['total'] = $this->_sections['spi']['loop'];
    if ($this->_sections['spi']['total'] == 0)
        $this->_sections['spi']['show'] = false;
} else
    $this->_sections['spi']['total'] = 0;
if ($this->_sections['spi']['show']):

            for ($this->_sections['spi']['index'] = $this->_sections['spi']['start'], $this->_sections['spi']['iteration'] = 1;
                 $this->_sections['spi']['iteration'] <= $this->_sections['spi']['total'];
                 $this->_sections['spi']['index'] += $this->_sections['spi']['step'], $this->_sections['spi']['iteration']++):
$this->_sections['spi']['rownum'] = $this->_sections['spi']['iteration'];
$this->_sections['spi']['index_prev'] = $this->_sections['spi']['index'] - $this->_sections['spi']['step'];
$this->_sections['spi']['index_next'] = $this->_sections['spi']['index'] + $this->_sections['spi']['step'];
$this->_sections['spi']['first']      = ($this->_sections['spi']['iteration'] == 1);
$this->_sections['spi']['last']       = ($this->_sections['spi']['iteration'] == $this->_sections['spi']['total']);
?>
             <tr>
                 <td style="text-align:center;word-break:keep-all;">更多店铺图片</td>
                 <td>
                 	<img src="<?php echo $this->_tpl_vars['shopimg'][$this->_sections['spi']['index']]['img']; ?>
"><a class="delShopImg" rel="<?php echo $this->_tpl_vars['shopimg'][$this->_sections['spi']['index']]['id']; ?>
" href="<?php echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'Shop','action' => 'DelShopImg'), $this);?>
">删 除</a>
                 	<input name="shop_oldimg[]" type="hidden" value="<?php echo $this->_tpl_vars['shopimg'][$this->_sections['spi']['index']]['img']; ?>
" />
                 </td>
             </tr>
             <?php endfor; endif; ?>
             <tr>
                 <td style="text-align:center;">更多店铺图片</td>
                 <td><input name="shop_img[]" type="file" style="width:240px;"></td>
             </tr>
             <tr id="shopImg_add"><td colspan="2" ><a style="margin-left:30px;" href="javascript:void(0)">添加图片</a></td></tr>
             <tr>
                 <td style="text-align:center;">营业时间</td>
                 <td><input name="hours" type="text" value="<?php echo $this->_tpl_vars['data']['hours']; ?>
" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">电话</td>
                 <td><input name="tel" type="text" value="<?php echo $this->_tpl_vars['data']['tel']; ?>
" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">地址</td>
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
                 <td style="text-align:center;">特色</td>
                 <td>
                 	<?php unset($this->_sections['t']);
$this->_sections['t']['name'] = 't';
$this->_sections['t']['loop'] = is_array($_loop=$this->_tpl_vars['tags']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['t']['show'] = true;
$this->_sections['t']['max'] = $this->_sections['t']['loop'];
$this->_sections['t']['step'] = 1;
$this->_sections['t']['start'] = $this->_sections['t']['step'] > 0 ? 0 : $this->_sections['t']['loop']-1;
if ($this->_sections['t']['show']) {
    $this->_sections['t']['total'] = $this->_sections['t']['loop'];
    if ($this->_sections['t']['total'] == 0)
        $this->_sections['t']['show'] = false;
} else
    $this->_sections['t']['total'] = 0;
if ($this->_sections['t']['show']):

            for ($this->_sections['t']['index'] = $this->_sections['t']['start'], $this->_sections['t']['iteration'] = 1;
                 $this->_sections['t']['iteration'] <= $this->_sections['t']['total'];
                 $this->_sections['t']['index'] += $this->_sections['t']['step'], $this->_sections['t']['iteration']++):
$this->_sections['t']['rownum'] = $this->_sections['t']['iteration'];
$this->_sections['t']['index_prev'] = $this->_sections['t']['index'] - $this->_sections['t']['step'];
$this->_sections['t']['index_next'] = $this->_sections['t']['index'] + $this->_sections['t']['step'];
$this->_sections['t']['first']      = ($this->_sections['t']['iteration'] == 1);
$this->_sections['t']['last']       = ($this->_sections['t']['iteration'] == $this->_sections['t']['total']);
?>
                 	<label><input name="features[]" type="checkbox" <?php echo $this->_tpl_vars['tags'][$this->_sections['t']['index']]['checked']; ?>
 value="<?php echo $this->_tpl_vars['tags'][$this->_sections['t']['index']]['tag']; ?>
"><?php echo $this->_tpl_vars['tags'][$this->_sections['t']['index']]['tag']; ?>
</label>&nbsp;
                 	<?php endfor; endif; ?>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">简介</td>
                 <td><textarea name="introduction" style="width:540px;height:80px;"><?php echo $this->_tpl_vars['data']['introduction']; ?>
</textarea></td>
             </tr>
             <?php unset($this->_sections['sec']);
$this->_sections['sec']['name'] = 'sec';
$this->_sections['sec']['loop'] = is_array($_loop=$this->_tpl_vars['menu']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['sec']['show'] = true;
$this->_sections['sec']['max'] = $this->_sections['sec']['loop'];
$this->_sections['sec']['step'] = 1;
$this->_sections['sec']['start'] = $this->_sections['sec']['step'] > 0 ? 0 : $this->_sections['sec']['loop']-1;
if ($this->_sections['sec']['show']) {
    $this->_sections['sec']['total'] = $this->_sections['sec']['loop'];
    if ($this->_sections['sec']['total'] == 0)
        $this->_sections['sec']['show'] = false;
} else
    $this->_sections['sec']['total'] = 0;
if ($this->_sections['sec']['show']):

            for ($this->_sections['sec']['index'] = $this->_sections['sec']['start'], $this->_sections['sec']['iteration'] = 1;
                 $this->_sections['sec']['iteration'] <= $this->_sections['sec']['total'];
                 $this->_sections['sec']['index'] += $this->_sections['sec']['step'], $this->_sections['sec']['iteration']++):
$this->_sections['sec']['rownum'] = $this->_sections['sec']['iteration'];
$this->_sections['sec']['index_prev'] = $this->_sections['sec']['index'] - $this->_sections['sec']['step'];
$this->_sections['sec']['index_next'] = $this->_sections['sec']['index'] + $this->_sections['sec']['step'];
$this->_sections['sec']['first']      = ($this->_sections['sec']['iteration'] == 1);
$this->_sections['sec']['last']       = ($this->_sections['sec']['iteration'] == $this->_sections['sec']['total']);
?>
             <tr>
                 <td style="text-align:center;word-break:keep-all;">(宽高200:280)<br/>菜品</td>
                 <td><input style="margin-bottom:10px" type="text" name="menu_oldtitle[]" value="<?php echo $this->_tpl_vars['menu'][$this->_sections['sec']['index']]['title']; ?>
" ><br/>
                 	<img src="<?php echo $this->_tpl_vars['menu'][$this->_sections['sec']['index']]['img']; ?>
"><a class="delImg" rel="<?php echo $this->_tpl_vars['menu'][$this->_sections['sec']['index']]['id']; ?>
" href="<?php echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'Shop','action' => 'DelMenu'), $this);?>
">删 除</a>
                 	<input name="menu_oldimg[]" type="hidden" value="<?php echo $this->_tpl_vars['menu'][$this->_sections['sec']['index']]['img']; ?>
" />
                 </td>
             </tr>
             <?php endfor; endif; ?>
             <tr>
                 <td style="text-align:center;word-break:keep-all;">(宽高200:280)<br/>菜品</td>
                 <td><input type="text" name="menu_title[]" ><input name="menu_img[]" type="file" style="width:240px;"></td>
             </tr>
             <tr id="photo_add"><td colspan="2" ><a style="margin-left:30px;color:#f00;" href="javascript:void(0)">添加菜品</a></td></tr>
             <tr>
                 <td style="text-align:center;">是否发布</td>
                 <td>
                 	<label><input name="status" type="radio" value="1" checked="checked">准备中</label>
                 	<label><input name="status" type="radio" value="2" <?php if ($this->_tpl_vars['data']['status'] == 2): ?>checked="checked"<?php endif; ?> >发布中</label>
                 </td>
             </tr>
         </table>
         <p class="btn"><input type="submit" value=" 确定修改 "></p>
         </form>
 	</div>       
 </td>