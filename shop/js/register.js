$(function(){
	$('#getCode').click(function(){
		var url=$('#baseUrl').val();
		var mobile=$('#mobile').val();
		$.ajax({
			type:"post",
			url:url+'login/getcode/'+mobile,
			data:'',
			success:function(res){
				alert(res);
			}
		})
	});
});