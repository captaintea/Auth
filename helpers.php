<?php

if (! function_exists('config'))  {

    function config($key = null, $default = null)
    {
        $path = explode('.', $key);
        $configDirectory = __DIR__ . '/config/';
        if (!file_exists($configDirectory)) {
            return $default;
        }
        $configFilenames = scandir($configDirectory);
        foreach ($configFilenames as $filename) {
            if ($filename === $path[0].'.php' && file_exists($configPath = $configDirectory . $filename)) {
                $config =  include $configPath;
                array_shift($path);
                $result = &$config;
                foreach ($path as $k) {
                    $result = isset($result[$k]) ? $result[$k] : $default;
                }
                return $result;
            }
        }
        return $default;
    }

}

if (! function_exists('elixir')) {
    /**
     * Get the path to a versioned Elixir file.
     *
     * @param  string  $file
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    function elixir($file)
    {
        static $manifest = null;
        static $manifestPath = __DIR__ . '/public/build/rev-manifest.json';

        if (is_null($manifest) && file_exists($manifestPath)) {
            $manifest = json_decode(file_get_contents($manifestPath), true);
        }

        if (isset($manifest[$file])) {
            return '/build/'.$manifest[$file];
        }

        $unversioned = __DIR__ . '/public/';

        if (file_exists($unversioned)) {
            return '/'.trim($file, '/');
        }

        throw new InvalidArgumentException("File {$file} not defined in asset manifest.");
    }
}

if (! function_exists('env'))  {

    function env($key, $default = null)
    {
        if (!file_exists(__DIR__ . '/env.php')) {
            return $default;
        }
        $env = include 'env.php';
        if (isset($env[$key])) {
            return $env[$key];
        } else {
            return $default;
        }
    }

}

