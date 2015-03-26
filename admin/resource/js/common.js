var uri=window.location+'';
if(uri.indexOf('/guanli/')!=-1){
	window.location='http://www.coffee15.com';
}
$(function(){
	$(window).resize(function(){
		$('.main_l').height($(window).height()-160);
	});
	$('.main_l').height($(window).height()-160);
	$('.selectPage').change(function(){
		var uri=window.location+'';
		var page=$(this).val();
		if(uri.indexOf('page_no')!=-1){
			uri=uri.replace(/&page_no=\d+/,'');
		}
		window.location=uri+'&page_no='+page;
	});
});