<?php
/**
 * @package Forms
 * @code author wpplugin (wpplugin.com)
 */

if ( ! defined( 'wptsc_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

if ( ! class_exists( 'plugin_admin' ) ) {
	/**
	 * class plugin_admin
	 *
	 * Class to add functionality to admin pages.
	 */
	class plugin_admin {	
	
		/**
		 * @var string $currentoption The option in use for the current admin page.
		 */
		var $currentoption = 'wptsc';
	
		/**
		 * @var array $adminpages Array of admin pages that the plugin uses.
		 */
		var $adminpages = array( 'wptsc_settings','wptsc' );
		
		function __construct() {
			add_action( 'init', array( $this, 'init' ), 20 );
		}
	
		/**
		 * Make sure the needed scripts are loaded for admin pages
		 */
		function init() {
			$this->adminpages = apply_filters( 'plugin_admin_pages', $this->adminpages );
	
			add_action( 'admin_print_scripts', array( $this, 'config_page_scripts' ) );
			add_action( 'admin_print_styles', array( $this, 'config_page_styles' ) );
		}	
		
		/**
		 * Generates the header for admin pages
		 *
		 * @param bool   $form           Whether or not the form should be included.
		 * @param string $option         The long name of the option to use for the current page.
		 * @param string $optionshort    The short name of the option to use for the current page.
		 * @param bool   $contains_files Whether the form should allow for file uploads.
		 */
		function admin_header( $form = true, $option = 'plugin_options', $optionshort = 'wpplugin', $contains_files = false ) {
			?>
			<div class="wrap">
			<?php
			/**
			 * Display the updated/error messages
			 * Only needed as our settings page is not under options, otherwise it will automatically be included
			 * @see settings_errors()
			 */
			require_once( ABSPATH . 'wp-admin/options-head.php' );
			?>
			<h2 id="wpplugin-title"><?php echo get_admin_page_title(); ?></h2>
			<div id="wpplugin_content_top" class="postbox-container" style="min-width:400px; max-width:600px; padding: 0 20px 0 0;">
			<div class="metabox-holder">
			<div class="meta-box-sortables">
			<?php
			if ( $form ) {
				echo '<form action="' . admin_url( 'options.php' ) . '" method="post" id="wpplugin-conf"' . ( $contains_files ? ' enctype="multipart/form-data"' : '' ) . ' accept-charset="' . get_bloginfo( 'charset' ) . '">';
				settings_fields( $option );
				$this->currentoption = $optionshort;
			}
	
		}
	
		/**
		 * Generates the footer for admin pages
		 *
		 * @param bool $submit Whether or not a submit button should be shown.
		 */
		function admin_footer( $submit = true ) {
			if ( $submit ) {
				submit_button();
			} ?>
			</form>
			</div>
			</div>
			</div>
			<?php //$this->admin_sidebar(); ?>
			</div>
		<?php
		}
		
		/**
		 * Loads the required styles for the config page.
		 */
		function config_page_styles() {
			global $pagenow;
			
			if ( $pagenow == 'tools.php' && isset( $_GET['page'] ) && in_array( $_GET['page'], $this->adminpages ) ) {
				wp_enqueue_style( 'dashboard' );
				wp_enqueue_style( 'thickbox' );
				wp_enqueue_style( 'global' );
				wp_enqueue_style( 'wp-admin' );
				wp_enqueue_style( 'plugin-admin-css', plugins_url( 'css/plugin-admin.css', dirname( __FILE__ ) ), array(), wptsc_VERSION );
	
				//if ( is_rtl() )
					//wp_enqueue_style( 'wpplugin-rtl', plugins_url( 'css/wpplugin-rtl.css', dirname( __FILE__ ) ), array(), wpplugin_VERSION );
			}
		}
	
		/**
		 * Loads the required scripts for the config page.
		 */
		function config_page_scripts() {
			global $pagenow;
			
			if ( $pagenow == 'tools.php' && isset( $_GET['page'] ) && in_array( $_GET['page'], $this->adminpages ) ) {
				//wp_enqueue_script( 'wpplugin-admin-script', plugins_url( 'js/plugin-admin.js', dirname( __FILE__ ) ), array( 'jquery' ), wpplugin_VERSION, true );
				wp_enqueue_script( 'postbox' );
				wp_enqueue_script( 'dashboard' );
				wp_enqueue_script( 'thickbox' );
			}
		}
		
		/**
		 * Retrieve options based on the option or the class currentoption.
		 *
		 * @since 1.2.4
		 *
		 * @param string $option The option to retrieve.
		 * @return array
		 */
		function get_option( $option ) {
			if ( function_exists( 'is_network_admin' ) && is_network_admin() )
				return get_site_option( $option );
			else
				return get_option( $option );
		}
		
		/**
		 * Create a Checkbox input field.
		 *
		 * @param string $var        The variable within the option to create the checkbox for.
		 * @param string $label      The label to show for the variable.
		 * @param bool   $label_left Whether the label should be left (true) or right (false).
		 * @param string $option     The option the variable belongs to.
		 * @return string
		 */
		function checkbox( $var, $label, $label_left = false, $option = '' ) {
			if ( empty( $option ) ) {
				$option = $this->currentoption;
			}

			$options = $this->get_option( $option );

			if ( ! isset( $options[$var] ) ) {
				$options[$var] = false;
			}

			if ( $options[$var] === true ) {
				$options[$var] = 'on';
			}

			if ( $label_left !== false ) {
				if ( ! empty( $label_left ) ) {
					$label_left .= ':';
				}
				$output_label = '<label class="checkbox" for="' . esc_attr( $var ) . '">' . $label_left . '</label>';
				$class        = 'checkbox';
			}
			else {
				$output_label = '<label for="' . esc_attr( $var ) . '">' . $label . '</label>';
				$class        = 'checkbox double';
			}

			$output_input = '<input class="' . esc_attr( $class ) . '" type="checkbox" id="' . esc_attr( $var ) . '" name="' . esc_attr( $option ) . '[' . esc_attr( $var ) . ']" value="on"' . checked( $options[$var], 'on', false ) . '/>';

			if ( $label_left !== false ) {
				$output = $output_label . $output_input . '<label class="checkbox" for="' . esc_attr( $var ) . '">' . $label . '</label>';
			}
			else {
				$output = $output_input . $output_label;
			}
			return $output . '<br class="clear" />';
		}

		/**
		 * Create a Text input field.
		 *
		 * @param string $var    The variable within the option to create the text input field for.
		 * @param string $label  The label to show for the variable.
		 * @param string $option The option the variable belongs to.
		 * @return string
		 */
		function textinput( $var, $label, $option = '' ) {
			if ( empty( $option ) ) {
				$option = $this->currentoption;
			}

			$options = $this->get_option( $option );
			$val     = ( isset( $options[$var] ) ) ? $options[$var] : '';

			return '<label class="textinput" for="' . esc_attr( $var ) . '">' . $label . ':</label><input class="textinput" type="text" id="' . esc_attr( $var ) . '" name="' . esc_attr( $option ) . '[' . esc_attr( $var ) . ']" value="' . esc_attr( $val ) . '"/>' . '<br class="clear" />';
		}

		/**
		 * Create a textarea.
		 *
		 * @param string $var    The variable within the option to create the textarea for.
		 * @param string $label  The label to show for the variable.
		 * @param string $option The option the variable belongs to.
		 * @param string $class  The CSS class to assign to the textarea.
		 * @return string
		 */
		function textarea( $var, $label, $option = '', $class = '' ) {
			if ( empty( $option ) ) {
				$option = $this->currentoption;
			}

			$options = $this->get_option( $option );
			$val     = ( isset( $options[$var] ) ) ? $options[$var] : '';

			return '<label class="textinput" for="' . esc_attr( $var ) . '">' . esc_html( $label ) . ':</label><textarea class="textinput ' . esc_attr( $class ) . '" id="' . esc_attr( $var ) . '" name="' . esc_attr( $option ) . '[' . esc_attr( $var ) . ']">' . esc_textarea( $val ) . '</textarea>' . '<br class="clear" />';
		}

		/**
		 * Create a hidden input field.
		 *
		 * @param string $var    The variable within the option to create the hidden input for.
		 * @param string $option The option the variable belongs to.
		 * @return string
		 */
		function hidden( $var, $option = '' ) {
			if ( empty( $option ) ) {
				$option = $this->currentoption;
			}

			$options = $this->get_option( $option );

			$val = ( isset( $options[$var] ) ) ? $options[$var] : '';
			if ( is_bool( $val ) ) {
				$val = ( $val === true ) ? 'true' : 'false';
			}

			return '<input type="hidden" id="hidden_' . esc_attr( $var ) . '" name="' . esc_attr( $option ) . '[' . esc_attr( $var ) . ']" value="' . esc_attr( $val ) . '"/>';
		}

		/**
		 * Create a Select Box.
		 *
		 * @param string $var    The variable within the option to create the select for.
		 * @param string $label  The label to show for the variable.
		 * @param array  $values The select options to choose from.
		 * @param string $option The option the variable belongs to.
		 * @return string
		 */
		function select( $var, $label, $values, $option = '' ) {
			if ( ! is_array( $values ) || $values === array() ) {
				return '';
			}
			if ( empty( $option ) ) {
				$option = $this->currentoption;
			}

			$options = $this->get_option( $option );

			$output  = '<label class="select" for="' . esc_attr( $var ) . '">' . $label . ':</label>';
			$output .= '<select class="select" name="' . esc_attr( $option ) . '[' . esc_attr( $var ) . ']" id="' . esc_attr( $var ) . '">';

			foreach ( $values as $value => $label ) {
				if ( ! empty( $label ) )
					$output .= '<option value="' . esc_attr( $value ) . '"' . selected( $options[$var], $value, false ) . '>' . $label . '</option>';
			}
			$output .= '</select>';
			return $output . '<br class="clear"/>';
		}

		/**
		 * Create a File upload field.
		 *
		 * @param string $var    The variable within the option to create the file upload field for.
		 * @param string $label  The label to show for the variable.
		 * @param string $option The option the variable belongs to.
		 * @return string
		 */
		function file_upload( $var, $label, $option = '' ) {
			if ( empty( $option ) ) {
				$option = $this->currentoption;
			}

			$options = $this->get_option( $option );

			$val = '';
			if ( isset( $options[$var] ) && is_array( $options[$var] ) ) {
				$val = $options[$var]['url'];
			}

			$var_esc = esc_attr( $var );
			$output  = '<label class="select" for="' . $var_esc . '">' . esc_html( $label ) . ':</label>';
			$output .= '<input type="file" value="' . esc_attr( $val ) . '" class="textinput" name="' . esc_attr( $option ) . '[' . $var_esc . ']" id="' . $var_esc . '"/>';

			// Need to save separate array items in hidden inputs, because empty file inputs type will be deleted by settings API.
			if ( ! empty( $options[$var] ) ) {
				$output .= '<input class="hidden" type="hidden" id="' . $var_esc . '_file" name="wpplugin_local[' . $var_esc . '][file]" value="' . esc_attr( $options[$var]['file'] ) . '"/>';
				$output .= '<input class="hidden" type="hidden" id="' . $var_esc . '_url" name="wpplugin_local[' . $var_esc . '][url]" value="' . esc_attr( $options[$var]['url'] ) . '"/>';
				$output .= '<input class="hidden" type="hidden" id="' . $var_esc . '_type" name="wpplugin_local[' . $var_esc . '][type]" value="' . esc_attr( $options[$var]['type'] ) . '"/>';
			}
			$output .= '<br class="clear"/>';

			return $output;
		}

		/**
		 * Create a Radio input field.
		 *
		 * @param string $var    The variable within the option to create the file upload field for.
		 * @param array  $values The radio options to choose from.
		 * @param string $label  The label to show for the variable.
		 * @param string $option The option the variable belongs to.
		 * @return string
		 */
		function radio( $var, $values, $label, $option = '' ) {
			if ( ! is_array( $values ) || $values === array() ) {
				return '';
			}
			if ( empty( $option ) ) {
				$option = $this->currentoption;
			}

			$options = $this->get_option( $option );

			if ( ! isset( $options[$var] ) ) {
				$options[$var] = false;
			}

			$var_esc = esc_attr( $var );

			$output = '<br/><label class="select">' . $label . ':</label>';
			foreach ( $values as $key => $value ) {
				$key_esc = esc_attr( $key );
				$output .= '<input type="radio" class="radio" id="' . $var_esc . '-' . $key_esc . '" name="' . esc_attr( $option ) . '[' . $var_esc . ']" value="' . $key_esc . '" ' . checked( $options[$var], $key_esc, false ) . ' /> <label class="radio" for="' . $var_esc . '-' . $key_esc . '">' . esc_html( $value ) . '</label>';
			}
			$output .= '<br/>';

			return $output;
		}
		
		/**
		 * Create a postbox widget.
		 *
		 * @param string $id      ID of the postbox.
		 * @param string $title   Title of the postbox.
		 * @param string $content Content of the postbox.
		 */
		function postbox( $id, $title, $content ) {
			?>
			<div id="<?php echo esc_attr( $id ); ?>" class="wppluginbox">
				<h2><?php echo $title; ?></h2>
				<?php echo $content; ?>
			</div>
		<?php
		}
	
	
		/**
		 * Create a form table from an array of rows.
		 *
		 * @param array $rows Rows to include in the table.
		 * @return string
		 */
		function form_table( $rows ) {
			$content = '<table class="form-table">';
			foreach ( $rows as $row ) {
				$content .= '<tr><th valign="top" scrope="row">';
				if ( isset( $row['id'] ) && $row['id'] != '' )
					$content .= '<label for="' . esc_attr( $row['id'] ) . '">' . esc_html( $row['label'] ) . ':</label>';
				else
					$content .= esc_html( $row['label'] );
				if ( isset( $row['desc'] ) && $row['desc'] != '' )
					$content .= '<br/><small>' . esc_html( $row['desc'] ) . '</small>';
				$content .= '</th><td valign="top">';
				$content .= $row['content'];
				$content .= '</td></tr>';
			}
			$content .= '</table>';
			return $content;
		}
	}
	global $plugin_admin;
	$plugin_admin = new plugin_admin();
}

?>