
var Commons = {
	addItem: function() {
		var urlArray = window.location.href.split('/');
		var itemId = urlArray[urlArray.length-1];
		jQuery.post(window.webRoot + "/commons/items/ajax", {'itemId': itemId}, Commons.addResponseHandler);
	},

	addResponseHandler: function(response) {
		title = jQuery('#dublin-core-title div').html();
		jQuery('#commons-item-add').replaceWith('<p> "' + title + '" is now part of the Omeka Commons!</p>');
	},

	toggleSelected: function() {
        if(jQuery(this).is(':checked')) {
            Commons.batchSelect();
        } else {
            Commons.batchUnselect();
        }
    },

    batchSelect: function() {
        jQuery('input.commons-batch-select').attr('checked', 'checked');
    },

    batchUnselect: function() {
        jQuery('input.commons-batch-select').removeAttr('checked');
    },

    enforceTos: function() {
        if(jQuery(this).is(':checked')) {
            jQuery("input[type='submit']").prop('disabled', false);
        } else {
            jQuery("input[type='submit']").prop('disabled', true);
        }
    }
};

jQuery(document).ready(function() {
	jQuery('#commons-item-add').click(Commons.addItem);
	jQuery('#commons-check-all').click(Commons.toggleSelected);
	jQuery('#commons_tos').change(Commons.enforceTos);
}); 