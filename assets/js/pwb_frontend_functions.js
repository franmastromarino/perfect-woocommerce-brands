jQuery.noConflict();

jQuery(document).ready(function( $ ) {
    $('.pwb-dropdown-widget').on('change',function(){
        var href = $(this).find(":selected").val();
        location.href = href;
    });

    $('.pwb-carousel').slick({
        infinite: true,
        draggable: false,
        prevArrow: '<div class="slick-prev"><span title="Prev">&lt;</span></div>',
        nextArrow: '<div class="slick-next"><span title="Next">&gt;</span></div>',
        speed: 300,
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
        infinite: true,
        draggable: false,
        prevArrow: '<div class="slick-prev"><span title="Prev">&lt;</span></div>',
        nextArrow: '<div class="slick-next"><span title="Next">&gt;</span></div>',
        speed: 300,
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
});
