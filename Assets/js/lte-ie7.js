/* Load this script using conditional IE comments if you need to support IE 7 and IE 6. */

window.onload = function() {
	function addIcon(el, entity) {
		var html = el.innerHTML;
		el.innerHTML = '<span style="font-family: \'moviesdatabase\'">' + entity + '</span>' + html;
	}
	var icons = {
			'icon-home' : '&#xe000;',
			'icon-heart' : '&#xe005;',
			'icon-heart-2' : '&#xe006;',
			'icon-list' : '&#xe00e;',
			'icon-envelope' : '&#xe00f;',
			'icon-star' : '&#xf005;',
			'icon-star-empty' : '&#xf006;',
			'icon-film' : '&#xf008;',
			'icon-trophy' : '&#xf091;',
			'icon-eye-open' : '&#xf06e;',
			'icon-comments' : '&#xf086;',
			'icon-comment' : '&#xf075;',
			'icon-twitter' : '&#xf099;',
			'icon-facebook' : '&#xf09a;',
			'icon-google-plus' : '&#xf0d5;',
			'icon-pinterest' : '&#xf0d2;',
			'icon-code' : '&#xf121;',
			'icon-youtube' : '&#xf167;',
			'icon-female' : '&#xf182;',
			'icon-male' : '&#xf183;',
			'icon-link' : '&#xe001;',
			'icon-earth' : '&#xe002;',
			'icon-statistics' : '&#xe003;',
			'icon-images' : '&#xe004;',
			'icon-image' : '&#xe010;',
			'icon-play' : '&#xe007;',
			'icon-certificate' : '&#xf0a3;',
			'icon-info' : '&#xf129;',
			'icon-directions' : '&#xe008;',
			'icon-tag' : '&#xe009;',
			'icon-docs' : '&#xe00a;',
			'icon-pictures' : '&#xe00b;',
			'icon-download' : '&#xe00d;',
			'icon-movie' : '&#xe011;',
			'icon-fullscreen' : '&#xf0b2;',
			'icon-save' : '&#xf0c7;',
			'icon-info-2' : '&#xe025;',
			'icon-thumbs-up' : '&#xe014;',
			'icon-thumbs-up-2' : '&#xe015;',
			'icon-heart-3' : '&#xe016;',
			'icon-heart-4' : '&#xe01b;',
			'icon-heart-broken' : '&#xe017;',
			'icon-fire' : '&#xe018;',
			'icon-flow-tree' : '&#xe019;',
			'icon-list-2' : '&#xe00c;',
			'icon-grid' : '&#xe01a;',
			'icon-none' : '&#xe03b;',
			'icon-users' : '&#xe01c;',
			'icon-user' : '&#xe01d;',
			'icon-plus' : '&#xe01e;',
			'icon-munis' : '&#xe01f;',
			'icon-checkmark' : '&#xe020;',
			'icon-cancel' : '&#xe021;',
			'icon-equals' : '&#xe022;',
			'icon-clock' : '&#xe023;',
			'icon-trophy-2' : '&#xe024;',
			'icon-target' : '&#xe026;',
			'icon-list-3' : '&#xe012;',
			'icon-image-2' : '&#xe013;',
			'icon-camera' : '&#xe027;'
		},
		els = document.getElementsByTagName('*'),
		i, attr, c, el;
	for (i = 0; ; i += 1) {
		el = els[i];
		if(!el) {
			break;
		}
		attr = el.getAttribute('data-icon');
		if (attr) {
			addIcon(el, attr);
		}
		c = el.className;
		c = c.match(/icon-[^\s'"]+/);
		if (c && icons[c[0]]) {
			addIcon(el, icons[c[0]]);
		}
	}
};