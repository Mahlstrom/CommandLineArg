<?php
///**
// * @author Magnus Ahlstrom <magnus@atuin.se>
// * @time 2015-01-19 22:49
// */
//use mahlstrom\CommandLineArg;
//
//
///**
// * Class CommandLineArgTest
// */
//class CommandLineArgTest extends PHPUnit_Framework_TestCase
//{
//
//    protected $expStr;
//
//    public function setUp()
//    {
//        $this->expStr = 'awef is required' . PHP_EOL;
//        $this->expStr .= 'Usage: ide-phpunit.php [OPTIONS]... ' . PHP_EOL;
//        $this->expStr .= '  -a          --awef            blutti (required)' . PHP_EOL;
//
//        CommandLineArg::reset();
//        CommandLineArg::addArgument('awef', 'a', 'blutti');
//        CommandLineArg::addArgument('bwef', 'b', 'blutti', false, false);
//        CommandLineArg::addArgument('cwef', 'c', 'blutti', false, true);
//      CommandLineArg::addArgument('dwef', 'd', 'blutti', true);
////        CommandLineArg::addArgument('ewef', 'e', 'blutti', true, false);
////        CommandLineArg::addArgument('fwef', 'f', 'blutti', true, true);
//
//    }
//
//    public function testAll()
//    {
//        CommandLineArg::parse([
//                '-a',
//                '-b',
//                '-c', 'af',
//                '-d', 'd'
//            ]);
//#        CommandLineArg::parse(['ff', '-f', 'awef']);
//#        $this->assertTrue(CommandLineArg::get('fwef'));
//    }
//
//    /**
//     * @test
//     */*
//    public function showHelp()
//    {
//        CommandLineArg::parse(['awef', '-h']);
//        CommandLineArg::reset();
//    }
//
//    /**
//     * @test
//     * @expectedException InvalidArgumentException
//     */
//    public function putNoShitIn()
//    {
//        CommandLineArg::reset();
//        CommandLineArg::parse();
//    }
//
//    /**
//     * @test
//     */
//    public function doRequired()
//    {
//        CommandLineArg::addArgument('awef', 'a', 'blutti', true);
//        $this->expectOutputString($this->expStr);
//        CommandLineArg::parse(['', 'b']);
//    }
//
//    /**
//     * @test
//     * @expectedException InvalidArgumentException
//     * @expectedExceptionMessage awef must have value.
//     */
//    public function doRequiredValue()
//    {
//        CommandLineArg::addArgument('awef', 'a', 'blutti', true, true);
//        CommandLineArg::parse(['', '-a', '-b']);
//    }
//
//    /**
//     * @test
//     */
//    public function doNoRequiredValue()
//    {
//        CommandLineArg::addArgument('awef', 'a', 'blutti', true, false);
//        CommandLineArg::addArgument('bwef', 'b', 'blutti', true, false);
//        CommandLineArg::parse(['', '-a', 'be', '-b']);
//        $this->assertEquals('be', CommandLineArg::get('awef'));
//        $this->assertTrue(CommandLineArg::get('bwef'));
//    }
//
//    /**
//     * @test
//     */
//    public function tryGetWrongValue()
//    {
//        $this->assertFalse(CommandLineArg::get('fea'));
//    }
//}
