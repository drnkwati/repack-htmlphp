<?php

namespace Repack\HtmlPhp;

class Minifier
{
    /**
     * @param $html
     * @param array $options
     * @return string
     */
    public static function minify($html, $options = array())
    {
        return static::blinkMinify($html, $options);
    }

    /**
     * @param $html
     * @param array $options
     * @return string
     */
    public static function blinkMinify($html, $options = array())
    {
        $html = Blink\HTMLMinify::minify($html, array_merge(array(
            'tidy' => true,
            'doctype' => Blink\HTMLMinify::DOCTYPE_HTML5,
            'optimizationLevel' => Blink\HTMLMinify::OPTIMIZATION_ADVANCED,
            // 'emptyElementAddWhitespaceBeforeSlash' => false,
            // 'emptyElementAddSlash' => false,
        ), $options));

        return isset($options['tidy']) ? static::replaceSpaces($html) : $html;
    }

    public static function quickMinify($html, $tidy = true)
    {
        $replace = static::shouldReplaceSpecial($html) ? static::specificReplacement() : static::defaultReplacement();

        $html = preg_replace(array_keys($replace), array_values($replace), $html);

        return $tidy ? static::replaceSpaces($html) : $html;
    }

    /**
     * Check should special RegEx rules be applied
     *
     * @param string $html
     *
     * @return string
     */
    public static function varEscape($html)
    {
        $escaped = str_replace(array("[&quot;", "[&bdquo;", "[&#039;", '[&sbquo;'), '[\'', $escaped);

        return str_replace(array("&quot;]", "&ldquo;]", "&#039;]", "&lsquo;]"), '\']', $escaped);
    }

    /**
     * Check should special RegEx rules be applied
     *
     * @param string $html
     *
     * @return string
     */
    protected static function replaceSpaces($html)
    {
        return str_replace(array(PHP_EOL, "\n", "\t", "\r", "\0", "\x0B"), '', $html);
    }

    /**
     * Check should special RegEx rules be applied
     *
     * @param string $html
     *
     * @return bool
     */
    protected static function shouldReplaceSpecial($html)
    {
        return strpos($html, '<pre') !== false || strpos($html, '&lt;pre') !== false || strpos($html, '//') !== false;
    }

    protected static function defaultReplacement()
    {
        return array(
            '/<!--[^\[](.*?)[^\]]-->/s' => '',
            "/<\?php/" => '<?php ',
            "/\n([\S])/" => '$1',
            "/\r/" => '',
            "/\n/" => '',
            "/\t/" => '',
            '/ +/' => ' ',
            '/> +</' => '><',
        );
    }

    protected static function specificReplacement()
    {
        return array(
            '/<!--[^\[](.*?)[^\]]-->/s' => '',
            "/<\?php/" => '<?php ',
            "/\r/" => '',
            "/>\n</" => '><',
            "/>\s+\n</" => '><',
            "/>\n\s+</" => '><',
        );
    }
}
