<?php

if (!function_exists('getOptions')) {
    function getOptions(array $args, array $option_names) : array
    {
        $option_names_map = [];
        foreach ($option_names as $name) {
            $option_names_map[$name] = $name;
        }
        $options = [];
        foreach ($args as $k => $arg) {
            if (strpos($arg, '-') === 0) {
                if (strpos($arg, '--') === 0) {
                    $option = substr($arg, 2);
                    if (strpos($option, '=') > 0) {
                        $option_arr = explode('=', $option);
                        if (count($option_arr) == 2) {
                            list($option_name, $option_value) = $option_arr;
                        } else {
                            $option_name = $option;
                            $option_value = '';
                        }
                    } else {
                        $option_name = $option;
                        $option_value = '';
                    }
                    if (array_key_exists($option_name, $option_names_map)) {
                        $options[$option_name] = $option_value;
                    }
                } else {
                    $option_name = substr($arg, 1);
                    if (array_key_exists($option_name, $option_names_map)) {
                        $option_value = '';
                        if (isset($args[$k + 1])) {
                            $next_arg = $args[$k + 1];
                            if (strpos($next_arg, '-') === 0) {
                                if (strpos($next_arg, '--') === 0) {
                                    if (!array_key_exists(substr($next_arg, 2), $option_names_map)) {
                                        $option_value = $next_arg;
                                    }
                                } else {
                                    if (!array_key_exists(substr($next_arg, 1), $option_names_map)) {
                                        $option_value = $next_arg;
                                    }
                                }
                            } else {
                                $option_value = $next_arg;
                            }
                        }
                        $options[$option_name] = $option_value;
                    }
                }
            }
        }
        return $options;
    }
}
