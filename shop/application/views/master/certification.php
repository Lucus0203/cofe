<script type="text/javascript" src="<?php echo base_url();?>js/lightbox.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/certification.js"></script>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=ho6LXkYw6eWBzWFlPvcMpLhR"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">认证信息</div>
         <?php if($msg!=''){?>
         <p style="color:red;font-size:14px;text-align:left;padding-left:20px;"><?php echo $msg; ?></p>
         <?php } ?>
         <form action="" method="post" enctype="multipart/form-data" onsubmit="return checkFrom();">
         <input type="hidden" name="act" value="edit" />
         <input type="hidden" name="id" value="<?php echo $data['id'] ?>" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">认证内容</td>
             </tr>
             <tr>
                 <td style="text-align:center;">店主姓名<span class="red">*</span></td>
                 <td><input name="name" type="text" value="<?php echo $data['name'] ?>" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">联系电话<span class="red">*</span></td>
                 <td><input name="tel" type="text" value="<?php echo $data['tel'] ?>" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">QQ</td>
                 <td><input name="qq" type="text" value="<?php echo $data['qq'] ?>" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">微信号</td>
                 <td><input name="weixin" type="text" value="<?php echo $data['weixin'] ?>" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">身份证<br/>(照片或者扫描,文件格式gif|jpg|png|jpeg)<span class="red">*</span></td>
                 <td>
                 	<input name="IDfile" type="file" style="width:240px;">
                 	<?php if(!empty($data['idfile'])){?><br/><a href="<?php echo base_url().$data['idfile'] ?>" data-lightbox="idfile"><img src="<?php echo base_url().$data['idfile']; ?>" height="300" /></a><?php } ?>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">营业执照<br/>(必须和本人有关,文件格式gif|jpg|png|jpeg)<span class="red">*</span></td>
                 <td>
                 	<input name="business_license" type="file" style="width:240px;">
                 	<?php if(!empty($data['business_license'])){?><br/><a href="<?php echo base_url().$data['business_license'] ?>" data-lightbox="business_license"><img src="<?php echo base_url().$data['business_license']; ?>" height="300" /></a><?php } ?>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">审核状态</td>
                 <td>
                 	<?php if ($data['status']==2){ ?>审核通过 <?php }else{ ?>等待审核<?php } ?>
                 </td>
             </tr>
         </table>
         <p class="btn"><input type="submit" value=" 确定保存 "></p>
         </form>
 	</div>       
 </td>