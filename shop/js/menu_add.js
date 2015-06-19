$(function(){
	//裁剪工具
	$('.image-menuer').cropit({ imageBackground: true ,imageBackgroundBorderWidth: 25 });// Width of background border
	$('#shopimgtool').click(function(){
		if (typeof FileReader =='undefined'){
            alert("您的浏览器不支持文件上传工具,建议换谷歌或者火狐浏览器.");
		}
		$(this).text($("#shopimgBox").is(":hidden") ? "收起上传工具" : "显示上传工具");
		$("#shopimgBox").slideToggle();
	});
	$('#menuimgtool').click(function(){
		if (typeof FileReader =='undefined'){
            alert("您的浏览器不支持文件上传工具,建议换谷歌或者火狐浏览器.");
		}
		$(this).text($("#menuimgBox").is(":hidden") ? "收起上传工具" : "显示上传工具");
		$("#menuimgBox").slideToggle();
	});
	
	//上传菜单
	$('#menuImg_add').click(function(){
		var title=$('#menuTitle').val();
		if($.trim(title)==''){
			alert('请填写菜品名称');
			return;
		}
		var baseUrl=$('#baseUrl').val();
		var shopAddUrl=baseUrl+'shop/ajaxUploadShopMenu'
	    var imageData = $('.image-menuer').cropit('export');
		if(imageData){
			$('#menuList').append('<tr class="loading"><td class="menu_img">'+
	                 	'<img src="'+baseUrl+'images/loading.gif" width="32" height="32">'+
		             '</td><td class="menu_title">'+title+'</td>'+
	                 '<td><ul class="menu_price"><li>'+
		                 		'价格:<input class="price" type="text" value="" style="ime-mode:disabled;" /> '+
		                 		'规格:<select class="type">'+
		                 			'<option value="常规">常规</option>'+
		                 			'<option value="小">小</option>'+
		                 			'<option value="中">中</option>'+
		                 			'<option value="大">大</option>'+
		                 			'<option value="超大">超大</option>'+
		                 			'<option value="其他">其他</option>'+
		                 		'</select>	<a class="del" href="#">删除</a>'+
		                 	'</li><li class="add"><a href="#">添加</a></li>'+
		                 '</ul></td>'+
	                 '<td style="text-align:center;">'+
	                 	'待售中'+
	                 '</td>'+
	                 '<td class="opera">'+
	                 	'<a class="updatePrice" href="#">更新价格</a>'+
	                 	'<a class="public" href="#">上架</a>'+
	                 	'<a class="delMenuImg" rel="" href="javascript:void(0)">删 除</a>'+
	                 '</td></tr>');//'<li class="loading"><img src="'+baseUrl+'images/loading.gif" width="32" height="32"></li>';
			$.ajax({
				type:'post',
				url:shopAddUrl,
				data:{'image-data':imageData,'title':title},
				dataType:'json',
				success:function(res){
					if(res.src!=''){
						$('#menuList .loading').eq(0).find('.menu_img').html('<a href="'+res.src+'" data-lightbox="menu-group"><img src="'+res.src+'"></a>');
						$('#menuList .loading').eq(0).find('.delMenuImg').attr('rel',res.id);
						$('#menuimgs .loading').eq(0).removeAttr('class');
					}else{
						alert('图片上传失败,请联系管理员');
					}
					
				}
			});
		}
	});

	//删除咖啡甜点
	$('#menuList').on('click','a.delMenuImg',function(){
		var baseUrl=$('#baseUrl').val();
		var thisimg=$(this).parent().parent();
		if(confirm('确定删除吗?')){
			var pid=$(this).attr('rel');
			$.ajax({
				type:'get',
				url:baseUrl+'shop/delmenu',
				data:{'pid':pid},
				success:function(res){
					if(res==1){
						thisimg.remove();
					}
				}
			})
		}
		return false;
	});
	//其他规格
	$('#menuList').on('change','.menu_price select.type',function(){
		if($(this).val()=='其他'&&$(this).next().text()=='删除'){
			$(this).after('<input type="text" value="" />');
		}else if($(this).next().text()!='删除'){
			$(this).next().remove();
		}
	});
	//价格删除
	$('#menuList').on('click','.menu_price a.del',function(){
		if($(this).parent().find('.price').val()!=''){
			if(confirm('确定删除吗?')){
				$(this).parent().remove();
			}
		}else{
			$(this).parent().remove();
		}
		return false;
	});
	//价格规格添加
	$('#menuList').on('click','.menu_price .add',function(){
		$(this).before('<li>价格:<input class="price" type="text" value="" style="ime-mode:disabled;" /> '+
 		'规格:<select class="type">'+
 			'<option value="常规">常规</option>'+
 			'<option value="小">小</option>'+
 			'<option value="中">中</option>'+
 			'<option value="大">大</option>'+
 			'<option value="超大">超大</option>'+
 			'<option value="其他">其他</option>'+
 		'</select>	<a class="del" href="#">删除</a></li>');
		return false;
	});
	
	//更新价格
	$('#menuList').on('click','.opera .updatePrice',function(){
		var baseUrl=$('#baseUrl').val();
		var menuid=$(this).parent().find('.delMenuImg').attr('rel');
		var menuprice=$(this).parent().parent().find('.menu_price');
		var prices='';
		var typies='';
		menuprice.find('.price').each(function(i){
			prices+=$(this).val()+',';
			var t=menuprice.find('.type').eq(i).val();
			if(t=='其他'){
				t=menuprice.find('.type').eq(i).next().val();
			}
			typies+=t+',';
		});
		prices=prices.slice(0,-1);
		typies=typies.slice(0,-1);
		$.ajax({
			type:'post',
			url:baseUrl+'shop/menuPriceUpdate',
			data:{'menuid':menuid,'prices':prices,'typies':typies},
			success:function(res){
				if(res==1){
					alert('价格更新成功');
				}else{
					alert('价格更新失败');
				}
			}
		});
	});

	//上架
	$('#menuList').on('click','.opera .public',function(){
		var baseUrl=$('#baseUrl').val();
		var menuid=$(this).parent().find('.delMenuImg').attr('rel');
		var thisobj=$(this);
		$.ajax({
			type:'post',
			url:baseUrl+'shop/menuPublic',
			data:{'menuid':menuid,'public':2},
			success:function(res){
				if(res==1){
					alert('成功上架');
					thisobj.parent().prev().text('寄售中');
					thisobj.attr('class','depublic').text('下架');
				}else{
					alert('操作失败');
				}
			}
		});
	});
	//下架
	$('#menuList').on('click','.opera .depublic',function(){
		var baseUrl=$('#baseUrl').val();
		var menuid=$(this).parent().find('.delMenuImg').attr('rel');
		var thisobj=$(this);
		$.ajax({
			type:'post',
			url:baseUrl+'shop/menuPublic',
			data:{'menuid':menuid,'public':1},
			success:function(res){
				if(res==1){
					alert('成功下架');
					thisobj.parent().prev().text('待售');
					thisobj.attr('class','public').text('上架');
				}else{
					alert('操作失败');
				}
			}
		});
	});
	
});

function checkFrom(){
	// Move cropped image data to hidden input
    var imageData = $('.image-editor').cropit('export');
    $('.hidden-image-data').val(imageData);
    
	var flag=true;
	if($('input[name=img]').length<=0){
		msg+='请上传至少一张店铺图片\n';
		flag=false;
	}else{
		if($('input[name=img]:checked').length<=0){
			msg+='请选择一张店铺图片作为主图\n';
			flag=false;
		}
	}
	if(!flag){
		alert(msg);
	}
	
	if(flag){
		return confirm('确认修改信息吗?')
	}
	return flag;
}