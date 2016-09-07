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


class PageManager
{

	use SmartObject;

	/**
	 * @var array
	 */
	private $groups = [];


	/**
	 * Adds new a group.
	 *
	 * @param string $name
	 * @param string $label
	 * @param string|null $icon
	 * @param integer|null $priority
	 *
	 * @return Group
	 *
	 * @throws DuplicateEntryException
	 */
	public function addGroup($name, $label, $icon = null, $priority = 100)
	{
		if (isset($this->getGroups()[$name])) {
			throw new DuplicateEntryException("Group is named '$name' already exists.");
		}

		$this->groups[$priority][$name] = $group = new Group($name, $label);
		$group->setIcon($icon);

		return $group;
	}


	/**
	 * Adds new a page.
	 *
	 * @param string $groupName
	 * @param Page $page
	 * @param integer $priority
	 *
	 * @return Page
	 *
	 * @throws InvalidArgumentException
	 */
	public function addPage($groupName, Page $page, $priority = 100)
	{
		if (!isset($this->getGroups()[$groupName])) {
			throw new InvalidArgumentException("Group named '$groupName' does not exist.");
		}

		$this->getGroups()[$groupName]->addPage($page, $priority);

		return $page;
	}


	/**
	 * Returns all groups.
	 *
	 * @return array
	 */
	public function getGroups()
	{
		$groups = [];

		krsort($this->groups);

		foreach ($this->groups as $priority) {
			foreach ($priority as $name => $group) {
				$groups[$name] = $group;
			}
		}

		return $groups;
	}

}
