<?php /* Smarty version 2.6.18, created on 2014-11-12 11:22:19
         compiled from shop/shop_add.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'url', 'shop/shop_add.tpl', 7, false),)), $this); ?>
<script type="text/javascript" src="<?php echo @SITE; ?>
resource/js/shop_add.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">添加店铺</div>
         <form action="" method="post" enctype="multipart/form-data" onsubmit="return checkFrom();">
         <input type="hidden" name="act" value="add" />
         <input type="hidden" id="checkShopRepeat" value="<?php echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'Shop','action' => 'CheckShopRepeat'), $this);?>
" />
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
                 <td style="text-align:center;">(宽高640:310)<br/>店面图片</td>
                 <td><input name="img" type="file" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">更多店铺图片</td>
                 <td><input name="shop_img[]" type="file" style="width:240px;"></td>
             </tr>
             <tr id="shopImg_add"><td colspan="2" ><a style="margin-left:30px;" href="javascript:void(0)">添加图片</a></td></tr>
             <tr>
                 <td style="text-align:center;">营业时间</td>
                 <td><input name="hours" type="text" value="" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">电话</td>
                 <td><input name="tel" type="text" value="" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">地址</td>
                 <td><input name="address" type="text" value="" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">经度</td>
                 <td><input name="lng" type="text" value="" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">纬度</td>
                 <td><input name="lat" type="text" value="" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">特色</td>
                 <td>
                 	<?php unset($this->_sections['tag']);
$this->_sections['tag']['name'] = 'tag';
$this->_sections['tag']['loop'] = is_array($_loop=$this->_tpl_vars['tags']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['tag']['show'] = true;
$this->_sections['tag']['max'] = $this->_sections['tag']['loop'];
$this->_sections['tag']['step'] = 1;
$this->_sections['tag']['start'] = $this->_sections['tag']['step'] > 0 ? 0 : $this->_sections['tag']['loop']-1;
if ($this->_sections['tag']['show']) {
    $this->_sections['tag']['total'] = $this->_sections['tag']['loop'];
    if ($this->_sections['tag']['total'] == 0)
        $this->_sections['tag']['show'] = false;
} else
    $this->_sections['tag']['total'] = 0;
if ($this->_sections['tag']['show']):

            for ($this->_sections['tag']['index'] = $this->_sections['tag']['start'], $this->_sections['tag']['iteration'] = 1;
                 $this->_sections['tag']['iteration'] <= $this->_sections['tag']['total'];
                 $this->_sections['tag']['index'] += $this->_sections['tag']['step'], $this->_sections['tag']['iteration']++):
$this->_sections['tag']['rownum'] = $this->_sections['tag']['iteration'];
$this->_sections['tag']['index_prev'] = $this->_sections['tag']['index'] - $this->_sections['tag']['step'];
$this->_sections['tag']['index_next'] = $this->_sections['tag']['index'] + $this->_sections['tag']['step'];
$this->_sections['tag']['first']      = ($this->_sections['tag']['iteration'] == 1);
$this->_sections['tag']['last']       = ($this->_sections['tag']['iteration'] == $this->_sections['tag']['total']);
?>
                 	<label><input name="features[]" type="checkbox" value="<?php echo $this->_tpl_vars['tags'][$this->_sections['tag']['index']]; ?>
" <?php if ($this->_tpl_vars['tags'][$this->_sections['tag']['index']] == '休闲小憩' || $this->_tpl_vars['tags'][$this->_sections['tag']['index']] == '情侣约会' || $this->_tpl_vars['tags'][$this->_sections['tag']['index']] == '随便吃吃'): ?>checked<?php endif; ?>><?php echo $this->_tpl_vars['tags'][$this->_sections['tag']['index']]; ?>
</label>&nbsp;
                 	<?php endfor; endif; ?>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">简介</td>
                 <td><textarea name="introduction" style="width:540px;height:80px;"></textarea></td>
             </tr>
             <tr>
                 <td style="text-align:center;word-break:keep-all;">(宽高200:280)<br/>菜品</td>
                 <td><input name="menu_title[]" type="text" value="" /><input name="menu_img[]" type="file" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">菜品</td>
                 <td><input name="menu_title[]" type="text" value="" /><input name="menu_img[]" type="file" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">菜品</td>
                 <td><input name="menu_title[]" type="text" value="" /><input name="menu_img[]" type="file" style="width:240px;"></td>
             </tr>
             <tr id="photo_add"><td colspan="2" ><a style="margin-left:30px;" href="javascript:void(0)">添加菜品</a></td></tr>
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