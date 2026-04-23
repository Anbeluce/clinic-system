<?php
require_once('wp-load.php');

$appointments = get_posts(array(
    'post_type' => 'appointment',
    'posts_per_page' => -1,
));

echo "Total Appointments: " . count($appointments) . "\n";
foreach ($appointments as $app) {
    echo "ID: " . $app->ID . " | Title: " . $app->post_title . "\n";
    echo "  _selected_doctor: " . get_post_meta($app->ID, '_selected_doctor', true) . "\n";
    echo "  _doctor_id: " . get_post_meta($app->ID, '_doctor_id', true) . "\n";
    echo "------------------\n";
}

$doctors = get_posts(array('post_type' => 'doctor', 'posts_per_page' => -1));
echo "\nTotal Doctors: " . count($doctors) . "\n";
foreach ($doctors as $doc) {
    echo "ID: " . $doc->ID . " | Title: " . $doc->post_title . "\n";
    echo "  _doctor_user_id: " . get_post_meta($doc->ID, '_doctor_user_id', true) . "\n";
    echo "------------------\n";
}
