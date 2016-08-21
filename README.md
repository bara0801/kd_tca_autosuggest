# kd_tca_autosuggest
Provides a new render type for TCA select type.
For example:
```
$GLOBALS['TCA']['my_table']['columns']['example_column'] => [
	'label' => 'Example column',
	'config' => [
		'type' => 'select',
		'renderType' => 'autosuggest',
		'foreign_table' => 'tt_content'
	]
];
```
The configuration options are the same as of other select renderTypes. 
Please visit the [TCA Reference](https://docs.typo3.org/typo3cms/TCAReference/Reference/Columns/Select/Index.html) for more information.

The autosuggest field is done with [selectize.js](https://github.com/selectize/selectize.js).
