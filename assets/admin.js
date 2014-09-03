jQuery(function ($) {

var presets = {
	'preset-anonymous': {
		'skip_first_visit': true,
		'display_pace': 1440,
		'max_display_count': 1,
		'detect-no-tracking': true,
		'silent': false
	},

	'preset-silent': {
		'skip_first_visit': true,
		'display_pace': 1440,
		'max_display_count': 0,
		'detect-hash': true,
		'silent': true
	},

	'preset-display-once': {
		'skip_first_visit': true,
		'display_pace': 1440,
		'max_display_count': 1,
		'detect-hash': true,
		'silent': false
	},

	'preset-always-show': {
		'skip_first_visit': false,
		'display_pace': 1440,
		'max_display_count': 0,
		'detect-hash': true,
		'silent': false
	}
};

// Select the settings tab
$(window).on('hashchange', selectTab);

function selectTab () {
	var hash = document.location.hash.substr(1);

	if ( !hash ) {
		return;
	}

	var $tab = $('#tab-' + hash);

	if ( $tab.size() ) {
		$('#plugin-settings-tabs a').removeClass('nav-tab-active');
		$tab.addClass('nav-tab-active');

		$('#plugin-settings-wrapper .plugin-tab-content').css('display', 'none');
		$('#plugin-' + hash + '-content').css('display', 'block');
	}
}

function updatePresets () {
	$.each(presets, function (index, parms) {
		var checked = true;

		var $field = $('#' + index);

		$field.attr('checked', false);
		$field.on('change', function () {
			$.each(parms, function (field, preset) {
				var $f = $('#' + field);
				if ( $f.prop('type') == 'checkbox' || $f.prop('type') == 'radio' ) {
					$f.attr('checked', preset);
				} else {
					$f.val(preset);
				}
			});
		});

		$.each(parms, function (field, preset) {
			var $f = $('#' + field);
			var val = $f.prop('type') == 'checkbox' || $f.prop('type') == 'radio' ? $f.prop('checked') : $f.val();
			checked = checked && val == preset;
		});

		$field.attr('checked', checked);
	});
}

selectTab();
updatePresets();
$('#skip_first_visit,#display_pace,#max_display_count,#detect-no-tracking,#detect-webapp-capable,#detect-hash,#detect-query-string,#detect-smart-url,#silent').on('change', updatePresets);

$('#clear-session-storage').click(function () {
	addToHomescreen.removeSession();
	alert('Session cleared');
});

// media upload
var appIconFrame;

$('#insert-media-button').click(function (e) {
	e.preventDefault();

	if ( appIconFrame ) {
		appIconFrame.open();
		return;
	}

	appIconFrame = wp.media.frames.appIconFrame = wp.media({
		title: 'Application Icon',
		button: {
			text: 'Set Application Icon'
		},
		multiple: false
	});

	appIconFrame.on('select', function () {
		var attachment = appIconFrame.state().get('selection').first().toJSON();
		$('#_new_application_icon').val(attachment.id);
		$('#icon-preview img').prop('src', attachment.url);
		$('#icon-preview').show(400);
	});

	appIconFrame.open();
});

// remove application icon
$('#remove-application-icon').click(function () {
	$('#icon-preview').hide(400, function () {
		$('#application_icon').val('');
		$('#_new_application_icon').val('');
		$('#icon-preview img').prop('src', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAACklEQVR4nGMAAQAABQABDQottAAAAABJRU5ErkJggg==');
	});
});

// stats
$.plot('#stats-conversion', [
	{
		label: 'views',
		data: cubiqAddToHomeStatsDisplay
	},
	{
		label: 'added',
		data: cubiqAddToHomeStatsAdd
	}],
	{
		grid: {
			hoverable: true
		},
		series: {
			lines: { show: true, fill: true },
			points: { show: true }
		},
		xaxis: {
			mode: 'categories'
		},
		yaxis: {
			minTickSize: 1,
			tickDecimals: 0
		}
	}
);

// hover tooltip
var $tooltip = $('<div id="tooltip"></div>').css({
	position: 'absolute',
	display: 'none',
	padding: '3px 5px',
	'background-color': 'rgba(0,0,0,0.65)',
	color: '#fff'
}).appendTo('body');

$('#stats-conversion').bind('plothover', function (event, pos, item) {
	if (item) {
		var x = item.datapoint[1];

		$tooltip.html(item.series.label + ' ' + x)
			.css({ top: item.pageY + 10, left: item.pageX + 10 })
			.fadeIn(200);
	} else {
		$tooltip.hide();
	}
});

$('#stats-internal, #stats-ga').on('click', function () {
	if ( $(this).val() == 'stats-ga' ) {
		$('#stats-conversion').hide();
	} else {
		$('#stats-conversion').show();
	}
});

});