<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Pages;

use Nette\SmartObject;


class Group
{

	use SmartObject;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $title;

	/**
	 * @var string|null
	 */
	private $icon;

	/**
	 * @var array
	 */
	public $pages = [];


	public function __construct($name, $title)
	{
		$this->name = $name;
		$this->title = $title;
	}


	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}


	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}


	/**
	 * @return null|string
	 */
	public function getIcon()
	{
		return $this->icon;
	}


	/**
	 * @param string|null $icon
	 */
	public function setIcon($icon)
	{
		$this->icon = $icon;
	}


	/**
	 * Adds a child item.
	 *
	 * @param Page $page
	 * @param integer $priority
	 *
	 * @throws DuplicateEntryException
	 */
	public function addPage(Page $page, $priority = 100)
	{
		if (isset($this->getPages()[$page->getPageName()])) {
			throw new DuplicateEntryException("Page with name '" . $page->getPageName() . "' in group '" . $this->getName() . "' already exists.");
		}

		$this->pages[$priority][$page->getPageName()] = $page;
	}


	/**
	 * Returns all children.
	 *
	 * @return array
	 */
	public function getPages()
	{
		$pages = [];

		krsort($this->pages);

		foreach ($this->pages as $priority) {
			foreach ($priority as $name => $page) {
				$pages[$name] = $page;
			}
		}

		return $pages;
	}


	/**
	 * Returns visible pages.
	 *
	 * @return array
	 */
	public function getVisiblePages()
	{
		$pages = [];

		foreach ($this->getPages() as $name => $page) {
			if ($page->isVisibleInMenu()) {
				$pages[$name] = $page;
			}
		}

		return $pages;
	}

}
