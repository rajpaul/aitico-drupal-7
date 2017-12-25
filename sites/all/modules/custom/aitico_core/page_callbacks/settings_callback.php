<?php

/**
 * Settings page for alert settings configuration form
 * 
 * @return theme
 */

function aitio_core_settings_page(){
    $settings_form = drupal_get_form('aitico_core_settings_form');
    $title = "Settings";
    return theme('content_entry_page', array('form_title' => $title,
                'node_add_form' => render($settings_form)));
}


/**
 * Settings form with some prefix suffix to theme from css
 * 
 * @return $form
 */

function aitico_core_settings_form() {
    
    //Check PIN expiration time is set or not
    $get_pin_time_value = variable_get('pin_expiration_time_value');
    if (isset($get_pin_time_value) && !empty($get_pin_time_value)) {
        $pin_time_default_value = $get_pin_time_value;
    } else {
        //Set PIN expiration time 6 hours by default if not set
        $pin_time_default_value = 6;
    }

    //Set PIN expiration time
    variable_set('pin_expiration_time_value', $pin_time_default_value);
    
    $form['handshaking_value'] = array(
        '#type' => 'checkbox',
        '#default_value' => variable_get('handshaking_value'),
        '#title' => t('Enforce MD5 Handshaking')
    );
    $form['device_activity'] = array(
        '#type' => 'checkbox',
        '#default_value' => variable_get('device_activity'),
        '#title' => t('All device of the charging station are inactive')
    );
    
    $form['charging_station_connection'] = array(
        '#type' => 'checkbox',
        '#default_value' => variable_get('charging_station_connection'),
        '#title' => t('No connection to the charging station')
    );
    $form['device_usage_lower_limit'] = array(
        '#type' => 'checkbox',
        '#default_value' => variable_get('device_usage_lower_limit'),
        '#title' => t('Usage rate is lower than ')
    );

    foreach (range(0, 12) as $number) {
        
        $options[$number] = $number."%";;
    }
    
    $form['device_usage_lower_limit_value'] = array(
        '#type' => 'select',
        '#options' => $options,
        '#default_value' => variable_get('device_usage_lower_limit_value')
    );
    
    
    $form['device_usage_higher_limit'] = array(
        '#type' => 'checkbox',
        '#default_value' => variable_get('device_usage_higher_limit'),
        '#title' => t('Usage rate is higher than')
    );
    
    $form['device_usage_higher_limit_value'] = array(
        '#type' => 'select',
        '#options' => $options,
        '#default_value' => variable_get('device_usage_higher_limit_value')
    );
    
    $form['device_usage_count_lower_limit'] = array(
        '#type' => 'checkbox',
        '#default_value' => variable_get('device_usage_count_lower_limit'),
        '#title' => t('Usage count is lower than')
    );
    $form['device_usage_count_lower_limit_value'] = array(
        '#type' => 'textfield',
        '#size' => 5,
        '#default_value' => variable_get('device_usage_count_lower_limit_value')
    );
    
    $form['device_usage_count_higher_limit'] = array(
        '#type' => 'checkbox',
        '#default_value' => variable_get('device_usage_count_higher_limit'),
        '#title' => t('Usage count is higher than')
    );
    $form['device_usage_count_higher_limit_value'] = array(
        '#type' => 'textfield',
        '#size' => 5,
        '#default_value' => variable_get('device_usage_count_higher_limit_value')
    );
    
    $form['device_total_usage_count_lower_limit'] = array(
        '#type' => 'checkbox',
        '#default_value' => variable_get('device_total_usage_count_lower_limit'),
        '#title' => t('Total Usage hours are lower than')
    );
    $form['device_total_usage_count_lower_limit_value'] = array(
        '#type' => 'textfield',
        '#size' => 5,
        '#default_value' => variable_get('device_total_usage_count_lower_limit_value')
    );

    $form['device_total_usage_count_higher_limit'] = array(
        '#type' => 'checkbox',
        '#default_value' => variable_get('device_total_usage_count_higher_limit'),
        '#title' => t('Total Usage hours are higher than')
    );
    $form['device_total_usage_count_higher_limit_value'] = array(
        '#type' => 'textfield',
        '#size' => 5,
        '#default_value' => variable_get('device_total_usage_count_higher_limit_value')
    );
    $form['pin_expiration_time_value'] = array(
        '#title' =>t('PIN expiration time in hours'),
        '#type' => 'textfield',
        '#size' => 5,
        '#default_value' => variable_get('pin_expiration_time_value',6)
    );
    
    $form['submit_button'] = array(
        '#type' => 'submit',
        '#value' => t('Save settings'),
        '#submit' => array('aitico_core_settings_form_submit'),
    );

    return $form;
}

/**
 * Alert settings form submit handler to save settings in variable table
 * 
 * @param $form
 * 
 * @param $form_state
 */
function aitico_core_settings_form_submit($form, $form_state) {
    variable_set('device_activity', intval($form_state['values']['device_activity']));
    variable_set('charging_station_connection', intval($form_state['values']['charging_station_connection']));
    variable_set('device_usage_lower_limit', intval($form_state['values']['device_usage_lower_limit']));
    variable_set('device_usage_higher_limit', intval($form_state['values']['device_usage_higher_limit']));
    variable_set('device_usage_count_lower_limit', intval($form_state['values']['device_usage_count_lower_limit']));
    variable_set('device_usage_count_higher_limit', intval($form_state['values']['device_usage_count_higher_limit']));
    variable_set('device_total_usage_count_lower_limit', intval($form_state['values']['device_total_usage_count_lower_limit']));
    variable_set('device_total_usage_count_higher_limit', intval($form_state['values']['device_total_usage_count_higher_limit']));

    variable_set('device_usage_lower_limit_value', intval($form_state['values']['device_usage_lower_limit_value']));
    variable_set('device_usage_higher_limit_value', intval($form_state['values']['device_usage_higher_limit_value']));
    variable_set('device_usage_count_lower_limit_value', intval($form_state['values']['device_usage_count_lower_limit_value']));
    variable_set('device_usage_count_higher_limit_value', intval($form_state['values']['device_usage_count_higher_limit_value']));
    variable_set('device_total_usage_count_lower_limit_value', intval($form_state['values']['device_total_usage_count_lower_limit_value']));
    variable_set('device_total_usage_count_higher_limit_value', intval($form_state['values']['device_total_usage_count_higher_limit_value']));
    variable_set('pin_expiration_time_value', intval($form_state['values']['pin_expiration_time_value']));
    variable_set('handshaking_value', intval($form_state['values']['handshaking_value']));
    
    drupal_set_message(t('Saved alarm settings succesfully.'));
}
