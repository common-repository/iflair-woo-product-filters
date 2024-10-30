/* Global variables */ 
var ifwoopf_filter_products,
ifwoopf_load_more,
ifwoopf_before_ajax_send,
ifwoopf_display_response,
ifwoopf_get_filter_form_data,
ifwoopf_search,
search_minlength;

window.ifwoopf_filter_data = '';

var load_more = true;

var ajaxurl = ifwoopf_public_object.ajaxurl;
var loader = '<div class="ifwoopf-loader"></div>';
search_minlength = parseInt(ifwoopf_public_object.search_min_input);
var is_enabled_button = ifwoopf_public_object.is_enabled_button;
var ifwoopf_prefix = ifwoopf_public_object.prefix;
var enabled_filters_names = ifwoopf_public_object.enabled_filters_names;
var posts_per_page_load_more = ifwoopf_public_object.posts_per_page_load_more;
var store_filter_in_url = ifwoopf_public_object.store_filter_in_url;

jQuery(document).ready(function() {
   jQuery("#ifwoopf_submit").on("click", function(e){

      /* Main ajax call for filter products */
      ifwoopf_filter_products = function(serialize_data = null, is_return = false){

            var page = jQuery("#ifwoopf_page").val();
            var offset = jQuery("#ifwoopf_offset").val();
            var nonce_val = ifwoopf_public_object['nonce'];

            if ( !ifwoopf_is_empty(serialize_data) ) {
                  window.ifwoopf_filter_data = serialize_data;
                  serialize_data = serialize_data+'&page='+page;
            }
            
            serialize_data = serialize_data+'&action=ifwoopf_filter_products';

            if ( !ifwoopf_is_empty(offset) ) {
                  serialize_data = serialize_data+"&offset="+offset;
            }

            if(!ifwoopf_is_empty(nonce_val)){
                  serialize_data = serialize_data+"&nonce="+nonce_val;
            }

            var ajax_filter = jQuery.ajax({
                  type: "POST",
                  url: ifwoopf_public_object.ajaxurl,
                  nonce: ifwoopf_public_object.nonce,
                  data: serialize_data,
                  dataType: "json",
                  beforeSend: function(){
                        ifwoopf_before_ajax_send();
                        ifwoopf_update_filter_url();
                  },
                  success: function (response) {
                        jQuery('html, body').animate({ scrollTop: 0 }, 0);
                        ifwoopf_display_response(response);
                        ifwoopf_javascript_after_ajax();
                  },
                  error: function(){

                  }
            });

            if ( is_return ) {
                  return ajax_filter;
            }

      }

      /* Load more products with infinite scrool */
      ifwoopf_load_more = function(){

            var serialize = window.ifwoopf_filter_data;
            var max_page = parseInt(jQuery("#ifwoopf_max_page").val());
            var page = jQuery("#ifwoopf_page").val();
            var offset = jQuery("#ifwoopf_offset").val();
            var nonce_val = ifwoopf_public_object['nonce'];

            if ( page > max_page ) {
                  return false;
            }

            var additional_param = 'action=ifwoopf_filter_products&page='+page+'&ifwoopf_load_more=true'+"&nonce="+nonce_val;

            if( !ifwoopf_is_empty(serialize) ){
                  serialize = serialize+'&'+additional_param;
            }else{
                  serialize = additional_param;
            }

            if ( !ifwoopf_is_empty(offset) ) {
                  serialize = serialize+"&offset="+offset;
            }

            jQuery.ajax({
                  type: "POST",
                  url: ifwoopf_public_object.ajaxurl,
                  data: serialize,
                  dataType: "json",
                  beforeSend: function(){
                        load_more = false;
                        ifwoopf_before_ajax_send();
                  },
                  success: function (response) {
                        ifwoopf_display_response(response, true);
                        load_more = true;
                        ifwoopf_javascript_after_ajax();
                  },
                  complete: function(){
                        jQuery('body').removeClass('no-scroll');
                  },
                  error: function(){
                        
                  }
            });

      }

      /* Search product */
      ifwoopf_search = function(value){

            var serialize_data = ifwoopf_get_filter_form_data();
            ifwoopf_clear_filter_data();
            return ifwoopf_filter_products(serialize_data, true);

      }

      /* Before filter ajax call */
      ifwoopf_before_ajax_send = function(){

            if ( ifwoopf_is_empty(is_enabled_button) ){
                  jQuery(".ifwoopf-from").addClass("loading");
            }
            jQuery('.ifwoopf-main-wrap > ul').addClass("ifwoopf-loading");
            jQuery(".ifwoopf-main-wrap > ul").append(loader);
            jQuery('.ifwoopf-mobile-overlay').removeClass('ifwoopf-mobile-active');
            jQuery('.ifwoopf-form-wrap').removeClass('ifwoopf-mobile-active');
            jQuery(".woocommerce-pagination").remove();

      }

      /* Display response after filterd data */
      ifwoopf_display_response = function(response, is_load_more=false){

            if ( ifwoopf_is_empty(is_enabled_button) ){
                  jQuery(".ifwoopf-from").removeClass("loading");
            }
            jQuery('.ifwoopf-main-wrap > ul').removeClass("ifwoopf-loading");
            jQuery(".ifwoopf-main-wrap > ul .ifwoopf-loader").remove();

            if ( response.hasOwnProperty('products_html') && response.products_html !== '' ) {
                  if ( is_load_more ) {
                        jQuery(".ifwoopf-main-wrap > ul").append(response.products_html);  
                  }else{
                        jQuery(".ifwoopf-main-wrap > ul").html(response.products_html);
                  }
                  
            }

            var count_product = jQuery('li.product.status-publish').length;                   

            if(response.max_page == 0){
                  jQuery(".woocommerce-result-count-display").html('Showing '+count_product+' products out of '+count_product+' products'); 
            }  else {
                  jQuery(".woocommerce-result-count-display").html('Showing '+count_product+' products out of '+response.total_found_posts+' products');
            }

            if(response.max_page == 0 && count_product == 0){
                  jQuery(".woocommerce-result-count-display").html('');
            }

            if(response.max_page == 0){
                  jQuery(".msgerror").html('No more products found!');
                  jQuery('#loadmorebtn').hide();
            }
            if(response.max_page == 0 && count_product == 0){
                  jQuery(".msgerror").html('');
            } 
            if(response.max_page > 0){
                  jQuery(".msgerror").html('');
                  jQuery('#loadmorebtn').show();
            }
            if(response.total_found_posts == count_product){
                jQuery('#loadmorebtn').hide();
                jQuery(".msgerror").html('No more products found!');
            }
            if(response.max_page == 1){
                  jQuery(".msgerror").html('');
            }
            if(response.total_found_posts == 0){
                  jQuery(".msgerror").html('');
            }
            if ( response.hasOwnProperty('pagination_html') && response.pagination_html !== '' ) {
                  jQuery("nav.woocommerce-pagination").html(response.pagination_html);
            }

            if ( response.hasOwnProperty('max_page') ) {
                  jQuery("#ifwoopf_max_page").val(response.max_page);
            }

            if ( response.hasOwnProperty('result_count') ) {
                  jQuery("p.woocommerce-result-count").replaceWith(response.result_count);
            }

            if ( response.hasOwnProperty('page') ) {
                  jQuery("#ifwoopf_page").val(response.page);
            }

            if ( response.hasOwnProperty('offset') ) {
                  jQuery("#ifwoopf_offset").val(response.offset);
            }

            ifwoopf_update_pag_filter_url();
            
      }

      /* Clear hidden input */
      ifwoopf_clear_filter_data = function(){
            jQuery("#ifwoopf_max_page").val("");
            jQuery("#ifwoopf_page").val("1");
            jQuery("#ifwoopf_offset").val("");
      }

      /* Get filter form data in serialize format */
      ifwoopf_get_filter_form_data = function(is_serializeArray=false){
           var serialize;
            if( is_serializeArray ){
                 serialize = jQuery(".ifwoopf-from").serializeArray();
            }else{
                  serialize = jQuery(".ifwoopf-from").serialize();
            }
           return serialize;
      }

      /* Update url for filter */
      ifwoopf_update_filter_url = function() {
            
            if( store_filter_in_url === 'yes' ){

                  var data = ifwoopf_get_filter_form_data(true);
                  var url_data = ifwoopf_get_url_form_data(data);

                  ifwoopf_remove_param_current_url(url_data);

                  if( Object.keys(url_data).length > 0 ){

                        jQuery.each(url_data, function(param_name, param_value) {
                              
                              window.history.replaceState('', '', ifwoopf_upd_url_param(window.location.href, param_name, param_value));

                        });

                  }    

                  window.history.pushState("data","Title",window.location.href);

            }

      }

      /* Add pagination parameters to url */
      ifwoopf_update_pag_filter_url = function() {

            if( store_filter_in_url === 'yes' ){

                  var ifwoopf_max_page = jQuery("#ifwoopf_max_page").val();
                  var ifwoopf_page = jQuery("#ifwoopf_page").val();
                  var ifwoopf_offset = jQuery("#ifwoopf_offset").val();

                  window.history.replaceState('', '', ifwoopf_upd_url_param(window.location.href, 'ifwoopf_max_page', ifwoopf_max_page));
                  window.history.replaceState('', '', ifwoopf_upd_url_param(window.location.href, 'ifwoopf_page', ifwoopf_page));
                  window.history.replaceState('', '', ifwoopf_upd_url_param(window.location.href, 'ifwoopf_offset', ifwoopf_offset));
                  window.history.replaceState('', '', ifwoopf_upd_url_param(window.location.href, 'ifwoopf_page_limit', posts_per_page_load_more)); 

                  window.history.pushState("data","Title",window.location.href);
                  
            }

      }

      window.ifwoopf_filter_data = ifwoopf_get_filter_form_data();

      // Code for shop products ul class columns override
      var ifwoopf_shop_columns = jQuery('#ifwoopf_shop_columns').val();
      var classList = jQuery('.ifwoopf-main-wrap > ul').attr('class').split(/\s+/);
      var finalval_shop_columns = 'columns-'+ifwoopf_shop_columns;

      var columns_arr = ['columns-1', 'columns-2', 'columns-3', 'columns-4', 'columns-5', 'columns-6'];

      var classList = jQuery('.ifwoopf-main-wrap > ul').attr('class').split(/\s+/);
      for (var i = 0; i < classList.length; i++) {
            if (jQuery.inArray(classList[i], columns_arr) > -1)
            {
                  jQuery('.ifwoopf-main-wrap > ul').removeClass(classList[i]);
                  jQuery('.ifwoopf-main-wrap > ul').addClass(finalval_shop_columns);
            }
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

/* Update url parameters */
function ifwoopf_upd_url_param(url, param, paramVal){
      var TheAnchor = null;
      var newAdditionalURL = "";
      var tempArray = url.split("?");
      var baseURL = tempArray[0];
      var additionalURL = tempArray[1];
      var temp = "";

      if (additionalURL) 
      {
            var tmpAnchor = additionalURL.split("#");
            var TheParams = tmpAnchor[0];
            TheAnchor = tmpAnchor[1];
            if(TheAnchor)
                  additionalURL = TheParams;

            tempArray = additionalURL.split("&");

            for (var i=0; i<tempArray.length; i++)
            {
                  if(tempArray[i].split('=')[0] != param)
                  {
                        newAdditionalURL += temp + tempArray[i];
                        temp = "&";
                  }
            }        
      }
      else
      {
            var tmpAnchor = baseURL.split("#");
            var TheParams = tmpAnchor[0];
            TheAnchor  = tmpAnchor[1];

            if(TheParams)
                  baseURL = TheParams;
      }

      if(TheAnchor)
            paramVal += "#" + TheAnchor;

      var rows_txt = ( !ifwoopf_is_empty(paramVal) ) ? temp + "" + param + "=" + paramVal : '';
      return baseURL + "?" + newAdditionalURL + rows_txt;
}

/* Replace array to array key */
function ifwoopf_replace_array_to_key(string){

      var last_array = string.substr(string.lastIndexOf("["), string.length);
      last_array_key = last_array.replace(/[[\]]/g, '');

      if( ifwoopf_is_empty(last_array_key) ){
            new_string = string.replace(/\s/g,'').replace(/\]\[/g, '_').replace(/\[/g, '_').replace(/\]/g, '_').replace(/\_\_/g, '');
      }else{
            new_string = string.replace(/\s/g,'').replace(/\]\[/g, '_').replace(/\[/g, '_').replace(/\]/g, '').replace(/\_\_/g, '');
      }

      return new_string;
}

/* Add prefix to sting */
function ifwoopf_add_prefix_to_string(string){

      if( !string.startsWith(ifwoopf_prefix) ){
            string = ifwoopf_prefix + '_' + string;
      }

      return string;
}

/* Ge data from url */
function ifwoopf_get_url_form_data(data){

      var url_data = {};

      for (var i = 0; i < data.length; i++) {
            
            var param = data[i];  
            var param_name = param.name;
            var param_value = param.value; 
            var param_values = [];         

            if( param_name.lastIndexOf("[") === -1 ){

                  param_name = ifwoopf_add_prefix_to_string(param_name);

            }else{

                  for (var j = 0; j < data.length; j++) {
                        
                        if( data[j].name === param_name ){
                            param_values.push(data[j].value);  
                        }

                  }

                  if( param_values.length ){

                        param_name = ifwoopf_replace_array_to_key(param_name);
                        param_name = ifwoopf_add_prefix_to_string(param_name);
                        param_value = param_values.join(',');

                  }

            }

            if( !ifwoopf_is_empty(param_value) ){
                  url_data[param_name] = param_value;
            }

      }

      return url_data;

}

/* Remove parameters from url */
function ifwoopf_remove_param(param_name, URL) {
    var rtn = URL.split("?")[0],
        param,
        params_arr = [],
        queryString = (URL.indexOf("?") !== -1) ? URL.split("?")[1] : "";
    if (queryString !== "") {
        params_arr = queryString.split("&");
        for (var i = params_arr.length - 1; i >= 0; i -= 1) {
            param = params_arr[i].split("=")[0];
            if (param === param_name) {
                params_arr.splice(i, 1);
            }
        }
        if (params_arr.length) rtn = rtn + "?" + params_arr.join("&");
    }
    return rtn;
}

/* Remove parameters from url by enabled filters */
function ifwoopf_remove_param_current_url(url_data) {
      
      for (const filter in enabled_filters_names) {
           
            if( !url_data.hasOwnProperty(filter) ){
                  window.history.replaceState('', '', ifwoopf_remove_param(filter, window.location.href));
            }

      }

      window.history.pushState("data","Title",window.location.href);
}

/* Get parameters from url */
function ifwoopf_get_URL_param(param_name=''){

      var vars = [], hash, value = '';
      var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');

      for(var i = 0; i < hashes.length; i++) {

            hash = hashes[i].split('=');
            vars.push(hash[0]);
            vars[hash[0]] = hash[1];

      }

      if( vars.hasOwnProperty(param_name) ){
            value = vars[param_name];
      }

      return value;
}

jQuery(document).ready(function () {  
      jQuery("<span class='msgerror'></span>").insertAfter(".ifwoopf-main-wrap > ul"); 
      jQuery( "<div class='loadmorebtn_cls'><input type='button' value='Load more' id='loadmorebtn'/></div>" ).insertAfter( ".ifwoopf-main-wrap > ul" );
      jQuery( "p.woocommerce-result-count-display" ).insertBefore( jQuery( ".ifwoopf-main-wrap > ul") );
      jQuery('#loadmorebtn').hide();
      jQuery("#loadmorebtn").click(function(){
            if ( load_more ) {
                  ifwoopf_load_more_new();
            }
      });      
});