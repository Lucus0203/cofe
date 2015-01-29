<?php /* Smarty version 2.6.18, created on 2014-11-13 16:56:08
         compiled from publicEvent/public_list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'url', 'publicEvent/public_list.tpl', 10, false),)), $this); ?>
<script type="text/javascript" src="<?php echo @SITE; ?>
resource/js/public_list.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">官方活动管理</div>
         <form action="" method="get">
         <input type="hidden" name="controller" value="PublicEvent" />
         <input type="hidden" name="action" value="Index" />
         <div class="hd_t1">查找活动<input class="cz_input" type="text" name="title" value="<?php echo $this->_tpl_vars['title']; ?>
"><input class="cz_btn" type="submit" value="查找"></div>
         </form>
         <input id="orderNumUrl" type="hidden" value="<?php echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'PublicEvent','action' => 'order'), $this);?>
" />
         <table class="hd_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
			<colgroup>
				<col width="5%">
				<col width="15%">
				<col width="">
				<col width="15%">
				<col width="20%">
				<col width="7%">
				<col width="7%">
			</colgroup>
             <tr>
                 <th>排序</th>
                 <th>缩略图</th>
                 <th>官方活动标题</th>
                 <th>时间</th>
                 <th>地点</th>
                 <th>发布状态</th>
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
                 <td><input class="num" type="text" value="<?php echo $this->_tpl_vars['list'][$this->_sections['sec']['index']]['num']; ?>
" style="width:40px;"><input type="hidden" value="<?php echo $this->_tpl_vars['list'][$this->_sections['sec']['index']]['id']; ?>
" /></td>
                 <td><?php if ($this->_tpl_vars['list'][$this->_sections['sec']['index']]['img'] != ''): ?><img src="<?php echo $this->_tpl_vars['list'][$this->_sections['sec']['index']]['img']; ?>
"><?php else: ?><img src="<?php echo @SITE; ?>
resource/images/no_img.gif"><?php endif; ?></td>
                 <td class="hd_td_l"><?php echo $this->_tpl_vars['list'][$this->_sections['sec']['index']]['title']; ?>
</td>
                 <td><?php echo $this->_tpl_vars['list'][$this->_sections['sec']['index']]['datetime']; ?>
</td>
                 <td><?php echo $this->_tpl_vars['list'][$this->_sections['sec']['index']]['address']; ?>
</td>
                 <td><?php if ($this->_tpl_vars['list'][$this->_sections['sec']['index']]['ispublic'] == '2'): ?>未发布<?php else: ?>发布中<?php endif; ?></td>
                 <td style="word-break:keep-all;">
                 	<a href="<?php echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'PublicEvent','action' => 'Edit','id' => $this->_tpl_vars['list'][$this->_sections['sec']['index']]['id']), $this);?>
">编辑</a><a class="delBtn" href="<?php echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'PublicEvent','action' => 'Del','id' => $this->_tpl_vars['list'][$this->_sections['sec']['index']]['id']), $this);?>
">删除</a><br/>
                 	<?php if ($this->_tpl_vars['list'][$this->_sections['sec']['index']]['ispublic'] == '2'): ?><a class="pubBtn" href="<?php echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'PublicEvent','action' => 'Public','id' => $this->_tpl_vars['list'][$this->_sections['sec']['index']]['id']), $this);?>
">发布</a><?php else: ?><a class="depubBtn" href="<?php echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'PublicEvent','action' => 'DePublic','id' => $this->_tpl_vars['list'][$this->_sections['sec']['index']]['id']), $this);?>
">不发布<?php endif; ?></a>
                 </td>
             </tr>
             <?php endfor; endif; ?>
         </table>
         <p class="btn"><input id="changeOrder" type="button" value="变更顺序" /></p>
         <?php echo $this->_tpl_vars['page']; ?>

     </div>
 </td>