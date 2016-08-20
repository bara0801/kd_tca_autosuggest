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

/**
 * Description of AutosuggestHelper
 *
 * @author Kevin Ditscheid <ditscheid@engine-productions.de>
 */
class AutosuggestHelper {
	/**
	 * Get the config for the autosuggest field
	 *
	 * @param string $fieldName
	 * @param string $foreignTable
	 * @param array $customSettingOverride
	 *
	 * @return array
	 */
	public function getAutosuggestFieldTCAConfig($fieldName, $foreignTable, array $customSettingOverride = []){
		return [
			'type' => 'user',
			'userFunc'=> AutosuggestRenderer::class . '->render',
			'parameters' => [
				'foreign_table' => $foreignTable,
				'MM' => $MmTable,
				''
			]
		];
	}
}
