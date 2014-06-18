<?php

namespace tests\BBCParser;

use BBCParser\Parser;

class ParserTest extends \PHPUnit_Framework_TestCase
{
	protected $parser = NULL;

	public function setUp()
    {
    	$parser = new Parser();
    	/*
    	$parser = $this->getMock('Parser', array('setPageSource'));

		$parser->expects($this->any())
			->method('setPageSource')
				->with('something');
		*/

	   	if(!$this->parser)
       		$this->parser = $parser;
    }

    public function testFetchedData()
    {
		// at least one module must be present
		$this->assertContains('class="module"', $this->parser->pageSource);
    }

	public function testInitialModulesDataArrayShouldBeEmpty()
	{
		$this->assertEmpty($this->parser->returnDataForModule("News"));
	}

    public function testReturnedDataForModule()
    {
    	$this->parser->parseAndSerializeData();
	  	$this->assertNotEmpty($this->parser->returnDataForModule("Sport"));
    }
}