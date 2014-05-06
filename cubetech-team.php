<?php
/**
 * Plugin Name: cubetech Team
 * Plugin URI: http://www.cubetech.ch
 * Description: cubetech Team - create some team members, reusable within groups and shorttags
 * Version: 1.2.1
 * Author: cubetech GmbH
 * Author URI: http://www.cubetech.ch
 */

include_once('lib/cubetech-install.php');
include_once('lib/cubetech-post-type.php');
include_once('lib/cubetech-shortcode.php');
include_once('lib/cubetech-group.php');
include_once('lib/cubetech-metabox.php');
include_once('lib/cubetech-settings.php');

add_image_size( 'cubetech-team-thumb', 231, 124, true );

wp_enqueue_script('jquery');
wp_register_script('cubetech_team_js', plugins_url('assets/js/cubetech-team.js', __FILE__), 'jquery');
wp_enqueue_script('cubetech_team_js');

if(get_option('cubetech_team_layout') == '3coljquery') {
	wp_register_script('cubetech-team-js-3coljquery', plugins_url('assets/js/cubetech-team-3coljquery.js', __FILE__) );
	wp_enqueue_script('cubetech-team-js-3coljquery');
}

add_action('wp_enqueue_scripts', 'cubetech_team_add_styles');

function cubetech_team_add_styles() {
	wp_register_style('cubetech-team-css', plugins_url('assets/css/cubetech-team.css', __FILE__) );
	wp_enqueue_style('cubetech-team-css');
	if(get_option('cubetech_team_layout') == '3coljquery') {
		wp_register_style('cubetech-team-css-3coljquery', plugins_url('assets/css/cubetech-team-3coljquery.css', __FILE__) );
		wp_enqueue_style('cubetech-team-css-3coljquery');
	}
}

/* Add button to TinyMCE */
function cubetech_team_addbuttons() {

	if ( (! current_user_can('edit_posts') && ! current_user_can('edit_pages')) )
		return;
	
	if ( get_user_option('rich_editing') == 'true') {
		add_filter("mce_external_plugins", "add_cubetech_team_tinymce_plugin");
		add_filter('mce_buttons', 'register_cubetech_team_button');
		add_action( 'admin_footer', 'cubetech_team_dialog' );
	}
}
 
function register_cubetech_team_button($buttons) {
   array_push($buttons, "|", "cubetech_team_button");
   return $buttons;
}
 
function add_cubetech_team_tinymce_plugin($plugin_array) {
	$plugin_array['cubetech_team'] = plugins_url('assets/js/cubetech-team-tinymce.js', __FILE__);
	return $plugin_array;
}

add_action('init', 'cubetech_team_addbuttons');

function cubetech_team_dialog() { 

	$args=array(
		'hide_empty' => false,
		'orderby' => 'name',
		'order' => 'ASC'
	);
	$taxonomies = get_terms('cubetech_team_group', $args);
	
	?>
	<style type="text/css">
		#cubetech_team_dialog { padding: 10px 30px 15px; }
	</style>
	<div style="display:none;" id="cubetech_team_dialog">
		<div>
			<p>Wählen Sie bitte die einzufügende Teamgruppe:</p>
			<p><select name="cubetech_team_taxonomy" id="cubetech_team_taxonomy">
				<option value="">Bitte Gruppe auswählen</option>
				<option value="all">Alle Kategorien anzeigen</option>
				<?php
				foreach($taxonomies as $tax) :
					echo '<option value="' . $tax->term_id . '">' . $tax->name . '</option>';
				endforeach;
				?>
			</select></p>
		</div>
		<div>
			<p><input type="submit" class="button-primary" value="Teamgruppe einfügen" onClick="if ( cubetech_team_taxonomy.value != '' && cubetech_team_taxonomy.value != 'undefined' ) { tinyMCE.activeEditor.execCommand('mceInsertContent', 0, '[cubetech-team group=' + cubetech_team_taxonomy.value + ']'); tinyMCEPopup.close(); }" /></p>
		</div>
	</div>
	<?php
}

?>
