<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Enqueue all js files.
 */
function cd_ab_add_js() {
	// do not load on admin pages
	if ( is_admin() ) {
		return;
	}

	$cd_ab = bp_get_option( 'cd_ab' );

	if ( $cd_ab['access'] === 'admin' && ! is_super_admin() ) {
		return;
	}

	$min = '.min';

	if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
		$min = '';
	}

	wp_enqueue_script( 'bpab', WP_PLUGIN_URL . '/cd-bp-avatar-bubble/assets/js/bpab'.$min.'.js', array( 'jquery' ) );
}

add_action( 'wp_print_scripts', 'cd_ab_add_js' );

/**
 * Global js variables.
 */
function cd_ab_add_global_js_vars() {
	$cd_ab = bp_get_option( 'cd_ab' );
	?>
	<!--suppress JSUnusedLocalSymbols -->
	<script type="text/javascript">
		var bpab_ajax_image = "<?php echo CD_AB_IMAGE_URI; ?>";
		var bpab_ajax_delay = <?php echo (int) $cd_ab['delay'] ?>;
		var bpab_action = "<?php echo $cd_ab['action'] ?>";
	</script>
<?php }

add_action( 'wp_head', 'cd_ab_add_global_js_vars' );

function cd_ab_add_css() {
	// do not load on admin pages
	if ( is_admin() ) {
		return;
	}

	$cd_ab = bp_get_option( 'cd_ab' );

	if ( $cd_ab['access'] === 'admin' && ! is_super_admin() ) {
		return;
	}

	$url  = WP_PLUGIN_URL . '/cd-bp-avatar-bubble/assets/css/';
	$path = WP_PLUGIN_DIR . '/cd-bp-avatar-bubble/assets/css/';

	switch ( $cd_ab['color'] ) {
		case 'red':
			$bubbleUrl  = $url . $cd_ab['borders'] . '/bubble-red.css';
			$bubbleFile = $path . $cd_ab['borders'] . '/bubble-red.css';
			break;
		case 'black':
			$bubbleUrl  = $url . $cd_ab['borders'] . '/bubble-black.css';
			$bubbleFile = $path . $cd_ab['borders'] . '/bubble-black.css';
			break;
		case 'grey':
			$bubbleUrl  = $url . $cd_ab['borders'] . '/bubble-grey.css';
			$bubbleFile = $path . $cd_ab['borders'] . '/bubble-grey.css';
			break;
		case 'green':
			$bubbleUrl  = $url . $cd_ab['borders'] . '/bubble-green.css';
			$bubbleFile = $path . $cd_ab['borders'] . '/bubble-green.css';
			break;
		case 'blue':
		default:
			$bubbleUrl  = $url . $cd_ab['borders'] . '/bubble-blue.css';
			$bubbleFile = $path . $cd_ab['borders'] . '/bubble-blue.css';
			break;
	}

	if ( file_exists( $bubbleFile ) ) {
		wp_register_style( 'bubbleSheets', $bubbleUrl );
		wp_enqueue_style( 'bubbleSheets' );
	}
}

add_action( 'wp_print_styles', 'cd_ab_add_css' );
