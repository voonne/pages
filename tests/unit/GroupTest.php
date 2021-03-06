<?php

namespace Voonne\TestPages;

use Codeception\Test\Unit;
use Mockery;
use UnitTester;
use Voonne\Pages\DuplicateEntryException;
use Voonne\Pages\Group;
use Voonne\Pages\Page;


class GroupTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var Group
	 */
	private $group;


	protected function _before()
	{
		$this->group = new Group('name', 'label');
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testInitialize()
	{
		$this->assertEquals('name', $this->group->getName());
		$this->assertEquals('label', $this->group->getTitle());
		$this->assertNull($this->group->getIcon());

		$this->group->setIcon('user');

		$this->assertEquals('user', $this->group->getIcon());
	}


	public function testAddPage()
	{
		$page1 = Mockery::mock(Page::class);
		$page2 = Mockery::mock(Page::class);

		$page1->shouldReceive('getPageName')
			->twice()
			->withNoArgs()
			->andReturn('page1');

		$page2->shouldReceive('getPageName')
			->times(4)
			->withNoArgs()
			->andReturn('page2');

		$this->group->addPage($page1);
		$this->group->addPage($page2);

		$this->assertEquals([
			'page1' => $page1,
			'page2' => $page2
		], $this->group->getPages());

		$this->expectException(DuplicateEntryException::class);
		$this->group->addPage($page2);
	}


	public function testGetVisiblePage()
	{
		$page1 = Mockery::mock(Page::class);
		$page2 = Mockery::mock(Page::class);
		$page3 = Mockery::mock(Page::class);

		$page1->shouldReceive('getPageName')
			->twice()
			->withNoArgs()
			->andReturn('page1');

		$page1->shouldReceive('isVisibleInMenu')
			->once()
			->withNoArgs()
			->andReturn(false);

		$page2->shouldReceive('getPageName')
			->twice()
			->withNoArgs()
			->andReturn('page2');

		$page2->shouldReceive('isVisibleInMenu')
			->once()
			->withNoArgs()
			->andReturn(true);

		$page2->shouldReceive('isAuthorized')
			->once()
			->withNoArgs()
			->andReturn(true);

		$page3->shouldReceive('getPageName')
			->twice()
			->withNoArgs()
			->andReturn('page3');

		$page3->shouldReceive('isVisibleInMenu')
			->once()
			->withNoArgs()
			->andReturn(true);

		$page3->shouldReceive('isAuthorized')
			->once()
			->withNoArgs()
			->andReturn(false);

		$this->group->addPage($page1);
		$this->group->addPage($page2);
		$this->group->addPage($page3);

		$this->assertEquals([
			'page2' => $page2
		], $this->group->getVisiblePages());
	}

}
