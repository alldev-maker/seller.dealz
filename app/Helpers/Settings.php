<?php

use App\Models\Settings\Setting;

if (!function_exists('settings')) {

    /**
     * @param $key
     *
     * @return string|array|null
     */
    function settings($key = '')
    {
        try {
            if (!empty($key)) {
                if (false !== strpos($key, '*')) {
                    $key      = str_replace('*', '%', $key);
                    $settings = Setting::where('key', 'like', $key . '%')->get();

                    $array = [];
                    foreach ($settings as $setting) {
                        $array[$setting->key] = $setting->value;
                    }

                    return $array;
                } else {
                    /** @var Setting $setting */
                    $setting = Setting::findOrFail($key);

                    return $setting->value;
                }
            } else {
                $settings = Setting::all();

                $array = [];
                foreach ($settings as $setting) {
                    $array[$setting->key] = $setting->value;
                }

                return $array;
            }
        } catch (Exception $e) {
            return null;
        }
    }

}

if (!function_exists('ts')) {
    function ts()
    {
        $setting = Setting::find('meta.title.separator');

        if ($setting) {
            return $setting->value;
        } else {
            return ' - ';
        }
    }
}