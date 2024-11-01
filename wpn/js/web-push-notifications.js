var webPushApi = {
	pageDetails: function(){
		try{
			var pDetails = {
				domain: location.hostname.replace('www',''),
				protocol: location.protocol.replace(':','')
			};
			return pDetails;
		}catch(err){
			console.log(err.message);
		}
	},
	sortByKey: function(array, key){
		try{
			return array.sort(function(a, b) {
				var x = a[key]; var y = b[key];
				return ((x<y)?-1: ((x > y)?1:0));
			});
		}catch(err){
			console.log(err.message);
		}
	},
	getCookies: function(){
		try{
			var cArr = document.cookie.split(';');
			var bCookies = [];
			for(var i=0;i < cArr.length;i++){
				var c = cArr[i].split('=', 2); 
				bCookies.push({
					name: $.trim(c[0].replace(/^\s+/, '')),
					value: $.trim(c[1])
				})
			}
			return webPushApi.sortByKey(bCookies, 'name');
		}catch(err){
			console.log(err.message);
		}
	},
	getCookie: function(cookieName){
		try{
			var _Cookies = webPushApi.getCookies();
			var _cIndex = -1
			$.each(_Cookies, function(index, value){
				if(cookieName == _Cookies[index].name)
					_cIndex = index;
			});
			if(_cIndex>-1)
				return _Cookies[_cIndex];
			else
				return null;
		}catch(err){
			console.log(err.message);
		}
	},
	removeCookie: function(cookieName){
		// var cookieName = 'webPushApi-closed';
		var expires = ';expires=Thu, 01-Jan-70 00:00:01 GMT';
		document.cookie = cookieName+'='+expires+';domain='+webPushApi.pageDetails().domain+';path=/';
	},
	setCookie: function(cookieName, cookieValue, expireDate){
		try{
			if(expireDate){
				var date = new Date();
				date.setTime(date.getTime()+(expireDate*24*60*60*1000));
				var expires = ';expires='+date.toGMTString();
			}else var expires = '';
			document.cookie = cookieName+'='+cookieValue+expires+';domain='+webPushApi.pageDetails().domain+';path=/';
			var cookiesEnabled = document.cookie.indexOf(cookieName+'=') != -1;
			if(!cookiesEnabled)
				console.log('-- enable cookies plz --');
		}catch(err){
			console.log(err.message);
		}            
	},
	hasParameter: function(sParam){
		var sPageURL = decodeURIComponent(window.location.search.substring(1)),
			sURLVariables = sPageURL.split('&'),
			sParameterName,
			i;

		for(i=0;i<sURLVariables.length; i++){
			sParameterName = sURLVariables[i].split('=');
			if(sParameterName[0] === sParam)
				return true;
			else
				return false
		}
	},
	getParameter: function(sParam){
		var sPageURL = decodeURIComponent(window.location.search.substring(1)),
			sURLVariables = sPageURL.split('&'),
			sParameterName,
			i;

		for(i=0;i<sURLVariables.length; i++){
			sParameterName = sURLVariables[i].split('=');
			if(sParameterName[0] === sParam)
				return (sParameterName[1] === undefined)?true:sParameterName[1];
		}
	},
	test: function(sParam){
		webPushApi.removeCookie('webPushApi-closed');
	},
	actionCall: function(){
		$(document).on('click', '.web-push-notifications span, .web-push-notifications a', function(){
			webPushApi.setCookie('webPushApi-closed', '1', _webPushApi.actionMessageInterval);
			bQuery('.web-push-notifications, .web-push-notifications-overlay').remove();
		})
	},
	init: function(bQuery){
		console.log('-----------------------------------\n'+_webPushApi.apiName+' '+_webPushApi.apiVersion+'\npName: '+_webPushApi.pName+'\n-----------------------------------');		
		webPushApi.actionCall();
		if(webPushApi.hasParameter('tKey'))
			webPushApi.test();
	}
}