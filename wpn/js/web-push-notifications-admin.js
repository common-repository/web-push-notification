var sendNotification = function(url){
	var contentType ="application/x-www-form-urlencoded; charset=utf-8";
	if(window.XDomainRequest) //for IE8,IE9
		contentType = "text/plain";

	jQuery.ajax({
		url: url,
		type: 'POST',
		dataType: 'json',
		data: '{"some":"json"}',		
		contentType: contentType,    
		success:function(data){
			var response = data;
			jQuery('.notification-response-notice').fadeIn().addClass('notice-success').find('p').text(('[ '+response.postId+' ] - '+response.message));
		}, error:function(jqXHR,textStatus,errorThrown){
			//alert("You can not send Cross Domain AJAX requests: "+errorThrown);
		}
	});
}

jQuery(document).ready(function(){		
	jQuery('.SendNotification').click(function(){
		var url = jQuery(this).attr('url');
		if(url!='')
			sendNotification(url);
	})
})