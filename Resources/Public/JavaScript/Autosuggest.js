/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

requirejs.config({
    "paths": {
		"jqueryui": "../typo3conf/ext/kd_tca_autosuggest/Resources/Public/JavaScript/Libraries/jquery-ui/jquery-ui",
		"selectize": "../typo3conf/ext/kd_tca_autosuggest/Resources/Public/JavaScript/Libraries/selectize.js/selectize"
    }
});
define([
		'jquery', 'jqueryui', 'selectize'
	], function($){
		$(function(){
			$('.selectize').selectize({
				create: false,
				searchField: 'label',
				labelField: 'label',
				valueField: 'uid',
				sortField: 'label',
				plugins: ['drag_drop'],
				render: {
					option: function(item, escape){
						// entity decode
						var sprite = $('<div />').html(item.sprite).text();
						return '<div>' + sprite + item.label + '</div>';
					},
					item: function(item, escape){
						// entity decode
						var sprite = $('<div />').html(item.sprite).text();
						return '<div>' + sprite + item.label + '</div>';
					}
				},
				onChange: function(value){
					var name = this.$input.data('formengine-input-name'),
						$hidden = this.$input.parent().find('input[name="' + name + '"]');
					if(value !== '' && value !== [] && value !== null){
						$hidden.val(value.join());
					}
				},
				onItemAdd: function(value, $item){
					var selectizeObject = this;
					$item.on('click',function(event) {
						event.preventDefault();
						selectizeObject.$input.find('option[value="' + value + '"]').trigger('focus');
					});
					var name = this.$input.data('formengine-input-name'),
						$hidden = this.$input.parent().find('input[name="' + name + '"]');
					if($hidden.val() !== ''){
						var hiddenValues = $hidden.val().split(',');
						if(typeof hiddenValues === "object"){
							hiddenValues.push(value);
						}else{
							hiddenValues = [value]
						}
						$hidden.val(hiddenValues.join());
					}else{
						$hidden.val(value);
					}
				},
				onItemRemove: function(value){
					var name = this.$input.data('formengine-input-name'),
						$hidden = this.$input.parent().find('input[name="' + name + '"]');
					if($hidden.val() !== ''){
						var hiddenValues = $hidden.val().split(',');
						if(typeof hiddenValues === "object"){
							var indexOfValue = hiddenValues.indexOf(value),
								newValues = [];
							if(indexOfValue >= 0){
								$.each(hiddenValues,function(index){
									if(index !== indexOfValue){
										newValues.push(hiddenValues[index]);
									}
								});
								$hidden.val(newValues.join());
							}
						}
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