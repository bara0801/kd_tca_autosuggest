requirejs.config({
    "paths": {
	  "selectize": "../typo3conf/ext/kd_tca_autosuggest/Resources/Public/JavaScript/Libraries/selectize.js/selectize"
    }
});
define([
		'jquery', 'selectize'
	], function($){
		$(function(){
			$('.selectize').selectize({
				create: false,
				searchField: 'label',
				labelField: 'label',
				valueField: 'uid',
				sortField: 'label',
				onChange: function(value){
					var $hidden = this.$input.parent().find('input[name="' + this.$input.attr('name') + '"]');
					if(typeof value === "object"){
						$hidden.val(value.join());
					}else if(typeof value === "String"){
						$hidden.val(value);
					}
				},
				load: function (query, callback){
					if (!query.length) return callback();
					var table = this.$input.data('table'),
						field = this.$input.data('field'),
						uid = this.$input.data('uid'),
						pid = this.$input.data('pid'),
						newRecordRow = this.$input.data('recorddata'),
						url = TYPO3.settings.ajaxUrls['record_suggest'],
						params = {
							'table': table,
							'field': field,
							'uid': uid,
							'pid': pid,
							'newRecordRow': newRecordRow,
							'value': query
						};
					$.ajax({
						url: url,
						data: params,
						method: "POST",
						success: function (data){
							callback(data);
						},
						error: function () {
							callback();
						}
					});
				}
			});
		});
	}
);