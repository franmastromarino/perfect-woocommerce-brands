jQuery( function ( $ ) {

    $('.pwb-dropdown-widget').on('change',function(){
        var href = $(this).find(":selected").val();
        location.href = href;
    });

    if( typeof $.fn.slick === 'function' ){

      $('.pwb-carousel').slick({
          slide: '.pwb-slick-slide',
          infinite: true,
          draggable: false,
          prevArrow: '<div class="slick-prev"><span>'+pwb_ajax_object.carousel_prev+'</span></div>',
          nextArrow: '<div class="slick-next"><span>'+pwb_ajax_object.carousel_next+'</span></div>',
          speed: 300,
          lazyLoad: 'progressive',
          responsive: [
              {
                  breakpoint: 1024,
                  settings: {
                      slidesToShow: 4,
                      draggable: true,
                      arrows: false
                  }
              },
              {
                  breakpoint: 600,
                  settings: {
                      slidesToShow: 3,
                      draggable: true,
                      arrows: false
                  }
              },
              {
                  breakpoint: 480,
                  settings: {
                      slidesToShow: 2,
                      draggable: true,
                      arrows: false
                  }
              }
          ]
      });

      $('.pwb-product-carousel').slick({
          slide: '.pwb-slick-slide',
          infinite: true,
          draggable: false,
          prevArrow: '<div class="slick-prev"><span>'+pwb_ajax_object.carousel_prev+'</span></div>',
          nextArrow: '<div class="slick-next"><span>'+pwb_ajax_object.carousel_next+'</span></div>',
          speed: 300,
          lazyLoad: 'progressive',
          responsive: [
              {
                  breakpoint: 1024,
                  settings: {
                      slidesToShow: 3,
                      draggable: true,
                      arrows: false
                  }
              },
              {
                  breakpoint: 600,
                  settings: {
                      slidesToShow: 2,
                      draggable: true,
                      arrows: false
                  }
              },
              {
                  breakpoint: 480,
                  settings: {
                      slidesToShow: 1,
                      draggable: true,
                      arrows: false
                  }
              }
          ]
      });

    }

    /* ··························· Filter by brand widget ··························· */

    var PWBFilterByBrand = function(){

      var baseUrl    = [location.protocol, '//', location.host, location.pathname].join('');
      var currentUrl = window.location.href;

      var marcas = [];
      $('.pwb-filter-products input[type="checkbox"]').each(function(index){
        if( $(this).prop('checked') ) marcas.push( $(this).val() );
      });
      marcas = marcas.join();

      if( marcas ){

        //removes previous "pwb-brand" from url
        currentUrl = currentUrl.replace(/&?pwb-brand-filter=([^&]$|[^&]*)/i, "");

        //removes pagination
        currentUrl = currentUrl.replace(/\/page\/\d*\//i, "");

        if( currentUrl.indexOf("?") === -1 ){
          currentUrl = currentUrl + '?pwb-brand-filter='+marcas;
        }else{
          currentUrl = currentUrl + '&pwb-brand-filter='+marcas;
        }

      }else{
        currentUrl = baseUrl;
      }

      location.href = currentUrl;

    }

    $('.pwb-filter-products button').on( 'click', function(){ PWBFilterByBrand(); } );
    $('.pwb-filter-products.pwb-hide-submit-btn input').on( 'change', function(){ PWBFilterByBrand(); } );

    var brands = PWBgetUrlParameter('pwb-brand-filter');

  	if(brands!=null){
  		var brands_array = brands.split(',');
  		$('.pwb-filter-products input[type="checkbox"]').prop('checked',false);
  		for ( var i = 0, l = brands_array.length; i < l; i++ ) {
  			$('.pwb-filter-products input[type="checkbox"]').each(function(index){
  				if($(this).val()){
  					if(brands_array[i]==$(this).val()){
  						$(this).prop('checked',true);
  					}
  				}
  			});
  		}
  	}else{
      $('.pwb-filter-products input[type="checkbox"]').prop('checked', false);
    }

    /* ··························· /Filter by brand widget ··························· */

});

var PWBgetUrlParameter = function PWBgetUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};
