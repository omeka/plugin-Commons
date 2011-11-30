
var Commons = {
	
	addItem: function() {
		var urlArray = window.location.href.split('/');
		var itemId = urlArray[urlArray.length-1];
		jQuery.post(window.webRoot + "/commons/items/ajax", {'itemId': itemId}, Commons.addResponseHandler);
	
	},
	
	addResponseHandler: function(response) {
		title = jQuery('#dublin-core-title div').html();
		alert(response);
		jQuery('#commons-item-add').replaceWith('<p> "' + title + '" is now part of the Omeka Commons!</p>');
	}
};



jQuery(document).ready(function() {
	jQuery('#commons-item-add').click(Commons.addItem);	
}); 