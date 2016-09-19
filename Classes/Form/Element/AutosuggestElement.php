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

namespace KevinDitscheid\KdTcaAutosuggest\Form\Element;

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\Icon;

/**
 * Description of AutosuggestElement
 *
 * @author Kevin Ditscheid <ditscheid@engine-productions.de>
 */
class AutosuggestElement extends AbstractFormElement {

	/**
	 * Main result array as defined in initializeResultArray() of AbstractNode
	 *
	 * @var array
	 */
	protected $resultArray;

	/**
	 * Render the autosuggest element
	 *
	 * @return array
	 */
	public function render () {
		$this->resultArray = $this->initializeResultArray();
		$this->addRequireJsModule();
		$this->addStylesheet();
		$table = $this->data['tableName'];
		$fieldName = $this->data['fieldName'];
		$row = $this->data['databaseRow'];
		$config = $this->data['parameterArray'];
		$disabled = !empty($config['fieldConf']['config']['readOnly']);

		$opt = [
				'<option value=""></option>'
		];
		$foreignTable = $config['fieldConf']['config']['foreign_table'];
		$mmTable = $config['fieldConf']['config']['MM'];
		$tableConfig = $this->getTcaTableCtrl($foreignTable);
		$selectedUids = [ ];
		if ( is_array($config['itemFormElValue']) ) {
			$selectedUids = $config['itemFormElValue'];
		} elseif ( is_string($config['itemFormElValue']) ) {
			$selectedUids = explode(',', $config['itemFormElValue']);
		} elseif ( is_integer($config['itemFormElValue']) ) {
			$selectedUids = [$config['itemFormElValue'] ];
		}
		if ( count($selectedUids) > 0 ) {
			if ( $mmTable ) {
				$resultHandle = $this->getDatabase()->exec_SELECTquery(
					'`foreign_table`.`uid`, `foreign_table`.`' . $tableConfig['label'] . '` AS "label"',
					'`' . $table . '` AS `local_table`,`' . $mmTable . '`,`' . $foreignTable . '` AS `foreign_table`',
					'`local_table`.uid=`' . $mmTable . '`.uid_local AND ' .
					'`foreign_table`.`uid`=`' . $mmTable . '`.`uid_foreign` AND ' .
					'`local_table`.`uid` IN (' . (int) $row['uid'] . ')'
				);
				if ( $resultHandle ) {
					$optionRows = [ ];
					while ( $optionRow = $this->getDatabase()->sql_fetch_assoc($resultHandle) ) {
						$optionRows[] = $optionRow;
					}
				}
			} else {
				$optionRows = $this->getDatabase()->exec_SELECTgetRows(
					'`' . $foreignTable . '`.`uid`, `' . $foreignTable . '`.`' . $tableConfig['label'] . '` AS "label"', $foreignTable,
					'`' . $foreignTable . '`.`uid` IN(' . implode(',', $selectedUids) . ')'
				);
			}
			if ( is_array($optionRows) ) {
				$iconFactory = GeneralUtility::makeInstance(IconFactory::class);
				foreach ( $optionRows as $selectedOption ) {
					$spriteIcon = $iconFactory->getIconForRecord($foreignTable, $selectedOption, Icon::SIZE_SMALL)->render();
					$selectizeData = [
							'sprite' => htmlentities($spriteIcon),
							'uid' => $selectedOption['uid'],
							'label' => $selectedOption['label']
					];
					$opt[] = '<option selected="selected" data-data="' . htmlentities(json_encode($selectizeData)) . '" value="' . $selectedOption['uid'] . '">' . $selectedOption['label'] . '</option>';
				}
			}
		}
		$jsRow = '';
		if ( !MathUtility::canBeInterpretedAsInteger($row['uid']) ) {
			// If we have a new record, we hand that row over to JS.
			// This way we can properly retrieve the configuration of our wizard
			// if it is shown in a flexform
			$jsRow = serialize($row);
		}
		$multiple = '';
		$name = $config['itemFormElName'];
		if (
			$config['fieldConf']['config']['MM'] ||
			$config['fieldConf']['config']['maxitems'] > 1
		) {
			$multiple = 'multiple="multiple" ';
		}
		$output = '<input type="hidden" data-formengine-input-name="' . htmlspecialchars($name) . '" />'
			. '<select ' . ($disabled ? 'disabled="disabled"' : '')
			. 'id="' . $config['itemFormElID'] . '" '
			. 'data-formengine-input-name="' . htmlspecialchars($name) . '" '
			. 'data-table="' . htmlspecialchars($table) . '" '
			. 'data-field="' . htmlspecialchars($fieldName) . '" '
			. 'data-uid="' . htmlspecialchars($row['uid']) . '" '
			. 'data-pid="' . (int) $row['pid'] . '" '
			. 'data-recorddata="' . htmlspecialchars($jsRow) . '" '
			. 'class="selectize" '
			. 'name="' . $name . '" '
			. $multiple
			. '>';
		$output .= implode(LF, $opt);
		$output .= '</select>';
		$output .= '<input type="hidden" value="' . implode(',', $selectedUids) . '" name="' . $name . '" />';
		// remove suggest wizard now, because we don't need it anymore
		unset($config['fieldConf']['config']['wizards']['suggest']);
		if ( !$disabled ) {
			$this->resultArray['html'] = $this->renderWizards(
				array( $output ), $config['fieldConf']['config']['wizards'], $table, $row, $fieldName, $config, $config['itemFormElName'],
				BackendUtility::getSpecConfParts($config['fieldConf']['defaultExtras'])
			);
		} else {
			$this->resultArray['html'] = $output;
		}
		return $this->resultArray;
	}

	/**
	 * Add the require JS module to the resultArray
	 *
	 * @return void
	 */
	protected function addRequireJsModule () {
		$this->resultArray['requireJsModules'][] = 'TYPO3/CMS/KdTcaAutosuggest/Autosuggest';
	}

	/**
	 * Add the stylesheet to the resultArray
	 *
	 * @return void
	 */
	protected function addStylesheet () {
		$cssPath = GeneralUtility::getFileAbsFileName('EXT:kd_tca_autosuggest/Resources/Public/Stylesheets/selectize.css');
		$webCssPath = '../' . str_replace(PATH_site, '', $cssPath);
		$this->resultArray['stylesheetFiles'][] = $webCssPath;
	}

	/**
	 * Get the database
	 *
	 * @return DatabaseConnection
	 */
	protected function getDatabase () {
		return $GLOBALS['TYPO3_DB'];
	}

	/**
	 * Get the ctrl section of a tables TCA
	 *
	 * @param string $tableName The tablename
	 *
	 * @return array
	 */
	protected function getTcaTableCtrl ($tableName) {
		return $GLOBALS['TCA'][$tableName]['ctrl'];
	}

}
