$(function(){
	$('#photo_add').click(function(){
		$(this).before('<tr><td style="text-align:center;">菜品</td>'+
                '<td><input name="menu_title[]" type="text" ><input name="menu_img[]" type="file" style="width:240px;"></td></tr>');
	});
	$('#shopImg_add').click(function(){
		$(this).before('<tr><td style="text-align:center;">更多店铺图片</td>'+
                 '<td><input name="shop_img[]" type="file" style="width:240px;"></td></tr>');
	});
	
	$('a.delImg').click(function(){
		var url=$(this).attr('href');
		var thistr=$(this).parent().parent();
		if(confirm('确定删除吗?')){
			var pid=$(this).attr('rel');
			$.ajax({
				type:'get',
				url:url,
				data:{'pid':pid},
				success:function(res){
					if(res==1){
						thistr.remove();
					}
				}
			})
		}
		return false;
	});
	
	$('a.delShopImg').click(function(){
		var url=$(this).attr('href');
		var thistr=$(this).parent().parent();
		if(confirm('确定删除吗?')){
			var pid=$(this).attr('rel');
			$.ajax({
				type:'get',
				url:url,
				data:{'pid':pid},
				success:function(res){
					if(res==1){
						thistr.remove();
					}
				}
			})
		}
		return false;
	});
	
});

function checkFrom(){
	var flag=true;
	var title=$('input[name=title]').val();
	var address=$('input[name=address]').val();
	
	var msg='';
	if($.trim(title)==''){
		msg+='请填写店铺名称\n';
		flag=false;
	}
	if($('#checkShopRepeat').val()){
		var checkShopRepeat=$('#checkShopRepeat').val();
		$.ajax({
			type:'get',
			url:checkShopRepeat,
			data:{'address':encodeURIComponent($.trim(address))},
			async: false,
			success:function(res){
				if(res>0){
					flag=false;
					msg+='这个地址的咖啡店发布过了\n';
				}
			}
		})
	}
	if(!flag){
		alert(msg);
	}
	return flag;
}