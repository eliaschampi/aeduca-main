<?php

namespace App\Cache;

use Illuminate\Support\Facades\Cache;

class ConfigCache
{
    public static function catCache(string $name, \Closure $callback)
    {
        return Cache::rememberForever($name, function () use ($callback) {
            return $callback();
        });
    }

    public static function getCoreConfig(): array
    {
        return Cache::rememberForever("core", function () {
            return json_decode(file_get_contents(config_path() . "/core/aeduca.json"), true);
        });
    }

    public static function getCompanyData(): array
    {
        return Cache::rememberForever("company", function () {
            return json_decode(file_get_contents(config_path() . "/core/company.json"), true);
        });
    }

    public static function updateConfig(array $data, string $key): array
    {
        $config = [];
        $url = "";
        if ($key === "core") {
            $config = self::getCoreConfig();
            $url = "/core/aeduca.json";
        } elseif ($key === "company") {
            $config = self::getCompanyData();
            $url = "/core/company.json";
        }
        $config = array_merge($config, $data);
        file_put_contents(config_path() . $url, json_encode($config, JSON_PRETTY_PRINT));
        Cache::forget($key);
        Cache::put($key, $config);
        return $config;
    }

}
