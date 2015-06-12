<?php
namespace mahlstrom;

/**
 * Created by PhpStorm.
 * User: mahlstrom
 * Date: 02/12/14
 * Time: 15:31
 */

class CommandLineArg
{

    private static $arguments = [];
    private static $flags = [];
    private static $found = [];
    private static $A = [
    ];

    /**
     * Adds an argument, both long name and single character is needed.
     * 'needsValue' has the following attributes:
     * true = must have value
     * false = must NOT have value
     * null = can have value but not needed
     *
     * @param string $longName
     * @param string $character
     * @param string $description
     * @param bool $isReq
     * @param null|bool $needsValue Null=no argument, False=Not needed, True=required
     */
    public static function addArgument($longName, $character, $description, $isReq = false, $needsValue = null)
    {
        self::$flags[$longName] = (object)[
            'short'       => $character,
            'isRequired'  => $isReq,
            'needsValue'  => $needsValue,
            'description' => $description
        ];
        self::$A[$longName] = [
            'short'       => $character,
            'requireArg'  => $needsValue,
            'required'    => $isReq,
            'description' => $description
        ];
    }

    /**
     * @param array|bool $args
     * @return array
     */
    public static function parse($args = false)
    {
        if ($args === false) {
            global $argv;
            $args = $argv;
        }
        // Remove first argument as it is the run file itself
        array_shift($args);
#        unset($args[0]);

        // Check if we need to print the help
        if (in_array(current($args), ['-h', '--help'])) {
            self::printHelp();
            return true;
        }
        while ($arg = current($args)) {
            if (substr($arg, 0, 2) == '--') {
                self::parseDoubleDash(substr($arg, 2));
            } elseif (substr($arg, 0, 1) == '-') {
                if (strlen($arg) > 2) {
                    $arg = self::splitSingleDash($arg, $args);
                }
                self::parseSingleDash(substr($arg, 1), $args);
            } else {
                self::$arguments[] = $arg;
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
            $short = $aAr['short'];
            $long = $aKey;

            if ($aAr['requireArg'] === true) {
                $short .= ' <arg>';
                $long .= '=<arg>';
            } elseif ($aAr['requireArg'] === false) {
                $short .= ' [<arg>]';
                $long .= '[=<arg>]';
            }
            echo sprintf('  -%-10s --%-25s %s', $short, $long, $aAr['description']);
            if ($aAr['required']) {
                echo ' (required)';
            }
            echo PHP_EOL;
        }
    }

    /**
     * @param $arg
     * @throws \InvalidArgumentException
     */
    private static function parseDoubleDash($arg)
    {
        $eqPos = strpos($arg, '=');
        if ($eqPos) {
            $value = substr($arg, $eqPos + 1);
            $flagName = substr($arg, 0, $eqPos);
        } else {
            $flagName = $arg;
            $value = true;
        }

        if (!array_key_exists($flagName, self::$A)) {
            throw new \InvalidArgumentException($flagName . ' is not a valid argument');
        }
        $value = self::parseValueArg($flagName, $value);
        self::$found[$flagName] = $value;
    }

    /**
     * @param $flagName
     * @param $value
     * @return mixed
     * @throws \InvalidArgumentException
     */
    private static function parseValueArg($flagName, $value)
    {
        if (self::$flags[$flagName]->needsValue === true && $value === true) {
            throw new \InvalidArgumentException($flagName . ' must have value.');
        } elseif (self::$flags[$flagName]->needsValue === false && $value !== true) {
            echo "$flagName needs no argument" . PHP_EOL;
        }
        return $value;
    }

    private static function splitSingleDash($arg, &$ar)
    {
        $currKey = key($ar);
        $b = str_split(substr($arg, 1));
        $remKey = $currKey + count($b);
        foreach ($b as $key => $val) {
            $b[$key] = '-' . $val;
        }
        unset($ar[$currKey]);
        array_splice($ar, $currKey, 0, $b);
        array_slice($ar, -1, $remKey);
        for ($i = 0; $i < $currKey; $i++) {
            next($ar);
        }
        return $b[0];
    }

    /**
     * @param $arg
     * @param $args
     * @throws \InvalidArgumentException
     * @return bool
     */
    private static function parseSingleDash($arg, &$args)
    {

        $flagName = false;
        foreach (self::$A as $argKey => $argAr) {
            if ($argAr['short'] == $arg) {
                $flagName = $argKey;
                break;
            }
        }
        if (!$flagName) {
            throw new \InvalidArgumentException($arg . ' is not a valid argument');
        }
        $value = next($args);
        if ($value && substr($value, 0, 1) == '-') {
            prev($args);
            $value = true;
        } elseif ($value === false) {
            $value = true;
        }
        $value = self::parseValueArg($flagName, $value);
        self::$found[$flagName] = $value;
        return true;
    }

    /**
     * @param $string
     * @return mixed
     */
    public static function get($string)
    {
        if (array_key_exists($string, self::$found)) {
            return self::$found[$string];
        }
        return false;
    }

    public static function reset()
    {
        self::$found = [];
        self::$A = [];
    }

    /**
     * @return array
     */
    public static function getArgs()
    {

        return self::$arguments;
    }
}
