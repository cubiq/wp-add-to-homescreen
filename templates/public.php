<?php if ( $isCompatible ): ?>

<?php if ( $opt['meta_tags'] ): ?>

	<?php if ( !empty($opt['webapp_capable']) ): ?>
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="mobile-web-app-capable" content="yes">
	<?php endif; ?>

	<?php if ( !empty($opt['title']) ): ?>
	<meta name="apple-mobile-web-app-title" content="<?php echo $opt['title'] ?>">
	<?php endif; ?>

	<?php if ( $opt['application_icon'] && $opt['meta_tags'] ):
	$baseurl = wp_upload_dir();
	$baseurl = $baseurl['baseurl'] . '/' . $this->_slug . '/application-icon-' . $opt['application_icon'];
	?>
	<link rel="shortcut icon" sizes="196x196" href="<?php echo $baseurl . '-196x196.png'; ?>">
	<link rel="apple-touch-icon-precomposed" sizes="152x152" href="<?php echo $baseurl . '-152x152.png'; ?>">
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo $baseurl . '-144x144.png'; ?>">
	<link rel="apple-touch-icon-precomposed" sizes="120x120" href="<?php echo $baseurl . '-120x120.png'; ?>">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo $baseurl . '-114x114.png'; ?>">
	<link rel="apple-touch-icon-precomposed" sizes="76x76" href="<?php echo $baseurl . '-76x76.png'; ?>">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo $baseurl . '-72x72.png'; ?>">
	<?php endif; ?>

<?php endif; ?>

<script type="text/javascript">
var addtohome = addToHomescreen({
<?php if ( $opt['detect_homescreen'] != 'false' || !empty($opt['webapp_capable']) ): ?>
	<?php if ( $opt['stats_type'] == 'stats-ga' ): ?>
	onShow: function () {
		if ( typeof ga != 'undefined' ) {
			ga('send', 'event', 'ATH', 'show', 'Message displayed');
		}
	},
	onAdd: function () {
		if ( typeof ga != 'undefined' ) {
			ga('send', 'event', 'ATH', 'add', 'Added to the homescreen');
		}
	},
	<?php else: ?>
	onShow: function () {
		jQuery.post('<?php echo $ajaxURL; ?>', {
			action: 'ath_stats',
			a: 1,
			o: addToHomescreen.OS,
			d: addToHomescreen.isTablet
		});
	},
	onAdd: function () {
		jQuery.post('<?php echo $ajaxURL; ?>', {
			action: 'ath_stats',
			a: 2,
			o: addToHomescreen.OS,
			d: addToHomescreen.isTablet
		});
	},
	<?php endif; ?>
<?php endif; ?>
	autostart: false,
	appID: '<?php echo $opt['application_id']; ?>',
	icon: <?php echo $opt['display_icon']; ?>,
	skipFirstVisit: <?php echo $opt['skip_first_visit']; ?>,
	displayPace: <?php echo $opt['display_pace']; ?>,
	maxDisplayCount: <?php echo $opt['max_display_count']; ?>,
	startDelay: <?php echo $opt['start_delay']; ?>,
	lifespan: <?php echo $opt['lifespan']; ?>,
	detectHomescreen: <?php echo $opt['detect_homescreen']; ?>,
	debug: <?php echo $opt['debug']; ?>,
	modal: <?php echo $opt['modal']; ?>,
	message: '<?php echo $opt['message']; ?>'

});
<?php if ( empty($opt['silent']) ): ?>
window.addEventListener('load', addtohome.show.bind(addtohome,false), false);
<?php endif; ?>
</script>
<?php else: ?>
<script type="text/javascript">
// Add to homescreen URL cleaning
var _reSmartURL = /\/ath(\/)?$/;
var _reQueryString = /([\?&]ath=[^&]*$|&ath=[^&]*(&))/;
if ( document.location.hash == '#ath' ) {
	history.replaceState('', window.document.title, document.location.href.split('#')[0]);
}
if ( _reSmartURL.test(document.location.href) ) {
	history.replaceState('', window.document.title, document.location.href.replace(_reSmartURL, '$1'));
}
if ( _reQueryString.test(document.location.search) ) {
	history.replaceState('', window.document.title, document.location.href.replace(_reQueryString, '$2'));
}
</script>
<?php endif; ?>
