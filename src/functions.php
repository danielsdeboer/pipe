<?php

if (! function_exists('take')) {
    /**
     * Get a Pipe instance for the given value.
     * @param mixed $value
     * @param string $placeholder
     * @return \Aviator\Pipe\Pipe
     */
    function take ($value, string $placeholder = '$$')
    {
        return Aviator\Pipe\Pipe::take($value, $placeholder);
    }
}
