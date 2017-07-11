<?php

/*
  Plugin Name: Outbound Links
  Plugin URI: http://www.satollo.net/plugins/outbound-links
  Description: Forces all outbund link to open on a new window. Track outbound link clicks with Google Analytics.
  Version: 2.0.1
  Author: Stefano Lissa
  Author URI: http://www.satollo.net
  Disclaimer: Use at your own risk. No warranty expressed or implied is provided.
 */

class OutboundLinks {

    static $instance;
    var $options;
    var $white_domains = null;

    function __construct() {
        self::$instance = $this;
        $this->options = get_option('outbound-links');

        register_activation_hook('outbound-links/plugin.php', array($this, 'hook_activate'));
        register_deactivation_hook('outbound-links/plugin.php', array($this, 'hook_deactivate'));

        if (!is_admin()) {
            add_filter('the_content', array($this, 'hook_the_content'), 1);
        } else {
            add_action('admin_menu', array($this, 'hook_admin_menu'));
        }
    }

    function hook_admin_menu() {
        add_options_page('Outbound Links', 'Outbound Links', 'manage_options', 'outbound-links/options.php');
    }

    function hook_activate() {
        // Old options migration
        // Set options autoload
    }

    function hook_deactivate() {
        // Set options no autoload
    }

    function hook_the_content($content) {
        if (!is_singular())
            return $content;

        $buffer = '';
        $offset = 0;

        // Find "a" tags
        while (($start = strpos($content, '<a ', $offset)) !== false) {
            $buffer .= substr($content, $offset, $start - $offset);
            $end = strpos($content, '>', $start + 1);
            //echo $start;
            //$buffer .= substr($content, $offset, $start - $offset);
            $tag = substr($content, $start, $end - $start + 1);
            //echo $tag;
            //break;

            if (isset($this->options['nofollow'])) {
                if ($this->is_nofollow($tag)) {
                    $tag = preg_replace('#rel=[\'"].*?[\'"]#', '', $tag);
                    $tag = str_replace('<a', '<a rel="nofollow"', $tag);
                }
            }

            if (isset($this->options['newwindow'])) {
                if ($this->is_newwindow($tag)) {
                    $tag = preg_replace('#target=[\'"].*?[\'"]#', '', $tag);
                    $tag = str_replace('<a', '<a target="_blank"', $tag);
                }
            }
            /*
              // Second method
              $attrs = $this->parse_tag($tag);
              $attrs['rel'] = $rel;
              $attrs['target'] = $target;

              // Adjust attributes
              // Rebuild the tag
              $tag = build_tag('a', $attrs);
             */

            $buffer .= $tag;
            $offset = $end + 1;
        }
        $buffer .= substr($content, $offset);
        return $buffer;
    }

    function is_nofollow($tag) {
        if (strpos($tag, $_SERVER['HTTP_HOST']) !== false)
            return false;

        if ($this->white_domains === null) {
            $this->white_domains = array();
            $list = explode("\n", $this->options['white_domains']);
            if (!empty($list)) {
                foreach ($list as &$item) {
                    $item = strtolower(trim($item));
                    if (empty($item)) continue;
                    $this->white_domains[] = $item;
                }
            }
        }

        if (empty($this->white_domains)) return true;
        foreach ($this->white_domains as &$domain) {
            if (stripos($tag, $domain) !== false) return false;

        }
        // Check for other domains

        return true;
    }

    function is_newwindow($tag) {
        if (strpos($tag, $_SERVER['HTTP_HOST']) !== false)
            return false;

        // Check for other domains

        return true;
    }

    function parse_tag($tag) {
        $attrs = array();

        return $attrs;
    }

    function build_tag($tag, &$attrs) {
        $buffer = ' ';
        foreach ($attrs as $name => $value) {
            $buffer .= $name . '="' . $value . '" ';
        }
        $buffer = rtrim($buffer);
        return '<' . $tag . $buffer . '>';
    }

}

new OutboundLinks();
/*
add_action('wp_footer', 'outl_wp_footer');

function outl_wp_footer() {
    global $user_ID;

    $options = get_option('outl');

    if ($user_ID == '' || $options['track_admin']) {
        echo $options['footer'];
    }

    if ($options['target'] || $options['track']) {
        echo "\n" . '<script type="text/javascript">' . "\n<!--\n";
        echo 'var a=document.getElementsByTagName("a");var d=/^(http|https):\/\/([a-z-.0-9]+)[\/]{0,1}/i.exec(window.location);var il=new RegExp("^(http|https):\/\/"+d[2], "i");for(var i=0; i<a.length; i++) {if (!il.test(a[i].href) && a[i].href.toLowerCase().substring(0, 4) == "http") {' .
        ($options['target'] ? 'a[i].target="_blank";' : '');

        if ($options['track'] && ($user_ID == '' || $outl_options['track_admin'])) {
            $tracker = $options['track_version'] ? 'urchinTracker' : 'pageTracker._trackPageview';
            if ($options['track_prefix'] == '')
                $outl_options['track_prefix'] = '/out';
            if ($options['track_mode']) {
                echo 'a[i].onclick=function(){' . $tracker . '("' . $options['track_prefix'] . '/"+this.href.replace(/^http:\/\/|https:\/\//i, "").split("/")[0])};';
            } else {
                echo 'a[i].onclick=function(){' . $tracker . '("' . $options['track_prefix'] . '/"+this.href.replace(/^http:\/\/|https:\/\//i, "").split("/").join("|"))};';
            }
        }
        echo '}}' . "\n";
        echo "//-->\n</script>\n";
    }
}

*/
