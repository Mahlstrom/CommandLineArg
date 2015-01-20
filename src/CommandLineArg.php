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

    private static $found = [];
    private static $A = [
    ];

    /**
     * @param $longName
     * @param $shortName
     * @param $description
     * @param bool $isReq
     * @param null $argReqOrNull Null=no argument, False=Not needed, True=required
     */
    public static function addArguments($longName, $shortName, $description, $isReq = false, $argReqOrNull = null)
    {
        self::$A[$longName] = [
            'short'       => $shortName,
            'requireArg'  => $argReqOrNull,
            'required'    => $isReq,
            'description' => $description
        ];
    }

    /**
     * @param array|bool $args
     * @return array
     */
    public static function parse($args=false)
    {
        if($args===false){
            global $argv;
            $args = $argv;
        }
        // Remove first argument as it is the runfile itself
        unset($args[0]);

        // Check if we need to print the help
        if ((in_array(current($args), ['-h', '--help'])) || !count($args)) {
            self::printHelp();
            return true;

        }
        while ($arg = current($args)) {
            if (substr($arg, 0, 2) == '--') {
                self::parseDoubleDash(substr($arg, 2));
            } elseif (substr($arg, 0, 1) == '-') {
                self::parseSingleDash(substr($arg, 1), $args);
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
     * @param $arg
     * @param $args
     * @return bool
     */
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
            $value=self::parseValue($args, $argName);
        } else {
            self::notValidArgument($arg);
            return false;
        }
//        echo $argName.' ';
//        if($value===true){echo '(true)';}
//        elseif($value===false){echo '(false)';}
//        elseif($value===null){echo '(false)';}
//        else{echo $value;}
//
//        echo PHP_EOL;

        self::$found[$argName] = $value;
        return true;
    }

    /**
     * @param $arg
     */
    private static function parseDoubleDash($arg)
    {
        $argName = $arg;
        $eqPos   = strpos($argName, '=');
        $value   = false;
        if ($eqPos) {
            $value   = substr($argName, $eqPos + 1);
            $argName = substr($argName, 0, $eqPos);
        }

        if (!array_key_exists($argName, self::$A)) {
            self::notValidArgument($argName);
        }
        if (self::$A[$argName]['requireArg'] == true && $value == false) {
            self::requiredError($argName);
        } elseif (self::$A[$argName]['requireArg'] == false && $value == false) {
            $value = true;
        } elseif (self::$A[$argName]['requireArg'] == null && $value != false) {
        }
        self::$found[$argName] = $value;
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
            $short=$aAr['short'];
            $long=$aKey;

            if($aAr['requireArg']===true){
                $short.=' <arg>';
                $long.='=<arg>';
            }elseif($aAr['requireArg']===false){
                $short.=' [<arg>]';
                $long.='[=<arg>]';
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
    private static function notValidArgument($arg)
    {
        throw new \InvalidArgumentException($arg . ' is not a valid argument');
    }


    private static function requiredError($argName)
    {
        throw new \InvalidArgumentException($argName . ' must have value.');
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

    public static function reset() {
        self::$found=[];
        self::$A=[];
    }

    /**
     * @param $args
     * @param $argName
     * @internal param $value
     * @return bool|mixed
     */
    private static function parseValue(&$args, $argName)
    {
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
            $test = next($args);
            prev($args);
            $value = true;
            if ($test && substr($test, 0, 1) != '-') {
                echo "$argName needs no argument" . PHP_EOL;
            }
        }
        return $value;
    }
}
