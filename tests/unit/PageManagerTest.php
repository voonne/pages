<?php

namespace Voonne\TestPages;

use Codeception\Test\Unit;
use Mockery;
use Mockery\MockInterface;
use UnitTester;
use Voonne\Layouts\LayoutManager;
use Voonne\Pages\DuplicateEntryException;
use Voonne\Pages\Page;
use Voonne\Pages\PageManager;
use Voonne\Panels\Renderers\RendererManager;
use Voonne\Security\User;
use Voonne\Voonne\Content\ContentForm;


class PageManagerTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var MockInterface
	 */
	private $layoutManager;

	/**
	 * @var MockInterface
	 */
	private $rendererManager;

	/**
	 * @var MockInterface
	 */
	private $contentForm;

	/**
	 * @var MockInterface
	 */
	private $user;

	/**
	 * @var PageManager
	 */
	private $pageManager;


	protected function _before()
	{
		$this->layoutManager = Mockery::mock(LayoutManager::class);
		$this->rendererManager = Mockery::mock(RendererManager::class);
		$this->contentForm = Mockery::mock(ContentForm::class);
		$this->user = Mockery::mock(User::class);

		$this->pageManager = new PageManager($this->layoutManager, $this->rendererManager, $this->contentForm, $this->user);
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testAddGroup()
	{
		$this->pageManager->addGroup('users', 'Users');
		$this->pageManager->addGroup('options', 'Options', 'cog');

		$groups = $this->pageManager->getGroups();

		$this->assertCount(2, $groups);

		$this->assertEquals('Users', $groups['users']->getTitle());
		$this->assertNull($groups['users']->getIcon());

		$this->assertEquals('Options', $groups['options']->getTitle());
		$this->assertEquals('cog', $groups['options']->getIcon());
	}


	public function testAddGroupDuplicateEntry()
	{
		$this->pageManager->addGroup('users', 'Users');

		$this->expectException(DuplicateEntryException::class);
		$this->pageManager->addGroup('users', 'Users');
	}


	public function testAddPage()
	{
		$group = $this->pageManager->addGroup('group1', 'Group');
		$page1 = Mockery::mock(Page::class);
		$page2 = Mockery::mock(Page::class);

		$page1->shouldReceive('getPageName')
			->twice()
			->withNoArgs()
			->andReturn('page1');

		$page1->shouldReceive('injectPrimary')
			->once()
			->with($this->layoutManager, $this->rendererManager, $this->contentForm, $this->user);

		$page2->shouldReceive('getPageName')
			->twice()
			->withNoArgs()
			->andReturn('page2');

		$page2->shouldReceive('injectPrimary')
			->once()
			->with($this->layoutManager, $this->rendererManager, $this->contentForm, $this->user);

		$this->pageManager->addPage('group1', $page1);
		$this->pageManager->addPage('group1', $page2);

		$this->assertEquals([
			'page1' => $page1,
			'page2' => $page2
		], $group->getPages());
	}

}
