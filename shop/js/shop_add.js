$(function(){
	//裁剪工具
	$('.image-shoper').cropit({ imageBackground: true ,imageBackgroundBorderWidth: 25 });// Width of background border
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
	//上传店铺图片
	$('#shopImg_add').click(function(){
		var baseUrl=$('#baseUrl').val();
		var shopAddUrl=baseUrl+'shop/ajaxUploadShopImg'
	    var imageData = $('.image-shoper').cropit('export');
		if(imageData){
			$('#shopimgs').append('<li class="loading"><img src="'+baseUrl+'images/loading.gif" width="32" height="32"></li>');
			$.ajax({
				type:'post',
				url:shopAddUrl,
				data:{'image-data':imageData},
				dataType:'json',
				success:function(res){
					if(res.src!=''){
						$('#shopimgs .loading').eq(0).remove();
						$('#shopimgs').append('<li><a href="'+res.src+'" data-lightbox="roadtrip"><img src="'+res.src+'"></a><a class="delShopImg" rel="'+res.id+'" href="javascript:void(0);">删 除</a>'+
	             			'<label><input type="radio" name="img" value="'+res.src+'" />作为主图</label></li>');
					}else{
						alert('图片上传失败,请联系管理员');
					}
				}
			});
		}
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
			$('#menuimgs').append('<li class="loading"><img src="'+baseUrl+'images/loading.gif" width="32" height="32"></li>');
			$.ajax({
				type:'post',
				url:shopAddUrl,
				data:{'image-data':imageData,'title':title},
				dataType:'json',
				success:function(res){
					if(res.src!=''){
						$('#menuimgs .loading').eq(0).remove();
						$('#menuimgs').append('<li><a href="'+res.src+'" data-lightbox="menu-group"><img src="'+res.src+'"></a><a class="delMenuImg" rel="'+res.id+'" href="javascript:void(0);">删 除</a>'+
		             			'<label>'+res.title+'</li>');
					}else{
						alert('图片上传失败,请联系管理员');
					}
					
				}
			});
		}
	});
	
	$('#shopimgs').on('click','a.delShopImg',function(){
		var baseUrl=$('#baseUrl').val();
		var thisimg=$(this).parent();
		if(confirm('确定删除吗?')){
			var pid=$(this).attr('rel');
			$.ajax({
				type:'get',
				url:baseUrl+'shop/delshopimg',
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

	$('#menuimgs').on('click','a.delMenuImg',function(){
		var baseUrl=$('#baseUrl').val();
		var thisimg=$(this).parent();
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
	
	$('.province_id').change(function(){
		var index=$('.province_id').index($(this));
		var provinceURL=$('#baseUrl').val()+'api/getCityByProvince';
		var pro_id=$(this).val();
		$.ajax({
			type:'get',
			url:provinceURL,
			data:{'province_id':pro_id},
			success:function(res){
				$('.city_id').eq(index).html('<option value="">选择</option>'+res);
				$('.town_id').eq(index).html('<option value="">选择</option>');
			}
		})
	});
	$('.city_id').change(function(){
		var index=$('.city_id').index($(this));
		var cityApiURL=$('#baseUrl').val()+'api/getTownByCity';
		var city_id=$(this).val();
		$.ajax({
			type:'get',
			url:cityApiURL,
			data:{'city_id':city_id},
			success:function(res){
				$('.town_id').eq(index).html('<option value="">选择</option>'+res);
			}
		})
	});
	
	//北京
	var lng=$('#lng').val()!=''?$('#lng').val():116.400244;
	var lat=$('#lat').val()!=''?$('#lat').val():39.92556;
	// 百度地图API功能
	var map = new BMap.Map("allmap");
	map.enableScrollWheelZoom();                         //启用滚轮放大缩小
	map.addControl(new BMap.ScaleControl());             // 添加比例尺控件
	var point = new BMap.Point(lng,lat); //121.487899,31.249162 上海
	map.centerAndZoom(point, 13);
	var marker = new BMap.Marker(point);// 创建标注
	map.addOverlay(marker);             // 将标注添加到地图中
	marker.enableDragging();           // 可拖拽
	//单击获取点击的经纬度
	map.addEventListener("mousemove",function(e){
		var p = marker.getPosition();  //获取marker的位置
		$('#lng').val(p.lng);
		$('#lat').val(p.lat);
	});
	
	$('#address').blur(function(){
		changeMap(map,point,marker,18);
	});
	$('.town_id,.city_id').change(function(){
		changeMap(map,point,marker,11);
	});
	
	
});

//根据地址变化变更地图
function changeMap(map,point,marker,zoom){
	var address=$('.province_id option:selected').text()+$('.city_id option:selected').text()+$('.town_id option:selected').text()+$('#address').val();
	var myGeo = new BMap.Geocoder();// 创建地址解析器实例
	// 将地址解析结果显示在地图上,并调整地图视野
	myGeo.getPoint(address, function(point){
		if (point) {
			map.centerAndZoom(point, zoom);
			marker.setPosition(point);
			$('#lng').val(point.lng);
			$('#lat').val(point.lat);
		}else{
			alert("您选择地址没有解析到结果!");
		}
	}, $('.city_id option:selected').text());
}

function checkFrom(){
	// Move cropped image data to hidden input
    var imageData = $('.image-editor').cropit('export');
    $('.hidden-image-data').val(imageData);
    
	var flag=true;
	var title=$('input[name=title]').val();
	var address=$('input[name=address]').val();
	var province_id=$('select[name=province_id]').val();
	var city_id=$('select[name=city_id]').val();
	var town_id=$('select[name=town_id]').val();
	
	var msg='';
	if($.trim(title)==''){
		msg+='请填写店铺名称\n';
		flag=false;
	}
	if($.trim(address)==''){
		msg+='请填写店铺地址\n';
		flag=false;
	}
	if($.trim(province_id)==''){
		msg+='请选择省份\n';
		flag=false;
	}
	if($.trim(city_id)==''){
		msg+='请选择城市\n';
		flag=false;
	}
	if($.trim(town_id)==''){
		msg+='请选择区域/县\n';
		flag=false;
	}
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