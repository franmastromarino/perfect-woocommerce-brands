jQuery( function ( $ ) {

  var media_uploader = null;

  function open_media_uploader_image( event, imageSelectorButton ){

      var $imageSelectorScope = imageSelectorButton.parent();

      media_uploader = wp.media({
          frame:    "post",
          state:    "insert",
          multiple: false
      });

      media_uploader.on("insert", function(){
          var json = media_uploader.state().get("selection").first().toJSON();
          var image_id = json.id;
          var image_url = json.url;
          var image_html = '<img src="'+image_url+'" width="90" height="90">';

          var current_selector = '';
          switch (event.target.id) {
            case 'pwb_brand_image_select':
              current_selector = '.taxonomy-pwb-brand #pwb_brand_'+'image';
              break;
            case 'pwb_brand_banner_select':
              current_selector = '.taxonomy-pwb-brand #pwb_brand_'+'banner';
              break;
          }

          $(current_selector).val(image_id);
          $(current_selector+'_result').remove();

          if( $('.pwb_brand_image_selected',$imageSelectorScope).length ){
            $('.pwb_brand_image_selected span', $imageSelectorScope).html(image_html);
          }else{
            $imageSelectorScope.append('<div class="pwb_brand_image_selected"><span>'+image_html+'</span></div>');
          }
          add_delete_link( $imageSelectorScope );

      });

      media_uploader.open();
  }


  $('.taxonomy-pwb-brand #pwb_brand_image_select, .taxonomy-pwb-brand #pwb_brand_banner_select').on('click',function(event){
    open_media_uploader_image( event, $(this) );
  });

  //bind remove image event for edit page
  $('.taxonomy-pwb-brand #pwb_brand_image_select, .taxonomy-pwb-brand #pwb_brand_banner_select').each(function(){
    add_delete_link( $(this).parent() );
  });

  //clear custom fields when brand is added
  if( $('body').hasClass('edit-tags-php') && $('body').hasClass('taxonomy-pwb-brand') ){
    jQuery( document ).ajaxSuccess(function( event, xhr, settings ) {
      //Check ajax action of request that succeeded
      if( typeof settings != "undefined" && settings.data && ~settings.data.indexOf("action=add-tag") && ~settings.data.indexOf("taxonomy=pwb-brand") ) {
        $('#pwb_brand_image').val('');
        $('#pwb_brand_banner').val('');
        $('.pwb_brand_image_selected').remove();
      }
    });
  }

  function add_delete_link( $imageSelectorScope ){

    $( '.pwb_brand_image_selected span', $imageSelectorScope ).append('<a href="#" class="pwb_brand_image_selected_remove">X</a>');

    $( '.pwb_brand_image_selected_remove', $imageSelectorScope ).on( 'click', function( event ){

      event.preventDefault();
      $(this).closest('.pwb_brand_image_selected').remove();

      //remove the img
      $('#pwb_brand_image',$imageSelectorScope).val('');
      $('#pwb_brand_banner',$imageSelectorScope).val('');

    });

  }

  /* ····························· Edit brands page ····························· */
  $('.taxonomy-pwb-brand table .column-featured > span').not('pwb-blocked').on('click', function(e){
    e.preventDefault();
    var $currentStar = $(this);
    $currentStar.addClass('pwb-blocked');
    if( $currentStar.hasClass('dashicons-star-filled') ){
      $currentStar.removeClass('dashicons-star-filled');
      $currentStar.addClass('dashicons-star-empty');
    }else{
      $currentStar.removeClass('dashicons-star-empty');
      $currentStar.addClass('dashicons-star-filled');
    }
    var data = { 'action': 'pwb_admin_set_featured_brand', 'brand': $currentStar.data('brand-id') };
    $.post(pwb_ajax_object_admin.ajax_url, data, function( response ) {
      $currentStar.removeClass('pwb-blocked');
      if( response.success ){
        var $featuredCount = $('.taxonomy-pwb-brand .pwb-featured-count > span');
        if( response.data.direction == 'up' ){
          $featuredCount.html( parseInt( $featuredCount.text() ) + 1 );
        }else{
          $featuredCount.html( parseInt( $featuredCount.text() ) - 1 );
        }
      }else{
        alert( response.data.error_msg );
      }
    });
  });

  $('.taxonomy-pwb-brand #pwb-first-featured-brands').on('change', function(e){
    e.preventDefault();
    $('#screen-options-apply').replaceWith('<img src="'+pwb_ajax_object_admin.site_url+'/wp-admin/images/loading.gif">');
    var data = { 'action': 'pwb_admin_save_screen_settings', 'new_val': $(this).is(':checked') };
    $.post(pwb_ajax_object_admin.ajax_url, data, function(response) { location.reload(); });
  });

  $('.pwb-edit-brands-bottom > span').on('click', function(e){
    e.preventDefault();
    $('.taxonomy-pwb-brand #col-left').toggleClass('pwb-force-full-width');
    $('.taxonomy-pwb-brand #col-right').toggleClass('pwb-force-full-width');
  });
  /* ····························· /Edit brands page ····························· */

  /* ····························· Settings tab ····························· */

  if( $('.pwb-admin-selectwoo').length ){
    $('.pwb-admin-selectwoo').selectWoo();
  }

  // migrate brands
  $('#wc_pwb_admin_tab_tools_migrate').on( 'change', function(){

    if( $(this).val() != '-' ){

      if( confirm(pwb_ajax_object_admin.translations.migrate_notice) ){

        $('html').append('<div class="pwb-modal"><div class="pwb-modal-inner"></div></div>');
        $('.pwb-modal-inner').html('<p>'+pwb_ajax_object_admin.translations.migrating+'</p>');

        var data = {
      		'action': 'pwb_admin_migrate_brands',
      		'from': $(this).val()
      	};
      	$.post(pwb_ajax_object_admin.ajax_url, data, function(response) {

          setTimeout( function(){
            location.href = pwb_ajax_object_admin.brands_url;
          }, 1000 );

      	});

      }else{

      }

    }

    $(this).val('-');//reset to default value

  } );

  // dummy data
  $('#wc_pwb_admin_tab_tools_dummy_data').on( 'change', function(){

    if( $(this).val() != '-' ){

      if( confirm(pwb_ajax_object_admin.translations.dummy_data_notice) ){

        $('html').append('<div class="pwb-modal"><div class="pwb-modal-inner"></div></div>');
        $('.pwb-modal-inner').html('<p>'+pwb_ajax_object_admin.translations.dummy_data+'</p>');

        var data = {
      		'action': 'pwb_admin_dummy_data',
      		'from': $(this).val()
      	};
      	$.post(pwb_ajax_object_admin.ajax_url, data, function(response) {

          setTimeout( function(){
            location.href = pwb_ajax_object_admin.brands_url;
          }, 1000 );

      	});

      }else{

      }

    }

    $(this).val('-');//reset to default value

  } );

  var $systemStatusBtn = $('#wc_pwb_admin_tab_tools_system_status').siblings('p');
  $systemStatusBtn.addClass('button wc_pwb_admin_tab_status_btn');
  $('.wc_pwb_admin_tab_status_btn').on( 'click', function(e){
    e.preventDefault();
    if( !$('#wc_pwb_admin_status_result').length ){
      $systemStatusTextarea = $('#wc_pwb_admin_tab_tools_system_status');
      $('<pre id="wc_pwb_admin_status_result"></pre>').insertAfter($systemStatusTextarea);
      jQuery( '#wc_pwb_admin_status_result' ).click( function(e) {
        e.preventDefault();
        var refNode = $( this )[0];
        if ( $.browser.msie ) {
            var range = document.body.createTextRange();
            range.moveToElementText( refNode );
            range.select();
        } else if ( $.browser.mozilla || $.browser.opera ) {
            var selection = window.getSelection();
            var range = document.createRange();
            range.selectNodeContents( refNode );
            selection.removeAllRanges();
            selection.addRange( range );
        } else if ( $.browser.safari ) {
            var selection = window.getSelection();
            selection.setBaseAndExtent( refNode, 0, refNode, 1 );
        }
      } );
    }
    $('#wc_pwb_admin_status_result').html('<img src="'+pwb_ajax_object_admin.site_url+'/wp-admin/images/spinner.gif'+'" alt="Loading" height="20" width="20">');
    $('#wc_pwb_admin_status_result').show();
    var data = {
      'action': 'pwb_system_status'
    };
    jQuery.post(ajaxurl, data, function(response) {
      $('#wc_pwb_admin_status_result').html(response);
      $('#wc_pwb_admin_status_result').trigger('click');
    });

  } );

  /* ····························· /Settings tab ····························· */

  /* ····························· Admin notices ····························· */
  $( document ).on( 'click', '.pwb-notice-dismissible .notice-dismiss', function(e) {

    e.preventDefault();

    var noticeName = $( this ).closest( '.pwb-notice-dismissible' ).data( 'notice' );

    var data = {
  		'action': 'dismiss_pwb_notice',
  		'notice_name': noticeName
  	};
  	jQuery.post(ajaxurl, data, function(response) {
  		//callback
  	});

  } );
  /* ····························· /Admin notices ····························· */

  /* ····························· Widgets ····························· */
  pwbBindEventsToWigets();
  //Fires when a widget is added to a sidebar
  jQuery(document).bind('widget-added',function(e, widget){
    pwbBindEventsToWigets( widget );
  });
  //Fires on widget save
  jQuery(document).on('widget-updated', function(e, widget){
    pwbBindEventsToWigets( widget );
  });
  function pwbBindEventsToWigets( widget ){
    $currentWidget = $(".pwb-select-display-as");
    if( widget != undefined ){
      $currentWidget = $(".pwb-select-display-as", widget);
    }
    $currentWidget.on("change",function(){
      if( $(this).val()=="brand_logo" ){
        $(this).parent().siblings(".pwb-display-as-logo").addClass("show");
      }else{
        $(this).parent().siblings(".pwb-display-as-logo").removeClass("show");
      }
    });
  }
  /* ····························· /Widgets ····························· */

  /* ····························· Brands exporter ····························· */
  $('button.pwb-brands-export').on('click', function(e){
    e.preventDefault();

    var $clickedBtn = $(this);
    $clickedBtn.addClass('pwb-loading-overlay');
    $clickedBtn.prop("disabled",true);

    var data = { 'action': 'pwb_brands_export' };
    $.post(pwb_ajax_object_admin.ajax_url, data, function(response) {

      if( response.success ){
        $clickedBtn.removeClass('pwb-loading-overlay');
        $clickedBtn.prop("disabled",false);

        //descarga el archivo json de exportación
        $('#pwb-download-export-file').remove();
        var link      = document.createElement("a");
        link.download = 'brands.json';
        link.id       = 'pwb-download-export-file';
        link.href     = response.data.export_file_url;
        $('body').append( link );
        link.click();
      }

    });

  });

  $('button.pwb-brands-import').on('click', function(e){
    e.preventDefault();
    $('input.pwb-brands-import-file').trigger('click');
  });
  $('input.pwb-brands-import-file').on('change', function(e){
    e.preventDefault();

    var $clickedBtn = $('button.pwb-brands-import');
    $clickedBtn.addClass('pwb-loading-overlay');
    $clickedBtn.prop("disabled",true);

    var file = $(this)[0].files[0];

    var reqData = new FormData();
    reqData.append('action', 'pwb_brands_import');
    reqData.append('file', file);

    $.ajax({
      url: pwb_ajax_object_admin.ajax_url,
      type: 'post',
      cache: false,
      dataType: 'json',
      contentType: false,
      processData: false,
      data: reqData,
      success: function( resp ){
        if( resp.success ){
          $clickedBtn.removeClass('pwb-loading-overlay');
          location.reload();
        }else{
          alert('Importer error');
        }
      }
    });

  })
  /* ····························· /Brands exporter ····························· */

});
