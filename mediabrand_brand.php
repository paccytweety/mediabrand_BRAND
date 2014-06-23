<?php
/*
Plugin Name: Mediabrand Brand Wordpress
Description: Personalizzare e brandizzare le principali sezioni di Wordpress (login, dashboard logo e menu, footer, ecc) by Mediabrand (Versioni >= 3.0)
Version: 1.0
Author: Patrizia Petrera
Author URI: http://www.mediabrand.it
*/
define ('MEDIABRAND_PLUGIN_URL', plugin_dir_url(__FILE__) );
	
	/******************************************************/
	/* Esegue gli script all'attivazione del plugin       */
	/******************************************************/
	function mediabrand_activate() {
		
	}
	register_activation_hook( __FILE__, 'mediabrand_activate' );
		
	
	/******************************************************/
	/* Esegue gli script alla disattivazione del plugin   */
	/******************************************************/
	function mediabrand_uninstall () {
		
	}
	register_deactivation_hook( __FILE__, 'mediabrand_uninstall' );

	
	/***************************************************************/
	/* Aggiunge un messaggio di "update" nella pagina del plugin   */
	/***************************************************************/
	function mediabrand_activation_notice() {
		
		if ( isset ( $_GET['page'] ) && $_GET['page'] == "mediabrand_menu" ) {
		
			$versione =  get_bloginfo( 'version' ) ;
				if ( $versione >= 3.0 ) {
			echo '<div class="updated fade" id="message"><p>Mediabrand Plugin attivato con successo!</p></div>';
				}else{
					echo '<div class="error" id="message"><p>Attenzione stai utilizzando una versione di Wordpress non completamente compatibile con questo Plugin</p><p>Versione Consigliata: <strong>superiore alla 3.0</strong></p></div>';
				}
		}
	}
	add_action( 'admin_notices', 'mediabrand_activation_notice');
	
	
	/************************************************************************/
	/* Creo i campi personalizzati del plugin e la pagina per modificarli   */
	/************************************************************************/
	function mediabrand_admin_options() {
	
		global $options_mediabrand;
		
			$options_mediabrand = array(
			
				array( 'name' => 'Wp-login Settings', 'type' => 'title' ),
				array( 'type' => 'open' ),
				array( 'name' => 'Logo (URL Immagine)', 'id' => 'login_logo_img', 'type' => 'url' ),
				array( 'name' => 'Logo Link (url a cui punta l\'immagine)', 'id' => 'login_logo_url', 'type' => 'url' ),
				array( 'name' => 'Titolo logo (title immagine)', 'id' => 'login_logo_title', 'type' => 'text' ),
				array( 'name' => 'Altezza Immagine (es:200)', 'id' => 'login_logo_img_height', 'type' => 'number' ),
				//array( 'name' => 'Note', 'id' => 'login_logo_note', 'type' => 'textarea' ),
				array( 'type' => 'close' )
			);
		}
	add_action( 'admin_init', 'mediabrand_admin_options' );
		
		
	function mediabrand_register_general_settings() {
	
		global $options_mediabrand;
		
		foreach( $options_mediabrand as $value ) {
		
			// in base al tipo di input lanciamo la funzione di sanitize appropriata
			switch ( $value['type'] ) {
				case 'text':
					$sanitize_callback = 'strip_tags'; //elimina tag HTML
					break;
				case 'textarea':
					$sanitize_callback = 'strip_tags'; //elimina tag HTML
					break;					
				case 'textarea_html':
					$sanitize_callback = '';
					//$sanitize_callback = 'mediabrand_sanitize_html';
					break;
				case 'number':
					$sanitize_callback = '';
					break;
					//$sanitize_callback = 'mediabrand_sanitize_number';
				case 'url':
					$sanitize_callback = 'esc_url';
					break;
				case 'e-mail':
					$sanitize_callback = 'sanitize_email';
					break;
				default:
					$sanitize_callback = '';
					break;
			}
			if ( $sanitize_callback != '') {
				register_setting( 'mediabrand-settings-group', $value['id'], $sanitize_callback );
			}else{
				register_setting( 'mediabrand-settings-group', $value['id'] );
			}
		}
	}
	add_action( 'admin_init', 'mediabrand_register_general_settings' );
	
	
	function mediabrand_general_settings_page() {
		global $options_mediabrand;
?>
<div class="wrap">
	<div class="icon32" id="icon-options-general"></div>
	<h2>MediaBRAND</h2>
	<br/>
	<div class="mediabrand_settings">
		<form method="post" action="options.php">	
<?php
		settings_fields( 'mediabrand-settings-group' );

		foreach( $options_mediabrand as $value ) {
			switch( $value['type'] ) {
				case "open":
					break;
				case "message"
?>
				<div>
					<p><?php echo $value['text']; ?></p>
				</div>
<?php
					break;
				case "title":
?>
			<div style="display: block;" class="postbox hide-if-js">
				<div class="handlediv" title="Fare clic per cambiare."><br></div>
				<h3 class="hndle"><span><?php echo $value['name']; ?></span></h3>
				<div class="inside">
			
					<table width="100%" border="0">
<?php
					break;
				case 'text':
				case 'text_html':
				case 'url':
				case 'e-mail':
?>
					<tr>
						<td class="settings-table" width="25%"><?php echo $value['name']; ?></td>
						<td class="settings-table" width="75%"><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" size="39" type="text" value="<?php if ( get_option( $value['id'] ) != "" ) { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></td>
					</tr>
<?php
					break;
				case 'number':
?>
					<tr>
						<td class="settings-table" width="25%"><?php echo $value['name']; ?></td>
						<td class="settings-table" width="75%"><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" class="number" size="39" type="text" value="<?php if ( get_option( $value['id'] ) != "" ) { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></td>
					</tr>
<?php
					break;
				case 'float':
?>
					<tr>
						<td class="settings-table" width="25%"><?php echo $value['name']; ?></td>
						<td class="settings-table" width="75%"><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" class="float" size="39" type="text" value="<?php if ( get_option( $value['id'] ) != "" ) { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></td>
					</tr>
<?php
					break;
				case 'textarea':
				case 'textarea_html':
?>
					<tr>
						<td class="settings-table" width="25%"><?php echo $value['name']; ?></td>
						<td class="settings-table" width="75%"><textarea name="<?php echo $value['id']; ?>" style="height:200px;" type="<?php echo $value['type']; ?>" cols="59" rows=""><?php if ( get_option( $value['id'] ) != "" ) { echo get_option( $value['id'] ); } else { echo $value['std']; } ?></textarea></td>
					</tr>
<?php
					break;
				case 'select':
?>
					<tr>
						<td class="settings-table" width="25%"><?php echo $value['name']; ?></td>
						<td class="settings-table" width="75%">
							<select style="width:240px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
<?php
					foreach ( $value['options'] as $option ) {
?>
							<option<?php if ( get_option( $value['id'] ) == $option ) { echo ' selected="selected"'; } else if ( $option == $value['std'] ) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
<?php
					}
?>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="2"><small><?php echo $value['desc']; ?></small></td>
					</tr>
<?php
					break;
				case "checkbox":
?>
				<tr>
					<td class="settings-table" width="25%"><?php echo $value['name']; ?></td>
					<td class="settings-table" width="75%"><?php if( get_option( $value['id'] ) ) { $checked = "checked=\"checked\""; } else { $checked = ""; } ?><input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> /><small><?php echo $value['desc']; ?></small></td>
				</tr>
<?php
					break;
				case "close":
?>
					</table>
				</div>
			</div>
<?php
					break;
			}
		}
?>
			<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Salva modifiche" /></p>
		</form>
	</div>
</div>
<?php 
	}
	
	
	/*******************************************************/
	/* Creazione dei menu nell'admin area                  */
	/*******************************************************/
	function mediabrand_create_options() {
		global $blog_id;
		
		if ( current_user_can( 'manage_options' ) ) {
			add_menu_page( 'Mediabrand', 'MediaBRAND', 'manage_options', 'mediabrand_menu', 'mediabrand_general_settings_page', '' );
			//add_submenu_page( 'mediabrand_menu', 'Generale', 'Generale', 'manage_options', 'mediabrand_menu', 'mediabrand_general_settings_page' );
		}
		
		
	}
	add_action( 'admin_menu', 'mediabrand_create_options' );
	
	
	/*********************************************/
	/* Logo wp-login personalizzato              */
	/*********************************************/
	function mediabrand_custom_wp_login_logo(){
	
		if ( get_option( 'login_logo_img' ) ) { 
			$url_image = esc_url ( get_option( 'login_logo_img' ) );
		}else {	
			$url_image = MEDIABRAND_PLUGIN_URL . "images/login_logo.png" ;
		}		
		
		if ( get_option( 'login_logo_img_height' ) ) {
			$logo_height = get_option( 'login_logo_img_height' ) . "px";
		}else {	
			$logo_height = "70px";
		}	
		
		if ( $url_image) :
			echo '<style type="text/css">' . "\n" . 'body.login #login h1 a { background: url("' . $url_image . '") no-repeat scroll center top transparent; width: auto; min-height:' . $logo_height . '; background-size: auto ' . $logo_height . '; }' . "\n" . '</style>' . "\n";
		endif;
	}
	add_action('login_head','mediabrand_custom_wp_login_logo');
	
	
	/*********************************************/
	/* Url del logo in wp-login personalizzato   */
	/*********************************************/
	function mediabrand_custom_wp_login_url() {
	
		if ( get_option( 'login_logo_url' ) ) { 
			return get_option( 'login_logo_url' ) ;
		}else {	
			return site_url(); 
		}		

	}
	add_filter( 'login_headerurl', 'mediabrand_custom_wp_login_url');
	
	
	/*********************************************/
	/* Titolo logo wp-login personalizzato       */
	/*********************************************/
	function mediabrand_custom_wp_login_title () {
	
		if ( get_option( 'login_logo_title' ) ) { 
			return get_option( 'login_logo_title' ) ;
		}else {	
			return get_bloginfo ('name'); 
		}
		
	}
	add_filter ('login_headertitle', 'mediabrand_custom_wp_login_title');
	
	
	/*********************************************/
	/* Custom wp-admin Dashboard LOGO            */
	/*********************************************/	
	function add_adminbar_menu() {
		global $wp_admin_bar;
	
		$wp_admin_bar->add_menu( array(
		'id'    => 'wp-logo',
		'title' => '<div class="mediabrand-wp-logo"></div>',
		'href'  => is_admin() ? home_url( '/' ) : admin_url(),
		'meta'  => array( 'title' => __('Visita sito') ),
		));
	}
	add_action( 'wp_before_admin_bar_render', 'add_adminbar_menu' );
	
	function custom_admin_logo() {  // css per il backend
		echo '<style> 
				#wpadminbar li#wp-admin-bar-wp-logo .mediabrand-wp-logo {
					background: url("'. MEDIABRAND_PLUGIN_URL .'/images/mediabrand_dashboard_logo.png") no-repeat scroll center 0 transparent !important;
					width: 145px !important;
					height: 32px !important;
				}
				#wpadminbar li#wp-admin-bar-wp-logo {
					background-color: transparent;
				}
				#wpadminbar li#wp-admin-bar-wp-logo a {
					padding:0;
				}
			
			</style>';
    }
	
    add_filter('wp_before_admin_bar_render','custom_admin_logo');
	
	/*********************************************/
	/* Custom CSS "impostazioni schermo" e dashboard widget DISPLAY NONE            */
	/*********************************************/	
	function custom_admin_css() {
		if (!is_super_admin()) {
			echo '<style> 
					#woothemes-settings, 
					#adv-settings .metabox-prefs label[for=woothemes-settings-hide], 
					#adv-settings .metabox-prefs label[for=postcustom-hide],
					#adv-settings .metabox-prefs label[for=dashboard_incoming_links-hide],
					#adv-settings .metabox-prefs label[for=dashboard_primary-hide],
					#adv-settings .metabox-prefs label[for=dashboard_secondary-hide],
					#adv-settings .metabox-prefs label[for=wp_welcome_panel-hide],
					#dashboard_primary,
					#dashboard_secondary,
					#dashboard_incoming_links,
					#dashboard_right_now .inside .versions p,
					#dashboard_right_now .inside .versions span#wp-version-message,
					#toplevel_page_wpseo_dashboard {
						display:none;
					}
				
				</style>';
		}
	}
	add_action( 'wp_before_admin_bar_render', 'custom_admin_css' );
	
	
	/**************************************************************/
	/* Rimuove le voci del menu nell'adminbar in wordpress 3.3    */
	/***************************************************************/
	// ATTENZIONE: NON rimuovere $wp_admin_bar->remove_menu('wp-logo'); ma solo i sotto menu.
	function remove_adminbar_menu() {
	
		if ( !is_super_admin() ) {
		
			global $wp_admin_bar;

			//$wp_admin_bar->remove_menu('wp-logo');
				$wp_admin_bar->remove_menu('about');
				$wp_admin_bar->remove_menu('wporg');
				$wp_admin_bar->remove_menu('documentation');
				$wp_admin_bar->remove_menu('support-forums');
				$wp_admin_bar->remove_menu('feedback');
				
			$wp_admin_bar->remove_menu('site-name');
				$wp_admin_bar->remove_menu('view-site');
			
			$wp_admin_bar->remove_menu('new-content');
				$wp_admin_bar->remove_menu('new-link', 'new-content');
				$wp_admin_bar->remove_menu('new-post', 'new-content');
				$wp_admin_bar->remove_menu('new-media', 'new-content');
				$wp_admin_bar->remove_menu('new-page', 'new-content');
				$wp_admin_bar->remove_menu('new-user', 'new-content');
				
				/*
				Hide custom link woothemes canvas

				$wp_admin_bar->remove_menu('new-portfolio', 'new-content');
				$wp_admin_bar->remove_menu('new-slide', 'new-content');
				$wp_admin_bar->remove_menu('new-feedback', 'new-content');
				*/
			
			$wp_admin_bar->remove_menu('updates');  
			$wp_admin_bar->remove_menu('comments');
			
			$wp_admin_bar->remove_menu('my-account'); 
			
			$wp_admin_bar->remove_menu('wpseo-menu');
		}
	}
	add_action( 'wp_before_admin_bar_render', 'remove_adminbar_menu' );
	
	
	
	/* --------------------------------------------------------------------------------------- */
	/* Nasconde i menu dell'admin                                                              */
	/* --------------------------------------------------------------------------------------- */
	function mediabrand_remove_admin_menu() {
		
		if ( !is_super_admin() ) {  
		
			$block_list = array();
			
			$block_list[] = 'tools.php';				//blocca il menu "Strumenti"
			remove_menu_page('tools.php');				//nasconde il menu "Strumenti"
			
			$block_list[] = 'edit-comments.php';		//blocca il menu "Commenti"
			remove_menu_page('edit-comments.php');		//nasconde il menu "Commenti"
			/*
			$block_list[] = 'edit.php?post_type=page';	//blocca il menu "Pagine"
			remove_menu_page('edit.php?post_type=page');//nasconde il menu "Pagine"
			*/
			
			$block_list[] = 'link-manager.php';			//blocca il menu "Link"
			remove_menu_page('link-manager.php');		//nasconde il menu "Link"
			
			$block_list[] = 'themes.php';						//blocca il menu "Aspetto -> Temi"
			remove_submenu_page( 'themes.php', 'themes.php' );	//nasconde il menu "Aspetto -> Temi"
			
			// Nascondo Custom Post Type
			$block_list[] = 'edit.php?post_type=slide';		//blocca il menu "Slides"
			remove_menu_page('edit.php?post_type=slide');	//nasconde il menu "Slides"
			
			$block_list[] = 'edit.php?post_type=portfolio';		//blocca il menu "Portfolio"
			remove_menu_page('edit.php?post_type=portfolio');	//nasconde il menu "Portfolio"
			
			$block_list[] = 'edit.php?post_type=feedback';		//blocca il menu "feedback"
			remove_menu_page('edit.php?post_type=feedback');	//nasconde il menu "feedback"
			
			//$block_list[] = 'admin.php?page=wpseo_bulk-title-editor';					// Worpdress SEO plugin
			//$block_list[] = 'admin.php?page=wpseo_bulk-description-editor';			// Worpdress SEO plugin
			
			// Per nascondere CF7 inserire nel wp-config.php
			//define( 'WPCF7_ADMIN_READ_CAPABILITY', 'manage_options' );
			//define( 'WPCF7_ADMIN_READ_WRITE_CAPABILITY', 'manage_options' );
			
			if ( !empty( $block_list ) ) {
				foreach ( $block_list as $lista ) {
					$result = stripos( $_SERVER['REQUEST_URI'], $lista );
					if ( $result && !is_super_admin() ) wp_redirect( get_option( 'siteurl' ) . '/wp-admin/index.php' );
				}
			}
		
		}
	}
	add_action( 'admin_menu', 'mediabrand_remove_admin_menu' );
	
	
	/**************************************************************/
	/* Nascondo Submenu "Testata" e "Sfondo"                      */
	/**************************************************************/
	//add_action( 'after_setup_theme','mediabrand_remove_sfondo_testata', 100 );
	function mediabrand_remove_sfondo_testata() {
		remove_custom_image_header();
		remove_custom_background();
	}
	
	
	
	/**************************************************************/
	/* Aggiunge capacità ai ruoli preimpostati                    */
	/**************************************************************/
	function mediabrand_add_caps() {
		
		$role = get_role( 'editor' );
		
		// Aggiunge la capacità di accedere alla sezione "Aspetto" al ruolo editore
		$role->add_cap( 'edit_theme_options' ); 
	}
	//add_action( 'admin_init', 'mediabrand_add_caps');
	
	
	
	/**************************************************************/
	/* Aggiunge le voci del menu nell'adminbar in wordpress 3.3   */
	/***************************************************************/
	function aggiungi_adminbar_menu() {
		global $wp_admin_bar;
		
		//aggiunge una voce al menu dell'adminbar in alto a destra "Salve, admin"
		$wp_admin_bar->add_menu(array(
			'parent' => 'my-account-with-avatar',
			'id' => 'google',
			'title' => __('Cerca con Google'),
			'href' => "http://www.google.it",
			'meta'  => array( 'title' => __('Cerca su Google ... ') )	
		));
	}
	//add_action('admin_bar_menu', 'aggiungi_adminbar_menu');
	
	
	
	/*********************************************/
	/* Custom admin FOOTER                       */
	/*********************************************/	
	function mediabrand_remove_footer_admin (){
	
		echo get_bloginfo() . " &copy; " . date('Y') ;
	}
	add_filter('admin_footer_text','mediabrand_remove_footer_admin');
	
	 
	function mediabrand_footer_update() {
		
		return 'Realizzato da <a href="http://www.mediabrand.it/" title="Visita il nostro sito" target="_blank">Mediabrand</a>';
	}
	add_filter( 'update_footer', 'mediabrand_footer_update', 11 );
	
	
	/***********************************************************************************/
	/* Nascondo "Aggiorna Wordpress" a tutti gli utenti tranne che all'amministratore  */
	/***********************************************************************************/
	function mediabrand_hide_upgrade_wp() {
		
		if (!current_user_can('administrator')) {
			
			remove_action( 'admin_notices', 'update_nag', 3 );
		}
	}
	add_action('admin_menu','mediabrand_hide_upgrade_wp');	
	
	
	/*************************************************************/
	/* Carica script e css delle pagine delle impostazioni       */
	/*************************************************************/
	function mediabrand_admin_scripts() {
		if ( is_admin() ) {
			
			// styles
			wp_enqueue_style( 'admin_css', MEDIABRAND_PLUGIN_URL . 'css/admin_css.css' );
			
			// scripts
			wp_enqueue_script( 'jqtransform', MEDIABRAND_PLUGIN_URL . 'js/jqtransform.js', array( 'jquery' ), '1.1', false );
			
		}
	}
	add_action( 'init', 'mediabrand_admin_scripts' );
	/*
	add_action( 'wp_enqueue_scripts', 'mediabrand_admin_scripts' ); // solo FrontEnd
	add_action( 'admin_enqueue_scripts', 'mediabrand_admin_scripts' ); // solo BackEnd
	*/
	
	
	/**************************************************************/
	/* Carica il document ready jquery                            */
	/**************************************************************/
	function mediabrand_script_loader() {
	 
		if ( is_admin() ) {
?>
			<script type="text/javascript">
				jQuery.noConflict()(function($){
					jQuery(document).ready(function(){
<?php
			if ( isset( $_GET['page'] ) && ( strstr( $_SERVER['REQUEST_URI'], 'mediabrand_menu' )  ) ) { 
		
?>
				jQuery('form').jqTransform();
<?php
			}
?>
					});
				});
			</script>
<?php
		}
	}
	
	add_action( 'admin_head', 'mediabrand_script_loader' );
	
	
	/**************************************************************/
	/* Nascondi la tab "Aiuto" nell'admin                         */
	/**************************************************************/	
	function mediabrand_nascondi_aiuto() {
	
		if ( !is_super_admin() ) {
		
			$screen = get_current_screen();
			$screen->remove_help_tabs();
		}
	}
	add_action( 'admin_head', 'mediabrand_nascondi_aiuto' );
	
	
	/**************************************************************/
	/* Wordpress SEO by Yoast                                     */
	/**************************************************************/
	/* Wordpress SEO by Yoast -> nascondi colonne */
	// add_filter( 'wpseo_use_page_analysis', '__return_false' );
	
	/* Wordpress SEO by Yoas -> nascondi metabox nelle single di articoli e pagine */
	//add_action( 'add_meta_boxes', 'mediabrand_remove_wp_seo_meta_box', 100000 );
	/* function mediabrand_remove_wp_seo_meta_box() {
		if ( ! is_super_admin() ) {
			remove_meta_box( 'wpseo_meta', 'post', 'normal' );
			remove_meta_box( 'wpseo_meta', 'page', 'normal' );
			remove_meta_box( 'wpseo_meta', 'upload', 'normal' );
			remove_meta_box( 'wpseo_meta', 'portfolio', 'normal' );
		}
	} */
?>