jQuery(document).ready(function($) {
    'use strict';

    /**
     * Compare toggle button
     */
    var compare_toggle_class = ".inventor-compare-btn-toggle";
    $(document).on("click", compare_toggle_class, function(e) {
        var action = $(this).hasClass("marked") ? "inventor_compare_remove": "inventor_compare_add";
        var toggler = $(this);

        $.ajax({
            url: toggler.data('ajax-url'),
            data: {
                'action': action,
                'id': toggler.data('listing-id')
            }
        }).done(function( data ) {
            if (data.success) {
                toggler.toggleClass("marked");
                var span = toggler.children("span");
                var toggleText = span.data("toggle");
                span.data("toggle", span.text());
                span.text(toggleText);

                var compare_info = $('#compare-info');

                if(data.count == 0) {
                    compare_info.hide();
                } else {
                    compare_info.show();
                    compare_info.removeClass('hidden');
                }

                if(data.text) {
                    var compare_anchor = compare_info.find('a');
                    compare_anchor.text(data.text);
                }

                if(data.message) {
                    alert(data.message);
                }
            } else {
                alert(data.message);
            }
        });

        e.preventDefault();
    });

    /**
     * Remove from comparison
     */
    var remove_from_compare_class = ".inventor-compare-btn-remove";
    $(document).on("click", remove_from_compare_class, function(e) {
        var action = "inventor_compare_remove";
        var toggler = $(this);
        var listing_id = toggler.data('listing-id');

        $('[rel=compare-listing-'+ listing_id +']').hide();

        $.ajax({
            url: toggler.data('ajax-url'),
            data: {
                'action': action,
                'id': listing_id
            }
        }).done(function( data ) {
            if (data.success) {
            } else {
                alert(data.message);
            }
        });

        e.preventDefault();
    });
});
