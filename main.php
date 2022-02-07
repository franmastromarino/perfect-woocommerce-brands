<?php

if ( $active_plugins = get_option( 'active_plugins', array() ) ) {
	foreach ( $active_plugins as $key => $active_plugin ) {
		if ( $active_plugin == 'perfect-woocommerce-brands/main.php' ) {
			$active_plugins[ $key ] = str_replace( '/main.php', '/perfect-woocommerce-brands.php', $active_plugin );
		}
	}
	update_option( 'active_plugins', $active_plugins );
}
