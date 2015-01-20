<?php
/**
 * Created by PhpStorm.
 * User: mahlstrom
 * Date: 02/12/14
 * Time: 15:31
 */

namespace mahlstrom;

/**
 * Class CommandLineArg
 *
 * @package mahlstrom
 */
class CommandLineArg
{

    private static $found = [];
    private static $A = [];

    /**
     * @param $longName
     * @param $shortName
     * @param $description
     * @param bool $isReq
     * @param null $argReqOrNull
     */
    public static function addArgument($longName, $shortName, $description, $isReq = false, $argReqOrNull = null)
    {
        self::$A[$longName] = [
            'short'       => $shortName,
            'requireArg'  => $argReqOrNull,
            'required'    => $isReq,
            'description' => $description
        ];
    }

    /**
     * @param bool|array $args
     * @return array
     */
    public static function parse($args = false)
    {
        if ($args === false) {
            echo 'OK';
            global $argv;
            $args = $argv;
        }
        unset($args[0]);
        if ((count($args) >= 1 && in_array(current($args), ['-h', '--help'])) || !count($args)) {
            self::printHelp();
            return true;
        }
        while ($arg = current($args)) {
            if (substr($arg, 0, 2) == '--') {
                echo $arg . PHP_EOL;
                self::parseDoubleDash(substr($arg, 2));
            } elseif (substr($arg, 0, 1) == '-') {
                self::parseSingleDash(substr($arg, 1), $args);
            } else {
            }
            next($args);
        }
        $break = false;
        foreach (self::$A as $key => $ar) {
            if ($ar['required']) {
                if (!array_key_exists($key, self::$found)) {
                    $break = true;
                    echo $key . ' is required' . PHP_EOL;
                }
            }
        }
        if ($break) {
            self::printHelp();
        }
        return self::$found;
    }

    /**
     * Prints help
     */
    public static function printHelp()
    {
        global $argv;
        $command = explode('/', $argv[0]);
        $command = end($command);

        echo 'Usage: ' . $command . ' [OPTIONS]... ' . PHP_EOL;

        foreach (self::$A as $aKey => $aAr) {
            echo sprintf('  -%-10s --%-15s %s', $aAr['short'], $aKey, $aAr['description']);
            if ($aAr['required']) {
                echo ' (required)';
            }
            echo PHP_EOL;
        }
    }

    private static function parseDoubleDash($arg)
    {
        die();
        $eqPos = strpos($arg, '=');
        $value = false;
        if ($eqPos) {
            $value = substr($arg, $eqPos + 1);
            $arg = substr($arg, 0, $eqPos);
        }
        if (!array_key_exists($arg, self::$A)) {
            self::notValidArgument($arg);
        }
        if (self::$A[$arg]['requireArg'] == true && $value == false) {
            self::requiredError($arg);
        } elseif (self::$A[$arg]['requireArg'] == null) {
        }
        self::$found[$arg] = $value;
    }

    /**
     * @param $arg
     * @throws \InvalidArgumentException
     */
    private static function notValidArgument($arg)
    {
        throw new \InvalidArgumentException($arg . ' is not a valid argument');
    }

    private static function requiredError($argName)
    {
        throw new \InvalidArgumentException($argName . ' must have value.');
    }

    private static function parseSingleDash($arg, &$args)
    {
        $argName = false;
        foreach (self::$A as $argKey => $argAr) {
            if ($argAr['short'] == $arg) {
                $argName = $argKey;
                break;
            }
        }
        if ($argName) {
            if (self::$A[$argName]['requireArg'] == true) {
                $value = next($args);
                if ($value == false || substr($value, 0, 1) == '-') {
                    self::requiredError($argName);
                }
            } elseif (self::$A[$argName]['requireArg'] === false) {
                $value = next($args);
                if ($value == false) {
                    $value = true;
                } elseif (substr($value, 0, 1) == '-') {
                    prev($args);
                    $value = true;
                }
            } else {
                $value = true;
                prev($args);
            }
        } else {
            self::notValidArgument($arg);
        }
        if (!isset($value)) {
            echo $argName;
            exit();
        }
        self::$found[$argName] = $value;
    }

    public static function get($string)
    {
        if (array_key_exists($string, self::$found)) {
            return self::$found[$string];
        }
        return false;
    }

    public static function reset() {
        self::$found=[];
        self::$A=[];
    }
}
