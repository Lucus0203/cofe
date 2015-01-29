<?php /* Smarty version 2.6.18, created on 2014-11-06 13:25:25
         compiled from index/banner.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'url', 'index/banner.tpl', 19, false),)), $this); ?>
<script type="text/javascript" src="<?php echo @SITE; ?>
resource/js/banner.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">首页滚动图</div>
         <p style="color:red;font-size:14px;text-align:left;padding-left:20px;"><?php echo $this->_tpl_vars['msg']; ?>
</p>
         <form action="" method="post" enctype="multipart/form-data" onsubmit="return checkFrom();">
         <input type="hidden" name="act" value="edit" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">首页滚动图</td>
             </tr>
             <?php unset($this->_sections['ban']);
$this->_sections['ban']['name'] = 'ban';
$this->_sections['ban']['loop'] = is_array($_loop=$this->_tpl_vars['banner']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['ban']['show'] = true;
$this->_sections['ban']['max'] = $this->_sections['ban']['loop'];
$this->_sections['ban']['step'] = 1;
$this->_sections['ban']['start'] = $this->_sections['ban']['step'] > 0 ? 0 : $this->_sections['ban']['loop']-1;
if ($this->_sections['ban']['show']) {
    $this->_sections['ban']['total'] = $this->_sections['ban']['loop'];
    if ($this->_sections['ban']['total'] == 0)
        $this->_sections['ban']['show'] = false;
} else
    $this->_sections['ban']['total'] = 0;
if ($this->_sections['ban']['show']):

            for ($this->_sections['ban']['index'] = $this->_sections['ban']['start'], $this->_sections['ban']['iteration'] = 1;
                 $this->_sections['ban']['iteration'] <= $this->_sections['ban']['total'];
                 $this->_sections['ban']['index'] += $this->_sections['ban']['step'], $this->_sections['ban']['iteration']++):
$this->_sections['ban']['rownum'] = $this->_sections['ban']['iteration'];
$this->_sections['ban']['index_prev'] = $this->_sections['ban']['index'] - $this->_sections['ban']['step'];
$this->_sections['ban']['index_next'] = $this->_sections['ban']['index'] + $this->_sections['ban']['step'];
$this->_sections['ban']['first']      = ($this->_sections['ban']['iteration'] == 1);
$this->_sections['ban']['last']       = ($this->_sections['ban']['iteration'] == $this->_sections['ban']['total']);
?>
             <tr>
                 <td style="text-align:center;word-break:keep-all;">(宽高640:310)滚动图片</td>
                 <td>
                 	<img src="<?php echo $this->_tpl_vars['banner'][$this->_sections['ban']['index']]['img']; ?>
"><a class="delImg" rel="<?php echo $this->_tpl_vars['banner'][$this->_sections['ban']['index']]['id']; ?>
" href="<?php echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'Index','action' => 'DelBanner'), $this);?>
">删 除</a>
                 	<input name="oldbanner[]" type="hidden" value="<?php echo $this->_tpl_vars['banner'][$this->_sections['ban']['index']]['img']; ?>
" />
                 </td>
             </tr>
             <?php endfor; endif; ?>
             <tr>
                 <td style="text-align:center;">(宽高640:310)滚动图片</td>
                 <td><input name="banners[]" type="file" style="width:240px;"></td>
             </tr>
             <tr id="banner_add"><td colspan="2" ><a style="margin-left:30px;" href="javascript:void(0)">添加图片</a></td></tr>
         </table>
         <p class="btn"><input type="submit" value=" 确定 "></p>
         </form>
 	</div>       
 </td>