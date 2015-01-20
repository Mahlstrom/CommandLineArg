<?php
/**
 * @author Magnus Ahlstrom <magnus@atuin.se>
 * @time 2015-01-19 22:49
 */
namespace mahlstrom\test;

use mahlstrom\CommandLineArg;

/**
 * Class CommandLineArgTest
 */
class CommandLineArgTest extends \PHPUnit_Framework_TestCase
{

    protected $expStr;

    public function setUp()
    {
        $this->expStr = 'Usage: ide-phpunit.php [OPTIONS]... '.PHP_EOL;
        $this->expStr .= '  -a          --ano                       Description'.PHP_EOL;
        $this->expStr .= '  -b [<arg>]  --bnn[=<arg>]               Description'.PHP_EOL;
        $this->expStr .= '  -c <arg>    --cny=<arg>                 Description'.PHP_EOL;
        $this->expStr .= '  -d          --dyo                       Description (required)'.PHP_EOL;
        $this->expStr .= '  -e [<arg>]  --eyn[=<arg>]               Description (required)'.PHP_EOL;
        $this->expStr .= '  -f <arg>    --fyy=<arg>                 Description (required)'.PHP_EOL;
        CommandLineArg::reset();
        CommandLineArg::addArgument('ano', 'a', 'Description');
        CommandLineArg::addArgument('bnn', 'b', 'Description', false, false);
        CommandLineArg::addArgument('cny', 'c', 'Description', false, true);
        CommandLineArg::addArgument('dyo', 'd', 'Description', true);
        CommandLineArg::addArgument('eyn', 'e', 'Description', true, false);
        CommandLineArg::addArgument('fyy', 'f', 'Description', true, true);
    }

    /**
     * @test
     */
    public function testAllGoodArguments()
    {
        $arguments=[
            '',
            '-a',
            '-b',
            '-c','cc',
            '-e',
            '-f','ff',
            '-d',
        ];
        CommandLineArg::parse($arguments);
        $this->assertTrue(CommandLineArg::get('ano'));
        $this->assertTrue(CommandLineArg::get('bnn'));
        $this->assertEquals('cc', CommandLineArg::get('cny'));
        $this->assertTrue(CommandLineArg::get('dyo'));
        $this->assertTrue(CommandLineArg::get('eyn'));
        $this->assertEquals('ff', CommandLineArg::get('fyy'));
    }

    public function testAllDoubleDashes()
    {
        $arguments=[
            '',
            '--ano=aa',
            '--bnn',
            '--cny=cc',
            '--dyo=dd',
            '--eyn',
            '--fyy=ff'
        ];
        CommandLineArg::parse($arguments);
        $this->assertEquals('aa', CommandLineArg::get('ano'));
        $this->assertTrue(CommandLineArg::get('bnn'));
        $this->assertEquals('cc', CommandLineArg::get('cny'));
        $this->assertEquals('dd', CommandLineArg::get('dyo'));
        $this->assertTrue(CommandLineArg::get('eyn'));
        $this->assertEquals('ff', CommandLineArg::get('fyy'));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function globalArgs()
    {
        CommandLineArg::parse();
    }

    /**
     * @test
     */
    public function showHelp()
    {
        $this->expectOutputString($this->expStr);
        CommandLineArg::parse(['', '-h']);
        CommandLineArg::reset();
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function checkInvalidArgument()
    {
        CommandLineArg::reset();
        CommandLineArg::parse(['', '-wrong']);
    }

    /**
     * @test
     */
    public function doRequiredFail()
    {
        $expStr='dyo is required'.PHP_EOL;
        $expStr.='eyn is required'.PHP_EOL;
        $expStr.='fyy is required'.PHP_EOL;
        $expStr.=$this->expStr;
        $this->expectOutputString($expStr);
        CommandLineArg::parse(['', '-a']);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage cny must have value.
     */
    public function doRequiredValueFail()
    {
        $arguments=[
            '',
            '-a',
            '-b',
            '-c',
            '-f','ff',
            '-e',
            '-d',
        ];
        CommandLineArg::parse($arguments);
    }

    /**
     * @test
     */
    public function doNoRequiredValue()
    {
        $expStr='bnn needs no argument'.PHP_EOL;
        $expStr.='eyn needs no argument'.PHP_EOL;
        $this->expectOutputString($expStr);
        $arguments=[
            '',
            '-a','argument',
            '-b','argument',
            '-c','argument',
            '-d','argument',
            '-e','argument',
            '-f','ff'
        ];
        CommandLineArg::parse($arguments);
    }

    /**
     * @test
     */
    public function tryGetWrongValue()
    {
        $this->assertFalse(CommandLineArg::get('fea'));
    }
}
