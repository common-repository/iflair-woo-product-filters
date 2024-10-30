var ifwoopf_filter_products_new,
ifwoopf_load_more_new,
ifwoopf_search_new,
ifwoopf_before_ajax_send_new,
ifwoopf_display_response_new,
ifwoopf_clear_filter_data_new,
ifwoopf_get_filter_form_data_new,
ifwoopf_update_filter_url_new,
ifwoopf_update_pag_filter_url_new,
ifwoopf_javascript_after_ajax;

function ifwoopf_load_more_new() {};
function ifwoopf_clear_filter_data_new() {};
function ifwoopf_get_filter_form_data_new() {};
function ifwoopf_filter_products_new() {};

jQuery(document).ready(function() {
  
  jQuery(".ifwoopf-type-sorting select, #text_ifwoopf_search, .ifwoopf-from :input").on('keyup change', function (){

    /* Main ajax call for filter products */
    ifwoopf_filter_products_new = function(serialize_data = null, is_return = false){

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
                    ifwoopf_before_ajax_send_new();
                    ifwoopf_update_filter_url_new();
              },
              success: function (response) {
                    jQuery('html, body').animate({ scrollTop: 0 }, 0);
                    ifwoopf_display_response_new(response);
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
      ifwoopf_load_more_new = function(){

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
                    ifwoopf_before_ajax_send_new();
              },
              success: function (response) {
                    ifwoopf_display_response_new(response, true);
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
      ifwoopf_search_new = function(value){

        var serialize_data = ifwoopf_get_filter_form_data_new();
        ifwoopf_clear_filter_data_new();
        return ifwoopf_filter_products_new(serialize_data, true);

      }

       /* Before filter ajax call */
      ifwoopf_before_ajax_send_new = function(){

        if ( ifwoopf_is_empty(is_enabled_button) ){
              jQuery(".ifwoopf-from").addClass("loading");
        }
        jQuery('.ifwoopf-main-wrap > ul').addClass("ifwoopf-loading");
        jQuery(".ifwoopf-main-wrap > ul").append(loader);
        jQuery('.ifwoopf-mobile-overlay').removeClass('ifwoopf-mobile-active');
        jQuery('.ifwoopf-form-wrap').removeClass('ifwoopf-mobile-active');
        jQuery('body').removeClass('ifwoopf-mobile-active');
        jQuery(".woocommerce-pagination").remove();

      }

      /* Display response after filterd data */
      ifwoopf_display_response_new = function(response, is_load_more=false){

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

        ifwoopf_update_pag_filter_url_new();
            
      }

      /* Clear hidden input */
      ifwoopf_clear_filter_data_new = function(){
        jQuery("#ifwoopf_max_page").val("");
        jQuery("#ifwoopf_page").val("1");
        jQuery("#ifwoopf_offset").val("");
      }

      /* Get filter form data in serialize format */
      ifwoopf_get_filter_form_data_new = function(is_serializeArray=false){
        var serialize;
        if( is_serializeArray ){
             serialize = jQuery(".ifwoopf-from").serializeArray();
        }else{
              serialize = jQuery(".ifwoopf-from").serialize();
        }
        return serialize;
      }

      /* Update url for filter */
      ifwoopf_update_filter_url_new = function() {
            
        if( store_filter_in_url === 'yes' ){

              var data = ifwoopf_get_filter_form_data_new(true);
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
      ifwoopf_update_pag_filter_url_new = function() {

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
  });
});


jQuery(function(jQuery){

  var is_enabled_button = ifwoopf_public_object.is_enabled_button;
  
  function ifwoopf_filter(e_data = null){ 

    ifwoopf_clear_filter_data_new();
    var serialize = ifwoopf_get_filter_form_data_new();
    if ( !ifwoopf_is_empty(e_data) ) {
      serialize = serialize+e_data;
    }
    ifwoopf_filter_products_new(serialize);

  }

  if ( is_enabled_button !== 'yes' ){
    jQuery(".ifwoopf-from :input").change(function(){
      ifwoopf_filter();
    });
  }

  jQuery(".ifwoopf-from").submit(function(e){
    e.preventDefault();
    ifwoopf_filter();
  });

  jQuery('.ifwoopf-type-sorting select').change(function(e){
    e.preventDefault();
    ifwoopf_filter();
  });

  jQuery("#text_ifwoopf_search").keyup(function(){
    var that = this,
    value = jQuery(this).val();
    var searchRequest = null;

    if (value.length >= search_minlength ) { 
      if (searchRequest != null){ 
        searchRequest.abort();
      }
      searchRequest = ifwoopf_search_new(value);
    }else if ( value.length < 1 ){ 
      ifwoopf_filter();
    }
  });

  jQuery('.ifwoopf-toggle-enabeled.taxonomy-page .ifwoopf-current-toggle .ifwoopf-term-children').css('display','block');

  jQuery('.ifwoopf-toggle-btn').click(function(e) {
    e.preventDefault();
    var btn = jQuery(this);
    var wraper = jQuery(this).closest('.ifwoopf-type');
    var parent = jQuery(this).closest('.ifwoopf-term-item');
    var last_parent = jQuery(this).parents('.ifwoopf-term-parent').last();
    var length = jQuery('.ifwoopf-term-children').length;
    wraper.find('.ifwoopf-term-parent').removeClass('ifwoopf-current-toggle');
    last_parent.addClass('ifwoopf-current-toggle');

    wraper.find('.ifwoopf-toggle-btn:not(.ifwoopf-current-toggle .ifwoopf-toggle-btn)').removeClass('ifwoopf-toggled');
    wraper.find('.ifwoopf-term-children:not(.ifwoopf-current-toggle .ifwoopf-term-children)').slideUp("fast");
    wraper.find('.ifwoopf-term-item:not(.ifwoopf-current-toggle, .ifwoopf-current-toggle .ifwoopf-term-item)').removeClass('ifwoopf-toggled');

    if( !btn.hasClass('ifwoopf-toggled') ){
      btn.addClass('ifwoopf-toggled');
      parent.addClass('ifwoopf-toggled');
      parent.find('.ifwoopf-term-children:first').slideDown("fast");
    }else{
      last_parent.removeClass('ifwoopf-current-toggle');
      btn.removeClass('ifwoopf-toggled');
      parent.removeClass('ifwoopf-toggled');
      parent.find('.ifwoopf-toggle-btn').removeClass('ifwoopf-toggled');
      parent.find('.ifwoopf-term-children').removeClass('ifwoopf-toggled').slideUp("fast");

    }
  });

  jQuery('.ifwoopf-mobile-btn').click(function(){
    jQuery('.ifwoopf-mobile-overlay').addClass('ifwoopf-mobile-active');
    jQuery('.ifwoopf-form-wrap').addClass('ifwoopf-mobile-active');
    jQuery('body').addClass('ifwoopf-mobile-active');
  });

  jQuery('.ifwoopf-mobile-close').click(function(e){
    e.preventDefault();
    jQuery('.ifwoopf-mobile-overlay').removeClass('ifwoopf-mobile-active');
    jQuery('.ifwoopf-form-wrap').removeClass('ifwoopf-mobile-active');
    jQuery('body').removeClass('ifwoopf-mobile-active');
  });

  jQuery('.ifwoopf-toggle-enabeled .ifwoopf-type-heading').click(function(e) {

    e.preventDefault();
    var field = jQuery(this).closest('.ifwoopf-type');
    var btn = field.find('.ifwoopf-toggle-field-btn');

    if( field.hasClass('ifwoopf-type-toggled') ){
      field.find('.ifwoopf-field-wrap').slideUp('fast');
    }else{
      field.find('.ifwoopf-field-wrap').slideDown('fast');
    }
    field.toggleClass('ifwoopf-type-toggled');
    btn.toggleClass('ifwoopf-type-btn-toggled');

  });

  jQuery("#ifwoopf_reset").click(function(){

    jQuery(".ifwoopf-from")[0].reset();
    ifwoopf_filter();
    jQuery(".msgerror").html('');

  });

  var get_offset = ifwoopf_get_URL_param('ifwoopf_offset');

  if( !ifwoopf_is_empty(get_offset) ){
    get_offset = parseInt(get_offset)-1;
     var pT = jQuery('.ifwoopf-main-wrap > ul').offset().top,
     pH = jQuery('.ifwoopf-main-wrap > ul').outerHeight() + 150,
     wH = jQuery(window).height();
    var hh = (75/100) * (pT+pH-wH);
    jQuery([document.documentElement, document.body]).animate({
      scrollTop: hh
    }, 2000);

  }

  jQuery('.ifwoopf-toggle-enabeled.ifwoopf-type-toggled .ifwoopf-field-wrap').css('display','inherit');

});