jQuery.noConflict();

jQuery(document).ready(function( $ ) {

  var media_uploader = null;

  function open_media_uploader_image(event){
      media_uploader = wp.media({
          frame:    "post",
          state:    "insert",
          multiple: false
      });

      media_uploader.on("insert", function(){
          var json = media_uploader.state().get("selection").first().toJSON();
          var image_id = json.id;
          var image_url = json.url;

          var current_selector = '';
          switch (event.target.id) {
            case 'pwb_brand_image_select':
              current_selector = '.taxonomy-pwb-brand #pwb_brand_'+'image';
              break;
            case 'pwb_brand_banner_select':
              current_selector = '.taxonomy-pwb-brand #pwb_brand_'+'banner';
              break;
          }

          jQuery(current_selector).val(image_id);
          jQuery(current_selector+'_result').remove();
          jQuery(current_selector+'_select').after('<div>'+image_url+'</div>');
      });

      media_uploader.open();
  }


  $('.taxonomy-pwb-brand #pwb_brand_image_select, .taxonomy-pwb-brand #pwb_brand_banner_select').on('click',function(event){
    open_media_uploader_image(event);
  });


  /* ····························· Settings tab ····························· */

  $('#wc_pwb_admin_tab_tools_migrate').on( 'change', function(){

    if( $(this).val() != '-' ){

      if( confirm(ajax_object.translations.migrate_notice) ){

        $('html').append('<div class="pwb-modal"><div class="pwb-modal-inner"></div></div>');
        $('.pwb-modal-inner').html('<p>'+ajax_object.translations.migrating+'</p>');

        var data = {
      		'action': 'pwb_admin_migrate_brands',
      		'from': $(this).val()
      	};
      	jQuery.post(ajax_object.ajax_url, data, function(response) {

          setTimeout( function(){
            location.href = ajax_object.brands_url;
          }, 1000 );

      	});

      }else{

      }

    }

    $(this).val('-');//reset to default value

  } );

  /* ····························· /Settings tab ····························· */

});
