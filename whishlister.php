<?php
/**
 * Plugin Name: Whishlister
 * Plugin URI: https://patrickposner.de
 * Description: Easy huh?
 * Version: 1.0
 * Author: Patrick Posner
 */


define( "WHISHLIST_URL", untrailingslashit( plugin_dir_url( __FILE__ ) ) );


/* adding ajax assets and localize */

add_action( 'wp_enqueue_scripts', 'wl_add_whishlist_scripts' );

function wl_add_whishlist_scripts() {

	wp_enqueue_script( 'wl-whishlist-js', WHISHLIST_URL . '/js/whishlist.js', array(), '1.0.0', true );

	wp_localize_script( 'wl-whishlist-js', 'whishlist', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
	) );

}

/* hook whishlist icon to single product and product archives */

add_action( 'woocommerce_after_add_to_cart_button', 'atomtion_add_whishlist_icon', 15 );
//add_action( 'woocommerce_after_shop_loop_item_title', 'atomtion_add_whishlist_icon' );


function atomtion_add_whishlist_icon() {
	echo '<a href="#" class="button whishlist-add" data-product="' . get_the_id() . '">' . __( 'Add to Whishlist', 'wl' ) . '</a>';
}

/* CRUD for whishlist items with ajax */

add_action( 'wp_ajax_add_to_whishlist', 'wl_add_to_whishlist' );
add_action( 'wp_ajax_nopriv_add_to_whishlist', 'wl_add_to_whishlist' );

function wl_add_to_whishlist() {

	$whishlist = get_user_meta( get_current_user_id(), '_whishlist', true );

	if ( ! is_array( $whishlist ) ) {
		$whishlist = array();
	}

	if ( ! in_array( $_POST['product'], $whishlist ) ) {

		array_push( $whishlist, $_POST['product'] );
		update_user_meta( get_current_user_id(), '_whishlist', $whishlist );

	}
}

add_action( 'wp_ajax_remove_from_whishlist', 'wl_delete_from_whishlist' );
add_action( 'wp_ajax_nopriv_remove_from_whishlist', 'wl_delete_from_whishlist' );

function wl_delete_from_whishlist() {

	$whishlist = get_user_meta( get_current_user_id(), '_whishlist', true );


	if ( ( $key = array_search( $_POST['product'], $whishlist ) ) !== false ) {
		unset( $whishlist[ $key ] );

		update_user_meta( get_current_user_id(), '_whishlist', $whishlist );
	}


}


/* woocommerce account page */


add_filter( 'woocommerce_account_menu_items', 'wl_whishlist_menu_item', 10, 1 );

function wl_whishlist_menu_item( $items ) {

	$items['whishlist'] = __( 'Wishlist', 'wl' );

	return $items;

}

add_action( 'init', 'wl_add_whishlist_endpoint' );

function wl_add_whishlist_endpoint() {

	add_rewrite_endpoint( 'whishlist', EP_PAGES );

}

add_action( 'woocommerce_account_whishlist_endpoint', 'wl_whishlist_endpoint_content' );


function wl_whishlist_endpoint_content() {

	$whishlist_items = get_user_meta( get_current_user_id(), '_whishlist', true );

	if ( $whishlist_items ) : ?>

		<?php do_action( 'woocommerce_before_available_downloads' ); ?>
		<?php foreach ( $whishlist_items as $item_id ) : ?>
            <div class="wl-account-orders-left">
                <div class="wl-account-order">
                    <table>
                        <tbody>
                        <tr class="downloads">
                            <td>
                                <span class="wl-product-image">
                                    <?php echo get_the_post_thumbnail( $item_id ); ?>
                                </span>
                            </td>
                            <td><b><?php echo get_the_title( $item_id ) . '</b>'; ?></td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="wl-order-meta">
                        <div class="wl-order-meta-left">
                        </div>
                        <div class="wl-order-meta-right">
                        </div>
                    </div>
                </div>
            </div>
            <div class="wl-account-orders-right">

                <p>
					<?php echo '<a class="wl-order-link whishlist-remove" data-product="' . $item_id . '" href="' . wc_get_cart_url() . '?add-to-cart=' . $item_id . '">' . __( 'Add to Cart', 'wl' ) . '</a>'; ?>
                </p>

                <p>
					<?php echo '<a class="wl-order-link whishlist-remove" data-product="' . $item_id . '" href="#">' . __( 'Remove', 'wl' ) . '</a>'; ?>
                </p>

            </div>

            <hr>
		<?php endforeach; ?>

	<?php else : ?>
        <div class="woocommerce-Message woocommerce-Message--info woocommerce-info">
            <a class="woocommerce-Button button"
               href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
				<?php esc_html_e( 'Go shop', 'woocommerce' ) ?>
            </a>
			<?php esc_html_e( 'No items on your whishlist.', 'wl' ); ?>
        </div>
	<?php endif; ?>

	<?php

}
