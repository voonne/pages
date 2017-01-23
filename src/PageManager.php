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
use Voonne\Layouts\LayoutManager;
use Voonne\Panels\Renderers\RendererManager;
use Voonne\Security\User;
use Voonne\Voonne\Content\ContentForm;


class PageManager
{

	use SmartObject;

	/**
	 * @var LayoutManager
	 */
	private $layoutManager;

	/**
	 * @var RendererManager
	 */
	private $rendererManager;

	/**
	 * @var ContentForm
	 */
	private $contentForm;

	/**
	 * @var User
	 */
	private $user;

	/**
	 * @var array
	 */
	private $groups = [];


	public function __construct(
		LayoutManager $layoutManager,
		RendererManager $rendererManager,
		ContentForm $contentForm,
		User $user)
	{
		$this->layoutManager = $layoutManager;
		$this->rendererManager = $rendererManager;
		$this->contentForm = $contentForm;
		$this->user = $user;
	}


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

		$page->injectPrimary(
			$this->layoutManager,
			$this->rendererManager,
			$this->contentForm,
			$this->user);

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
