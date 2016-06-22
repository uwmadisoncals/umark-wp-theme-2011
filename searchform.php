<?php
/**
 * The template for displaying search forms in UW-Madison
 *
 * @package WordPress
 * @subpackage UW_Madison
 * @since UW-Madison 1.0
 */
?>
	<form role="search" method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label for="s" class="assistive-text"><?php _e( 'Search', 'uwmadison' ); ?></label>
		<input type="text" class="field" name="s" id="s" placeholder="<?php esc_attr_e( 'Search', 'uwmadison' ); ?>" />
		<input type="submit" class="submit" name="submit" id="searchsubmit" value="<?php esc_attr_e( 'Search', 'uwmadison' ); ?>" />
	</form>
