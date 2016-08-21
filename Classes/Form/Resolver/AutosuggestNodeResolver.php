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

namespace KevinDitscheid\KdTcaAutosuggest\Form\Resolver;

use TYPO3\CMS\Backend\Form\NodeResolverInterface;
use TYPO3\CMS\Backend\Form\NodeFactory;
use KevinDitscheid\KdTcaAutosuggest\Form\Element\AutosuggestElement;

/**
 * Description of AutosuggestNodeResolver
 *
 * @author Kevin Ditscheid <ditscheid@engine-productions.de>
 */
class AutosuggestNodeResolver implements NodeResolverInterface{
	/**
     * Global options from NodeFactory
     *
     * @var array
     */
    protected $data;
	
	/**
	 * Construct the AutosuggestNodeResolver
	 *
	 * @param NodeFactory $nodeFactory The node factory object
	 * @param array $data The global options
	 *
	 * @return self
	 */
	public function __construct(NodeFactory $nodeFactory, array $data) {
		$this->data = $data;
	}

	/**
	 * Resolve the AutosuggestElement class
	 *
	 * @return AutosuggestElement
	 */
	public function resolve() {
		return AutosuggestElement::class;
	}

}
