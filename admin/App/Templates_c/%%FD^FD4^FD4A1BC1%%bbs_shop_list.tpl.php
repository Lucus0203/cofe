<?php /* Smarty version 2.6.18, created on 2014-11-13 16:35:23
         compiled from bbs/bbs_shop_list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'url', 'bbs/bbs_shop_list.tpl', 37, false),)), $this); ?>
<script type="text/javascript" src="<?php echo @SITE; ?>
resource/js/bbs_list.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t"><?php echo $this->_tpl_vars['shop']['title']; ?>
留言</div>
         <form action="" method="get">
         <input type="hidden" name="controller" value="Bbs" />
         <input type="hidden" name="action" value="Shop" />
         <input type="hidden" name="shopid" value="<?php echo $this->_tpl_vars['shop']['id']; ?>
" />
         <div class="hd_t1">查找留言<input class="cz_input" type="text" name="keyword" value="<?php echo $this->_tpl_vars['keyword']; ?>
"><input class="cz_btn" type="submit" value="查找"></div>
         </form>
         <table class="hd_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
			<colgroup>
				<col width="15%">
				<col width="10%">
				<col width="8%">
				<col width="">
				<col width="7%">
				<col width="7%">
			</colgroup>
             <tr>
                 <th>头像</th>
                 <th>咖啡号</th>
                 <th>留言时间</th>
                 <th>内容</th>
                 <th>审核状态</th>
                 <th>操作</th>
             </tr>
             <?php unset($this->_sections['sec']);
$this->_sections['sec']['name'] = 'sec';
$this->_sections['sec']['loop'] = is_array($_loop=$this->_tpl_vars['list']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
                 <td><?php if ($this->_tpl_vars['list'][$this->_sections['sec']['index']]['path'] != ''): ?><img src="<?php echo $this->_tpl_vars['list'][$this->_sections['sec']['index']]['path']; ?>
"><?php else: ?><img src="<?php echo @SITE; ?>
resource/images/no_img.gif"><?php endif; ?></td>
                 <td class="hd_td_l"><?php echo $this->_tpl_vars['list'][$this->_sections['sec']['index']]['user_name']; ?>
</td>
                 <td><?php echo $this->_tpl_vars['list'][$this->_sections['sec']['index']]['created']; ?>
</td>
                 <td><?php echo $this->_tpl_vars['list'][$this->_sections['sec']['index']]['content']; ?>
</td>
                 <td><?php if ($this->_tpl_vars['list'][$this->_sections['sec']['index']]['allow'] == 1): ?>通过<?php else: ?>不通过<?php endif; ?></td>
                 <td style="word-break:keep-all;">
                 	<?php if ($this->_tpl_vars['list'][$this->_sections['sec']['index']]['allow'] != 1): ?>
                 		<a class="pubBtn" href="<?php echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'Bbs','action' => 'Allow','type' => 'shop','id' => $this->_tpl_vars['list'][$this->_sections['sec']['index']]['id']), $this);?>
">通过</a>
                 	<?php else: ?>
                 		<a class="depubBtn" href="<?php echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'Bbs','action' => 'DeAllow','type' => 'shop','id' => $this->_tpl_vars['list'][$this->_sections['sec']['index']]['id']), $this);?>
">不通过</a>
                 	<?php endif; ?>
                 		<a class="delBtn" href="<?php echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'Bbs','action' => 'Del','type' => 'shop','id' => $this->_tpl_vars['list'][$this->_sections['sec']['index']]['id']), $this);?>
">删除</a>
                 </td>
             </tr>
             <?php endfor; endif; ?>
         </table>
         <?php echo $this->_tpl_vars['page']; ?>

     </div>
 </td>