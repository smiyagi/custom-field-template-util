<?php if ( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') ) { exit(); } ?>
<div class="wrap cftr">
	<h2 class="ptitle"><?php _e("Custom Field Template Util", 'custom-field-template-util'); ?></h2>

<?php
if (!empty($message))      { echo '<div id="message" class="updated"><p>'. $message .'</p></div>'; }
if (!empty($errormessage)) { echo '<div id="errormessage" class="error"><p>'. $errormessage .'</p></div>'; }

if (class_exists('custom_field_template')) :
?>
	<div class="postbox">
		<h3><?php _e('Export Options', 'custom-field-template'); ?></h3>
		<div class="inside">
			<form method="post">
				<?php wp_nonce_field(TOKEN_CFTU_KEY, 'cftu-nonce'); ?>
				<p><input type="submit" name="cftu_export_options_submit" value="<?php _e('Export Options &raquo;', 'custom-field-template'); ?>" class="button-primary" /></p>
			</form>
		</div>

		<h3><?php _e('Import Options', 'custom-field-template'); ?></h3>
		<?php _e("You can import only file that you exported in 'Custom Field Template Util'.", 'custom-field-template-util'); ?>
		<div class="inside">
			<form method="post" enctype="multipart/form-data" onsubmit="return confirm('<?php _e('Are you sure to import options? Options you set will be overwritten.', 'custom-field-template'); ?>');">
				<?php wp_nonce_field(TOKEN_CFTU_KEY, 'cftu-nonce'); ?>
				<p><input type="file" name="cftfile" /> <br>
				<input type="submit" name="cftu_import_options_submit" value="<?php _e('Import Options &raquo;', 'custom-field-template'); ?>" class="button-primary" /></p>
			</form>
		</div>

		<h3><?php _e('Reset Options', 'custom-field-template'); ?></h3>
		<div class="inside">
			<form method="post" onsubmit="return confirm('<?php _e('Are you sure to reset options? Options you set will be reset to the default settings.', 'custom-field-template'); ?>');">
				<?php wp_nonce_field(TOKEN_CFTU_KEY, 'cftu-nonce'); ?>
				<p><input type="submit" name="cftu_reset_options_submit" value="<?php _e('Reset Options &raquo;', 'custom-field-template'); ?>" class="button-primary" /></p>
			</form>
		</div>
	</div>

<?php else: ?>
<?php
    printf(
      '<div class="error"><p>%s</p></div>', __( 'Custom Field Template is not active', 'custom-field-template-util' )
    );
?>
<?php endif; ?>
</div>
