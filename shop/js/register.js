$(function(){
	$('#getCode').click(function(){
		var url=$('#baseUrl').val();
		var username=$('#username').val();
		var mobile=$('#mobile').val();
		if(ismobile(mobile)){
			$.ajax({
				type:"post",
				url:url+'login/getcode',
				data:{'mobile':mobile,'username':username},
				success:function(res){
					alert(res);
				}
			})
		}
		
	});
});