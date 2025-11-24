/**
 * Persian translation for DataTables
 */
(function(factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD
		define(['jquery'], function($) {
			return factory($, window, document);
		});
	}
	else if (typeof exports === 'object') {
		// CommonJS
		var jq = require('jquery');
		module.exports = function(root, $) {
			if (!root) {
				root = window;
			}
			if (!$) {
				$ = jq;
			}
			return factory($, root, root.document);
		};
	}
	else {
		// Browser
		factory(jQuery, window, document);
	}
}
(function($, window, document) {
	'use strict';
	
	var language = {
		"processing": "در حال پردازش...",
		"search": "جستجو:",
		"lengthMenu": "نمایش _MENU_ سطر",
		"info": "نمایش _START_ تا _END_ از _TOTAL_ سطر",
		"infoEmpty": "نمایش 0 تا 0 از 0 سطر",
		"infoFiltered": "(فیلتر شده از _MAX_ سطر)",
		"loadingRecords": "در حال بارگذاری...",
		"zeroRecords": "رکوردی یافت نشد",
		"emptyTable": "جدول خالی است",
		"paginate": {
			"first": "ابتدا",
			"previous": "قبلی",
			"next": "بعدی",
			"last": "انتها"
		},
		"aria": {
			"sortAscending": ": برای مرتب‌سازی صعودی فعال کنید",
			"sortDescending": ": برای مرتب‌سازی نزولی فعال کنید"
		},
		"select": {
			"rows": {
				"_": "%d سطر انتخاب شد",
				"0": "",
				"1": "1 سطر انتخاب شد"
			}
		}
	};

	$.extend(true, $.fn.dataTable.defaults, {
		language: language
	});

	return language;
})); 