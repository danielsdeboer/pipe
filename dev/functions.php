<?php

if (! function_exists('dd')) {
    /**
     * Die and dump.
     * @param array ...$stuff
     * @return void
     */
    function dd (...$stuff)
    {
        die(
            var_dump(...$stuff)
        );
    }
}
