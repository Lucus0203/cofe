$(function(){
	$('#photo_add').click(function(){
		$(this).before('<tr><td style="text-align:center;">海报图片</td>'+
                '<td><input name="photos[]" type="file" style="width:240px;"></td></tr>');
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
});

function checkFrom(){
	var flag=true;
	var title=$('input[name=title]').val();
	var content=$('textarea[name=content]').val();
	var msg='';
	if($.trim(title)==''){
		msg+='请填写活动标题\n';
		flag=false;
	}
	if($.trim(content)==''){
		msg+='请填写活动内容\n';
		flag=false;
	}
	if(!flag){
		alert(msg);
	}
	return flag;
}