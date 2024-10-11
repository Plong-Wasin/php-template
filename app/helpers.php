<?php
if (!function_exists('e')) {
    function e($str)
    {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
}
if (!function_exists('base_path')) {
    function base_path($path = null)
    {
        if (empty($path)) {
            return dirname(__DIR__);
        }
        if ($path[0] === '/') {
            $path = substr($path, 1);
        }
        return dirname(__DIR__) . '/' . $path;
    }
}
if (!function_exists('view_path')) {
    function view_path($path = null)
    {
        return base_path('resources/views') . '/' . ltrim($path, '/');
    }
}
if (!function_exists('base_url')) {
    function base_url($path = '')
    {
        // Determine the protocol (HTTP or HTTPS)
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

        // Get the host name
        $host = $_SERVER['HTTP_HOST'];

        // Get the directory path
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/';

        // Return the full base URL with an optional path
        return $protocol . $host . $basePath . ltrim($path, '/');
    }
}
if (!function_exists('project_url')) {
    function project_url($path = null)
    {
        // Determine the protocol (HTTP or HTTPS)
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
            || $_SERVER['SERVER_PORT'] == 443
            || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
        ) ? "https://" : "http://";

        // Get the host name
        $host = $_SERVER['HTTP_HOST'];

        // Get the directory path
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/';

        // Return the full base URL with an optional path
        $url = $protocol . $host . $basePath;
        $directoryName = basename(base_path());
        if (strpos($url, $directoryName)) {
            return substr($url, 0, strpos($url, $directoryName) + strlen($directoryName)) . '/' . ltrim($path, '/');
        }
        return $protocol . $host . '/' . ltrim($path, '/');
    }
}
