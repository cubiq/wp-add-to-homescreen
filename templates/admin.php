<div id="plugin-settings-wrapper" class="wrap">
	<h2><?php echo __('Add to Homescreen Settings', $this->_slug); ?></h2>

	<h2 id="plugin-settings-tabs" class="nav-tab-wrapper">
		<a href="#basic" id="tab-basic" class="nav-tab nav-tab-active"><span style="vertical-align:text-top" class="dashicons dashicons-admin-settings"></span> <?php echo __('Basic'); ?></a>
		<a href="#advanced" id="tab-advanced" class="nav-tab"><span style="vertical-align:text-top" class="dashicons dashicons-admin-generic"></span> <?php echo __('Advanced'); ?></a>
		<a href="#debug" id="tab-debug" class="nav-tab"><span style="vertical-align:text-top" class="dashicons dashicons-hammer"></span> <?php echo __('Debug'); ?></a>
		<a href="#stats" id="tab-stats" class="nav-tab"><span style="vertical-align:text-top" class="dashicons dashicons-chart-line"></span> <?php echo __('Statistics'); ?></a>
	</h2>

	<form method="post" action="options.php">
		<?php settings_fields($this->_slug . '-group'); ?>

		<div class="plugin-tab-content" id="plugin-basic-content">
			<h3><?php echo __('Basic Configuration'); ?></h3>
			<hr />
			<p><?php echo __('The following presets automatically configure Add to Homescreen with optimal values. You can pick one preset and fine tune the plugin behavior from the <a href="#advanced">Advanced Options</a>.'); ?></p>

			<table class="form-table"><tr valign="top">
				<th scope="row">Presets</th>
				<td><fieldset>
					<label for="preset-anonymous"><input type="radio" name="presets" id="preset-anonymous" value="anonymous" />
						<span><strong><?php echo __('Anonymous'); ?></strong></span><p class="description"><?php echo __('Display the message but never track users. This is the safest option and doesn\'t interfere with other plugins.'); ?></p></label><br />
					<label for="preset-display-once"><input type="radio" name="presets" id="preset-display-once" value="display-once" />
						<span><strong><?php echo __('Display Once'); ?></strong></span><p class="description"><?php echo __('Display the callout only once, track when added. This is the best option if you want to enable statistics.'); ?></p></label><br />
					<label for="preset-always-show"><input type="radio" name="presets" id="preset-always-show" value="always-show" />
						<span><strong><?php echo __('Always show'); ?></strong></span><p class="description"><?php echo __('Keep showing the callout every 24h until the user adds the website to the homescreen.'); ?></p></label><br />
					<label for="preset-silent"><input type="radio" name="presets" id="preset-silent" value="silent" />
						<span><strong><?php echo __('Silent'); ?></strong></span><p class="description"><?php echo __('Never show the callout but track when users add the application to the homescreen.'); ?></p></label><br />
				</fieldset></td>
			</tr></table>

			<h3><?php echo __('Application Icon', $this->_slug); ?></h3>
			<hr />
			<p><?php echo __('Choose the icon that will be installed on the user\'s homescreen.', $this->_slug); ?></p>

			<table class="form-table"><tr valign="top">
				<th scope="row"><?php echo __('Choose Icon'); ?></th>
				<td><a href="#" class="button button-hero" id="insert-media-button" title="Add Image"><span style="vertical-align:text-top" class="dashicons dashicons-format-gallery"></span> <?php echo __('Add/Change Image'); ?></a>
				<p><?php echo __('For optimal results upload a square image of at least 196x196 pixel.'); ?></p>
				<input type="hidden" name="<?php echo $this->_slug . '[application_icon]' ?>" id="application_icon" value="<?php echo esc_attr($opt['application_icon']); ?>" />
				<input type="hidden" name="<?php echo $this->_slug . '[_new_application_icon]' ?>" id="_new_application_icon" />

				<p id="icon-preview" style="<?php if ( empty($opt['application_icon']) ) { echo 'display:none;'; } ?>margin-top:1em;"><img src="<?php echo empty($opt['application_icon']) ? 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAACklEQVR4nGMAAQAABQABDQottAAAAABJRU5ErkJggg==' : wp_get_attachment_url($opt['application_icon']); ?>" width="72" height="72" alt="Application icon" /><br />
				<button type="button" class="button action" id="remove-application-icon"><span style="vertical-align:text-top" class="dashicons dashicons-no-alt"></span> Remove icon</button></p></td>

			</tr></table>

			<?php submit_button(); ?>
		</div>

		<div id="plugin-advanced-content" class="plugin-tab-content" style="display:none">
			<h3><?php echo __('Advanced Options', $this->_slug); ?></h3>
			<hr />
			<p><?php echo __('Fine tune the plugin behavior.', $this->_slug); ?></p>

			<?php //do_settings_sections($this->_slug); ?>
			<table class="form-table">
			<?php do_settings_fields( $this->_slug, $this->_slug . '-advanced' ); ?>
			</table>

			<h3><?php echo __('Detect Homescreen Mechanism', $this->_slug); ?></h3>
			<hr />
			<p><?php echo __('Choose the mechanism used to detect when the web site is added to the homescreen.', $this->_slug); ?></p>
			<table class="form-table">
			<?php do_settings_fields( $this->_slug, $this->_slug . '-detect' ); ?>
			</table>

			<?php submit_button(); ?>
		</div>

		<div id="plugin-debug-content" class="plugin-tab-content" style="display:none">
			<h3><?php echo __('Debug &amp; Development', $this->_slug); ?></h3>
			<hr />
			<p><?php echo __('Use only in development phase!', $this->_slug); ?></p>

			<table class="form-table">
			<?php do_settings_fields( $this->_slug, $this->_slug . '-debug' ); ?>
			</table>

			<table class="form-table"><tr valign="top">
				<th scope="row">&nbsp;</th>
				<td><button type="button" class="button action" id="clear-session-storage"><span style="vertical-align:text-top" class="dashicons dashicons-editor-removeformatting"></span> Clear local storage</button><p class="description">This can be useful to force the callout to show up again while testing various options during development.</p></td>
			</tr></table>

			<?php submit_button(); ?>
		</div>

		<div id="plugin-stats-content" class="plugin-tab-content" style="display:none">
<script type="text/javascript">
var cubiqAddToHomeStatsDisplay = <?php

echo '[';

foreach ( $displayData as $date => $count ) {
	echo '["' . $date . '",' . $count . '],';
}
echo '];';
?>

var cubiqAddToHomeStatsAdd = <?php

echo '[';

foreach ( $addData as $date => $count ) {
	echo '["' . $date . '",' . $count . '],';
}

echo '];';
?>

</script>
			<h3><?php echo __('Conversion rate', $this->_slug); ?></h3>
			<p><?php echo __('Number of views vs added to homescreen. Stats are available only if user tracking is enabled.', $this->_slug); ?></p>

			<table class="form-table">
			<?php do_settings_fields( $this->_slug, $this->_slug . '-stats' ); ?>
			</table>

			<div style="width:100%;height:300px;margin-bottom:10px<?php if ( $opt['stats_type'] == 'stats-ga' ) echo ';display:none' ?>" id="stats-conversion"></div>

			<?php submit_button(); ?>
		</div>

	</form>
</div>
