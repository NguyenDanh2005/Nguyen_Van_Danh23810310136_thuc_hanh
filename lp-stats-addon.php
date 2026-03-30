<?php


if ( ! defined( 'ABSPATH' ) ) exit; // Bảo mật: Không cho phép truy cập trực tiếp

// 1. Hàm lấy dữ liệu thống kê
function lp_get_statistics() {
    global $wpdb;

    // Tổng số khóa học (Post type là 'lp_course')
    $total_courses = wp_count_posts('lp_course')->publish;

    // Tổng số học viên (Dựa trên bảng user_items của LearnPress)
    $total_students = $wpdb->get_var("SELECT COUNT(DISTINCT user_id) FROM {$wpdb->prefix}learnpress_user_items WHERE item_type = 'lp_course'");

    // Số lượng khóa học đã hoàn thành (Status: 'completed')
    $completed_courses = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}learnpress_user_items WHERE status = 'completed' AND item_type = 'lp_course'");

    return array(
        'courses'   => $total_courses ? $total_courses : 0,
        'students'  => $total_students ? $total_students : 0,
        'completed' => $completed_courses ? $completed_courses : 0,
    );
}

// 2. Tạo Dashboard Widget trong trang Admin
add_action('wp_dashboard_setup', 'lp_stats_add_dashboard_widgets');
function lp_stats_add_dashboard_widgets() {
    wp_add_dashboard_widget('lp_stats_widget', 'LearnPress Statistics', 'lp_stats_widget_display');
}

function lp_stats_widget_display() {
    $stats = lp_get_statistics();
    echo "<ul>";
    echo "<li><strong>Tổng khóa học:</strong> " . $stats['courses'] . "</li>";
    echo "<li><strong>Tổng học viên:</strong> " . $stats['students'] . "</li>";
    echo "<li><strong>Khóa học hoàn thành:</strong> " . $stats['completed'] . "</li>";
    echo "</ul>";
}

// 3. Tạo Shortcode [lp_total_stats] để hiển thị ngoài Frontend
add_shortcode('lp_total_stats', 'lp_stats_shortcode_display');
function lp_stats_shortcode_display() {
    $stats = lp_get_statistics();
    $html = '<div class="lp-stats-container" style="border: 1px solid #ddd; padding: 15px; background: #f9f9f9;">';
    $html .= '<h3>Thống kê học tập</h3>';
    $html .= '<p>Khóa học hiện có: ' . $stats['courses'] . '</p>';
    $html .= '<p>Học viên đang học: ' . $stats['students'] . '</p>';
    $html .= '<p>Khóa học đã hoàn thành: ' . $stats['completed'] . '</p>';
    $html .= '</div>';
    return $html;
}