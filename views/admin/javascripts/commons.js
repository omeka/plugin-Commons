
var Commons = {
	
	addItem: function() {
		var urlArray = window.location.href.split('/');
		var itemId = urlArray[urlArray.length-1];
		jQuery.post(window.webRoot + "/commons/items/add", {'itemId': itemId}, Commons.responseHandler);
	
	},
	
	responseHandler: function(response) {
		alert(response);
	}
};



jQuery(document).ready(function() {
	jQuery('#commons-item-add').click(Commons.addItem);	
}); 