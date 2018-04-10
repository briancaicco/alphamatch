jQuery(document).ready(function($) {
    'use strict';

    $('.one-click-run:not(.disabled)').on('click', function(e) {
        e.preventDefault();
        $(this).addClass('disabled');

        var steps = [];
        var index = 0;

        $('.one-click-step').each(function() {
            var action = $(this).data('action');
            if (action) {
                steps.push({
                    'index': index,
                    'action': action
                });
            }
            index++;
        });

        if (steps.length !== 0) {
            console.log(steps);
            processStep(steps);
        }
    });

    function processStep(steps) {
        var step = steps.shift();
        $('.one-click-step').eq(step.index).addClass('processing');

        $.ajax({
            url: step.action,
            success: function(data) {
                $('.one-click-step').eq(step.index).removeClass('processing').addClass('completed');

                if (steps.length !== 0) {
                    processStep(steps);
                }
            }
        });
    }
});