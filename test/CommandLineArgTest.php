<?php
/**
 * @author Magnus Ahlstrom <magnus@atuin.se>
 * @time 2015-01-19 22:49
 */
use mahlstrom\CommandLineArg;


/**
 * Class CommandLineArgTest
 */
class CommandLineArgTest extends PHPUnit_Framework_TestCase
{

    protected $expStr;

    public function setUp()
    {
#        $this->expStr .= 'Usage: ide-phpunit.php [OPTIONS]... ' . PHP_EOL;
#        $this->expStr .= '  -a          --awef            blutti (required)' . PHP_EOL;
        $this->expStr = 'Usage: ide-phpunit.php [OPTIONS]... '.PHP_EOL;
        $this->expStr .= '  -a          --awef                      blutti'.PHP_EOL;
        $this->expStr .= '  -b [<arg>]  --bwef[=<arg>]              blutti'.PHP_EOL;
        $this->expStr .= '  -c <arg>    --cwef=<arg>                blutti'.PHP_EOL;
        $this->expStr .= '  -d          --dwef                      blutti (required)'.PHP_EOL;
        $this->expStr .= '  -e [<arg>]  --ewef[=<arg>]              blutti (required)'.PHP_EOL;
        $this->expStr .= '  -f <arg>    --fwef=<arg>                blutti (required)'.PHP_EOL;
        CommandLineArg::reset();
        CommandLineArg::addArgument('awef', 'a', 'blutti');
        CommandLineArg::addArgument('bwef', 'b', 'blutti', false, false);
        CommandLineArg::addArgument('cwef', 'c', 'blutti', false, true);
        CommandLineArg::addArgument('dwef', 'd', 'blutti', true);
        CommandLineArg::addArgument('ewef', 'e', 'blutti', true, false);
        CommandLineArg::addArgument('fwef', 'f', 'blutti', true, true);
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
            '-d',
            '-e',
            '-f','ff',
        ];
        CommandLineArg::parse($arguments);
        $this->assertTrue(CommandLineArg::get('awef'));
        $this->assertTrue(CommandLineArg::get('bwef'));
        $this->assertEquals('cc',CommandLineArg::get('cwef'));
        $this->assertTrue(CommandLineArg::get('dwef'));
        $this->assertTrue(CommandLineArg::get('ewef'));
        $this->assertEquals('ff',CommandLineArg::get('fwef'));
    }

    public function testAllDouble()
    {
        $arguments=[
            '',
            '--awef',
            '--bwef',
            '--cwef=cc',
            '--dwef',
            '--ewef',
            '--fwef=ff'
        ];
        CommandLineArg::parse($arguments);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function globalArgs(){
        CommandLineArg::parse();
    }

    /**
     * @test
     */
    public function showHelp()
    {
        $this->expectOutputString($this->expStr);
        CommandLineArg::parse(['awef', '-h']);
        CommandLineArg::reset();
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function checkInvalidArgument()
    {
        CommandLineArg::reset();
        CommandLineArg::parse(['','-awef']);
    }

    /**
     * @test
     */
    public function doRequiredFail()
    {
        $expStr='dwef is required'.PHP_EOL;
        $expStr.='ewef is required'.PHP_EOL;
        $expStr.='fwef is required'.PHP_EOL;
        $expStr.=$this->expStr;
        $this->expectOutputString($expStr);
        CommandLineArg::parse(['', 'b']);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage cwef must have value.
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
        $expStr='awef needs no argument'.PHP_EOL;
        $expStr.='dwef needs no argument'.PHP_EOL;
#        $expStr.=$this->expStr;
        $this->expectOutputString($expStr);
        $arguments=[
            '',
            '-a','awef',
            '-b','awef',
            '-c','ff',
            '-d','awef',
            '-e','awef',
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
