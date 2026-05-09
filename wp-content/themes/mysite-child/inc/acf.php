<?php
/**
 * ACF field group registration.
 *
 * Field groups are registered in PHP (not JSON-synced) so definitions live
 * in version control alongside the templates that consume them. ACF Free
 * only — no repeater / flexible / clone fields (those are Pro features).
 */

defined('ABSPATH') || exit;

add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group([
        'key'    => 'group_mysite_product_specs',
        'title'  => __('Product specifications', 'mysite-child'),
        'fields' => [
            [
                'key'          => 'field_mysite_product_dimensions',
                'label'        => __('Dimensions', 'mysite-child'),
                'name'         => 'dimensions',
                'type'         => 'text',
                'instructions' => __('Width × Depth × Height (e.g., 80" × 36" × 32").', 'mysite-child'),
            ],
            [
                'key'          => 'field_mysite_product_material',
                'label'        => __('Material', 'mysite-child'),
                'name'         => 'material',
                'type'         => 'text',
                'instructions' => __('Primary material (e.g., Solid oak, Linen, Stoneware).', 'mysite-child'),
            ],
            [
                'key'   => 'field_mysite_product_care',
                'label' => __('Care instructions', 'mysite-child'),
                'name'  => 'care_instructions',
                'type'  => 'textarea',
                'rows'  => 4,
            ],
            [
                'key'           => 'field_mysite_product_assembly',
                'label'         => __('Assembly required', 'mysite-child'),
                'name'          => 'assembly_required',
                'type'          => 'true_false',
                'ui'            => 1,
                'default_value' => 0,
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'product',
                ],
            ],
        ],
        'menu_order' => 10,
        'position'   => 'normal',
    ]);
});
