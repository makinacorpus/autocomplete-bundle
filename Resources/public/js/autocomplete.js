/**
 * Created by nero on 18.05.17.
 */

$(function () {
    $('.mc-autocomplete').each(function () {
        var $this = $(this);
        $this.select2({
            ajax: {
                url: $this.data('url'),
                allowClear: true,
                dataType: 'json',
                delay: 500,
                data: function (params) {
                    return {
                        query: params.term, // search term
                        page: params.page,
                        limit: 30
                    };
                },
                processResults: function (data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    params.page = params.page || 1;
                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 30) < data.total
                        }
                    };
                },
                cache: true
            },
            // We already did that in the PHP controller
            escapeMarkup: function (markup) { return markup; },
            minimumInputLength: 1,
            // No need for this, rendered in PHP side
            // templateResult: function () {},
            templateSelection: function (data) { return data.title || data.text;}
        });
    });
});