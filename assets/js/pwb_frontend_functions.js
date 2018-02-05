jQuery.noConflict();

jQuery(document).ready(function( $ ) {
    $('.pwb-dropdown-widget').on('change',function(){
        var href = $(this).find(":selected").val();
        location.href = href;
    });

    $('.pwb-carousel').slick({
        slide: '.pwb-slick-slide',
        infinite: true,
        draggable: false,
        prevArrow: '<div class="slick-prev"><span title="Prev">&lt;</span></div>',
        nextArrow: '<div class="slick-next"><span title="Next">&gt;</span></div>',
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
        prevArrow: '<div class="slick-prev"><span title="Prev">&lt;</span></div>',
        nextArrow: '<div class="slick-next"><span title="Next">&gt;</span></div>',
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


    /* ··························· Filter by brand widget ··························· */

    var brands = PWBgetUrlParameter('pwb-brand-filter');

    $('.pwb-filter-products button').on( 'click', function(){

      var currentUrl = window.location.href;

      var marcas = '';
      $('.pwb-filter-products input[type="checkbox"]').each(function(index){
        var checked = $(this).prop('checked');
        if(checked){
          marcas+=$(this).val();
          if($('.pwb-filter-products input[type="checkbox"]').length-1 != index){
            marcas+=',';
          }
        }
      });

      //removes previous "pwb-brand" from url
      currentUrl = currentUrl.replace(/&?pwb-brand-filter=([^&]$|[^&]*)/i, "");

      //removes pagination
      currentUrl = currentUrl.replace(/\/page\/\d*\//i, "");

      if( currentUrl.indexOf("?") === -1 ){
        currentUrl = currentUrl + '?pwb-brand-filter='+marcas;
      }else{
        currentUrl = currentUrl + '&pwb-brand-filter='+marcas;
      }
      location.href = currentUrl;

    });

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
