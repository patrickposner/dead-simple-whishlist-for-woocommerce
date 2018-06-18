jQuery(document).ready(function ($) {

    $('.whishlist-add').on('click', function () {

        let data = {
            action: 'add_to_whishlist',
            product: $(this).data("product")
        };

        $.ajax({
            url: whishlist.ajax_url,
            type: 'post',
            data: data,
            success: function () {

                console.log('added to whishlist');

            }
        })
    });

    $('.whishlist-remove').on('click', function () {

        let data = {
            action: 'remove_from_whishlist',
            product: $(this).data("product")
        };

        $.ajax({
            url: whishlist.ajax_url,
            type: 'post',
            data: data,
            success: function () {

                console.log('removed from whishlist');

            }
        })
    });

});