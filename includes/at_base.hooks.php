<?php

/**
 * @file at_base.hooks.php
 */

/**
 * Implements hook_menu()
 */
function at_base_menu()
{
    return at()->getHookImplementation()->getHookMenu()->execute();
}

/**
 * Implements hook_flush_caches().
 *
 * @tag cache
 */
function at_base_flush_caches()
{
    return at()->getHookImplementation()->getHookFlushCache()->execute();
}

/**
 * Implements hook_modules_enabled().
 */
function at_base_modules_enabled($modules)
{
    // Refresh the cached-modules
    at_modules('at_base', TRUE);

    // Rebuild module weight
    (new FlushCache())->fixModuleWeight();
}

/**
 * Implements hook_block_info()
 */
function at_base_block_info()
{
    return at()->getHookImplementation()->getHookBlockInfo()->execute();
}

/**
 * Implements hook_block_view
 */
function at_base_block_view($delta)
{
    return at()->getHookImplementation()->getHookBlockView($delta)->execute();
}

/**
 * Implements hook_admin_paths()
 */
function at_base_admin_paths()
{
    return array('at/twig' => TRUE);
}

/**
 * Implements hook_entity_view()
 */
function at_base_entity_view($entity, $type, $view_mode, $langcode)
{
    at()->getApi()->getBreadcrumbAPI()->checkEntityConfig($entity, $type, $view_mode, $langcode);
}

if (defined('AT_BASE_ENTITY_TEMPLATE') && constant('AT_BASE_ENTITY_TEMPLATE')) {

    /**
     * Implements hook_entity_view_alter()
     */
    function at_base_entity_view_alter(&$build, $entity_type)
    {
        at()
            ->getHookImplementation()
            ->getHookEntityViewAlter($build, $entity_type)
            ->execute();
    }

}

/**
 * Implements hook_entity_insert()
 */
function at_base_entity_update($entity, $type)
{
    at_container('cache.warmer')
        ->setEventName('entity_update')
        ->setContext(array('entity_type' => $type, 'entity' => $entity))
        ->warm()
    ;
}

/**
 * Implements hook_entity_insert()
 */
function at_base_entity_insert($entity, $type)
{
    at_container('cache.warmer')
        ->setEventName('entity_insert')
        ->setContext(array('entity_type' => $type, 'entity' => $entity))
        ->warm()
    ;
}

/**
 * Implements hook_entity_insert()
 */
function at_base_entity_delete($entity, $type)
{
    at_container('cache.warmer')
        ->setEventName('entity_delete')
        ->setContext(array('entity_type' => $type, 'entity' => $entity))
        ->warm()
    ;
}

/**
 * Implements hook_user_login()
 */
function at_base_user_login(&$edit, $account)
{
    at_container('cache.warmer')
        ->setEventName('user_login')
        ->setContext(array('entity_type' => 'user', 'entity' => $account))
        ->warm()
    ;
}

/**
 * Implements hook_user_login()
 */
function at_base_user_logout($account)
{
    at_container('cache.warmer')
        ->setEventName('user_logout')
        ->setContext(array('entity_type' => 'user', 'entity' => $account))
        ->warm()
    ;
}

/**
 * Implements hook_page_build().
 *
 * Renders blocks into their regions.
 *
 * @see Controller::prepareContextBlocks()
 */
function at_base_page_build(&$page)
{
//    if (at_container()->hasParameter('page.blocks')) {
//        at()
//            ->getHookImplementation()
//            ->getHookPageBuild($page, at_container()->getParameter('page.blocks'))
//            ->execute();
//    }
//    at()->getApi()->getBreadcrumbAPI()->pageBuild();
}

/**
 * Implements hook_form_FORM_ID_alter().
 *
 * Add 'Rebuild service container' and 'Flush compiled Twig templates' buttons
 * to /admin/config/development/performance form.
 */
function at_base_form_system_performance_settings_alter(&$form, $form_state)
{
    $form['clear_cache']['at_container'] = [
        '#type'   => 'submit',
        '#value'  => t('Rebuild service container'),
        '#submit' => [function() {
                $fileName = variable_get('file_private_path', '') . '/at_container.php';
                if (file_exists($fileName)) {
                    unlink($fileName);
                }
            }],
    ];

    $form['clear_cache']['at_twig_templates'] = [
        '#type'   => 'submit',
        '#value'  => t('Flush compiled Twig templates'),
        '#submit' => [function() {
                $files = file_scan_directory(drupal_realpath(variable_get('file_temporary_path')) . '/', '/\.php$/');
                foreach ($files as $file) {
                    unlink($file->uri);
                }
            }],
    ];
}
