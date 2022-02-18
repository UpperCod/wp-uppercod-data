<?php namespace UpperCodShortcodeData;

/**
 * Plugin Name:       UpperCod - Shortcode data
 * Plugin URI:        http://github.com/uppercod/wordpress-plugin-uppercod-data
 * Description:       shortcode that accelerates the reuse of information..
 * Version:           1.0.0
 * Requires PHP:      7.2
 * Author:            UpperCod
 * Author URI:        http://github.com/uppercod
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       UpperCod
 * Domain Path:       /languages
 */

defined("ABSPATH") || exit;

require __DIR__ . "/src/getData.php";
require __DIR__ . "/src/Thumbnail.php";

global $UpperCodShortcodeData;

$UpperCodShortcodeData = [
    "date" => function ($option, $value) {
        return wp_date($option, is_numeric($value) ? $value : strtotime($value));
    },
    "json" => function ($option, $value) {
        return JSON_ENCODE($value);
    },
    "md5" => function ($option, $value) {
        return md5($value);
    },
    "striptags" => function ($option, $value) {
        return striptags($value);
    },
    "htmlencode" => function ($option, $value) {
        return htmlentities($value);
    },
    "urlencode" => function ($option, $value) {
        return urlencode($value);
    },
    "base64encode" => function ($option, $value) {
        return base64_encode($value);
    },
    "slug" => function ($option, $value) {
        return sanitize_title($value);
    },
];

/**
 * Retrieve data from the concurrent object and apply filters based on the shortcode arguments
 * [data title] will get from the concurrent object the `title` property
 * [data relation.title] will get from the concurrent object the `relation` property and then `title` only if it is of type object.
 */
add_shortcode("data", function ($attrs) {
    global $UpperCodShortcodeData;
    $data = "";
    $i = 0;
    foreach ($attrs as $key => $value) {
        $prop = is_numeric($key) ? $value : $key;
        // only the first index will go into getData
        if (!$i) {
            $data = getData($prop);
        } else if ($UpperCodShortcodeData[$prop]) {
            // the following indices are analyzed by filters
            $data = $UpperCodShortcodeData[$prop]($value, $data);
        }
        $i++;
    }
    return $data;
});
