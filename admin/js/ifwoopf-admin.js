jQuery(function($){
	// Start Display Notes using jquery
	var message = `
        <div class="iflair_admin_notes">
            <p><strong>Note:</strong> Please select the figure according to your theme settings (Appearance -> Customize -> WooCommerce -> Product Catalog: "Products per row"). Ensure the values for "On page load" & "On load more" are multiples of the selected figure. For example, if you choose 3, set values like 3, 6, 9, 12, 15, and so on.</p>
        </div>
    `;    
    jQuery(message).insertAfter("#number_products_4_display");
    // End Display Notes using jquery

	window.ifwoopf_editors = {};
	var ifwoopf_default_settings = ifwoopf_admin_obj.ifwoopf_default_settings;
	var select2_object = ifwoopf_admin_obj.select2_object;
	var select2_objects = ifwoopf_admin_obj.select2_objects;
	var select2_placeholder = ifwoopf_admin_obj.select2_placeholder;
	var select2_objects_keys = ( !ifwoopf_is_empty(select2_objects) ) ? Object.keys(select2_objects) : [];
	var default_sorting_option = ifwoopf_admin_obj.default_sorting_option;
	var editor_error_messages = ifwoopf_admin_obj.editor_error_messages;

	$('.ifwoopf-select2').each(function(key, val){

		var select_id = $(this).attr('id');
		var settings = ( select2_objects_keys.length > 0 && select2_objects.hasOwnProperty(select_id) ) ? select2_objects[select_id] : select2_object;
		if( settings.length < 1 ){
			settings = {};
		}

		$(this).select2(settings);

	});

	/* Show setting popup */
	$(".ifwoopf-table-modal-open").click(function(e){

		e.preventDefault();
		var id = $(this).data('id');
		$('#ifwoopf_popup_'+id).addClass('ifwoopf-popup-show');		

	});

	/* Hide setting popup */
	$(".ifwoopf-popup-close").click(function(e){

		e.preventDefault();
		var popup = $(this).closest('.ifwoopf-popup-main');
		popup.removeClass('ifwoopf-popup-show');

	});

	/* Close popup on escape click */	
	document.addEventListener('keydown', function(event) {
    const key = event.key;
    	if (key === "Escape") {
        	var popup1 = jQuery('.ifwoopf-popup-main');
			popup1.removeClass('ifwoopf-popup-show');
    	}
	});

	// terms include and exclude selection code
	jQuery('.select2-selection--multiple').click(function(){
		var selfclass = jQuery(this).closest('tr').next().find('.ifwoopf-include-terms option:selected');
		var include_products = [];
		  jQuery(selfclass).each(function() {
		   include_products.push(this.text);
		});
		
    	var selfclass2 = jQuery(this).closest('tr').prev().find('.ifwoopf-exclude-terms option:selected');
		var exclude_products = [];
		jQuery(selfclass2).each(function() {
		    exclude_products.push(this.text);        
		});
    	
		var exclude_terms=[]; var include_terms=[];
        var exclude_clas1s = jQuery(this).parent().parent().prev();       
        var all_exclude_terms = [];
        if (jQuery(exclude_clas1s).hasClass('ifwoopf-exclude-terms')){
      		jQuery(exclude_clas1s).find('option').each(function() {
        		all_exclude_terms.push(this.text);
       		});
        }
        var include_clas2s = jQuery(this).parent().parent().prev();
        var all_include_terms = [];
        if (jQuery(include_clas2s).hasClass('ifwoopf-include-terms')){       
	        jQuery(include_clas2s).find('option').each(function() {
	        	all_include_terms.push(this.text);       
	    	});
        }
		
        jQuery.grep(include_products, function(el) {
		   if (jQuery.inArray(el, all_exclude_terms) != -1) {		 
				jQuery(exclude_clas1s).find('option').filter(function(){
				   return jQuery.trim(jQuery(this).text()) ==  el
				}).hide();
			}
		});

		jQuery.grep(exclude_products, function(el) {
	   		if (jQuery.inArray(el, all_include_terms) != -1) {	 
				jQuery(include_clas2s).find('option').filter(function(){
				   return jQuery.trim(jQuery(this).text()) ==  el
				}).hide();
	   		}
		});

		var select2_results__options = jQuery('.select2-results__options');
		var results_li_products = [];
		jQuery(select2_results__options).find("li").each(function()
	    {
	       results_li_products.push(jQuery(this).text());         
	    }); 
	    
		jQuery.grep(include_products, function(el) {
	   		if (jQuery.inArray(el, results_li_products) != -1) {	 
				jQuery(select2_results__options).find('li').filter(function(){
				   return jQuery.trim(jQuery(this).text()) ==  el
				}).hide();
	   		}
		});

		jQuery.grep(exclude_products, function(el) {
	   		if (jQuery.inArray(el, results_li_products) != -1) {	 
				jQuery(select2_results__options).find('li').filter(function(){
				   return jQuery.trim(jQuery(this).text()) ==  el
				}).hide();
	   		}
		});		

    });

	// Remove terms in include and exclude x click
    jQuery('.select2-selection__choice__remove').click(function(){
    	var removed_val = jQuery(this).next('.select2-selection__choice__display').text();
		var select2_results__options = jQuery('.select2-results__options');
		var results_li_products = [];
		jQuery(select2_results__options).find("li").each(function()
	    {
	       results_li_products.push(jQuery(this).text());         
	    }); 
	    
		jQuery.grep(removed_val, function(el) {
	   		if (jQuery.inArray(el, results_li_products) != -1) {	 
				jQuery(select2_results__options).find('li').filter(function(){
				   return jQuery.trim(jQuery(this).text()) ==  el
				}).show();
	   		}
		});
    });

	/* Hide show taxonomy heading text */
	if( $('.ifwoopf-display-heading').length > 0 ){

		$('.ifwoopf-display-heading').each(function(index, value){

			var parent = $(this).closest('.ifwoopf-popup-content');
			var toggle = parent.find('.ifwoopf-field-toggle-check');

			if( !$(this).is(":checked") ){
				parent.find(".ifwoopf-heading-text").closest('tr').hide();
				parent.find(".ifwoopf-field-toggle-check").closest('tr').hide();
				parent.find(".ifwoopf-field-defalut-toggle").closest('tr').hide();
			}else{
				parent.find(".ifwoopf-heading-text").closest('tr').show();
				parent.find(".ifwoopf-field-toggle-check").closest('tr').show();
				if( toggle.is(":checked") ){
					parent.find(".ifwoopf-field-defalut-toggle").closest('tr').show();
				}
			}

		});

	}

	$('.ifwoopf-display-heading').change(function(){

		var parent = $(this).closest('.ifwoopf-popup-content');
		var toggle = parent.find('.ifwoopf-field-toggle-check');

		if( !$(this).is(":checked") ){
			parent.find(".ifwoopf-heading-text").closest('tr').hide();
			parent.find(".ifwoopf-field-toggle-check").closest('tr').hide();
			parent.find(".ifwoopf-field-defalut-toggle").closest('tr').hide();
		}else{
			parent.find(".ifwoopf-heading-text").closest('tr').show();
			parent.find(".ifwoopf-field-toggle-check").closest('tr').show();
			if( toggle.is(":checked") ){
				parent.find(".ifwoopf-field-defalut-toggle").closest('tr').show();
			}
		}

	});

	/* Hide show all heading text */
	if( $('.ifwoopf-display-heading-check').length > 0 ){

		$('.ifwoopf-display-heading-check').each(function(index, value){

			var text_clss = $(this).data('text-class');

			if( !$(this).is(":checked") ){
				$("."+text_clss).closest('tr').hide();
			}else{
				$("."+text_clss).closest('tr').show();
			}

		});

	}

	$('.ifwoopf-display-heading-check').change(function(){

		var text_clss = $(this).data('text-class');

		if( !$(this).is(":checked") ){
			$("."+text_clss).closest('tr').hide();
		}else{
			$("."+text_clss).closest('tr').show();
		}

	});

	/* Enable/disable search in title checkbox */
	$('.ifwoopf-check-search-display').change(function(){

		if( $(this).is(":checked") ){
			$('.ifwoopf-check-search-in-title').prop("checked", true);
		}else{
			$('.ifwoopf-check-search-in-title').prop("checked", false);
		}

	});

	/* Hide show taxonomy dropdown placeholder */
	$('.selection-type').each(function(key, val){

		var parent = $(this).closest('.ifwoopf-popup-content');

		if( $(this).val() == 'dropdown' ){
			parent.find(".ifwoopf-dropdown-placeholder").closest('tr').show();
		}else{
			parent.find(".ifwoopf-dropdown-placeholder").closest('tr').hide();
		}

	});

	/* Hide show dropdown first option text field */
	$('.selection-type').change(function(){

		var parent = $(this).closest('.ifwoopf-popup-content');

		if( $(this).val() == 'dropdown' ){
			parent.find(".ifwoopf-dropdown-placeholder").closest('tr').show();
		}else{
			parent.find(".ifwoopf-dropdown-placeholder").closest('tr').hide();
		}

	});

	/* Initialize sortable fields */
	$( ".ifwoopf-sorting-field-parent" ).sortable({
	  cursor: "move",
	  handle: ".sorting-handle",
	});

	/* Reset sorting of sortable fields */
	$(".ifwoopf-reset-sorting").click(function(){

		var parent = $(this).closest('.ifwoopf-popup-content');
		var li_html = '';
		var li_length = parent.find('li.ifwoopf-multiple-field-child').length;

		if( li_length > 0 ){

			for (var i = 0; i <= li_length; i++) {
				
				var li_el = parent.find('li.ifwoopf-multiple-field-child [data-default-sorting="'+i+'"]');
				if( li_el.length > 0 ){
					li_html = li_el.closest('li.ifwoopf-multiple-field-child').clone();
				}

				if ( !ifwoopf_is_empty(li_html) ){
					li_el.closest('li.ifwoopf-multiple-field-child').remove();
					parent.find('ul.ifwoopf-sorting-field-parent').append(li_html);
				}

			}

		}

	});

	/* Set or unset value on default sorting opiton */
	$(document).on('change','.ifwoopf-enable-sorting',function(){
		
		var enabled_sorting = {};
		var first_key = Object.keys(default_sorting_option)[0];
		var first_val = default_sorting_option[first_key];

		$('.ifwoopf-default-sorting').html('');

		$('.ifwoopf-enable-sorting').each(function(key, val){

			if( $(this).is(":checked") ){

				$('.ifwoopf-default-sorting').prop('disabled', false);
				var s_key = $(this).data('sorting-key');
				var s_label = $(this).data('sorting-label');
				
				$('.ifwoopf-default-sorting').append($("<option></option>").attr("value", s_key).text(s_label)); 

				enabled_sorting[s_key] = s_label;
			}

		});

		if( Object.keys(enabled_sorting).length < 1 ){

			$('.ifwoopf-default-sorting').append($("<option></option>").attr("value", first_key).text(first_val));
			$('.ifwoopf-default-sorting').prop('disabled', true);

		}

		$('.ifwoopf-default-sorting option[value="'+first_key+'"]').prop('selected', true);

	});

	/* Initialize sortable fields */
	$( ".ifwoopf-sortable-table" ).sortable({
		cursor: "move",
		handle: ".sorting-handle",
		items: "tbody > tr",
		stop: function( event, ui ) {
			var item = ui.item;
			if( item.prev().length > 0 && !item.prev().hasClass("ifwoopf-row-sortable") ){
				$(this).sortable( "cancel" );
			}
		}
	});

	/* disable hidden input on checked or unchecked checkbox  */
	$('.ifwoopf-checkbox-has-hidden').change(function(){

		var hidden_input = $(this).next();

		if( $(this).is(':checked') ){
			hidden_input.prop('disabled', true);
		}else{
			hidden_input.prop('disabled', false);
		}

	});

	/* Reset all popup fields */
	$(".ifwoopf-popup-reset").click(function(){

		var popup = $(this).closest('.ifwoopf-popup-main');
		var popup_id = popup.attr('id');
		var fields = $('#'+popup_id+' :input');
	
		ifwoopf_reset_fields(fields);

		if( popup.find('.ifwoopf-sorting-fields-wrap').length > 0 ){
			popup.find('.ifwoopf-sorting-fields-wrap').each(function(index, value){
				ifwoopf_reset_sorting($(this));
			});
		}

	});

	/* Reset filds */
	function ifwoopf_reset_fields(fields){

		$.each(fields, function(index, value){

			var $this = $(this);
			var input_type = $(this).attr('type');
			var default_val = ifwoopf_check_for_default($this);

			if( ifwoopf_is_empty(input_type) ){
				if( $(this).is('select') ){
					input_type = 'select';
				}
			}

			if( input_type === 'checkbox' || input_type === 'radio' ){
				if( !ifwoopf_is_empty(default_val) ){
					$(this).prop('checked', true);
				}else{
					$(this).prop('checked', false);
					$(this).removeAttr('checked');
				}
				$(this).trigger('change');
			}else if( input_type === 'select' ){
				var multiple = $(this).attr('multiple');

				if( !ifwoopf_is_empty(default_val) ){
					$(this).val(default_val);
				}else{
					if ( !ifwoopf_is_empty(multiple) ){
						$(this).val('');
					}else{
						$(this).prop("selectedIndex", 0);
					}
				}

				if( $(this).hasClass('ifwoopf-select2') ){
					$(this).trigger('change');
				}
			}else{
				if( !ifwoopf_is_empty(default_val) ){
					$(this).val(default_val);
				}else{
					$(this).val('');
				}
			}

		});

	}

	function ifwoopf_check_for_default(el){

		var default_val = '';
		var data_name = el.data('name');

		if( !ifwoopf_is_empty(data_name) ){
			data_name = data_name.split('=>');

			if( data_name.length > 0 ){
				for (var i = 0; i < data_name.length; i++) {
					if( ifwoopf_is_empty(default_val) && ifwoopf_default_settings.hasOwnProperty(data_name[i]) ){
						default_val = ifwoopf_default_settings[data_name[i]];
					}else if( !ifwoopf_is_empty(default_val) && default_val.hasOwnProperty(data_name[i]) ){
						default_val = default_val[data_name[i]];
					}
				}
			}
		}

		return default_val;

	}

	/* Reset sorting */
	function ifwoopf_reset_sorting(sorting_el){

		var parent = sorting_el;
		var li_html = '';
		var li_length = parent.find('li.ifwoopf-multiple-field-child').length;

		if( li_length > 0 ){

			for (var i = 0; i <= li_length; i++) {
				
				var li_el = parent.find('li.ifwoopf-multiple-field-child [data-default-sorting="'+i+'"]');
				if( li_el.length > 0 ){
					li_html = li_el.closest('li.ifwoopf-multiple-field-child').clone();
				}

				if ( !ifwoopf_is_empty(li_html) ){
					li_el.closest('li.ifwoopf-multiple-field-child').remove();
					parent.find('ul.ifwoopf-sorting-field-parent').append(li_html);
					parent.sortable({
					  cursor: "move",
					  handle: ".sorting-handle",
					});
				}

			}

		}

	}

	/* tab js */
	jQuery(".ifwoopf-tab a").click(function(e){
		e.preventDefault();
	    jQuery(".ifwoopf-tab").removeClass("ifwoopf-tab-active-li");
	    jQuery(this).closest('.ifwoopf-tab').addClass("ifwoopf-tab-active-li");
	    var tagid = jQuery(this).data("tag");
	    jQuery("#ifwoopf_currnet_tab").val(tagid);
	    jQuery(".ifwoopf-tab-content").removeClass("ifwoopf-tab-active-content").addClass("ifwoopf-tab-content-hide");
	    jQuery("#" + tagid).addClass("ifwoopf-tab-active-content").removeClass("ifwoopf-tab-content-hide");
	});


	/* Hide show all toggle checkbox */
	if( $('.ifwoopf-toggle-check').length > 0 ){

		$('.ifwoopf-toggle-check').each(function(index, value){

			var popup = $(this).closest('.ifwoopf-popup-content');
			var display_order = popup.find('.ifwoopf-display-order').val();
			var selection_type = popup.find('.selection-type').val();

			if( display_order == 'hierarchical' && selection_type !== 'dropdown' ){
				$(this).closest('tr').show();
			}else{
				$(this).closest('tr').hide();
			}

		});

	}

	$('.ifwoopf-display-order, .selection-type').change(function(){

		var popup = $(this).closest('.ifwoopf-popup-content');
		var display_order = popup.find('.ifwoopf-display-order').val();
		var selection_type = popup.find('.selection-type').val();

		if( display_order == 'hierarchical' && selection_type !== 'dropdown' ){
			popup.find('.ifwoopf-toggle-check').closest('tr').show();
		}else{
			popup.find('.ifwoopf-toggle-check').closest('tr').hide();
		}

	});


	/* Hide show taxonomy default toggle select */
	if( $('.ifwoopf-field-toggle-check').length > 0 ){

		$('.ifwoopf-field-toggle-check').each(function(index, value){

			var parent = $(this).closest('.ifwoopf-popup-content');
			var heading = parent.find('.ifwoopf-display-heading');

			if( $(this).is(":checked") && heading.is(":checked") ){
				parent.find(".ifwoopf-field-defalut-toggle").closest('tr').show();
			}else{
				parent.find(".ifwoopf-field-defalut-toggle").closest('tr').hide();
			}

		});

	}

	$('.ifwoopf-field-toggle-check').change(function(){

		var parent = $(this).closest('.ifwoopf-popup-content');
		var heading = parent.find('.ifwoopf-display-heading');

		if( $(this).is(":checked") && heading.is(":checked") ){
			parent.find(".ifwoopf-field-defalut-toggle").closest('tr').show();
		}else{
			parent.find(".ifwoopf-field-defalut-toggle").closest('tr').hide();
		}

	});

	/* Hide show all heading text */
	if( $('.ifwoopf-display-button-check').length > 0 ){

		$('.ifwoopf-display-button-check').each(function(index, value){

			var text_id = $(this).data('btn-text-field');

			if( !$(this).is(":checked") ){
				$("#"+text_id).closest('tr').hide();
			}else{
				$("#"+text_id).closest('tr').show();
			}

		});

	}

	$('.ifwoopf-display-button-check').change(function(){

		var text_id = $(this).data('btn-text-field');

		if( !$(this).is(":checked") ){
			$("#"+text_id).closest('tr').hide();
		}else{
			$("#"+text_id).closest('tr').show();
		}

	});

});

/* Check if give value is empty or not */
function ifwoopf_is_empty(value){

	var is_empty = false;

	if( value === null || value === '' || typeof value === 'undefined' ){
	    is_empty  = true;
	}

	return is_empty;
}


// jQuery(document).ready(function() {

// 	jQuery('#search_min_input').on('input', function() {
//         var value = jQuery(this).val();
//         if (value < 1) {
//             jQuery(this).val(1);
//         }
//     });

// 	jQuery('#number_of_columns_selection_4_shop').parent().prev().addClass('shop_column_th');
// 	jQuery('#enable_filter_on_shop_page').parent().prev().addClass('enable_filter_th');

// 	jQuery('<span class="default_theme_msg">- Please select columns as per your theme settings. (Appearance -> Customize -> Woocomerce -> Product Catalog : "Products per row")</span>').insertAfter("#number_of_columns_selection_4_shop");
// 	jQuery( "span.default_theme_msg" ).wrap( "<div class='iflair_admin_notes'>Notes :</div>" );
// 	jQuery('<span class="column_msg"></span>').insertAfter("span.default_theme_msg");
// 	var col_1_val = '- Please set figure at below "On page load" & "On load more", multiple of value what we choose here. (For ex if you choose 1 than at both below field choose such as 1, 2, 3, 4, 5... and so on';
// 	var col_2_val = '- Please set figure at below "On page load" & "On load more", multiple of value what we choose here. (For ex if you choose 2 than at both below field choose such as 2, 4, 6, 8, 10... and so on';
// 	var col_3_val = '- Please set figure at below "On page load" & "On load more", multiple of value what we choose here. (For ex if you choose 3 than at both below field choose such as 3, 6, 9, 12, 15... and so on';
// 	var col_4_val = '- Please set figure at below "On page load" & "On load more", multiple of value what we choose here. (For ex if you choose 4 than at both below field choose such as 4, 8, 12, 16, 20... and so on';
// 	var col_5_val = '- Please set figure at below "On page load" & "On load more", multiple of value what we choose here. (For ex if you choose 5 than at both below field choose such as 5, 10, 15, 20, 25... and so on';
// 	var col_6_val = '- Please set figure at below "On page load" & "On load more", multiple of value what we choose here. (For ex if you choose 6 than at both below field choose such as 6, 12, 18, 24, 30... and so on';
  	
// 	var number_of_columns_selection_4_shop = jQuery("#number_of_columns_selection_4_shop").val();

// 	if(number_of_columns_selection_4_shop == 1){
// 		jQuery('.column_msg').text(col_1_val);
// 	}
// 	if(number_of_columns_selection_4_shop == 2){
// 		jQuery('.column_msg').text(col_2_val);
// 	} 
// 	if(number_of_columns_selection_4_shop == 3){
// 		jQuery('.column_msg').text(col_3_val);
// 	} 
// 	if(number_of_columns_selection_4_shop == 4){
// 		jQuery('.column_msg').text(col_4_val);
// 	} 
// 	if(number_of_columns_selection_4_shop == 5){
// 		jQuery('.column_msg').text(col_5_val);
// 	} 
// 	if(number_of_columns_selection_4_shop == 6){
// 		jQuery('.column_msg').text(col_6_val);
// 	} 
	
//   	jQuery('#number_of_columns_selection_4_shop').change(function() {
  	
// 	var number_of_columns_selection_4_shop_new = jQuery("#number_of_columns_selection_4_shop").val();

//   	if(number_of_columns_selection_4_shop_new == 1){
// 		jQuery('.column_msg').text(col_1_val);
// 		jQuery('#number_products_on_load').val(4);
// 		jQuery('#number_products_on_load_more').val(2);
// 	}
// 	if(number_of_columns_selection_4_shop_new == 2){
// 		jQuery('.column_msg').text(col_2_val);
// 		jQuery('#number_products_on_load').val(8);
// 		jQuery('#number_products_on_load_more').val(4);
// 	} 
// 	if(number_of_columns_selection_4_shop_new == 3){
// 		jQuery('.column_msg').text(col_3_val);
// 		jQuery('#number_products_on_load').val(12);
// 		jQuery('#number_products_on_load_more').val(6);
// 	} 
// 	if(number_of_columns_selection_4_shop_new == 4){
// 		jQuery('.column_msg').text(col_4_val);
// 		jQuery('#number_products_on_load').val(16);
// 		jQuery('#number_products_on_load_more').val(8);
// 	} 
// 	if(number_of_columns_selection_4_shop_new == 5){
// 		jQuery('.column_msg').text(col_5_val);
// 		jQuery('#number_products_on_load').val(20);
// 		jQuery('#number_products_on_load_more').val(10);
// 	} 
// 	if(number_of_columns_selection_4_shop_new == 6){
// 		jQuery('.column_msg').text(col_6_val);
// 		jQuery('#number_products_on_load').val(24);
// 		jQuery('#number_products_on_load_more').val(12);
// 	} 
    
//   });
  
// });