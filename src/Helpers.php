<?php

namespace Inertia;

use Closure;

class Helpers
{
//    public static function inertia($component = null, $props = [])
//    {
//        $inertia = Services::inertia();
//
//        if ($component) {
//            return $inertia->render($component, $props);
//        }
//
//        return $inertia;
//    }

    public static function arrayOnly($array, $keys)
    {
        return array_intersect_key($array, array_flip((array) $keys));
    }

    public static function arrayGet($array, $key, $default = null)
    {
        if (! is_array($array)) {
            return self::closureCall($default);
        }

        if (is_null($key)) {
            return $array;
        }

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        if (strpos($key, '.') === false) {
            return $array[$key] ?? self::closureCall($default);
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return self::closureCall($default);
            }
        }

        return $array;
    }

    public static function arraySet(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        foreach ($keys as $i => $key) {
            if (count($keys) === 1) {
                break;
            }

            unset($keys[$i]);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    public static function closureCall($closure)
    {
        return $closure instanceof Closure ? $closure() : $closure;
    }
}
