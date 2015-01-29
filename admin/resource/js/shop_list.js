$(function(){
	$('a.delBtn').click(function(){
		if(confirm('确定删除吗?')){
			window.location=$(this).attr('href');
		}
		return false;
	});
	$('a.pubBtn').click(function(){
		if(confirm('确定允许吗?')){
			window.location=$(this).attr('href');
		}
		return false;
	});
	$('a.depubBtn').click(function(){
		if(confirm('确定不允许吗?')){
			window.location=$(this).attr('href');
		}
		return false;
	});
});