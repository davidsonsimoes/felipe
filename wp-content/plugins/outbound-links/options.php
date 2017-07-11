<?php
$controls = new Controls();
$plugin = OutboundLinks::$instance;

if (!isset($plugin->options['translation_disabled'])) {
    if (function_exists('load_plugin_textdomain')) {
        load_plugin_textdomain('outbound-links', false, 'outbound-links/languages');
    }
}

if ($controls->is_action('save')) {
    $controls->options = stripslashes_deep($_POST['options']);

    update_option('outbound-links', $controls->options);
    $plugin->options = $controls->options;
    $controls->messages = __('Options saved.', 'outbound-links');
}

if ($controls->options == null) {
    $controls->options = get_option('outbound-links');
}
?>

<div class="wrap">

    <h2>Outbound Links</h2>

    <?php $controls->show(); ?>

    <form method="post" action="">
        <?php $controls->init(); ?>

        <p>
            Please, refer to the <a href="http://www.satollo.net/plugins/outbound-links" target="_blank">official page</a>
            and the <a href="http://www.satollo.net/forums/forum/outbound-links" target="_blank">official forum</a> for support.

            <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5PHGDGNHAYLJ8" target="_blank"><img style="vertical-align: bottom" src="http://www.satollo.net/images/donate.png"></a>
            Even <b>2$</b> helps! (<a href="http://www.satollo.net/donations" target="_blank">read more</a>)
        </p>


        <table class="form-table">

            <tr>
                <th>Nofollow</th>
                <td>
                    <?php $controls->checkbox('nofollow', 'Enable'); ?>
                    <p class="description">
                        Force the "nofollow" rel attribute on every link not in the blog domain.
                    </p>
                </td>
            </tr>
            <tr>
                <th>Follow domains</th>
                <td>
                    <?php $controls->textarea('white_domains'); ?>
                    <p class="description">
                        One per line. You domain is always whitelisted.
                    </p>
                </td>
            </tr>
            <tr>
                <th>Open on new window</th>
                <td>
                    <?php $controls->checkbox('newwindow', 'Enable'); ?>
                    <p class="description">
                        Force the page opening in a new window rel attribute on every link not in the blog domain.
                    </p>
                </td>
            </tr>
            <tr>
                <th>Disable translations</th>
                <td>
                    <?php $controls->checkbox('translation_disabled', 'Disable'); ?>
                    <p class="description">
                        <!-- Do not translate that -->
                        If you want to see this panel with the original labels, you can disable the
                        tranlsation.
                    </p>
                </td>
            </tr>
        </table>
        <p>
            <?php $controls->button('save', __('Save', 'outbound-links')); ?>
        </p>

    </form>
</div>

<?php

class Controls {

    var $options = null;
    var $errors = null;
    var $messages = null;

    function is_action($action = null) {
        if ($action == null)
            return !empty($_REQUEST['act']);
        if (empty($_REQUEST['act']))
            return false;
        if ($_REQUEST['act'] != $action)
            return false;
        if (check_admin_referer('save'))
            return true;
        die('Invalid call');
    }

    function text($name, $size = 20) {
        if (!isset($this->options[$name]))
            $this->options[$name] = '';
        $value = $this->options[$name];
        if (is_array($value))
            $value = implode(',', $value);
        echo '<input name="options[' . $name . ']" type="text" size="' . $size . '" value="';
        echo htmlspecialchars($value);
        echo '"/>';
    }

    function checkbox($name, $label = '') {
        if (!isset($this->options[$name]))
            $this->options[$name] = '';
        $value = $this->options[$name];
        echo '<label><input class="panel_checkbox" name="options[' . $name . ']" type="checkbox" value="1"';
        if (!empty($value))
            echo ' checked';
        echo '>';
        echo $label;
        echo '</label>';
    }

    function textarea($name) {
        if (!isset($this->options[$name]))
            $value = '';
        else
            $value = $this->options[$name];
        if (is_array($value))
            $value = implode("\n", $value);
        echo '<textarea name="options[' . $name . ']" style="width: 100%; heigth: 120px;">';
        echo htmlspecialchars($value);
        echo '</textarea>';
    }

    function select($name, $options) {
        if (!isset($this->options[$name]))
            $this->options[$name] = '';
        $value = $this->options[$name];

        echo '<select name="options[' . $name . ']">';
        foreach ($options as $key => $label) {
            echo '<option value="' . $key . '"';
            if ($value == $key)
                echo ' selected';
            echo '>' . htmlspecialchars($label) . '&nbsp;&nbsp;</option>';
        }
        echo '</select>';
    }

    function button($action, $label, $message = null) {
        if ($message == null) {
            echo '<input class="button-primary" type="submit" value="' . $label . '" onclick="this.form.act.value=\'' . $action . '\'"/>';
        } else {
            echo '<input class="button-primary" type="submit" value="' . $label . '" onclick="this.form.act.value=\'' . $action . '\';return confirm(\'' .
            htmlspecialchars($message) . '\')"/>';
        }
    }

    function init() {
        echo '<script type="text/javascript">
            jQuery(document).ready(function(){
                jQuery("textarea").focus(function() {
                    jQuery(this).css("height", "400px");
                });
                jQuery("textarea").blur(function() {
                    jQuery(this).css("height", "120px");
                });
            });
            </script>
            ';
        echo '<input name="act" type="hidden" value=""/>';
        wp_nonce_field('save');
    }

    function show() {
        if (!empty($this->errors)) {
            echo '<div class="error"><p>';
            echo $this->errors;
            echo '</p></div>';
        }

        if (!empty($this->messages)) {
            echo '<div class="updated"><p>';
            echo $this->messages;
            echo '</p></div>';
        }
    }

}
?>