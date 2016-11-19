<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Pages;

use Voonne\Controls\Control;
use Voonne\Layouts\Layout;
use Voonne\Layouts\Layout1\Layout1;
use Voonne\Layouts\LayoutManager;
use Voonne\Panels\Panels\Panel;
use Voonne\Panels\Panels\PanelManager;
use Voonne\Panels\Renderers\RendererManager;
use Voonne\Voonne\Content\ContentForm;


abstract class Page extends Control
{

	/**
	 * @var LayoutManager
	 */
	private $layoutManager;

	/**
	 * @var RendererManager
	 */
	private $rendererManager;

	/**
	 * @var PanelManager
	 */
	private $panelManager;

	/**
	 * @var ContentForm
	 */
	private $contentForm;

	/**
	 * @var string
	 */
	private $pageName;

	/**
	 * @var string
	 */
	private $pageTitle;

	/**
	 * @var bool
	 */
	private $visibleInMenu = true;

	/**
	 * @var string
	 */
	private $layout = Layout1::class;


	public function __construct($pageName, $pageTitle)
	{
		parent::__construct();

		$this->pageName = $pageName;
		$this->pageTitle = $pageTitle;
		$this->panelManager = new PanelManager();
	}


	/**
	 * @return string
	 */
	public function getPageName()
	{
		return $this->pageName;
	}


	/**
	 * @return string
	 */
	public function getPageTitle()
	{
		return $this->pageTitle;
	}


	/**
	 * @return bool
	 */
	public function isVisibleInMenu()
	{
		return $this->visibleInMenu;
	}


	/**
	 * Sets as visible.
	 */
	public function showInMenu()
	{
		$this->visibleInMenu = true;
	}


	/**
	 * Sets as hide.
	 */
	public function hideFromMenu()
	{
		$this->visibleInMenu = false;
	}


	/**
	 * @param string $layout
	 */
	public function setLayout($layout)
	{
		if(!is_subclass_of($layout, Layout::class)) {
			throw new InvalidArgumentException("Layout class must be child of '" . Layout::class . "', '"  . $layout . "' given.");
		}

		$this->layout = $layout;
	}


	/**
	 * @param Panel $panel
	 * @param array $tags
	 * @param int $priority
	 *
	 * @throws InvalidArgumentException
	 * @throws DuplicateEntryException
	 */
	public function addPanel(Panel $panel, array $tags, $priority = 100)
	{
		$this->panelManager->addPanel($panel, $tags, $priority);
	}


	/**
	 * @param LayoutManager $layoutManager
	 * @param RendererManager $rendererManager
	 * @param ContentForm $contentForm
	 */
	public function injectPrimary(LayoutManager $layoutManager, RendererManager $rendererManager, ContentForm $contentForm)
	{
		if($this->layoutManager !== null) {
			throw new InvalidStateException('Method ' . __METHOD__ . ' is intended for initialization and should not be called more than once.');
		}

		$this->layoutManager = $layoutManager;
		$this->rendererManager = $rendererManager;
		$this->contentForm = $contentForm;
	}


	public function beforeRender()
	{
		parent::beforeRender();

		$layout = $this->layoutManager->getLayout($this->layout);

		$layout->injectPrimary(
			$this->rendererManager,
			$this->panelManager,
			$this->contentForm
		);

		$this->addComponent($layout, 'layout');
	}


	public function render()
	{
		$this->template->setFile(__DIR__ . '/Page.latte');

		$this->template->render();
	}

}
