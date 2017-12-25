<?php


/**
 * @return null
 */
function aitico_ui_node_page() {
    if (user_access('PAGE_ADMINISTRATION'))
        drupal_goto('administration');
    if (user_access('PAGE_RENT'))
        drupal_goto('rent');

    return '';
}

/**
 * @return null|string
 */
function aitico_ui_403() {
    if (user_is_anonymous())
        return drupal_get_form('user_login');

    $message = t('You are not authorized to access this page.');

    drupal_set_title(t('Access denied'));
    drupal_set_message($message, 'error');

    return $message;
}

/**
 * @return null|string
 */
function aitico_ui_404() {
    $message = t('The requested page "@path" could not be found.', array('@path' => request_uri()));

    drupal_set_title(t('Page not found'));
    drupal_set_message($message, 'error');

    return $message;
}