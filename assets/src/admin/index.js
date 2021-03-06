/**
 * Controls
 * Coupons Overview: Reset Stats
 * Settings: Delete Log
 * Settings: Move wp admin notices above the Settings Nav
 * Settings: Handle "review request" admin notice buttons click
 */
jQuery(document).ready(function ($) {

    /**
     * Controls
     */
    $(".affcoups-input-colorpicker").wpColorPicker();

    /**
     * Coupons Overview: Reset Stats
     */
    $('[data-affcoups-settings-nav]').on( "click", function( event ) {

        // Don't follow link
        event.preventDefault();

        var navItems = $('.affcoups-settings-nav-item');
        var navItem = $(this).parent('li');

        var tab = $(this).data('affcoups-settings-nav');

        if ( ! tab )
            return;

        var content = $('[data-affcoups-settings-content="' + tab + '"]');

        if ( ! content )
            return;

        var contentItems = $('.affcoups-settings-content');

        if ( ! contentItems )
            return;

        // Remove active class from all nav items and containers
        $('[data-affcoups-settings-nav]').removeClass('active');
        navItems.removeClass('active');
        contentItems.removeClass('active');

        // Add active class to nav item and target container
        $(this).addClass('active');
        navItem.addClass('active');
        content.addClass('active');

        var inputActiveTab = $('#affcoups_settings_active_tab');

        if ( inputActiveTab )
            inputActiveTab.val( tab );

        // Update url
        if (typeof (history.pushState) !== "undefined" ) {

            // Get current URL
            var currentURL = window.location.href;

            // Prepare new URL
            var newURL = new URL( currentURL );

            // Set or replace URL parameter
            newURL.searchParams.set( 'tab', tab );

            // Change URL without reloading
            history.pushState({}, null, newURL);
        }
    });

    /**
     * Settings: Delete Log
     */
    $( document ).on( 'click', '#affcoups-delete-log-submit', function(event) {
        $('#affcoups-delete-log').val('1');
    });

    /**
     * Settings: Move wp admin notices above the Settings Nav
     */
    setTimeout( function() {
        $('#wpbody-content > .affcoups-settings .affcoups-settings-content > .card > .error, .notice').insertBefore( $( '#affcoups-admin-page' ) );
    }, 10 );

    /**
     * Settings: Handle "review request" admin notice buttons click
     */
    $('.affcoups-notice-btn').on( 'click', function(event) {

        var
            nonce,
            action = $(this).data('action'),
            $this = $(this);

        if ( ! action )
            return;

        if ( 'affcoups_remove_review_request' === action ) {
            nonce = affcoups_admin_post.nonce.remove_review_request;

        } else if ( 'affcoups_hide_review_request' === action ) {
            nonce = affcoups_admin_post.nonce.hide_review_request;
        }

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: affcoups_admin_post.ajax_url,
            data: {
                action: action,
                _wpnonce: nonce
            },
        })
        .done(function(response) {

            if (response.error) {
                console.log( 'response.error:' );
                console.log( response.error );
                return false;
            }

            $this.closest('.affcoups-notice').fadeOut('slow');
        })
        .fail(function(jqXHR, textStatus) {
            console.log( "Request failed: " + textStatus );
        });
    });
});