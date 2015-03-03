$(function(){
	$(window).resize(function(){
		$('.main_l').height($(window).height()-160);
	});
	$('.main_l').height($(window).height()-160);
	$('.selectPage').change(function(){
		var page=$(this).val();
		var uri=window.location+'';
		if(uri.indexOf('page_no')!=-1){
			uri=uri.replace(/&page_no=\d+/,'');
		}
		window.location=uri+'&page_no='+page;
	});
});