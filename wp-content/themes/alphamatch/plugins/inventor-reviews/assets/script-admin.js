jQuery(document).ready(function($) {
    'use strict';

    /**
     * Ratings
     */
    var fontawesome = {
        starType: 'i',
        starOn: 'fa fa-star',
        starHalf: 'fa fa-star-half-o',
        starOff: 'fa fa-star-o'
    };

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
});