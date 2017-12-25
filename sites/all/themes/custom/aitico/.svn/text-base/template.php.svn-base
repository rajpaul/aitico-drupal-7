<?php

/**
 * Implements theme_theme().
 *
 * @param $existing
 * @param $type
 * @param $theme
 * @param $path
 * @return array
 */
function aitico_theme($existing, $type, $theme, $path) {
    return array(
        'status-messages' => array(
            'variables' => array(),
            'template' => 'status-messages',
            'path' => $path . '/templates',
        ),
    );
}

/**
 * Implements theme_menu_tree__MENU_NAME().
 *
 * @param $variables
 * @return string
 */
function aitico_menu_tree__aitico_main_menu($variables) {
    return '<ul class="nav ace-nav">' . $variables['tree'] . '</ul>';
}

/**
 * Implements theme_menu_link__MENU_NAME().
 * @param $variables
 * @return string
 */
function aitico_menu_link__aitico_main_menu($variables) {
    $element = $variables['element'];

    $element['#attributes']['class'][] = 'purple';
    if ($element['#original_link']['in_active_trail'])
        $element['#attributes']['class'][] = 'active';

    $element['#title'] = '<span>' . $element['#title'] . '</span>';
    $element['#localized_options']['html'] = TRUE;

    $link = l($element['#title'], $element['#href'], $element['#localized_options']);

    return '<li ' . drupal_attributes($element['#attributes']) . '>' . $link  . '</li>';
}

/**
 * Implements theme_checkbox().
 * 
 * @param $variables
 * @return string
 */
function aitico_checkbox($variables) {
    $element = &$variables['element'];

    $label = '<span class="lbl"> ' . $element['#title'] . '</span>';

    return '<label>' . theme_checkbox($variables) . $label . '</label>';
}

/**
 * Implements theme_button().
 *
 * @param $variables
 * @return string
 */
function aitico_button($variables) {
    $element = &$variables['element'];

    $element['#attributes']['class'][] = 'btn';
    if ($element['#type'] == 'submit')
        $element['#attributes']['class'][] = 'btn-primary';

    return theme_button($variables);
}

/**
 * Implements theme_form_element().
 *
 * @param $variables
 * @return string
 */
function aitico_form_element($variables) {
    $element = &$variables['element'];

    if ($element['#type'] == 'checkbox')
        $element['#title_display'] = 'none';

    return theme_form_element($variables);
}

/**
 * Implements theme_status_messages
 *
 * @param $variables
 * @return string
 */
function aitico_status_messages($variables) {
    $display = $variables['display'];

    return theme('status-messages', array(
        'message_groups' => drupal_get_messages($display),
    ));
}

function aitico_preprocess(&$variables, $hook) {
    drupal_add_http_header('Expires', 'Sun, 19 Nov 1978 05:00:00 GMT');
    drupal_add_http_header('Cache-Control', 'no-cache, max-age=0, must-revalidate, no-store');
}