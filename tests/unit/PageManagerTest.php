<?php

namespace Voonne\TestPages;

use Codeception\Test\Unit;
use Mockery;
use UnitTester;
use Voonne\Pages\DuplicateEntryException;
use Voonne\Pages\Page;
use Voonne\Pages\PageManager;


class PageManagerTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var PageManager
	 */
	private $pageManager;


	protected function _before()
	{
		$this->pageManager = new PageManager();
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


		$page2->shouldReceive('getPageName')
			->twice()
			->withNoArgs()
			->andReturn('page2');

		$this->pageManager->addPage('group1', $page1);
		$this->pageManager->addPage('group1', $page2);

		$this->assertEquals([
			'page1' => $page1,
			'page2' => $page2
		], $group->getPages());
	}

}
