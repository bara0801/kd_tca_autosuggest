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

namespace KevinDitscheid\KdTcaAutosuggest\Form\FormDataProvider;

use TYPO3\CMS\Backend\Form\FormDataProvider\TcaSelectItems;

/**
 * Description of TcaAutosuggest
 *
 * @author Kevin Ditscheid <ditscheid@engine-productions.de>
 */
class TcaAutosuggest extends TcaSelectItems{
	/**
     * Determines whether the current field is a valid target for this DataProvider
     *
     * @param array $fieldConfig The fieldConfiguration
	 *
     * @return bool
     */
    protected function isTargetRenderType(array $fieldConfig)
    {
        return in_array(
            $fieldConfig['config']['renderType'],
            ['autosuggest'],
            true
        );
    }
}
