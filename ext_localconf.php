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
defined('TYPO3_MODE') or die();

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeResolver'][1471768437] = [
        'nodeName' => 'autosuggest',
        'priority' => 50,
        'class' => \KevinDitscheid\KdTcaAutosuggest\Form\Resolver\AutosuggestNodeResolver::class,
];
$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][\KevinDitscheid\KdTcaAutosuggest\Form\FormDataProvider\TcaAutosuggest::class] = [
	'depends' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][\TYPO3\CMS\Backend\Form\FormDataProvider\TcaSelectItems::class]['depends']
];