<?php

if (! function_exists('isActive')) {
    function isActive(array|string $routes): bool
    {
        foreach ((array) $routes as $route) {
            if (request()->routeIs($route)) {
                return true;
            }
        }
        return false;
    }
}
