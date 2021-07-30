
jQuery(".slides").slick({
    swipe: false,
    infinite: false,
    dots: false,
    prevArrow: false,
    nextArrow: false
}).on('beforeChange', function (event, slick, prev, index) {
    if (index === 0) {
        jQuery(".buttons .btn-prev").attr('disabled', true);
    } else {
        jQuery(".buttons .btn-prev").attr('disabled', false);
    }

    if (slick.$slides.length === index + slick.options.slidesToScroll) {
        jQuery(".buttons .btn-next").hide();
        jQuery(".buttons .btn-submit").removeClass('d-none').show();
    } else {
        jQuery(".buttons .btn-next").show();
        jQuery(".buttons .btn-submit").hide();
    }

    jQuery('.btn-navbox[data-index]').removeClass('active');
    jQuery('.btn-navbox[data-index="' + index + '"]').addClass('active');

    jQuery('.slide-passage[data-index="' + index + '"]').mCustomScrollbar({
        axis: 'y',
        theme: 'minimal-dark'
    });

    jQuery('.slide-question[data-index="' + index + '"]').mCustomScrollbar({
        axis: 'y',
        theme: 'minimal-dark'
    });

}).on('afterChange', function (event, slick, index) {

});

jQuery(".buttons .btn-prev").on('click', function () {
    jQuery('.slides').slick('slickPrev');
});

jQuery(".buttons .btn-next").on('click', function () {
    jQuery('.slides').slick('slickNext');
});

jQuery(".btn-navbox").on('click', function () {
    let i = jQuery(this).data('index');
    jQuery('.slides').slick('slickGoTo', i);
});

jQuery('#navbox-area').mCustomScrollbar({
    axis: 'y',
    theme: 'minimal-dark'
});