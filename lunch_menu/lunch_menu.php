<?php
/*
Plugin Name: D70 Lunch Menu
Plugin URI: http://www.d70.is/
Description: Displays lunch menu in a widgetized sidebar.
Author: D70 ehf.
Version: 1.1
Author URI: http://www.d70.is/
*/

/**
 * Retrives date and time and stores it in an array.
 *
 * @since 1.0
 **/
$lm_day = date( 'd');
$lm_month = date( 'm' );
$lm_year = date( 'Y' );
$lm_weekday = date( 'w', mktime( 0, 0, 0, $lm_month, $lm_day, $lm_year ) );
$lm_sunday = $lm_day - $lm_weekday;

class D70_Lunch_Menu_Widget extends WP_Widget {	
	/**
	 * Constructor
	 *
	 * @return void
	 **/
	function D70_Lunch_Menu_Widget() {
		$widget_args = array( 'classname' => 'd70_lunch_menu_widget', 'description' => __( 'Shows lunch menu.' ) );
		$this->WP_Widget( 'd70_lunch_menu_widget', __( 'Lunch menu', 'lm' ), $widget_args );

		// Adds options to the options table.
		add_option( 'd70_lm_array', '', null, 'no' );
		add_option( 'd70_lunch_menu_styling', '', null, 'no' );

		// Retrives date and time options and sets them.
		/*$this->lm_day = date( 'd' ); 
		$this->lm_month = date( 'm' ); 
		$this->lm_year = date( 'Y' ); 
		$this->lm_weekday = date( 'w', mktime( 0, 0, 0, $this->lm_month, $this->lm_day, $this->lm_year) ); 
		$this->lm_sunday = $this->lm_day - $this->lm_weekday; */
	}

	/**
	 * Outputs the HTML for this widget.
	 *
	 * @since 1.0
	 * @param array An array of standard parameters for widgets.
	 * @param array An array of settings for this widget instance.
	 * @return void Echoes it's output.
	 **/
	function widget( $args, $instance ) {
		global $lm_month, $lm_sunday, $lm_year;
		extract( $args );
		
		if( $instance['display_in_home'] && !is_home() ) {
			return false;
		}
				
		$title = apply_filters( 'widget-title', $instance['title'] );
		
		echo $before_widget;
		
		if( $instance['display_title'] ) {
			echo $before_title . $title . $after_title;
		}

		for( $i = 1; $i <= 5; $i++ ) {
			if( date( 'N', mktime( 0, 0, 0, $lm_month, $lm_sunday+$i, $lm_year ) ) < 6 ) {
				$date = mktime( 0, 0, 0, $lm_month, $lm_sunday+$i, $lm_year );
			}
			echo '<span class="lunch_wrapper">';
			echo '<span class="lunch_items">' . ( empty( $instance['lunch_day_' . $i] ) ? '' : strip_tags( $instance['lunch_day_' . $i] ) ) . '</span>';
			echo '<span class="lunch_dates">' . date_i18n( get_option( 'date_format' ), $date ) . '</span>';
			echo '</span>';
		}
		echo $after_widget;
	}
	
	/**
	 * Deals with the settings when they are saved by the admin. 
	 * Here is where any validation should be dealt with.
	 *
	 * @since 1.0
	 **/
	function update( $new_instance, $old_instance ) {
		global $lm_month, $lm_sunday, $lm_year;
		$instance = $old_instance;
		
		// Update widget information
		$instance['title'] = esc_attr( strip_tags( $new_instance['title'] ) );
		$instance['display_in_home'] = esc_attr( strip_tags( $new_instance['display_in_home'] ) );
		$instance['display_title'] = esc_attr( strip_tags( $new_instance['display_title'] ) );
		$instance['no_style'] = esc_attr( strip_tags( $new_instance['no_style'] ) );
		for( $i = 1; $i <= 5; $i++ ) {
			$instance['lunch_day_' . $i] = esc_attr( strip_tags( $new_instance['lunch_day_' . $i] ) );
		}

		// Update options
		update_option( 'd70_lunch_menu_styling', $instance['no_style'] );
		d70_update_option( $instance );
				
		return $instance;
	}
	
	/**
	 * Displays the form for this widget on
	 * the Widgets page of the WP Admin area.
	 *
	 * @since 1.0
	 **/
	function form( $instance ) {
		global $lm_month, $lm_sunday, $lm_year;
		$instance = wp_parse_args( (array)$instance, array( 'title' => __( 'Weeks lunch menu' ), 'display_in_home' => true, 'display_title' => true, 'no_style' => false, ) );
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ) ?>"><?php _e( 'Title:', 'lm' ) ?></label><br />
		<input type="text" id="<?php echo $this->get_field_id( 'title' ) ?>" name="<?php echo $this->get_field_name( 'title' ) ?>" value="<?php echo strip_tags( $instance['title'] ) ?>" />
		</p>
		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'display_in_home' ); ?>" name="<?php echo $this->get_field_name( 'display_in_home' ) ?>" <?php if( $instance['display_in_home'] ) : ?> checked="checked" <?php endif ?> />
			<label for="<?php echo $this->get_field_id( 'display_in_home' ) ?>"><?php _e( 'Show on homepage', 'lm' ) ?></label>
			<br />
			<input type="checkbox" id="<?php echo $this->get_field_id( 'display_title' ); ?>" name="<?php echo $this->get_field_name( 'display_title' ) ?>" <?php if( $instance['display_title'] ) : ?> checked="checked" <?php endif ?> />
			<label for="<?php echo $this->get_field_id( 'display_title' ) ?>"><?php _e( 'Show title', 'lm' ) ?></label>
			<br />
			<input type="checkbox" id="<?php echo $this->get_field_id( 'no_style' ); ?>" name="<?php echo $this->get_field_name( 'no_style' ) ?>" <?php if( $instance['no_style'] ) : ?> checked="checked" <?php endif ?> />
			<label for="<?php echo $this->get_field_id( 'no_style' ) ?>"><?php _e( 'Custom styling', 'lm' ) ?></label>
			<?php if( $instance['no_style'] ) { ?>
					<span class="lunch_menu_admin_style"><?php _e( 'You can use span.lunch_wrapper, span.lunch_dates and span.lunch_items to control the styling of the lunch menu.', 'lm' ); ?></span>
			<?php }	?>
		</p>
		<?php
			for( $i = 1; $i <= 5; $i++ ) {
				if( date( 'N', mktime( 0, 0, 0, $lm_month, $lm_sunday+$i, $lm_year ) ) < 6 ) {
					$date = mktime( 0, 0, 0, $lm_month, $lm_sunday+$i, $lm_year );
				}
		?>

				<p><label for="<?php echo $this->get_field_id( 'lunch_day_' . $i ) ?>"><?php echo date_i18n( get_option( 'date_format' ), $date ) ?></label><br />
				<input type="text" id="<?php echo $this->get_field_id( 'lunch_day_' . $i ) ?>" name="<?php echo $this->get_field_name( 'lunch_day_' . $i ) ?>" value="<?php echo strip_tags( $instance['lunch_day_' . $i] ); ?>" />
				</p>
		<?php
			}
	}
}

/**
 * Updates options used by page template.
 *
 * @since 1.1
 * @param array $lm_options Arry containing options to update.
 **/
function d70_update_option( $lm_options ) {
	$lm_array = array();

	for( $i = 1; $i <= 5; $i++ ) {
		if( date( 'N', mktime( 0, 0, 0, $lm_month, $$lm_sunday+$i, $lm_year ) ) < 6 ) {
			$date = mktime( 0, 0, 0, $lm_month, $lm_sunday+$i, $lm_year );
		}

		$lm_array[date_i18n( 'F', $date )][date_i18n( 'j', $date )] = $lm_options['lunch_day_' . $i];

		update_option( 'd70_lm_array', serialize( $lm_array ) );
	}
}

/**
 * Function that enqueues the styles
 * required for the widget if the admin
 * doesn't want to style the widget himself.
 *
 * @since 1.1
 **/
function d70_enqueue_style() {
	if( get_option( 'd70_lunch_menu_styling' ) == "" ) {
		wp_enqueue_style( 'D70_Lunch_Menu', plugins_url( '', __FILE__ ) . '/lunch_menu.css', false, '1.1', 'all' );
	}
}

/**
 * Styling for the admin section of WordPress.
 * 
 * @since 1.1
 **/
function d70_lunch_menu_admin_styling() {
	echo '<style type="text/css">
		 	span.lunch_menu_admin_style {
		 		color: #888;
		 		display: block;
		 		font-size: 90%;
		 	}
		 </style>';
}

/**
 * Registers the widget.
 * 
 * @since 1.0
 **/
function d70_lm_register_widgets() {
	register_widget( 'D70_Lunch_Menu_Widget' );
}

/**
 * Uninstallation hook to delete entries from
 * the options table.
 * 
 * @since 1.1
 **/
if ( function_exists('register_uninstall_hook') )
    register_uninstall_hook( __FILE__, 'd70_lunch_menu_uninstall' );
 
	function d70_lunch_menu_uninstall() {
    	delete_option( 'd70_lunch_menu_styling' );
    	delete_option( 'd70_lm_array' );
	}

/**
 * Hooks into the appropriate WordPress hooks.
 *
 * @since 1.0
 **/
add_action( 'wp_enqueue_scripts', 'd70_enqueue_style' );
add_action( 'widgets_init', 'd70_lm_register_widgets', 1 );
add_action( 'admin_head', 'd70_lunch_menu_admin_styling' );
?>