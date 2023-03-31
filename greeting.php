<?php
/*
Plugin Name: Custom Greeting Message
Plugin URI: https://apsaraaruna.com/
Description: Displays a customizable greeting message to website visitors.
Version: 1.0
Author: Your Name
Author URI: https://apsaraaruna.com/
License: GPL2
*/

// Store the default options when the plugin is activated
function custom_greeting_message_activate()
{
    $default_options = array(
        'message' => 'Hello!',
        'location' => 'top',
        'schedule' => 'always',
        'days' => array(),
        'start_time' => '9:00am',
        'end_time' => '5:00pm',
        'background_color' => '#33ffdf'
    );
    add_option('custom_greeting_message_options', $default_options);
}
register_activation_hook(__FILE__, 'custom_greeting_message_activate');

// Retrieve the options when needed
$options = get_option('custom_greeting_message_options');


// Output the greeting message in the wp_head hook
add_action('wp_head', 'custom_greeting_message_head');
function custom_greeting_message_head()
{

    $options = get_option('custom_greeting_message_options');
    $background = $options['background_color'];

    if ($options['location'] == 'top') {
        if (($options['schedule'] == 'always' ||
            ($options['schedule'] == 'specific_days' && !empty($options['days']) && in_array(date('l'), $options['days'])) ||
            ($options['schedule'] == 'specific_times' && strtotime($options['start_time']) <= time() && strtotime($options['end_time']) >= time())
        )) {
            echo '<div class="custom-greeting-message top" style="background-color:' . $background . '">' . $options['message'] . '</div>';
        }
    }
}

// Output the greeting message in the wp_footer hook
add_action('wp_footer', 'custom_greeting_message_footer');
function custom_greeting_message_footer()
{
    $options = get_option('custom_greeting_message_options');
    $background = $options['background_color'];

    if ($options['location'] == 'bottom') {
        if (($options['schedule'] == 'always' ||
            ($options['schedule'] == 'specific_days' && !empty($options['days']) && in_array(date('l'), $options['days'])) ||
            ($options['schedule'] == 'specific_times' && strtotime($options['start_time']) <= time() && strtotime($options['end_time']) >= time())
        )) {
            echo '<div class="custom-greeting-message bottom" style="background-color:' . $background . '">' . $options['message'] . '</div>';
        }
    }
}


// Output the greeting message in a widget
add_action('widgets_init', 'register_custom_widget');

function register_custom_widget()
{
    $options = get_option('custom_greeting_message_options');

    if ($options['location'] == 'sidebar') {
        register_sidebar_widget(
            'Greeting widget',  // Widget name
            'custom_widget',  // Callback function
            array(
                'description' => 'Add a custom widget to the end of the sidebar.',
                'before_widget' => '<div class="custom-widget">',
                'after_widget' => '</div>'
            )
        );
    }
}

function custom_widget($args)
{
    $options = get_option('custom_greeting_message_options');
    $background = $options['background_color'];

    if (($options['schedule'] == 'always' ||
        ($options['schedule'] == 'specific_days' && !empty($options['days']) && in_array(date('l'), $options['days'])) ||
        ($options['schedule'] == 'specific_times' && strtotime($options['start_time']) <= time() && strtotime($options['end_time']) >= time())
    )) {
        echo $args['before_widget'];
        echo '<div class="custom-greeting-message sidebar" style="background-color:' . $background . '">' . $options['message'] . '</div>';
        echo $args['after_widget'];
    }
}



// Define the options page
function custom_greeting_message_options_page()
{
    // Create the options form
?>
    <div class="wrap">
        <h2><?php _e('Custom Greeting Message Options'); ?></h2>
        <form method="post" action="options.php">
            <?php settings_fields('custom_greeting_message_options'); ?>
            <?php
            $options = get_option('custom_greeting_message_options');
            // var_dump($options);
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Greeting Message'); ?></th>
                    <td><input type="text" name="custom_greeting_message_options[message]" value="<?php echo !empty($options['message']) ? esc_attr($options['message']) : 'Hello!'; ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Location'); ?></th>
                    <td>
                        <select name="custom_greeting_message_options[location]">
                            <option value="top" <?php selected(!empty($options['location']) ? $options['location'] : 'top', 'top'); ?>><?php _e('Top of the Page'); ?></option>
                            <option value="bottom" <?php selected(!empty($options['location']) ? $options['location'] : 'top', 'bottom'); ?>><?php _e('Bottom of the Page'); ?></option>
                            <option value="sidebar" <?php selected(!empty($options['location']) ? $options['location'] : 'top', 'sidebar'); ?>><?php _e('Sidebar'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Schedule'); ?></th>
                    <td>
                        <select name="custom_greeting_message_options[schedule]">
                            <option value="always" <?php selected(!empty($options['schedule']) ? $options['schedule'] : 'always', 'always'); ?>><?php _e('Always'); ?></option>
                            <option value="specific_days" <?php selected(!empty($options['schedule']) ? $options['schedule'] : 'always', 'specific_days'); ?>><?php _e('Specific Days'); ?></option>
                            <option value="specific_times" <?php selected(!empty($options['schedule']) ? $options['schedule'] : 'always', 'specific_times'); ?>><?php _e('Specific Times'); ?></option>
                        </select>
                        <div class="schedule-specific-days" <?php if (!empty($options['schedule']) && $options['schedule'] != 'specific_days') echo ' style="display:none;"'; ?>>
                            <p><?php _e('Select the days when the greeting message should appear:'); ?></p>
                            <label><input type="checkbox" name="custom_greeting_message_options[days][]" value="Monday" <?php if (!empty($options['days']) && in_array('Monday', $options['days'])) echo 'checked="checked"'; ?> /> <?php _e('Monday'); ?></label><br />
                            <label><input type="checkbox" name="custom_greeting_message_options[days][]" value="Tuesday" <?php if (!empty($options['days']) && in_array('Tuesday', $options['days'])) echo 'checked="checked"'; ?> /> <?php _e('Tuesday'); ?></label><br />
                            <label><input type="checkbox" name="custom_greeting_message_options[days][]" value="Wednesday" <?php if (!empty($options['days']) && in_array('Wednesday', $options['days'])) echo 'checked="checked"'; ?> /> <?php _e('Wednesday'); ?></label><br />
                            <label><input type="checkbox" name="custom_greeting_message_options[days][]" value="Thursday" <?php if (!empty($options['days']) && in_array('Thursday', $options['days'])) echo 'checked="checked"'; ?> /> <?php _e('Thursday'); ?></label><br />
                            <label><input type="checkbox" name="custom_greeting_message_options[days][]" value="Friday" <?php if (!empty($options['days']) && in_array('Friday', $options['days'])) echo 'checked="checked"'; ?> /> <?php _e('Friday'); ?></label><br />
                            <label><input type="checkbox" name="custom_greeting_message_options[days][]" value="Saturday" <?php if (!empty($options['days']) && in_array('Saturday', $options['days'])) echo 'checked="checked"'; ?> /> <?php _e('Saturday'); ?></label><br />
                            <label><input type="checkbox" name="custom_greeting_message_options[days][]" value="Sunday" <?php if (!empty($options['days']) && in_array('Sunday', $options['days'])) echo 'checked="checked"'; ?> /> <?php _e('Sunday'); ?></label><br />
                        </div>
                        <div class="schedule-specific-times" <?php if (!empty($options['schedule']) && $options['schedule'] != 'specific_times') echo ' style="display:none;"'; ?>>
                            <p><?php _e('Select the times when the greeting message should appear:'); ?></p>
                            <input type="text" name="custom_greeting_message_options[start_time]" class="datepicker" value="<?php echo !empty($options['start_time']) ? esc_attr($options['start_time']) : ''; ?>" placeholder="<?php _e('Start Time'); ?>" /> <?php _e('to'); ?>
                            <input type="text" name="custom_greeting_message_options[end_time]" class="datepicker" value="<?php echo !empty($options['end_time']) ? esc_attr($options['end_time']) : ''; ?>" placeholder="<?php _e('End Time'); ?>" />
                        </div>
                    </td>
                </tr>
                <tr valign="top" class="specific-times-option">
                    <th scope="row"><?php _e('Background color');  ?></th>
                    <td>
                        <input type="text" name="custom_greeting_message_options[background_color]" class="spectrum-colorpicker" value="<?php echo esc_attr($options['background_color'] ?? '#33ffdf'); ?>" />

                    </td>
                </tr>

                <script type="text/javascript">
                    // Initialize the date time picker for the input fields.
                    jQuery(document).ready(function($) {
                        $('.spectrum-colorpicker').spectrum({
                            preferredFormat: "hex"
                        });

                        $('.datepicker').datetimepicker({
                            dateFormat: 'yy-mm-dd',
                            timeFormat: 'hh:mm:ss',
                            stepHour: 1,
                            stepMinute: 15,
                            stepSecond: 15
                        });
                    });
                </script>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
}

// Add the options page to the Settings menu
function custom_greeting_message_options_menu()
{
    add_options_page(__('Custom Greeting Message Options'), __('Custom Greeting Message'), 'manage_options', 'custom_greeting_message_options', 'custom_greeting_message_options_page');
}
add_action('admin_menu', 'custom_greeting_message_options_menu');

// Register the settings
function custom_greeting_message_register_settings()
{
    register_setting('custom_greeting_message_options', 'custom_greeting_message_options', 'custom_greeting_message_validate_options');
}
add_action('admin_init', 'custom_greeting_message_register_settings');

// Validate the options
function custom_greeting_message_validate_options($input)
{
    // var_dump($input);
    // exit;
    // Validate the message
    if (!isset($input['message']) || trim($input['message']) === '') {
        add_settings_error('custom_greeting_message_options', 'message', __('Please enter a greeting message.'));
    }
    // Validate the location
    if (!isset($input['location']) || !in_array($input['location'], array('top', 'bottom', 'sidebar'))) {
        add_settings_error('custom_greeting_message_options', 'location', __('Please select a valid location.'));
    }

    // Validate the days
    if (isset($input['days']) && !empty($input['days'])) {
        foreach ($input['days'] as $day) {
            if (!in_array($day, array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'))) {
                add_settings_error('custom_greeting_message_options', 'days', __('Please select valid days.'));
                break;
            }
        }
    }
    // Validate the start time
    if (isset($input['start_time']) && !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $input['start_time'])) {
        add_settings_error('custom_greeting_message_options', 'start_time', __('Please enter a valid start time (e.g. 2023-03-26 09:30:00).'));
    }

    // Validate the end time
    if (isset($input['end_time']) && !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $input['end_time'])) {
        add_settings_error('custom_greeting_message_options', 'end_time', __('Please enter a valid end time (e.g. 2023-03-26 17:00:00).'));
    }

    return $input;
}

// Display the greeting message
function custom_greeting_message_display()
{
    $options = get_option('custom_greeting_message_options');

    // Check if the message should be displayed based on the day and time options
    if (isset($options['days']) && !empty($options['days']) && isset($options['start_time']) && isset($options['end_time'])) {

        $timezone = get_option('timezone_string');
        if (empty($timezone)) {
            $timezone = 'UTC'; // set default timezone to UTC
        }
        // create a new DateTimeZone object
        $dtz = new DateTimeZone($timezone);

        $now = new DateTime('now', $dtz);
        $current_day = $now->format('l');
        $start_time = new DateTime($options['start_time']);
        $end_time = new DateTime($options['end_time']);

        if (in_array($current_day, $options['days']) && $now >= $start_time && $now <= $end_time) {
            $message = $options['message'];

            if (isset($options['position'])) {
                switch ($options['position']) {
                    case 'top':
                        echo '<div class="custom-greeting-message" style="text-align: center;">' . $message . '</div>';
                        break;

                    case 'bottom':
                        echo '<div class="custom-greeting-message" style="text-align: center; position: absolute; bottom: 0; left: 0; right: 0;">' . $message . '</div>';
                        break;

                    case 'sidebar':
                        echo '<div class="custom-greeting-message" style="text-align: center;">' . $message . '</div>';
                        break;
                }
            }
        }
    }
}
// add_action('wp_footer', 'custom_greeting_message_display');


function custom_greeting_message_scripts()
{
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_script('jquery-ui-timepicker-addon', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js', array('jquery', 'jquery-ui-datepicker'), '1.6.3', true);
    wp_enqueue_style('jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
    wp_enqueue_style('jquery-ui-timepicker-addon', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css', array(), '1.6.3');

    wp_enqueue_style('spectrum', 'https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.0/spectrum.min.css');
    wp_enqueue_script('spectrum', 'https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.0/spectrum.min.js', array('jquery'));
}
add_action('admin_enqueue_scripts', 'custom_greeting_message_scripts', 999);


function custom_greeting_message_options_validate($input)
{
    // Sanitize the greeting message text input
    $input['message'] = sanitize_text_field($input['message']);

    // Validate the location input
    if (!in_array($input['location'], array('top', 'bottom', 'sidebar'))) {
        $input['location'] = 'top';
    }

    // Validate the schedule input
    if (!in_array($input['schedule'], array('always', 'specific_days', 'specific_times'))) {
        $input['schedule'] = 'always';
    }

    // Sanitize and validate the specific days input
    if ($input['schedule'] == 'specific_days') {
        $input['days'] = array();
        foreach ($_POST['custom_greeting_message_options']['days'] as $day) {
            if (in_array($day, array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'))) {
                $input['days'][] = $day;
            }
        }
    }

    // Sanitize and validate the specific times input
    if ($input['schedule'] == 'specific_times') {
        $input['start_time'] = sanitize_text_field($input['start_time']);
        $input['end_time'] = sanitize_text_field($input['end_time']);
        if (!preg_match('/^(0?[1-9]|1[0-2]):([0-5][0-9]) (am|pm)$/i', $input['start_time'])) {
            $input['start_time'] = '12:00 am';
        }
        if (!preg_match('/^(0?[1-9]|1[0-2]):([0-5][0-9]) (am|pm)$/i', $input['end_time'])) {
            $input['end_time'] = '11:59 pm';
        }
    }

    return $input;
}


function custom_greeting_message_style()
{
?>
    <style type="text/css">
        .custom-greeting-message {
            padding: 10px 15px;
        }

        .custom-greeting-message.sidebar {
            margin-top: 15px;
            margin-bottom: 15px;
        }
    </style>
<?php
}
add_action('wp_head', 'custom_greeting_message_style');
