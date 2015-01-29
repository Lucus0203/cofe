<?php /* Smarty version 2.6.18, created on 2014-10-30 17:24:34
         compiled from publicEvent/public_edit.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'url', 'publicEvent/public_edit.tpl', 58, false),)), $this); ?>
<script type="text/javascript" src="<?php echo @SITE; ?>
resource/js/public_add.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">活动编辑</div>
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
                 <td style="text-align:center;">排序</td>
                 <td><input name="num" type="text" value="<?php echo $this->_tpl_vars['data']['num']; ?>
" style="width:40px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">活动标题</td>
                 <td><input name="title" type="text" value="<?php echo $this->_tpl_vars['data']['title']; ?>
" style="width:240px;"></td>
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
                 <td style="text-align:center;">价格费用</td>
                 <td><input name="price" type="text" value="<?php echo $this->_tpl_vars['data']['price']; ?>
" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">活动时间</td>
                 <td><input name="datetime" type="text" value="<?php echo $this->_tpl_vars['data']['datetime']; ?>
" style="width:140px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">活动内容</td>
                 <td><textarea name="content" style="width:540px;height:80px;"><?php echo $this->_tpl_vars['data']['content']; ?>
</textarea></td>
             </tr>
             <tr>
                 <td style="text-align:center;">(640*309)<br/>首页图片</td>
                 <td>
                 	<input name="imgIndex" type="file" style="width:240px;"><?php if ($this->_tpl_vars['data']['img'] != ''): ?><br><img src="<?php echo $this->_tpl_vars['data']['img']; ?>
" /><?php endif; ?>
                 	<input name="img" type="hidden" value="<?php echo $this->_tpl_vars['data']['img']; ?>
" />
                 </td>
             </tr>
             <?php unset($this->_sections['sec']);
$this->_sections['sec']['name'] = 'sec';
$this->_sections['sec']['loop'] = is_array($_loop=$this->_tpl_vars['photo']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
                 <td style="text-align:center;word-break:keep-all;">(160*220,宽高4:5)<br/>海报图片</td>
                 <td>
                 	<img src="<?php echo $this->_tpl_vars['photo'][$this->_sections['sec']['index']]['img']; ?>
"><a class="delImg" rel="<?php echo $this->_tpl_vars['photo'][$this->_sections['sec']['index']]['id']; ?>
" href="<?php echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'PublicEvent','action' => 'DelPhoto'), $this);?>
">删 除</a>
                 	<input name="public_photos[]" type="hidden" value="<?php echo $this->_tpl_vars['photo'][$this->_sections['sec']['index']]['img']; ?>
" />
                 </td>
             </tr>
             <?php endfor; endif; ?>
             <tr>
                 <td style="text-align:center;word-break:keep-all;">(160*220,宽高4:5)<br/>海报图片</td>
                 <td><input name="photos[]" type="file" style="width:240px;"></td>
             </tr>
             <tr id="photo_add"><td colspan="2" ><a style="margin-left:30px;color:#f00;" href="javascript:void(0)">添加海报</a></td></tr>
             <tr>
                 <td style="text-align:center;">是否发布</td>
                 <td>
                 	<lable><input name="ispublic" type="radio" value="1" checked="checked">发布</lable>
                 	<lable><input name="ispublic" type="radio" value="2" <?php if ($this->_tpl_vars['data']['ispublic'] == 2): ?>checked="checked"<?php endif; ?> >不发布</lable>
                 </td>
             </tr>
         </table>
         <p class="btn"><input type="submit" value=" 确定修改 "></p>
         </form>
 	</div>       
 </td>