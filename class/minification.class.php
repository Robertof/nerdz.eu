<?php
namespace NERDZ\Core;
// Used for minification of JS/CSS files. Also used to minify
// template files by templatecfg.class
// constants.inc.php: for MINIFICATION_*_CMD
require_once 'config/constants.inc.php';

final class Minification
{
    const PATH_VAR = '%path%'; // private constants do not exist :(

    public static function minifyJs ($path)
    {
        return shell_exec (str_ireplace (self::PATH_VAR, $path, \NERDZ\Config\MINIFICATION_JS_CMD));
    }

    public static function minifyCss ($path)
    {
        require_once 'vendor/autoload.php';
        $css = new csstidy();
        $css->set_cfg('allow_html_in_templates', false);
        $css->set_cfg('compress_colors', true);
        $css->set_cfg('compress_font-weight', true);
        $css->set_cfg('remove_last_', true);
        $css->set_cfg('remove_bslash', true);
        $css->set_cfg('template', 'highest');
        $css->set_cfg('preserve_css', true);
        $css->set_cfg('silent', true);
        $css->parse(file_get_contents($path));
        return $css->print->plain();
    }

    public static function minifyTemplateFile ($mTimeFile, $fileToMinify, $targetMinifiedFile, $ext)
    {
        file_put_contents ($targetMinifiedFile, ( $ext == 'js' ? self::minifyJs ($fileToMinify) : self::minifyCss ($fileToMinify)) );
        chmod ($targetMinifiedFile, 0775);
        file_put_contents ($mTimeFile, filemtime ($fileToMinify));
        chmod ($mTimeFile, 0775);
    }
}
?>
