jQuery(document).ready(function($) {
    'use strict';

    /**
     * Filter sorting options
     */
    $('.filter-sorting-inner a').on('click', function(e) {
        e.preventDefault();

        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
            $('input', this).attr('disabled', 'disabled');
        } else {
            $(this).closest('ul').find('input').attr('disabled', 'disabled');
            $(this).closest('ul').find('a').removeClass('active');
            $(this).find('input').removeAttr('disabled');
            $(this).addClass('active');
        }

        $(this).closest('form').submit();
    });

    /**
     * Filter price range
     */
    $('.filter-form input[class*="price-"]').change(function() {
        var form = $(this).parents('form:first');

        var price_from = form.find('input[class*="price-from"]');
        var price_to = form.find('input[class*="price-to"]');
        var price = form.find('input[name="price"]');

        var price_from_value = price_from.val();
        var price_to_value = price_to.val();
        var price_value = price_to.val();

        if ( ! price_from_value && ! price_to_value ) {
            price_value = '';
        } else {
            price_value = price_from_value + '-' + price_to_value;
        }

        price.val(price_value);
    }).change();

    /**
     * Filter date range
     */
    $('.filter-form input[class*="date-"]').change(function() {
        var form = $(this).parents('form:first');

        var date_from = form.find('input[class*="date-from"]');
        var date_to = form.find('input[class*="date-to"]');
        var date = form.find('input[name="date"]');

        var date_from_value = date_from.val();
        var date_to_value = date_to.val();
        var date_value = date_to.val();

        if ( ! date_from_value && ! date_to_value ) {
            date_value = '';
        } else {
            date_value = date_from_value + '-' + date_to_value;
        }

        date.val(date_value);
    }).change();

    /**
     * Autosubmit Filter Input changed
     */
    $('form.auto-submit-filter :input', '.widget_filter').on('change', function(e) {
        var value = $(this).val();
        var selected = $(this).data('selected');

        if ( selected == null ) {
            selected = '';
        }

        if( value != '' && value != selected || value == '' && selected != '' ) {
            var form = $(this).closest('form');
            form.submit();
        }
    });

    /**
     * Background image
     */
    $('*[data-background-image]').each(function() {
        $(this).css({
            'background-image': 'url(' + $(this).data('background-image') + ')'
        });
    });

    /**
     * Ratings
     */
    var fontawesome = {
        starType: 'i',
        starOn: 'fa fa-star',
        starHalf: 'fa fa-star-half-o',
        starOff: 'fa fa-star-o'
    };

    if ($('.review-rating-total').length !== 0) {
        $('.review-rating-total').each(function () {
            var rating = $(this);
            var opts = {
                path: rating.data('path'),
                score: rating.data('score'),
                readOnly: true
            };

            if ($(this).data('fontawesome') !== undefined) {
                $.extend(opts, fontawesome);
            }

            rating.raty(opts);
        });
    }

    if ($('.review-rating').length !== 0) {
        $('.review-rating').each(function () {
            var rating = $(this);
            var opts = {
                path: rating.data('path'),
                score: rating.data('score'),
                readOnly: true
            };

            if ($(this).data('fontawesome') !== undefined) {
                $.extend(opts, fontawesome);
            }

            rating.raty(opts);
        });
    }

    if ($('.review-form-rating').length !== 0) {
        var opts = {
            path: $('.review-form-rating').data('path'),
            targetScore: '#rating'
        };

        if ($('.review-form-rating').data('fontawesome') !== undefined) {
            $.extend(opts, fontawesome);
        }

        $('.review-form-rating').raty(opts);
    }

    /**
     * Don't submit empty form values
     */
    // Change 'form' to class or ID of your specific form
    $('form:not([method="post"],.live)').submit(function() {
        $(this).find('input[type!="radio"],select:not(:has(option:selected[value!=""]))').filter(function(){ return !this.value; }).attr("name", '');
        return true;
    });

    /**
     * Payment form gateway button toggler
     */
    var payment_gateways = $(".payment-form input[id^='gateway-']");

    payment_gateways.change(function() {
        var form = $(this).parents('form:first');
        var active_gateway = form.find("input[id^='gateway-']:checked");
        var gateway_contents = $('.gateway-content');
        var action = $(this).data('action');
        var submit_title = $(this).data('submit-title');
        var submit = form.find('[name="process-payment"]');
        //var submit = form.find('button[type=submit]');
        var stripe_submit = form.find('.stripe-button-el');
        var gateway_header = $(this).closest('.gateway-header');

        if ($(this).is(':checked')) {
            gateway_contents.hide();
            var gateway_content = gateway_header.next('.gateway-content');
            gateway_content.show();

            if (submit_title) {
                submit.html( submit_title );
            }

            // set form action
            form.attr('action', action);
        }

        if (active_gateway.attr('id') == 'gateway-stripe-checkout') {
            stripe_submit.removeClass('hidden');
            submit.addClass('hidden');
        } else {
            stripe_submit.addClass('hidden');
            submit.removeClass('hidden');
        }
    }).change();

    /**
     * Automatically check single payment gateway
     */
    if(payment_gateways.length == 1) {
        $(payment_gateways[0]).attr('checked', 'checked');
        $(payment_gateways[0]).change();
    }

    /**
     * Stripe button visibility
     */
    $(".payment-form input[type=checkbox]#terms-and-conditions").change(function() {
        var form = $(this).parents('form:first');
        var stripe_submit = form.find('.stripe-button-el');

        if( $(this).is( ':checked' ) ) {
            stripe_submit.removeClass('hidden');
        } else {
            stripe_submit.addClass('hidden');
        }
    }).change();

    /**
     * Payment
     */
    $('.payment-process').on('click', function(e) {
        e.preventDefault();
        $('.payment-form').submit();
    });

    /**
     * Masonry
     */
    var layoutPattern = [[2,1],[1,2],[1,1],[1,1],[1,2],[2,1],[1,1],[1,1]];

    $( window ).on("load", function() {
        masonryInit('.widget_listings .type-masonry .listings-row', '.listing-masonry-container', 3, 767);
    });

    $( window ).on("resize", function() {
        masonryInit('.widget_listings .type-masonry .listings-row', '.listing-masonry-container', 3, 767);
    });

    function masonryInit( wrapper, item, columns, oneColumnResolution) {
        var itemHeight = 300;
        columns = columns || 2;
        oneColumnResolution = oneColumnResolution || 991;

        if ( $( window ).width() <= oneColumnResolution ) {
            columns = 1;
        }

        var containerWidth = $(wrapper).width();
        var itemWidth = ( containerWidth ) / columns;

        if (itemWidth != 0) {
            itemWidth = (Math.round(itemWidth * 10) - 2) / 10;
        }

        if ( $( window ).width() > oneColumnResolution ) {
            var marginBottom = parseInt($('.listing-masonry-container').css('margin-bottom'));

            var index = 0;
            $(item).each(function(){
                $(this).css('width', itemWidth * layoutPattern[index][0]);
                $(this).css('height', itemHeight * layoutPattern[index][1] + marginBottom * layoutPattern[index][1] - marginBottom);

                if (layoutPattern.length == ++index) {
                    index = 0;
                }
            });
        } else {
            $(item).each(function(){
                $(this).css('width', '100%');
                $(this).css('height', itemHeight);
            });
        }

        $(wrapper).masonry({
            itemSelector: item,
            columnWidth: itemWidth,
            gutter: 0
        });
    }

    /**
     * Street View init
     */
    $('.cmb-type-street-view').each(function(){
        $(this).streetView();
    });

    /**
     * Listing banner
     */
    var banner_input = $('.cmb2-id-listing-banner .cmb2-list input');

    banner_extra_field(null);
    banner_input.each(function(){
        if($(this).attr("checked") == "checked") {
            banner_extra_field($(this));
        }
    });

    banner_input.on('click', function(){
        banner_extra_field($(this));
    });

    function banner_extra_field(inputElement) {
        $('.banner-image, .banner-video, .banner-map, .banner-street-view, .banner-inside-view').addClass('inactive');

        if( ! inputElement ) {
            return;
        }

        // custom image
        if(inputElement.attr('value') == 'banner_image') {
            $('.banner-image').removeClass('inactive');
        }
        // video
        else if(inputElement.attr('value') == 'banner_video') {
            $('.banner-video').removeClass('inactive');
        }
        // google map
        else if(inputElement.attr('value') == 'banner_map') {
            $('.banner-map').removeClass('inactive');
        }
        // street view
        else if(inputElement.attr('value') == 'banner_street_view') {
            $('.banner-street-view').removeClass('inactive');
        }
        // inside view
        else if(inputElement.attr('value') == 'banner_inside_view') {
            $('.banner-inside-view').removeClass('inactive');
        }
    }

    /**
     * Listing Location Street View
     */
    street_view_enable('.cmb2-id-listing-street-view input', '.cmb2-id-listing-street-view-location');
    street_view_enable('.cmb2-id-listing-inside-view input', '.cmb2-id-listing-inside-view-location');

    function street_view_enable(checkbox, elemToDisable) {

        if ($(checkbox).attr("checked") != "checked") {
            $(elemToDisable).addClass('inactive');
        }
        $(checkbox).on('click', function(){
            $(elemToDisable).toggleClass('inactive');
        });
    }

    /**
     * Listing carousel
     */
    if ($('.listing-carousel').length != 0) {
        $('.listing-carousel').owlCarousel({
            loop: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 1,
                    dots: false
                },
                768: {
                    items: 3
                },
                991: {
                    items: 4
                }
            },
            margin: 0,
            nav: true,
            dots: true,
            navigation: true
        });
    }

    /**
     * Chained selects
     */
    $(".cmb-type-taxonomy-select-chain select.cmb2-taxonomy-select-chain-child").each(function() {
        var taxonomy = $(this).data('taxonomy');
        var name = $(this).attr('name').replace('[]', '');
        var url = $(this).data('ajax-url');
        var ajax_action = $(this).data('ajax-action');
        var parent = $(this).data('parent');
        var selected = $(this).data('selected');

        $(this).remoteChained({
            parents : "#" + parent,
            url : url,
            extra: {
                'action': ajax_action,
                'taxonomy': taxonomy,
                'value_param': name,
                'selected': selected
            },
            loading : "Loading..."
        });
    });

    /**
     * Images colorbox
     */
    $("a[href$='png'], a[href$='jpg']").not('.ignore-colorbox').colorbox({
        rel: $(this).attr('rel'),
        maxWidth: '90%',
        maxHeight: '90%'
    });

    /**
     * Maximum number of allowed categories per listing
     */
    var max_categories = null;
    var last_checked_checkbox = null;

    $('.submission-form input[type="checkbox"][name="listing_listing_category[]"]').change(function(){
        if($(this).is(':checked')) {
            if ( max_categories == null ) {
                // maximum allowed number wasn't checked using ajax yet

                last_checked_checkbox = $(this);

                $.ajax({
                    method: "POST",
                    url: "/wp-admin/admin-ajax.php", // TODO get ajax_url() instead
                    data: { action: "inventor_max_listing_categories" },
                    cache: false,
                    async: false
                    //context: document.body
                }).done(function(data) {
                    max_categories = data.max_categories;
                    toggle_listing_category(last_checked_checkbox, max_categories);
                });
            } else {
                toggle_listing_category($(this), max_categories);
            }
        }
    });

    function toggle_listing_category(checkbox, max_categories) {
        if( max_categories !== false ) {
            var num_checked = $('.submission-form input[type="checkbox"][name="listing_listing_category[]"]:checked').length;

            if(num_checked > max_categories) {
                $(checkbox).attr('checked', false);
                // TODO localization
                alert('Maximum number of allowed categories already selected');
            }
        }
    }

    // Geolocation filter field
    $('.form-group-geolocation').each(function(index){
        var geolocation = $(this).find( '[name="geolocation"]' );
        var latitude = $(this).find( '[name="latitude"]' );
        var longitude = $(this).find( '[name="longitude"]' );

        // prevent submitting form
        $( geolocation ).keypress( function( event ) {
            if ( 13 === event.keyCode ) {
                event.preventDefault();
            }
        });

        // Search
        // https://developers.google.com/places/supported_types#table3
        var autocomplete_options = {
            types: ["(regions)"]  // geocode, address, establishment, (regions), (cities) or empty
        };
        var autocomplete = new google.maps.places.Autocomplete( geolocation[0], autocomplete_options );

        google.maps.event.addListener( autocomplete, 'place_changed', function() {
            var place = autocomplete.getPlace();
            if ( ! place || ! place.geometry ) {
                return true;
            }

            latitude.val( place.geometry.location.lat() );
            longitude.val( place.geometry.location.lng() );
        });

        // If we have saved values, skip fetching coordinates
        if ( ( latitude.val().length == 0 || longitude.val().length == 0 ) && geolocation.val().length != 0 ) {
            var address = geolocation.val();

            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({ 'address': address }, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    var location = results[0].geometry.location;
                    latitude.val( location.lat() );
                    longitude.val( location.lng() );
                }
            });
        }
    });
});

