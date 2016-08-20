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

namespace KevinDitscheid\KdTcaAutosuggest\Renderer;

use TYPO3\CMS\Backend\Form\Element\UserElement;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Core\Database\DatabaseConnection;

/**
 * Description of AutosuggestRenderer
 *
 * @author Kevin Ditscheid <ditscheid@engine-productions.de>
 */
class AutosuggestRenderer {
	/**
	 * Render the autosuggest form field
	 *
	 * @param array $config The field config
	 * @param UserElement $userElement The user element renderer class
	 *
	 * @return string
	 */
	public function render(array $config, UserElement $userElement){
		$row = $config['row'];
		$fieldName = $config['field'];
		$pageRenderer = $this->getPageRenderer();
		$cssPath = GeneralUtility::getFileAbsFileName('EXT:kd_tca_autosuggest/Resources/Public/Stylesheets/selectize.css');
		$webCssPath = \str_replace(PATH_site, '', $cssPath);
		$pageRenderer->addCssFile('../' . $webCssPath);
		$pageRenderer->loadRequireJsModule('TYPO3/CMS/KdTcaAutosuggest/Autosuggest');
		$opt = [
			'<option value=""></option>'
		];
		$foreignTable = $config['fieldConf']['config']['foreign_table'];
		$mmTable = $config['fieldConf']['config']['MM'];
		$tableConfig = $this->getTcaTableCtrl($foreignTable);
		$selectedUids = [];
		if(is_array($config['itemFormElValue'])){
			$selectedUids = $config['itemFormElValue'];
		}elseif(is_string($config['itemFormElValue'])){
			$selectedUids = explode(',', $config['itemFormElValue']);
		}elseif(is_integer($config['itemFormElValue'])){
			$selectedUids = [$config['itemFormElValue']];
		}
		if(count($selectedUids) > 0){
			if($mmTable){
				$resultHandle = $this->getDatabase()->exec_SELECT_mm_query(
					'`' . $foreignTable . '`.`uid`, `' . $foreignTable . '`.`' . $tableConfig['label'] . '` AS "label"', 
					'`' . $config['table'] . '`', 
					'`' . $mmTable . '`', 
					'`' . $foreignTable . '`',
					' AND `' . $foreignTable . '`.`uid` IN(' . implode(',',$selectedUids) . ')'
				);
				if($resultHandle){
					$optionRows = [];
					while($row = $this->getDatabase()->sql_fetch_assoc($resultHandle)){
						$optionRows[] = $row;
					}
				}
			}else{
				$optionRows = $this->getDatabase()->exec_SELECTgetRows(
					'`' . $foreignTable . '`.`uid`, `' . $foreignTable . '`.`' . $tableConfig['label'] . '` AS "label"',
					'`' . $foreignTable . '`', 
					'`' . $foreignTable . '`.`uid` IN(' . implode(',',$selectedUids) . ')'
				);
			}
			if(is_array($optionRows)){
				foreach($optionRows as $selectedOption){
					$opt[] = '<option selected="selected" value="' . $selectedOption['uid'] . '">' . $selectedOption['label'] . '</option>';
				}
			}
			
		}
		$jsRow = '';
        if (!MathUtility::canBeInterpretedAsInteger($row['uid'])) {
            // If we have a new record, we hand that row over to JS.
            // This way we can properly retrieve the configuration of our wizard
            // if it is shown in a flexform
            $jsRow = serialize($row);
        }
		$multiple = '';
		$name = $config['itemFormElName'];
		if(
			$config['fieldConf']['config']['MM'] || 
			$config['fieldConf']['config']['maxitems'] > 1
		){
			$multiple = 'multiple="multiple" ';
		}
		$output = '<select '
                . 'id="' . $config['itemFormElID'] . '" '
                . 'data-formengine-input-name="' . htmlspecialchars($name) . '" '
				. 'data-table="' . htmlspecialchars($config['table']) . '" '
				. 'data-field="' . htmlspecialchars($fieldName) . '" '
				. 'data-uid="' . htmlspecialchars($row['uid']) . '" '
				. 'data-pid="' . (int)$row['pid'] . '" '
				. 'data-recorddata="' . htmlspecialchars($jsRow) . '" '
                . 'class="selectize" '
				. 'name="' . $name . '" '
				. $multiple
                . '>';
		$output .= implode(LF, $opt);
		$output .= '</select>';
		$output .= '<input type="hidden" value="' . implode(',', $selectedUids) . '" name="' . $name . '" />';
		return $output;
	}
	
	/**
	 * Get the PageRenderer
	 *
	 * @return PageRenderer
	 */
	protected function getPageRenderer(){
		return GeneralUtility::makeInstance(PageRenderer::class);
	}
	
	/**
	 * Get the database
	 *
	 * @return DatabaseConnection
	 */
	protected function getDatabase(){
		return $GLOBALS['TYPO3_DB'];
	}
	
	/**
	 * Get the ctrl section of a tables TCA
	 *
	 * @param string $tableName The tablename
	 *
	 * @return array
	 */
	protected function getTcaTableCtrl($tableName){
		return $GLOBALS['TCA'][$tableName]['ctrl'];
	}
}
