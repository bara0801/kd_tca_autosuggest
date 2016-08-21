<?php

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

namespace KevinDitscheid\KdTcaAutosuggest\Helpers;

use KevinDitscheid\KdTcaAutosuggest\Renderer\AutosuggestRenderer;
use TYPO3\CMS\Core\Utility\ArrayUtility;

/**
 * Description of AutosuggestHelper
 *
 * @author Kevin Ditscheid <ditscheid@engine-productions.de>
 */
class AutosuggestHelper {
	/**
	 * Get the config for the autosuggest field
	 *
	 * @param string $foreignTable The foreign table
	 * @param string $mmTable The MM table, set to NULL if not needed
	 * @param array $customSettingOverride A custom configuration
	 *
	 * @return array
	 */
	static public function getAutosuggestFieldTCAConfig($foreignTable, $mmTable = NULL, array $customSettingOverride = []){
		$config = [
			'type' => 'select',
			'renderType' => 'autosuggest',
			'foreign_table' => $foreignTable,
			'MM' => $mmTable,
			'wizards' => [
				'suggest'=> [
					'type' => 'suggest'
				]
			]
		];
		ArrayUtility::mergeRecursiveWithOverrule($config, $customSettingOverride);
		return $config;
	}
}
