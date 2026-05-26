<?php
/*
Plugin Name: Hệ thống Đặt lịch Khám
Description: Plugin hỗ trợ đặt lịch khám bệnh và nhắc hẹn tự động qua Email/SMS.
Version: 1.1
Author: Team Dev
*/

// Ngăn chặn truy cập trực tiếp vào file
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Enqueue Google Font Inter và chèn CSS ghi đè font lỗi cho toàn bộ giao diện của Hệ thống Đặt lịch
function cb_enqueue_vietnamese_font_globally() {
    wp_enqueue_style('cb-google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap', array(), null);
    
    $custom_css = "
        body, input, select, textarea, button, p, h1, h2, h3, h4, h5, h6, td, th, span, strong, a, li, 
        .clinic-booking-container, .doctor-dashboard, .cb-schedule-manager, 
        .clinic-history-container, .clinic-auth-page, .profile-settings-wrapper {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !important;
        }
        .fa, .fas, .far, .fab, .fa-solid, .fa-regular, .fa-brands {
            font-family: 'Font Awesome 6 Free', 'Font Awesome 6 Brands', 'Font Awesome 5 Free', 'Font Awesome 5 Brands', sans-serif !important;
        }
    ";
    wp_add_inline_style('cb-google-fonts', $custom_css);
}
add_action('wp_enqueue_scripts', 'cb_enqueue_vietnamese_font_globally');

// Hàm khởi tạo Custom Post Type cho 'Cuộc hẹn'
function create_appointment_post_type() {
    $args = array(
        'labels' => array(
            'name' => 'Lịch khám',
            'singular_name' => 'Lịch khám',
            'add_new' => 'Thêm lịch khám mới',
            'add_new_item' => 'Thêm lịch khám',
            'edit_item' => 'Sửa lịch khám',
            'new_item' => 'Lịch khám mới',
            'view_item' => 'Xem lịch khám',
            'search_items' => 'Tìm kiếm lịch khám',
            'not_found' => 'Không tìm thấy lịch khám nào',
            'not_found_in_trash' => 'Không có lịch khám nào trong thùng rác',
            'all_items' => 'Tất cả lịch khám',
            'menu_name' => 'Lịch khám'
        ),
        'public' => true,
        'has_archive' => false,
        'supports' => array( 'title', 'custom-fields' ), // Tiêu đề và trường tùy chỉnh
        'menu_icon' => 'dashicons-calendar-alt', // Icon hiển thị trong admin
        'show_in_rest' => true, // Hỗ trợ REST API (cần thiết nếu sau này dùng React/Vue hoặc AJAX)
    );
    
    register_post_type( 'appointment', $args );
}

// Hook vào lúc init để đăng ký post type
add_action( 'init', 'create_appointment_post_type' );

// Đăng ký Custom Post Status 'completed' (Đã khám xong)
function cb_register_custom_post_status() {
    register_post_status( 'completed', array(
        'label'                     => 'Đã khám xong',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Đã khám xong <span class="count">(%s)</span>', 'Đã khám xong <span class="count">(%s)</span>' ),
    ) );
}
add_action( 'init', 'cb_register_custom_post_status' );

// Đăng ký Custom Post Type 'review' (Đánh giá Bác sĩ)
function cb_register_review_post_type() {
    $args = array(
        'labels' => array(
            'name'               => 'Đánh giá Bác sĩ',
            'singular_name'      => 'Đánh giá',
            'add_new'            => 'Thêm đánh giá mới',
            'add_new_item'       => 'Thêm đánh giá',
            'edit_item'          => 'Sửa đánh giá',
            'new_item'           => 'Đánh giá mới',
            'view_item'          => 'Xem đánh giá',
            'search_items'       => 'Tìm kiếm đánh giá',
            'not_found'          => 'Không tìm thấy đánh giá nào',
            'all_items'          => 'Tất cả đánh giá',
            'menu_name'          => 'Đánh giá Bác sĩ'
        ),
        'public'             => false, // Không cần hiển thị frontend dưới dạng trang riêng lẻ
        'show_ui'            => true,  // Nhưng hiển thị UI trong admin
        'show_in_menu'       => true,
        'has_archive'        => false,
        'supports'           => array( 'title', 'editor' ), // Tiêu đề và nội dung nhận xét
        'menu_icon'          => 'dashicons-star-filled',
    );
    register_post_type( 'review', $args );
}
add_action( 'init', 'cb_register_review_post_type' );

// Thêm cột trong Admin danh sách review
add_filter( 'manage_review_posts_columns', 'cb_set_review_posts_columns' );
function cb_set_review_posts_columns( $columns ) {
    $new_columns = array(
        'cb_rating'      => 'Số sao (Rating)',
        'cb_doctor'      => 'Bác sĩ được đánh giá',
        'cb_appointment' => 'Lịch khám liên quan',
    );
    $title_index = array_search( 'title', array_keys( $columns ) );
    if ( $title_index !== false ) {
        $columns = array_slice( $columns, 0, $title_index + 1, true ) + $new_columns + array_slice( $columns, $title_index + 1, null, true );
    } else {
        $columns = array_merge( $columns, $new_columns );
    }
    return $columns;
}

// Điền dữ liệu vào các cột tùy chỉnh
add_action( 'manage_review_posts_custom_column', 'cb_fill_review_posts_columns', 10, 2 );
function cb_fill_review_posts_columns( $column, $post_id ) {
    switch ( $column ) {
        case 'cb_rating':
            $rating = get_post_meta( $post_id, '_rating', true );
            $stars = str_repeat( '⭐', intval( $rating ) );
            echo esc_html( $rating ) . ' ' . $stars;
            break;
        case 'cb_doctor':
            $doctor_id = get_post_meta( $post_id, '_doctor_id', true );
            if ( $doctor_id ) {
                echo '<a href="' . esc_url( get_edit_post_link( $doctor_id ) ) . '">' . esc_html( get_the_title( $doctor_id ) ) . '</a>';
            } else {
                echo '<span style="color:#a0aec0;">Không rõ</span>';
            }
            break;
        case 'cb_appointment':
            $app_id = get_post_meta( $post_id, '_appointment_id', true );
            if ( $app_id ) {
                echo '<a href="' . esc_url( get_edit_post_link( $app_id ) ) . '">Lịch hẹn #' . esc_html( $app_id ) . '</a>';
            } else {
                echo '<span style="color:#a0aec0;">Không rõ</span>';
            }
            break;
    }
}

// Hàm khởi tạo Custom Post Type cho 'Bác sĩ'
function create_doctor_post_type() {
    $args = array(
        'labels' => array(
            'name' => 'Bác sĩ',
            'singular_name' => 'Bác sĩ',
            'add_new' => 'Thêm Bác sĩ',
            'add_new_item' => 'Thêm Bác sĩ mới',
            'edit_item' => 'Sửa Bác sĩ',
            'menu_name' => 'Bác sĩ'
        ),
        'public' => true,
        'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt' ), // Hỗ trợ ảnh đại diện và tóm tắt
        'menu_icon' => 'dashicons-groups',
        'has_archive' => false,
        'rewrite' => array('slug' => 'bac-si'), // Đường dẫn: domain.com/bac-si/ten-bac-si
    );
    register_post_type( 'doctor', $args );
    add_theme_support( 'post-thumbnails' ); // Kích hoạt tính năng ảnh đại diện

    // Thêm role Bác sĩ nếu chưa có
    if ( ! get_role( 'doctor' ) ) {
        add_role( 'doctor', 'Bác sĩ', array(
            'read'         => true,
            'edit_posts'   => false,
            'delete_posts' => false,
        ) );
    }

    // Taxonomy: Chi nhánh
    register_taxonomy('clinic_branch', array('doctor'), array(
        'hierarchical'      => true,
        'labels'            => array(
            'name'              => 'Chi nhánh',
            'singular_name'     => 'Chi nhánh',
            'menu_name'         => 'Chi nhánh',
            'add_new_item'      => 'Thêm Chi nhánh mới',
            'edit_item'         => 'Sửa Chi nhánh'
        ),
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'branch' ),
    ));

    // Taxonomy: Chuyên khoa
    register_taxonomy('specialty', array('doctor'), array(
        'hierarchical'      => true,
        'labels'            => array(
            'name'              => 'Chuyên khoa',
            'singular_name'     => 'Chuyên khoa',
            'menu_name'         => 'Chuyên khoa',
            'add_new_item'      => 'Thêm Chuyên khoa mới',
            'edit_item'         => 'Sửa Chuyên khoa'
        ),
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'specialty' ),
    ));
}
add_action( 'init', 'create_doctor_post_type' );

// Tạo Shortcode hiển thị Form đặt lịch
function clinic_booking_form_shortcode() {
    ob_start();
    $current_user = wp_get_current_user();
    $is_logged_in = is_user_logged_in();

    // Xử lý dữ liệu khi người dùng bấm nút "Xác nhận Đặt lịch"
    if ( isset( $_POST['submit_booking'] ) ) {
        // Làm sạch dữ liệu đầu vào để bảo mật
        $clinic          = sanitize_text_field( $_POST['clinic'] ?? '' );
        $specialty       = sanitize_text_field( $_POST['specialty'] ?? '' );
        $selected_doctor = sanitize_text_field( $_POST['selected_doctor'] ?? '' );
        $booking_date    = sanitize_text_field( $_POST['booking_date'] ?? '' );
        $booking_time    = sanitize_text_field( $_POST['booking_time'] ?? '' );
        
        $registrant_name = sanitize_text_field( $_POST['registrant_name'] ?? '' );
        $patient_phone   = sanitize_text_field( $_POST['patient_phone'] ?? '' );
        $patient_email   = sanitize_email( $_POST['patient_email'] ?? '' );
        $patient_name    = sanitize_text_field( $_POST['patient_name'] ?? '' );
        $patient_dob     = sanitize_text_field( $_POST['patient_dob'] ?? '' );
        $patient_gender  = sanitize_text_field( $_POST['patient_gender'] ?? '' );
        $symptoms        = sanitize_textarea_field( $_POST['symptoms'] ?? '' );

        // Cấu trúc mảng dữ liệu để tạo một "Cuộc hẹn" mới trong Database
        $appointment_data = array(
            'post_title'   => 'Lịch khám: ' . $patient_name . ' - ' . $booking_date . ' ' . $booking_time,
            'post_content' => 'Triệu chứng: ' . $symptoms,
            'post_status'  => 'pending', // Trạng thái chờ xác nhận
            'post_type'    => 'appointment', // Đúng với Custom Post Type đã tạo
            'post_author'  => get_current_user_id(), // Gắn ID người dùng nếu đã đăng nhập
        );

        // Chèn dữ liệu vào bảng wp_posts
        $post_id = wp_insert_post( $appointment_data );

        if ( $post_id ) {
            // Lưu các thông tin phụ vào Custom Fields
            update_post_meta( $post_id, '_clinic', $clinic );
            update_post_meta( $post_id, '_specialty', $specialty );
            update_post_meta( $post_id, '_selected_doctor', $selected_doctor );
            update_post_meta( $post_id, '_doctor_id', sanitize_text_field( $_POST['doctor_id'] ?? '' ) );
            update_post_meta( $post_id, '_booking_date', $booking_date );
            update_post_meta( $post_id, '_booking_time', $booking_time );
            update_post_meta( $post_id, '_registrant_name', $registrant_name );
            update_post_meta( $post_id, '_patient_phone', $patient_phone );
            update_post_meta( $post_id, '_patient_email', $patient_email );
            update_post_meta( $post_id, '_patient_name', $patient_name );
            update_post_meta( $post_id, '_patient_dob', $patient_dob );
            update_post_meta( $post_id, '_patient_gender', $patient_gender );
            
            // BẮT ĐẦU PHẦN GỬI EMAIL TỰ ĐỘNG
            $to = $patient_email; // Gửi đến email khách hàng vừa nhập
            
            // Lấy email admin từ Cài đặt, nếu chưa cài thì lấy email mặc định của web
            $admin_email = get_option('cb_admin_email');
            if (empty($admin_email)) {
                $admin_email = get_option('admin_email');
            }
            
            $subject = 'Xác nhận đặt lịch khám thành công';
            $admin_subject = '🎉 CÓ LỊCH KHÁM MỚI TỪ: ' . $registrant_name;
            
            // Xây dựng nội dung email gửi cho KHÁCH HÀNG
            $message = "Chào " . $registrant_name . ",\n\n";
            $message .= "Cảm ơn bạn đã đặt lịch khám. Hệ thống đã ghi nhận thông tin chi tiết như sau:\n\n";
            $message .= "- Phòng khám: " . $clinic . "\n";
            $message .= "- Chuyên khoa: " . $specialty . "\n";
            $message .= "- Bác sĩ yêu cầu: " . $selected_doctor . "\n";
            $message .= "- Ngày khám: " . $booking_date . " " . $booking_time . "\n";
            $message .= "- Họ tên bệnh nhân: " . $patient_name . " (" . $patient_gender . ", sinh ngày: " . $patient_dob . ")\n";
            $message .= "- Số điện thoại liên hệ: " . $patient_phone . "\n";
            $message .= "- Triệu chứng/Ghi chú: " . $symptoms . "\n\n";
            $message .= "Vui lòng giữ điện thoại, bộ phận Lễ tân của chúng tôi sẽ sớm liên hệ lại để chốt giờ khám chính xác cho bạn.\n\n";
            $message .= "Trân trọng,\nHệ thống Phòng khám";

            // Xây dựng nội dung (text) chung để gửi cho Webhook
            $admin_message = "Hệ thống vừa nhận được một đăng ký lịch khám mới:\n\n";
            $admin_message .= "- Người đăng ký: " . $registrant_name . "\n";
            $admin_message .= "- Điện thoại: " . $patient_phone . "\n";
            $admin_message .= "- Email: " . $patient_email . "\n";
            $admin_message .= "- Họ tên bệnh nhân: " . $patient_name . " (" . $patient_gender . ", sinh ngày: " . $patient_dob . ")\n";
            $admin_message .= "- Phòng khám: " . $clinic . "\n";
            $admin_message .= "- Chuyên khoa: " . $specialty . "\n";
            $admin_message .= "- Bác sĩ yêu cầu: " . $selected_doctor . "\n";
            $admin_message .= "- Thời gian: " . $booking_date . " " . $booking_time . "\n";
            $admin_message .= "- Ghi chú: " . $symptoms . "\n\n";
            $admin_message .= "Vui lòng đăng nhập vào quản trị website để xem chi tiết hoặc gọi ngay cho khách.";

            // --- GỬI WEBHOOK CHO ADMIN THAY VÌ EMAIL ---
            $webhook_url = get_option('cb_webhook_url');
            if (!empty($webhook_url)) {
                // Định dạng theo chuẩn Embed của Discord / Slack (có thuộc tính content và embeds)
                $webhook_data = array(
                    'content' => '🔔 **CÓ LỊCH ĐẶT KHÁM MỚI**',
                    'embeds' => array(
                        array(
                            'title' => 'Chi tiết thông tin đăng ký',
                            'color' => 3447003, // Màu xanh dương
                            'fields' => array(
                                array('name' => 'Người đăng ký', 'value' => $registrant_name, 'inline' => true),
                                array('name' => 'Điện thoại', 'value' => $patient_phone, 'inline' => true),
                                array('name' => 'Email', 'value' => empty($patient_email) ? 'Không có' : $patient_email, 'inline' => true),
                                array('name' => 'Họ tên bệnh nhân', 'value' => $patient_name . ' (' . $patient_gender . ')', 'inline' => true),
                                array('name' => 'Ngày sinh', 'value' => empty($patient_dob) ? 'Không có' : $patient_dob, 'inline' => true),
                                array('name' => 'Phòng khám', 'value' => $clinic, 'inline' => true),
                                array('name' => 'Chuyên khoa', 'value' => $specialty, 'inline' => true),
                                array('name' => 'Bác sĩ', 'value' => $selected_doctor, 'inline' => true),
                                array('name' => 'Thời gian khám', 'value' => $booking_time . ' ngày ' . $booking_date, 'inline' => false),
                                array('name' => 'Lời nhắn', 'value' => empty($symptoms) ? 'Không có' : $symptoms, 'inline' => false),
                            )
                        )
                    )
                );

                wp_remote_post($webhook_url, array(
                    'headers'     => array('Content-Type' => 'application/json'),
                    'body'        => wp_json_encode($webhook_data),
                    'method'      => 'POST',
                    'data_format' => 'body',
                    'timeout'     => 15,
                    'sslverify'   => false
                ));
            }

            // --- TÍCH HỢP BREVO API (CHỈ GỬI CHO KHÁCH HÀNG) ---
            $brevo_api_key = get_option('cb_brevo_api_key');
            $brevo_sender_email = get_option('cb_brevo_sender_email');
            if (empty($brevo_sender_email)) {
                $brevo_sender_email = 'no-reply@yourdomain.com';
            }

            // Nếu người dùng có điền API Key trong phần Cài đặt
            if (!empty($brevo_api_key) && $brevo_api_key !== 'ĐIỀN_API_KEY_CỦA_BẠN_VÀO_ĐÂY') {
                // Gửi email cho Khách hàng
                $response = wp_remote_post('https://api.brevo.com/v3/smtp/email', array(
                    'headers' => array(
                        'accept' => 'application/json',
                        'api-key' => $brevo_api_key,
                        'content-type' => 'application/json'
                    ),
                    'body' => wp_json_encode(array(
                        'sender' => array('name' => 'Phòng Khám', 'email' => $brevo_sender_email),
                        'to' => array(array('email' => $to, 'name' => $registrant_name)),
                        'subject' => $subject,
                        'textContent' => $message 
                    )),
                    'data_format' => 'body'
                ));

                $response_code = wp_remote_retrieve_response_code($response);
                $mail_sent = ($response_code === 201 || $response_code === 200);
            } else {
                // Nếu chưa điền API Key, fallback về wp_mail mặc định
                $headers = array('Content-Type: text/plain; charset=UTF-8');
                $mail_sent = wp_mail( $to, $subject, $message, $headers );
            }

            if ( $mail_sent ) {
                echo '<p style="color: green; font-weight: bold; margin-bottom: 15px;">✅ Đặt lịch thành công! Một email xác nhận đã được gửi đến bạn.</p>';
            } else {
                echo '<p style="color: green; font-weight: bold; margin-bottom: 15px;">✅ Đặt lịch thành công! (Lưu ý: Không thể gửi email xác nhận lúc này do cấu hình máy chủ).</p>';
            }
            
            // Xóa dữ liệu POST để ngăn chặn lỗi gửi mail liên tục khi bấm F5 (Refresh)
            echo '<script>
                if ( window.history.replaceState ) {
                    window.history.replaceState( null, null, window.location.href );
                }
            </script>';
            // KẾT THÚC PHẦN GỬI EMAIL
            
        } else {
            echo '<p style="color: red; font-weight: bold; margin-bottom: 15px;">❌ Có lỗi xảy ra trong quá trình hệ thống ghi nhận, vui lòng thử lại.</p>';
        }
    }

    // Lấy dữ liệu cấu hình
    $times_opt = get_option('cb_time_slots', '08:00');
    $times = array_map('trim', explode("\n", $times_opt));

    $branches = get_terms(array(
        'taxonomy' => 'clinic_branch',
        'hide_empty' => false,
    ));
    $specialties_terms = get_terms(array(
        'taxonomy' => 'specialty',
        'hide_empty' => false,
    ));

    // Giao diện HTML của Form
    // 1. Nhúng Flatpickr & Google Fonts
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@600;700;800&display=swap');
    wp_enqueue_style('flatpickr-css', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
    wp_enqueue_script('flatpickr-js', 'https://cdn.jsdelivr.net/npm/flatpickr', array(), null, true);
    
    ?>
    <style>
        .clinic-booking-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            max-width: 1100px;
            margin: 40px auto;
            font-family: 'Inter', sans-serif;
            align-items: start;
        }
        @media (max-width: 768px) {
            .clinic-booking-container {
                grid-template-columns: 1fr;
            }
        }
        
        /* Cột bên trái: FORM */
        .clinic-premium-form {
            background: #ffffff;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 3px solid #005086;
        }
        .clinic-premium-form h3 {
            color: #ff5722;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
            margin-top: 0;
            text-transform: uppercase;
        }
        .cbf-group {
            margin-bottom: 15px;
        }
        .cbf-group-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        .cbf-group input[type="text"], 
        .cbf-group input[type="tel"], 
        .cbf-group input[type="email"], 
        .cbf-group textarea,
        .cbf-group select,
        .cbf-group-row input[type="text"], 
        .cbf-group-row input[type="tel"], 
        .cbf-group-row input[type="email"], 
        .cbf-group-row select {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #dcdcdc;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 14px;
            color: #333;
            background: #fff;
            font-family: 'Inter', sans-serif;
        }
        /* Class for error validation */
        .cbf-group select.has-error,
        .cbf-group input.has-error,
        .cbf-group-row input.has-error,
        .cbf-group-row select.has-error {
            border-color: #e53935 !important;
            background-color: #fff8f8 !important;
        }

        .cbf-group input:focus, 
        .cbf-group textarea:focus,
        .cbf-group select:focus,
        .cbf-group-row input:focus,
        .cbf-group-row select:focus {
            border-color: #005086;
            outline: none;
        }
        /* Styling select dropdown arrows */
        .cbf-group select, .cbf-group-row select {
            appearance: none;
            -webkit-appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="%23333" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>');
            background-repeat: no-repeat;
            background-position-x: 96%;
            background-position-y: center;
        }

        .cbf-radio-group {
            display: flex;
            align-items: center;
            gap: 15px;
            height: 100%;
            padding-left: 10px;
        }
        .cbf-radio-group label {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            cursor: pointer;
        }
        
        .cbf-btn-primary {
            background: #5b9bd5;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .cbf-btn-primary:hover {
            background: #4a8bc4;
        }
        .cbf-user-info-badge {
            background: #f0f7ff;
            border: 1px dashed #5b9bd5;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #1a365d;
        }
        .cbf-user-info-badge i { font-size: 24px; color: #5b9bd5; }
        .cbf-btn-secondary {
            background: #005086;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .cbf-btn-secondary:hover {
            background: #003e6b;
        }
        .cbf-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .cbf-buttons-right {
            display: flex;
            justify-content: flex-start;
            margin-top: 20px;
        }

        #cbf-step-2 {
            display: none;
        }

        /* Cột bên phải: DANH SÁCH BÁC SĨ */
        .clinic-doctors-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .doctor-card {
            display: flex;
            align-items: flex-start;
            background: #fff;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            border: 1px solid #f0f0f0;
            transition: transform 0.3s ease;
        }
        .doctor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .doctor-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 20px;
            border: 3px solid #ebf8ff;
            flex-shrink: 0;
        }
        .doctor-info h4 {
            margin: 0 0 5px 0;
            color: #2b6cb0;
            font-size: 20px;
            font-weight: 600;
        }
        .doctor-info p {
            margin: 0;
            color: #718096;
            font-size: 14px;
            line-height: 1.6;
        }
    </style>

    <div class="clinic-booking-container">
        <?php
        // Lấy danh sách bác sĩ
        $doctors_list = get_posts(array(
            'post_type' => 'doctor',
            'numberposts' => -1,
            'post_status' => 'publish'
        ));
        ?>
        <!-- CỘT 1: FORM ĐẶT LỊCH -->
        <div class="clinic-premium-form">
            <h3>ĐẶT LỊCH HẸN KHÁM</h3>
            <form method="post" action="" id="clinic-booking-form" novalidate>
                
                <!-- BƯỚC 1 -->
                <div id="cbf-step-1">
                    <div class="cbf-group">
                        <div class="cbf-input-wrap">
                            <select name="clinic" id="clinic" required>
                                <option value="" data-id="">Vui lòng chọn chi nhánh / phòng khám</option>
                                <?php 
                                if (!is_wp_error($branches) && !empty($branches)) {
                                    foreach($branches as $b) {
                                        echo '<option value="'.esc_attr($b->name).'" data-id="'.esc_attr($b->term_id).'">'.esc_html($b->name).'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="cbf-group">
                        <div class="cbf-input-wrap">
                            <select name="specialty" id="specialty" required>
                                <option value="" data-id="">Vui lòng chọn chuyên khoa</option>
                                <?php 
                                if (!is_wp_error($specialties_terms) && !empty($specialties_terms)) {
                                    foreach($specialties_terms as $s) {
                                        echo '<option value="'.esc_attr($s->name).'" data-id="'.esc_attr($s->term_id).'">'.esc_html($s->name).'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="cbf-group">
                        <div class="cbf-input-wrap">
                            <select name="selected_doctor" id="selected_doctor" required>
                                <option value="">Vui lòng chọn bác sĩ</option>
                                <?php
                                if ($doctors_list) {
                                    foreach ($doctors_list as $doc) {
                                        $avg_rating = get_post_meta($doc->ID, '_average_rating', true);
                                        $review_count = get_post_meta($doc->ID, '_review_count', true);
                                        $rating_text = '';
                                        if (!empty($avg_rating) && floatval($avg_rating) > 0) {
                                            $rating_text = ' (⭐ ' . $avg_rating . ' - ' . $review_count . ' đánh giá)';
                                        }
                                        echo '<option value="' . esc_attr($doc->post_title) . '" data-doctor-id="' . esc_attr($doc->ID) . '">' . esc_html($doc->post_title) . esc_html($rating_text) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                            <input type="hidden" name="doctor_id" id="doctor_id_hidden" value="">
                        </div>
                    </div>

                    <div class="cbf-group-row">
                        <div class="cbf-input-wrap">
                            <input type="text" name="booking_date" id="booking_date" placeholder="dd/mm/yyyy" required autocomplete="off">
                        </div>
                        <div class="cbf-input-wrap">
                            <select name="booking_time" id="booking_time" required>
                                <option value="">Giờ</option>
                                <?php foreach($times as $t): if(trim($t)) echo '<option value="'.esc_attr($t).'">'.esc_html($t).'</option>'; endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="cbf-buttons-right">
                        <button type="button" class="cbf-btn-primary" id="btn-next">Tiếp theo</button>
                    </div>
                </div>

                <!-- BƯỚC 2 -->
                <div id="cbf-step-2">
                    <?php if ( $is_logged_in ) : ?>
                        <div class="cbf-user-info-badge">
                            <i class="fas fa-user-circle"></i>
                            <div>
                                <strong>Chào <?php echo esc_html($current_user->display_name); ?></strong><br>
                                <span style="font-size: 12px; color: #666;">Đang sử dụng email: <?php echo esc_html($current_user->user_email); ?></span>
                            </div>
                        </div>
                        <input type="hidden" name="registrant_name" value="<?php echo esc_attr($current_user->display_name); ?>">
                        <input type="hidden" name="patient_email" value="<?php echo esc_attr($current_user->user_email); ?>">
                        
                        <div class="cbf-group">
                            <div class="cbf-input-wrap">
                                <input type="tel" name="patient_phone" id="patient_phone" placeholder="Số điện thoại liên hệ" required>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="cbf-group">
                            <div class="cbf-input-wrap">
                                <input type="text" name="registrant_name" id="registrant_name" placeholder="Họ tên người đăng ký" required>
                            </div>
                        </div>
                        
                        <div class="cbf-group-row">
                            <div class="cbf-input-wrap">
                                <input type="tel" name="patient_phone" id="patient_phone" placeholder="Điện thoại" required>
                            </div>
                            <div class="cbf-input-wrap">
                                <input type="email" name="patient_email" id="patient_email" placeholder="Email" required>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="cbf-group">
                        <div class="cbf-input-wrap">
                            <input type="text" name="patient_name" id="patient_name" placeholder="Họ tên người khám" required>
                        </div>
                    </div>

                    <div class="cbf-group-row">
                        <div class="cbf-input-wrap">
                            <input type="text" name="patient_dob" id="patient_dob" placeholder="Ngày sinh: dd/mm/yyyy" required>
                        </div>
                        <div class="cbf-radio-group">
                            <label><input type="radio" name="patient_gender" value="Nam" checked> Nam</label>
                            <label><input type="radio" name="patient_gender" value="Nữ"> Nữ</label>
                        </div>
                    </div>

                    <div class="cbf-group" style="margin-bottom: 0;">
                        <div class="cbf-input-wrap">
                            <textarea name="symptoms" id="symptoms" rows="4" placeholder="Để lại lời nhắn" required style="border-color: #888; border-radius: 4px; width: 100%; box-sizing: border-box; padding: 10px 15px; font-family: Inter, sans-serif; font-size: 14px;"></textarea>
                        </div>
                    </div>

                    <div class="cbf-buttons">
                        <button type="button" class="cbf-btn-secondary" id="btn-back">Trở lại</button>
                        <button type="submit" name="submit_booking" class="cbf-btn-primary">Xác nhận</button>
                    </div>
                </div>

            </form>
        </div>

        <!-- CỘT 2: DANH SÁCH BÁC SĨ -->
        <div class="clinic-doctors-list">
            <h3 style="color: #1a365d; font-size: 24px; margin-bottom: 5px; margin-top: 0; font-family: 'Inter', sans-serif;">Đội ngũ Bác sĩ</h3>            
            <div id="cb-doctors-display">
                <!-- AJAX sẽ đổ dữ liệu vào đây -->
                <?php
                if ($doctors_list) {
                    $count = 0;
                    foreach ($doctors_list as $doctor) {
                        $count++;
                        $display = ($count > 4) ? 'none' : 'flex';
                        $img_url = get_post_meta($doctor->ID, '_doctor_image_url', true);
                        if (empty($img_url)) $img_url = get_the_post_thumbnail_url($doctor->ID, 'thumbnail');
                        if (empty($img_url)) $img_url = 'https://ui-avatars.com/api/?name='.urlencode($doctor->post_title).'&background=ebf8ff&color=2b6cb0&size=200';
                        ?>
                        <?php
                        $avg_rating = get_post_meta($doctor->ID, '_average_rating', true);
                        $review_count = get_post_meta($doctor->ID, '_review_count', true);
                        ?>
                        <div class="doctor-card" style="display: <?php echo $display; ?>;">
                            <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($doctor->post_title); ?>" class="doctor-avatar">
                            <div class="doctor-info" style="width: 100%;">
                                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 5px;">
                                    <h4 style="margin: 0;"><?php echo esc_html($doctor->post_title); ?></h4>
                                    <?php if (!empty($avg_rating) && floatval($avg_rating) > 0) : ?>
                                        <div class="doctor-rating-badge" style="background: #fffaf0; border: 1px solid #fbd38d; color: #dd6b20; font-size: 13px; font-weight: 700; padding: 4px 10px; border-radius: 50px; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="fas fa-star" style="color: #ecc94b;"></i> <?php echo esc_html($avg_rating); ?>/5 (<?php echo esc_html($review_count); ?> đánh giá)
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="doctor-excerpt" style="color: #718096; font-size: 14px; line-height: 1.5; margin: 10px 0;">
                                    <?php 
                                        $excerpt = get_the_excerpt($doctor->ID);
                                        echo wp_kses_post($excerpt ? $excerpt : wp_trim_words($doctor->post_content, 20)); 
                                    ?>
                                </div>

                                <!-- Bổ sung 3 đánh giá mới nhất của Bác sĩ -->
                                <?php
                                $reviews_query = new WP_Query(array(
                                    'post_type'      => 'review',
                                    'post_status'    => 'publish',
                                    'posts_per_page' => 3,
                                    'meta_query'     => array(
                                        array(
                                            'key'   => '_doctor_id',
                                            'value' => $doctor->ID,
                                        )
                                    ),
                                    'orderby'        => 'date',
                                    'order'          => 'DESC'
                                ));
                                if ($reviews_query->have_posts()) :
                                ?>
                                    <div class="doctor-mini-reviews" style="background: #f7fafc; padding: 15px; border-radius: 12px; border: 1px solid #edf2f7; margin-top: 15px; margin-bottom: 15px;">
                                        <div style="font-size: 12px; font-weight: 800; color: #4a5568; text-transform: uppercase; margin-bottom: 10px; display: flex; align-items: center; gap: 5px;">
                                            <i class="fas fa-comment-medical" style="color: #dd6b20;"></i> Nhận xét từ bệnh nhân
                                        </div>
                                        <div style="display: flex; flex-direction: column; gap: 10px;">
                                            <?php while ($reviews_query->have_posts()) : $reviews_query->the_post(); 
                                                $rev_id = get_the_ID();
                                                $rev_rating = get_post_meta($rev_id, '_rating', true);
                                                $rev_patient = get_post_meta($rev_id, '_patient_name', true);
                                                $rev_content = get_the_content();
                                            ?>
                                                <div style="font-size: 13px; line-height: 1.4; border-bottom: 1px dashed #edf2f7; padding-bottom: 8px; margin-bottom: 2px;">
                                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                                        <strong style="color: #2b6cb0;"><?php echo esc_html($rev_patient); ?></strong>
                                                        <span style="color: #ecc94b; font-size: 11px;"><?php echo str_repeat('★', intval($rev_rating)); ?></span>
                                                    </div>
                                                    <span style="color: #4a5568; font-style: italic;">"<?php echo esc_html(wp_trim_words($rev_content, 18)); ?>"</span>
                                                </div>
                                            <?php endwhile; wp_reset_postdata(); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <a href="<?php echo get_permalink($doctor->ID); ?>" target="_blank" style="color: #2b6cb0; font-size: 13px; font-weight: 600; text-decoration: none;">Xem chi tiết profile Bác sĩ →</a>
                            </div>
                        </div>
                        <?php
                    }
                    if (count($doctors_list) > 4) {
                        echo '<div class="cb-pagination" style="margin-top: 20px; display: flex; gap: 5px; justify-content: center;">';
                        $total_pages = ceil(count($doctors_list) / 4);
                        for ($i = 1; $i <= $total_pages; $i++) {
                            $active_bg = ($i == 1) ? '#2b6cb0' : '#edf2f7';
                            $active_color = ($i == 1) ? '#fff' : '#2b6cb0';
                            echo '<button class="page-num" data-page="'.$i.'" style="padding: 5px 12px; border: none; border-radius: 4px; background: '.$active_bg.'; color: '.$active_color.'; cursor: pointer; font-weight: 600;">'.$i.'</button>';
                        }
                        echo '</div>';
                    }
                } else {
                    echo '<div style="padding: 20px; background: #fff; border-radius: 10px; border: 1px dashed #cbd5e0; color: #718096; text-align: center;">Chưa có dữ liệu bác sĩ.</div>';
                }
                ?>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Khởi tạo Flatpickr cho biểu mẫu đặt lịch
            var flatpickrInterval = setInterval(function() {
                if (typeof flatpickr !== 'undefined') {
                    clearInterval(flatpickrInterval);
                    flatpickr("#booking_date", {
                        dateFormat: "d/m/Y",
                        minDate: "today",
                        disableMobile: "true",
                        onChange: function(selectedDates, dateStr, instance) {
                            cb_update_available_time_slots();
                        }
                    });
                    flatpickr("#patient_dob", {
                        dateFormat: "d/m/Y",
                        disableMobile: "true" 
                    });
                }
            }, 100);

            // Hàm fetch lịch làm việc của bác sĩ và cập nhật Flatpickr
            function cb_fetch_doctor_schedule_and_update_flatpickr() {
                var doctorIdHidden = document.getElementById('doctor_id_hidden');
                var doctorId = doctorIdHidden ? doctorIdHidden.value : '';
                var picker = document.querySelector("#booking_date") ? document.querySelector("#booking_date")._flatpickr : null;
                var timeSelect = document.getElementById('booking_time');
                
                if (!doctorId) {
                    if (picker) {
                        picker.set("disable", []);
                    }
                    if (timeSelect) {
                        timeSelect.innerHTML = '<option value="">Vui lòng chọn bác sĩ trước</option>';
                    }
                    return;
                }
                
                if (timeSelect) {
                    timeSelect.innerHTML = '<option value="">Vui lòng chọn ngày khám</option>';
                }
                
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (this.status === 200) {
                        try {
                            var res = JSON.parse(this.responseText);
                            if (res.success) {
                                var weekly_schedule = res.data.weekly_schedule;
                                var days_off = res.data.days_off;
                                
                                var dayMap = {
                                    'sunday': 0,
                                    'monday': 1,
                                    'tuesday': 2,
                                    'wednesday': 3,
                                    'thursday': 4,
                                    'friday': 5,
                                    'saturday': 6
                                };
                                
                                var disabledDays = [];
                                for (var day in weekly_schedule) {
                                    if (!weekly_schedule[day].enabled) {
                                        disabledDays.push(dayMap[day]);
                                    }
                                }
                                
                                if (picker) {
                                    picker.set("disable", [
                                        function(date) {
                                            return disabledDays.indexOf(date.getDay()) !== -1;
                                        },
                                        function(date) {
                                            var day = ('0' + date.getDate()).slice(-2);
                                            var month = ('0' + (date.getMonth() + 1)).slice(-2);
                                            var year = date.getFullYear();
                                            var dStr = day + '/' + month + '/' + year;
                                            return days_off.indexOf(dStr) !== -1;
                                        }
                                    ]);
                                    
                                    // Kiểm tra xem ngày đã chọn trước đó có bị disable không
                                    var currentDate = picker.selectedDates[0];
                                    if (currentDate) {
                                        var dayOfWeek = currentDate.getDay();
                                        var day = ('0' + currentDate.getDate()).slice(-2);
                                        var month = ('0' + (currentDate.getMonth() + 1)).slice(-2);
                                        var year = currentDate.getFullYear();
                                        var currentDateStr = day + '/' + month + '/' + year;
                                        
                                        if (disabledDays.indexOf(dayOfWeek) !== -1 || days_off.indexOf(currentDateStr) !== -1) {
                                            picker.clear();
                                            alert('Ngày đã chọn trùng vào ngày nghỉ hoặc ngày không hoạt động của bác sĩ. Vui lòng chọn ngày khám khác.');
                                        } else {
                                            cb_update_available_time_slots();
                                        }
                                    }
                                }
                            }
                        } catch(e) {}
                    }
                };
                xhr.send('action=cb_get_doctor_schedule&doctor_id=' + doctorId);
            }

            // Hàm fetch và render các khung giờ còn trống (Auto Block Conflicting Slots)
            function cb_update_available_time_slots() {
                var doctorIdHidden = document.getElementById('doctor_id_hidden');
                var doctorId = doctorIdHidden ? doctorIdHidden.value : '';
                var bookingDateInput = document.getElementById('booking_date');
                var bookingDate = bookingDateInput ? bookingDateInput.value : '';
                var timeSelect = document.getElementById('booking_time');
                
                if (!timeSelect) return;
                
                if (!doctorId || !bookingDate) {
                    timeSelect.innerHTML = '<option value="">Chọn giờ</option>';
                    return;
                }
                
                timeSelect.innerHTML = '<option value="">Đang tải khung giờ...</option>';
                
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (this.status === 200) {
                        try {
                            var res = JSON.parse(this.responseText);
                            if (res.success) {
                                if (res.data.is_day_off) {
                                    timeSelect.innerHTML = '<option value="">Bác sĩ nghỉ phép ngày này</option>';
                                    alert(res.data.message || 'Bác sĩ đăng ký nghỉ khám phép vào ngày này.');
                                    return;
                                }
                                
                                if (!res.data.is_working) {
                                    timeSelect.innerHTML = '<option value="">Bác sĩ không làm việc ngày này</option>';
                                    alert(res.data.message || 'Bác sĩ không làm việc vào ngày này.');
                                    return;
                                }
                                
                                var available = res.data.available;
                                if (available.length === 0) {
                                    timeSelect.innerHTML = '<option value="">Hết lịch trống ngày này</option>';
                                    alert('Hết lịch trống, vui lòng chọn ngày khác.');
                                    return;
                                }
                                
                                timeSelect.innerHTML = '<option value="">Chọn giờ khám</option>';
                                available.forEach(function(slot) {
                                    var opt = document.createElement('option');
                                    opt.value = slot;
                                    opt.textContent = slot;
                                    timeSelect.appendChild(opt);
                                });
                            } else {
                                timeSelect.innerHTML = '<option value="">Lỗi tải giờ</option>';
                            }
                        } catch(e) {
                            timeSelect.innerHTML = '<option value="">Lỗi tải giờ</option>';
                        }
                    }
                };
                xhr.send('action=cb_get_available_slots&doctor_id=' + doctorId + '&booking_date=' + encodeURIComponent(bookingDate));
            }

            var step1 = document.getElementById('cbf-step-1');
            var step2 = document.getElementById('cbf-step-2');
            var btnNext = document.getElementById('btn-next');
            var btnBack = document.getElementById('btn-back');

            // Hàm hiển thị lỗi
            function showError(el, message) {
                el.classList.add('has-error');
                var next = el.nextElementSibling;
                if (!next || !next.classList.contains('cbf-error-text')) {
                    var err = document.createElement('span');
                    err.className = 'cbf-error-text';
                    err.style.color = '#e53935';
                    err.style.fontSize = '12px';
                    err.style.display = 'block';
                    err.style.marginTop = '4px';
                    err.innerText = message;
                    el.parentNode.insertBefore(err, el.nextSibling);
                }
            }

            // Hàm xóa lỗi
            function removeError(el) {
                el.classList.remove('has-error');
                var next = el.nextElementSibling;
                if (next && next.classList.contains('cbf-error-text')) {
                    next.remove();
                }
            }

            // Loại bỏ class lỗi khi người dùng bắt đầu nhập
            document.querySelectorAll('#clinic-booking-form input, #clinic-booking-form select, #clinic-booking-form textarea').forEach(function(el) {
                el.addEventListener('input', function() {
                    if (this.value.trim() !== '') removeError(this);
                });
                el.addEventListener('change', function() {
                    if (this.value.trim() !== '') removeError(this);
                });
            });

            // AJAX Filter Doctors
            var clinicSelect = document.getElementById('clinic');
            var specialtySelect = document.getElementById('specialty');
            var doctorSelect = document.getElementById('selected_doctor');

            function fetchDoctors(callback) {
                var branch_id = clinicSelect.options[clinicSelect.selectedIndex].getAttribute('data-id');
                var specialty_id = specialtySelect.options[specialtySelect.selectedIndex].getAttribute('data-id');
                
                doctorSelect.innerHTML = '<option value="">Đang tải danh sách bác sĩ...</option>';
                var doctorsDisplay = document.getElementById('cb-doctors-display');
                if(doctorsDisplay) doctorsDisplay.style.opacity = '0.5';

                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (this.status === 200) {
                        try {
                            var res = JSON.parse(this.responseText);
                            if(doctorsDisplay) doctorsDisplay.style.opacity = '1';

                            // 1. Cập nhật Dropdown
                            doctorSelect.innerHTML = '<option value="">Vui lòng chọn bác sĩ</option>';
                            if (res.success && res.data.doctors.length > 0) {
                                res.data.doctors.forEach(function(doc) {
                                    var opt = document.createElement('option');
                                    opt.value = doc.title;
                                    opt.textContent = doc.title;
                                    opt.setAttribute('data-doctor-id', doc.id);
                                    doctorSelect.appendChild(opt);
                                });
                                // 2. Cập nhật Danh sách Card bên phải
                                if(doctorsDisplay) doctorsDisplay.innerHTML = res.data.html;
                                initPagination(); // Gán lại sự kiện phân trang
                                updateDoctorHiddenId(); // Cập nhật lại ID sau khi AJAX load xong
                            } else {
                                doctorSelect.innerHTML = '<option value="">Không có bác sĩ phù hợp</option>';
                                if(doctorsDisplay) doctorsDisplay.innerHTML = '<div style="padding: 20px; text-align: center; color: #718096;">Không tìm thấy bác sĩ nào thuộc chi nhánh/khoa này.</div>';
                            }
                            if (typeof callback === 'function') callback();
                        } catch(e) {}
                    }
                };
                xhr.send('action=cb_get_doctors&branch_id=' + (branch_id||'') + '&specialty_id=' + (specialty_id||''));
            }

            // MỚI: Lọc chuyên khoa theo chi nhánh
            function fetchSpecialties() {
                var branch_id = clinicSelect.options[clinicSelect.selectedIndex].getAttribute('data-id');
                if (!branch_id) return;

                specialtySelect.innerHTML = '<option value="">Đang tải...</option>';
                
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (this.status === 200) {
                        var res = JSON.parse(this.responseText);
                        specialtySelect.innerHTML = '<option value="">Vui lòng chọn chuyên khoa</option>';
                        if (res.success && res.data.length > 0) {
                            res.data.forEach(function(spec) {
                                var opt = document.createElement('option');
                                opt.value = spec.name;
                                opt.textContent = spec.name;
                                opt.setAttribute('data-id', spec.id);
                                specialtySelect.appendChild(opt);
                            });
                        }
                        fetchDoctors(); // Sau khi có khoa mới thì load lại bác sĩ
                    }
                };
                xhr.send('action=cb_get_specialties&branch_id=' + branch_id);
            }

            function initPagination() {
                var pageButtons = document.querySelectorAll('#cb-doctors-display .page-num');
                if (pageButtons.length > 0) {
                    pageButtons.forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            var page = this.getAttribute('data-page');
                            var cards = document.querySelectorAll('#cb-doctors-display .doctor-card');
                            
                            // Ẩn tất cả card và hiện card thuộc trang được chọn
                            cards.forEach(function(card, index) {
                                var cardPage = Math.ceil((index + 1) / 4);
                                card.style.display = (cardPage == page) ? 'flex' : 'none';
                            });

                            // Cập nhật trạng thái nút
                            pageButtons.forEach(function(b) {
                                b.style.background = '#edf2f7';
                                b.style.color = '#2b6cb0';
                            });
                            this.style.background = '#2b6cb0';
                            this.style.color = '#fff';
                        });
                    });
                }
            }
            initPagination();

            clinicSelect.addEventListener('change', fetchSpecialties);
            specialtySelect.addEventListener('change', fetchDoctors);
            
            // Hàm cập nhật ID bác sĩ vào hidden field
            function updateDoctorHiddenId() {
                var selectedOption = doctorSelect.options[doctorSelect.selectedIndex];
                var doctorId = selectedOption ? selectedOption.getAttribute('data-doctor-id') : '';
                document.getElementById('doctor_id_hidden').value = doctorId || '';
                
                // Fetch schedule and update Flatpickr disable rules
                cb_fetch_doctor_schedule_and_update_flatpickr();
            }

            // Cập nhật khi người dùng chọn thủ công
            doctorSelect.addEventListener('change', updateDoctorHiddenId);
            
            // Cập nhật ngay khi tải trang (nếu đã có sẵn bác sĩ)
            updateDoctorHiddenId();

            // Tự động chọn Bác sĩ hoặc Chuyên khoa từ URL (?auto_doctor=ID hoặc ?auto_specialty=ID)
            function autoSelectFromURL() {
                var urlParams = new URLSearchParams(window.location.search);
                var autoDoctorId = urlParams.get('auto_doctor');
                var autoSpecialtyId = urlParams.get('auto_specialty');
                
                if (autoDoctorId) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function() {
                        if (this.status === 200) {
                            var res = JSON.parse(this.responseText);
                            if (res.success && res.data) {
                                // 1. Chọn Chi nhánh
                                if (res.data.branch_id) {
                                    for (var i=0; i<clinicSelect.options.length; i++) {
                                        if (clinicSelect.options[i].getAttribute('data-id') == res.data.branch_id) {
                                            clinicSelect.selectedIndex = i;
                                            break;
                                        }
                                    }
                                }
                                // 2. Chọn Chuyên khoa
                                if (res.data.specialty_id) {
                                    for (var i=0; i<specialtySelect.options.length; i++) {
                                        if (specialtySelect.options[i].getAttribute('data-id') == res.data.specialty_id) {
                                            specialtySelect.selectedIndex = i;
                                            break;
                                        }
                                    }
                                }
                                // 3. Tải danh sách bác sĩ và chọn bác sĩ đích
                                fetchDoctors(function() {
                                    for (var i=0; i<doctorSelect.options.length; i++) {
                                        if (doctorSelect.options[i].value == res.data.doctor_title) {
                                            doctorSelect.selectedIndex = i;
                                            updateDoctorHiddenId(); 
                                            break;
                                        }
                                    }
                                    var formEl = document.getElementById('clinic-booking-form');
                                    if(formEl) formEl.scrollIntoView({ behavior: 'smooth' });
                                });
                            }
                        }
                    };
                    xhr.send('action=cb_get_doctor_info&doctor_id=' + autoDoctorId);
                } else if (autoSpecialtyId) {
                    // Nếu chỉ có auto_specialty
                    for (var i=0; i<specialtySelect.options.length; i++) {
                        if (specialtySelect.options[i].getAttribute('data-id') == autoSpecialtyId) {
                            specialtySelect.selectedIndex = i;
                            fetchDoctors();
                            var formEl = document.getElementById('clinic-booking-form');
                            if(formEl) formEl.scrollIntoView({ behavior: 'smooth' });
                            break;
                        }
                    }
                }
            }
            autoSelectFromURL();

            btnNext.addEventListener('click', function() {
                var valid = true;
                var requiredFieldsStep1 = [
                    { id: 'clinic', msg: 'Vui lòng chọn phòng khám' },
                    { id: 'specialty', msg: 'Vui lòng chọn chuyên khoa' },
                    { id: 'selected_doctor', msg: 'Vui lòng chọn bác sĩ' },
                    { id: 'booking_date', msg: 'Vui lòng chọn ngày' },
                    { id: 'booking_time', msg: 'Vui lòng chọn giờ' }
                ];
                
                requiredFieldsStep1.forEach(function(field) {
                    var el = document.getElementById(field.id);
                    if (!el.value.trim()) {
                        showError(el, field.msg);
                        valid = false;
                    } else {
                        removeError(el);
                    }
                });

                if(!valid) {
                    return;
                }

                step1.style.display = 'none';
                step2.style.display = 'block';
            });

            // Validate bước 2 khi submit form
            var form = document.getElementById('clinic-booking-form');
            form.addEventListener('submit', function(e) {
                var valid = true;
                var requiredFieldsStep2 = [
                    { id: 'registrant_name', msg: 'Vui lòng nhập họ tên người đăng ký' },
                    { id: 'patient_phone', msg: 'Vui lòng nhập số điện thoại' },
                    { id: 'patient_email', msg: 'Vui lòng nhập Email' },
                    { id: 'patient_name', msg: 'Vui lòng nhập họ tên người khám' },
                    { id: 'patient_dob', msg: 'Vui lòng nhập ngày sinh' },
                    { id: 'symptoms', msg: 'Vui lòng để lại lời nhắn' }
                ];
                
                requiredFieldsStep2.forEach(function(field) {
                    var el = document.getElementById(field.id);
                    if (!el.value.trim()) {
                        showError(el, field.msg);
                        valid = false;
                    } else {
                        removeError(el);
                    }
                });

                // Validate thêm cho SĐT
                var phone = document.getElementById('patient_phone');
                if (phone.value.trim() !== '' && !/^\d{9,12}$/.test(phone.value.replace(/[\s\-\.]/g, ''))) {
                    showError(phone, 'Số điện thoại không hợp lệ');
                    valid = false;
                }

                // Validate thêm cho Email (nếu có nhập)
                var email = document.getElementById('patient_email');
                if (email.value.trim() !== '' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
                    showError(email, 'Email không hợp lệ');
                    valid = false;
                }

                if(!valid) {
                    e.preventDefault();
                }
            });

            btnBack.addEventListener('click', function() {
                step2.style.display = 'none';
                step1.style.display = 'block';
            });
        });
    </script>
    <?php
    
    return ob_get_clean(); // Trả về nội dung HTML để hiển thị
}

// Đăng ký mã ngắn (shortcode) với hệ thống
add_shortcode( 'clinic_booking_form', 'clinic_booking_form_shortcode' );

add_action( 'add_meta_boxes', 'clinic_booking_add_meta_box' );
function clinic_booking_add_meta_box() {
    add_meta_box(
        'clinic_booking_details',
        'Thông tin khách hàng',
        'clinic_booking_meta_box_html',
        'appointment',
        'normal',
        'high'
    );

    // Meta box cho bác sĩ - Ảnh
    add_meta_box(
        'doctor_details',
        'Ảnh Đại Diện (Bằng Link URL)',
        'doctor_meta_box_html',
        'doctor',
        'normal',
        'high'
    );

    // Meta box cho bác sĩ - Tài khoản liên kết
    add_meta_box(
        'doctor_account_link',
        'Liên kết Tài khoản Bác sĩ',
        'doctor_account_link_meta_box_html',
        'doctor',
        'normal',
        'high'
    );
}

function doctor_account_link_meta_box_html( $post ) {
    $linked_user_id = get_post_meta( $post->ID, '_doctor_user_id', true );
    $users = get_users( array( 'fields' => array( 'ID', 'display_name', 'user_email' ) ) );
    
    wp_nonce_field( 'doctor_account_save_meta', 'doctor_account_meta_nonce' );
    
    echo '<div style="margin-bottom: 10px;">';
    echo '<label for="doctor_user_id"><strong>Chọn tài khoản người dùng:</strong></label><br>';
    echo '<select id="doctor_user_id" name="doctor_user_id" style="width: 100%; margin-top: 5px;">';
    echo '<option value="">-- Không liên kết --</option>';
    foreach ( $users as $user ) {
        $selected = ( $linked_user_id == $user->ID ) ? 'selected' : '';
        echo '<option value="' . esc_attr( $user->ID ) . '" ' . $selected . '>' . esc_html( $user->display_name ) . ' (' . esc_html( $user->user_email ) . ')</option>';
    }
    echo '</select>';
    echo '<p class="description">Khi liên kết, bác sĩ có thể đăng nhập bằng tài khoản này để xem danh sách lịch hẹn của chính mình.</p>';
    echo '</div>';
}

function doctor_meta_box_html( $post ) {
    $doctor_image_url = get_post_meta( $post->ID, '_doctor_image_url', true );
    $doctor_price = get_post_meta( $post->ID, '_doctor_price', true );
    $doctor_title_custom = get_post_meta( $post->ID, '_doctor_title_custom', true );
    
    wp_nonce_field( 'doctor_save_meta', 'doctor_meta_nonce' );
    echo '<div style="display: flex; flex-direction: column; gap: 15px; padding: 10px 0;">';
    
    // Ảnh đại diện
    echo '<div>';
    echo '<label for="doctor_image_url" style="display:block; margin-bottom: 5px; font-weight: bold;">Link ảnh đại diện (URL trực tiếp):</label>';
    echo '<input type="url" id="doctor_image_url" name="doctor_image_url" placeholder="Ví dụ: https://i.imgur.com/anh-bac-si.jpg" value="' . esc_attr( $doctor_image_url ) . '" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; box-sizing: border-box;">';
    echo '<p class="description" style="margin: 4px 0 0 0;">Dán trực tiếp link ảnh vào đây để không phải upload lên thư viện ảnh nặng máy.</p>';
    echo '</div>';

    // Giá khám bệnh
    echo '<div>';
    echo '<label for="doctor_price" style="display:block; margin-bottom: 5px; font-weight: bold;">Giá khám bệnh:</label>';
    echo '<input type="text" id="doctor_price" name="doctor_price" placeholder="Ví dụ: 200.000đ" value="' . esc_attr( $doctor_price ) . '" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; box-sizing: border-box;">';
    echo '<p class="description" style="margin: 4px 0 0 0;">Nhập giá khám để hiển thị ở trang chủ (mặc định: 200.000đ).</p>';
    echo '</div>';

    // Chức danh bác sĩ
    echo '<div>';
    echo '<label for="doctor_title_custom" style="display:block; margin-bottom: 5px; font-weight: bold;">Chức danh bác sĩ:</label>';
    echo '<input type="text" id="doctor_title_custom" name="doctor_title_custom" placeholder="Ví dụ: Bác sĩ Chuyên Khoa" value="' . esc_attr( $doctor_title_custom ) . '" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; box-sizing: border-box;">';
    echo '<p class="description" style="margin: 4px 0 0 0;">Nhập chức danh hiển thị ở dưới cùng của card (mặc định: Bác sĩ Chuyên Khoa).</p>';
    echo '</div>';
    
    echo '</div>';
}

function clinic_booking_meta_box_html( $post ) {
    $clinic          = get_post_meta( $post->ID, '_clinic', true );
    $specialty       = get_post_meta( $post->ID, '_specialty', true );
    $selected_doctor = get_post_meta( $post->ID, '_selected_doctor', true );
    $booking_date    = get_post_meta( $post->ID, '_booking_date', true );
    $booking_time    = get_post_meta( $post->ID, '_booking_time', true );
    $registrant_name = get_post_meta( $post->ID, '_registrant_name', true );
    $patient_phone   = get_post_meta( $post->ID, '_patient_phone', true );
    $patient_email   = get_post_meta( $post->ID, '_patient_email', true );
    $patient_name    = get_post_meta( $post->ID, '_patient_name', true );
    $patient_dob     = get_post_meta( $post->ID, '_patient_dob', true );
    $patient_gender  = get_post_meta( $post->ID, '_patient_gender', true );

    wp_nonce_field( 'clinic_booking_save_meta', 'clinic_booking_meta_nonce' );

    echo '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">';
    
    echo '<div><label><strong>Phòng khám:</strong></label><br>';
    echo '<input type="text" name="clinic" value="' . esc_attr( $clinic ) . '" style="width: 100%;"></div>';

    echo '<div><label><strong>Chuyên khoa:</strong></label><br>';
    echo '<input type="text" name="specialty" value="' . esc_attr( $specialty ) . '" style="width: 100%;"></div>';

    echo '<div><label><strong>Bác sĩ yêu cầu:</strong></label><br>';
    echo '<input type="text" name="selected_doctor" value="' . esc_attr( $selected_doctor ) . '" style="width: 100%;"></div>';

    echo '<div><label><strong>Thời gian khám:</strong></label><br>';
    echo '<input type="text" name="booking_date" value="' . esc_attr( $booking_date ) . '" style="width: 48%; display: inline-block;"> ';
    echo '<input type="text" name="booking_time" value="' . esc_attr( $booking_time ) . '" style="width: 48%; display: inline-block;"></div>';

    echo '<div><label><strong>Họ tên người đăng ký:</strong></label><br>';
    echo '<input type="text" name="registrant_name" value="' . esc_attr( $registrant_name ) . '" style="width: 100%;"></div>';

    echo '<div><label><strong>Số điện thoại:</strong></label><br>';
    echo '<input type="text" name="patient_phone" value="' . esc_attr( $patient_phone ) . '" style="width: 100%;"></div>';

    echo '<div><label><strong>Email:</strong></label><br>';
    echo '<input type="email" name="patient_email" value="' . esc_attr( $patient_email ) . '" style="width: 100%;"></div>';

    echo '<div><label><strong>Họ tên người khám:</strong></label><br>';
    echo '<input type="text" name="patient_name" value="' . esc_attr( $patient_name ) . '" style="width: 100%;"></div>';

    echo '<div><label><strong>Ngày sinh:</strong></label><br>';
    echo '<input type="text" name="patient_dob" value="' . esc_attr( $patient_dob ) . '" style="width: 100%;"></div>';

    echo '<div><label><strong>Giới tính:</strong></label><br>';
    echo '<input type="text" name="patient_gender" value="' . esc_attr( $patient_gender ) . '" style="width: 100%;"></div>';

    echo '</div>';
}

add_action( 'save_post', 'clinic_booking_save_meta_box_data' );
function clinic_booking_save_meta_box_data( $post_id ) {
    if ( ! isset( $_POST['clinic_booking_meta_nonce'] ) || ! wp_verify_nonce( $_POST['clinic_booking_meta_nonce'], 'clinic_booking_save_meta' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $fields = array(
        'clinic', 'specialty', 'selected_doctor', 'booking_date', 'booking_time',
        'registrant_name', 'patient_phone', 'patient_email', 'patient_name',
        'patient_dob', 'patient_gender'
    );

    foreach ($fields as $field) {
        if ( isset( $_POST[$field] ) ) {
            update_post_meta( $post_id, '_' . $field, sanitize_text_field( $_POST[$field] ) );
        }
    }
}

add_action( 'save_post', 'doctor_save_meta_box_data' );
function doctor_save_meta_box_data( $post_id ) {
    if ( ! isset( $_POST['doctor_meta_nonce'] ) || ! wp_verify_nonce( $_POST['doctor_meta_nonce'], 'doctor_save_meta' ) ) {
        // Nếu không có nonce của ảnh, kiểm tra nonce của tài khoản
        if ( ! isset( $_POST['doctor_account_meta_nonce'] ) || ! wp_verify_nonce( $_POST['doctor_account_meta_nonce'], 'doctor_account_save_meta' ) ) {
            return;
        }
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Lưu link ảnh
    if ( isset( $_POST['doctor_image_url'] ) ) {
        update_post_meta( $post_id, '_doctor_image_url', sanitize_url( $_POST['doctor_image_url'] ) );
    }

    // Lưu Giá khám bệnh
    if ( isset( $_POST['doctor_price'] ) ) {
        update_post_meta( $post_id, '_doctor_price', sanitize_text_field( $_POST['doctor_price'] ) );
    }

    // Lưu Chức danh bác sĩ
    if ( isset( $_POST['doctor_title_custom'] ) ) {
        update_post_meta( $post_id, '_doctor_title_custom', sanitize_text_field( $_POST['doctor_title_custom'] ) );
    }

    // Lưu ID tài khoản liên kết
    if ( isset( $_POST['doctor_user_id'] ) ) {
        update_post_meta( $post_id, '_doctor_user_id', sanitize_text_field( $_POST['doctor_user_id'] ) );
    }
}

// ==========================================
// CẤU HÌNH CÀI ĐẶT PLUGIN (SETTINGS PAGE)
// ==========================================
add_action('admin_menu', 'cb_register_settings_menu');
function cb_register_settings_menu() {
    add_options_page(
        'Cấu hình Đặt Lịch', 
        'Cấu hình Đặt Lịch', 
        'manage_options', 
        'clinic-booking-settings', 
        'cb_settings_page_html'
    );
    add_submenu_page(
        'edit.php?post_type=doctor',
        'Nhập nhanh Bác sĩ',
        'Nhập nhanh',
        'manage_options',
        'cb-bulk-add-doctors',
        'cb_bulk_add_doctors_page'
    );
}

add_action('admin_init', 'cb_register_settings');
function cb_register_settings() {
    register_setting('cb_settings_group', 'cb_admin_email');
    register_setting('cb_settings_group', 'cb_brevo_api_key');
    register_setting('cb_settings_group', 'cb_brevo_sender_email');
    register_setting('cb_settings_group', 'cb_webhook_url');
    register_setting('cb_settings_group', 'cb_bulk_doctors');
    
    // Thêm các cấu hình mới
    register_setting('cb_settings_group', 'cb_time_slots');
}

function cb_settings_page_html() {
    if (!current_user_can('manage_options')) return;
    ?>
    <div class="wrap">
        <h1>⚙️ Cấu hình Hệ thống Đặt Lịch</h1>
        <form method="post" action="options.php">
            <?php settings_fields('cb_settings_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Webhook URL (Nhận thông báo)</th>
                    <td>
                        <input type="url" name="cb_webhook_url" value="<?php echo esc_attr(get_option('cb_webhook_url')); ?>" style="width: 350px;" placeholder="https://..." />
                        <p class="description">Hệ thống sẽ gửi dữ liệu (POST request) đến đường dẫn này khi có khách đặt lịch mới thay vì gửi email cho Admin.</p>
                        <button type="button" id="btn-test-webhook" class="button button-secondary" style="margin-top: 10px;">Thử gửi Webhook</button>
                        <span id="webhook-test-result" style="margin-left: 10px; font-weight: bold;"></span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Email Quản Trị (Dự phòng)</th>
                    <td>
                        <input type="email" name="cb_admin_email" value="<?php echo esc_attr(get_option('cb_admin_email', get_option('admin_email'))); ?>" style="width: 350px;" />
                        <p class="description">Cài đặt này hiện không còn gửi email mỗi khi có lịch mới (đã chuyển sang dùng Webhook).</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Brevo API Key (Tùy chọn)</th>
                    <td>
                        <input type="text" name="cb_brevo_api_key" value="<?php echo esc_attr(get_option('cb_brevo_api_key')); ?>" style="width: 350px;" />
                        <p class="description">Nếu nhập API Key của Brevo, hệ thống sẽ gửi mail qua Brevo (chuyên nghiệp, không bị vào Spam). Nếu để trống, hệ thống dùng wp_mail mặc định.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Email Gửi (Brevo Sender)</th>
                    <td>
                        <input type="email" name="cb_brevo_sender_email" value="<?php echo esc_attr(get_option('cb_brevo_sender_email', 'no-reply@yourdomain.com')); ?>" style="width: 350px;" />
                        <p class="description">Email người gửi (phải là email đã được xác thực trên Brevo).</p>
                    </td>
                </tr>
                
                <tr valign="top">
                    <th scope="row">Danh sách Giờ Khám</th>
                    <td>
                        <textarea name="cb_time_slots" rows="5" style="width: 350px;"><?php echo esc_textarea(get_option('cb_time_slots', "08:00\n08:30\n09:00\n09:30\n10:00\n10:30\n14:00\n14:30\n15:00\n15:30\n16:00")); ?></textarea>
                        <p class="description">Mỗi khung giờ trên 1 dòng.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button('Lưu cấu hình'); ?>
        </form>
    </div>
    <script>
    jQuery(document).ready(function($) {
        $('#btn-test-webhook').click(function(e) {
            e.preventDefault();
            var webhook_url = $('input[name="cb_webhook_url"]').val();
            if (!webhook_url) {
                alert('Vui lòng nhập Webhook URL trước khi thử nghiệm!');
                return;
            }

            var btn = $(this);
            var resultSpan = $('#webhook-test-result');
            
            btn.text('Đang gửi...').prop('disabled', true);
            resultSpan.text('').css('color', '#333');

            $.post(ajaxurl, {
                action: 'test_clinic_webhook',
                webhook_url: webhook_url,
                _ajax_nonce: '<?php echo wp_create_nonce("test_clinic_webhook_nonce"); ?>'
            }, function(response) {
                btn.text('Thử gửi Webhook').prop('disabled', false);
                if (response.success) {
                    resultSpan.text('✅ ' + response.data.message).css('color', 'green');
                } else {
                    resultSpan.text('❌ Lỗi: ' + response.data.message).css('color', 'red');
                }
            }).fail(function() {
                btn.text('Thử gửi Webhook').prop('disabled', false);
                resultSpan.text('❌ Lỗi kết nối (Server Error)').css('color', 'red');
            });
        });
    });
    </script>
    <?php
}

add_action('wp_ajax_test_clinic_webhook', 'test_clinic_webhook_handler');
function test_clinic_webhook_handler() {
    check_ajax_referer('test_clinic_webhook_nonce', '_ajax_nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Không có quyền truy cập.'));
    }

    $webhook_url = isset($_POST['webhook_url']) ? esc_url_raw($_POST['webhook_url']) : '';
    if (empty($webhook_url)) {
        wp_send_json_error(array('message' => 'URL Webhook trống.'));
    }

    $webhook_data = array(
        'content' => '🔔 **[TEST] CÓ LỊCH ĐẶT KHÁM MỚI**',
        'embeds' => array(
            array(
                'title' => 'Chi tiết thông tin đăng ký (BẢN THỬ NGHIỆM)',
                'color' => 15158332, // Màu đỏ cho bản test
                'fields' => array(
                    array('name' => 'Người đăng ký', 'value' => 'Nguyễn Văn Test', 'inline' => true),
                    array('name' => 'Điện thoại', 'value' => '0987654321', 'inline' => true),
                    array('name' => 'Email', 'value' => 'test@example.com', 'inline' => true),
                    array('name' => 'Người khám', 'value' => 'Nguyễn Văn Khám (Nam)', 'inline' => true),
                    array('name' => 'Ngày sinh', 'value' => '01/01/1990', 'inline' => true),
                    array('name' => 'Phòng khám', 'value' => 'Phòng khám Demo', 'inline' => true),
                    array('name' => 'Chuyên khoa', 'value' => 'Nội khoa', 'inline' => true),
                    array('name' => 'Bác sĩ', 'value' => 'Bác sĩ A', 'inline' => true),
                    array('name' => 'Thời gian khám', 'value' => '08:00 ngày ' . date('d/m/Y', strtotime('+1 day')), 'inline' => false),
                    array('name' => 'Lời nhắn', 'value' => 'Đây là nội dung gửi thử từ tính năng Test Webhook.', 'inline' => false),
                )
            )
        )
    );

    $response = wp_remote_post($webhook_url, array(
        'headers'     => array('Content-Type' => 'application/json'),
        'body'        => wp_json_encode($webhook_data),
        'method'      => 'POST',
        'data_format' => 'body',
        'timeout'     => 15,
        'sslverify'   => false
    ));

    if (is_wp_error($response)) {
        wp_send_json_error(array('message' => $response->get_error_message()));
    } else {
        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code >= 200 && $status_code < 300) {
            wp_send_json_success(array('message' => 'Gửi thành công (HTTP ' . $status_code . ')'));
        } else {
            $body = wp_remote_retrieve_body($response);
            wp_send_json_error(array('message' => 'Gửi thất bại (HTTP ' . $status_code . '). Phản hồi: ' . wp_trim_words($body, 10, '...')));
        }
    }
}

add_action('wp_ajax_cb_get_doctors', 'cb_ajax_get_doctors');
add_action('wp_ajax_nopriv_cb_get_doctors', 'cb_ajax_get_doctors');

function cb_ajax_get_doctors() {
    $branch_id = isset($_POST['branch_id']) ? intval($_POST['branch_id']) : 0;
    $specialty_id = isset($_POST['specialty_id']) ? intval($_POST['specialty_id']) : 0;

    $args = array(
        'post_type' => 'doctor',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    );

    $tax_query = array();
    if ($branch_id > 0) {
        $tax_query[] = array('taxonomy' => 'clinic_branch', 'field' => 'term_id', 'terms' => $branch_id);
    }
    if ($specialty_id > 0) {
        $tax_query[] = array('taxonomy' => 'specialty', 'field' => 'term_id', 'terms' => $specialty_id);
    }
    if (count($tax_query) > 0) {
        $tax_query['relation'] = 'AND';
        $args['tax_query'] = $tax_query;
    }

    $query = new WP_Query($args);
    $doctors_data = array();
    $html = '';
    $count = 0;

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $id = get_the_ID();
            $title = get_the_title();
            $doctors_data[] = array('id' => $id, 'title' => $title);

            $count++;
            $display = ($count > 4) ? 'none' : 'flex';
            $img_url = get_post_meta($id, '_doctor_image_url', true);
            if (empty($img_url)) $img_url = get_the_post_thumbnail_url($id, 'thumbnail');
            if (empty($img_url)) $img_url = 'https://ui-avatars.com/api/?name='.urlencode($title).'&background=ebf8ff&color=2b6cb0&size=200';

            $excerpt = get_the_excerpt($id);
            $short_desc = $excerpt ? $excerpt : wp_trim_words(get_the_content($id), 20);

            $html .= '
            <div class="doctor-card" style="display: '.$display.';">
                <img src="'.esc_url($img_url).'" class="doctor-avatar">
                <div class="doctor-info">
                    <h4>'.esc_html($title).'</h4>
                    <div class="doctor-excerpt" style="color: #718096; font-size: 14px; line-height: 1.5; margin-bottom: 10px;">
                        '.wp_kses_post($short_desc).'
                    </div>
                    <a href="'.get_permalink($id).'" target="_blank" style="color: #2b6cb0; font-size: 13px; font-weight: 600; text-decoration: none;">Xem chi tiết →</a>
                </div>
            </div>';
        }
        if ($count > 4) {
            $html .= '<div class="cb-pagination" style="margin-top: 20px; display: flex; gap: 5px; justify-content: center;">';
            $total_pages = ceil($count / 4);
            for ($i = 1; $i <= $total_pages; $i++) {
                $active_bg = ($i == 1) ? '#2b6cb0' : '#edf2f7';
                $active_color = ($i == 1) ? '#fff' : '#2b6cb0';
                $html .= '<button class="page-num" data-page="'.$i.'" style="padding: 5px 12px; border: none; border-radius: 4px; background: '.$active_bg.'; color: '.$active_color.'; cursor: pointer; font-weight: 600;">'.$i.'</button>';
            }
            $html .= '</div>';
        }
        wp_reset_postdata();
    } else {
        $html = '<div style="padding: 20px; text-align: center; color: #718096;">Không tìm thấy bác sĩ nào thuộc chi nhánh/khoa này.</div>';
    }

    wp_send_json_success(array('doctors' => $doctors_data, 'html' => $html));
}

// Xử lý nhập nhanh bác sĩ hàng loạt
add_action('admin_init', 'cb_process_bulk_doctors');
function cb_process_bulk_doctors() {
    if (!isset($_POST['cb_bulk_doctors']) || empty(trim($_POST['cb_bulk_doctors']))) return;
    if (!current_user_can('manage_options')) return;

    $lines = explode("\n", str_replace("\r", "", trim($_POST['cb_bulk_doctors'])));
    
    foreach ($lines as $line) {
        $parts = array_map('trim', explode('|', $line));
        if (count($parts) < 1 || empty($parts[0])) continue;

        $doctor_name   = $parts[0];
        $specialty_name = isset($parts[1]) ? $parts[1] : '';
        $branch_name    = isset($parts[2]) ? $parts[2] : '';

        // Kiểm tra xem bác sĩ đã tồn tại chưa
        $existing_doctor = get_page_by_title($doctor_name, OBJECT, 'doctor');
        
        if (!$existing_doctor) {
            $doctor_id = wp_insert_post(array(
                'post_title'  => $doctor_name,
                'post_type'   => 'doctor',
                'post_status' => 'publish',
            ));
        } else {
            $doctor_id = $existing_doctor->ID;
        }

        if ($doctor_id && !is_wp_error($doctor_id)) {
            // Gán Chuyên khoa
            if (!empty($specialty_name)) {
                $spec_term = wp_insert_term($specialty_name, 'specialty');
                $spec_id = !is_wp_error($spec_term) ? $spec_term['term_id'] : (is_wp_error($spec_term) && isset($spec_term->error_data['term_exists']) ? $spec_term->error_data['term_exists'] : 0);
                if ($spec_id) wp_set_object_terms($doctor_id, intval($spec_id), 'specialty');
            }

            // Gán Chi nhánh
            if (!empty($branch_name)) {
                $branch_term = wp_insert_term($branch_name, 'clinic_branch');
                $branch_id = !is_wp_error($branch_term) ? $branch_term['term_id'] : (is_wp_error($branch_term) && isset($branch_term->error_data['term_exists']) ? $branch_term->error_data['term_exists'] : 0);
                if ($branch_id) wp_set_object_terms($doctor_id, intval($branch_id), 'clinic_branch');
            }
        }
    }

    // Xóa dữ liệu trong option sau khi xử lý để không bị lặp lại
    update_option('cb_bulk_doctors', '');
}

function cb_bulk_add_doctors_page() {
    if (!current_user_can('manage_options')) return;

    // Xử lý khi nhấn nút Lưu
    if (isset($_POST['cb_bulk_doctors_nonce']) && wp_verify_nonce($_POST['cb_bulk_doctors_nonce'], 'cb_bulk_add_action')) {
        if (!empty(trim($_POST['cb_bulk_doctors_data']))) {
            $lines = explode("\n", str_replace("\r", "", trim($_POST['cb_bulk_doctors_data'])));
            $count = 0;
            
            foreach ($lines as $line) {
                $parts = array_map('trim', explode('|', $line));
                if (count($parts) < 1 || empty($parts[0])) continue;

                $doctor_name    = $parts[0];
                $specialty_name  = isset($parts[1]) ? $parts[1] : '';
                $branch_name     = isset($parts[2]) ? $parts[2] : '';
                $image_url       = isset($parts[3]) ? $parts[3] : '';
                
                // Hỗ trợ ký hiệu [n] để xuống dòng
                $short_desc      = isset($parts[4]) ? str_replace('[n]', "\n", $parts[4]) : '';
                $full_detail     = isset($parts[5]) ? str_replace('[n]', "\n", $parts[5]) : '';

                $doctor_email    = isset($parts[6]) ? sanitize_email($parts[6]) : '';
                $doctor_user     = isset($parts[7]) ? sanitize_user($parts[7], true) : '';
                $doctor_pass     = isset($parts[8]) ? $parts[8] : '';

                $existing_doctor = get_page_by_title($doctor_name, OBJECT, 'doctor');
                $post_data = array(
                    'post_title'   => $doctor_name,
                    'post_type'    => 'doctor',
                    'post_status'  => 'publish',
                    'post_excerpt' => $short_desc,
                    'post_content' => $full_detail,
                );

                if (!$existing_doctor) {
                    $doctor_id = wp_insert_post($post_data);
                } else {
                    $post_data['ID'] = $existing_doctor->ID;
                    $doctor_id = wp_update_post($post_data);
                }

                if ($doctor_id && !is_wp_error($doctor_id)) {
                    // Lưu URL ảnh
                    if (!empty($image_url)) {
                        update_post_meta($doctor_id, '_doctor_image_url', sanitize_url($image_url));
                    }
                    if (!empty($specialty_name)) {
                        $spec_term = wp_insert_term($specialty_name, 'specialty');
                        $spec_id = !is_wp_error($spec_term) ? $spec_term['term_id'] : (is_wp_error($spec_term) && isset($spec_term->error_data['term_exists']) ? $spec_term->error_data['term_exists'] : 0);
                        if ($spec_id) wp_set_object_terms($doctor_id, intval($spec_id), 'specialty');
                    }
                    if (!empty($branch_name)) {
                        $branch_term = wp_insert_term($branch_name, 'clinic_branch');
                        $branch_id = !is_wp_error($branch_term) ? $branch_term['term_id'] : (is_wp_error($branch_term) && isset($branch_term->error_data['term_exists']) ? $branch_term->error_data['term_exists'] : 0);
                        if ($branch_id) wp_set_object_terms($doctor_id, intval($branch_id), 'clinic_branch');
                    }

                    // Xử lý tạo tài khoản WordPress cho bác sĩ
                    if (!empty($doctor_email)) {
                        $user_id = email_exists($doctor_email);
                        if (!$user_id) {
                            // Ưu tiên dùng Username người dùng nhập, nếu không có mới tự tạo
                            $username = !empty($doctor_user) ? $doctor_user : sanitize_user(str_replace(' ', '', strtolower(remove_accents($doctor_name))), true);
                            
                            if (username_exists($username)) {
                                $username .= '_' . rand(100, 999);
                            }
                            
                            // Nếu không có mật khẩu thì dùng mặc định
                            $password = !empty($doctor_pass) ? $doctor_pass : 'Bacsi123@';
                            
                            $user_id = wp_create_user($username, $password, $doctor_email);
                            if (!is_wp_error($user_id)) {
                                wp_update_user(array(
                                    'ID'           => $user_id,
                                    'display_name' => $doctor_name,
                                    'role'         => 'doctor'
                                ));
                            }
                        }
                        
                        if ($user_id && !is_wp_error($user_id)) {
                            update_post_meta($doctor_id, '_doctor_user_id', $user_id);
                        }
                    }

                    $count++;
                }
            }
            echo '<div class="updated"><p>✅ Đã xử lý xong ' . $count . ' bác sĩ.</p></div>';
        }
    }
    ?>
    <div class="wrap">
        <h1>👨‍⚕️ Nhập nhanh danh sách Bác sĩ</h1>
        <p>Sử dụng công cụ này để thêm hàng loạt bác sĩ vào hệ thống một cách nhanh chóng.</p>
        
        <form method="post" action="">
            <?php wp_nonce_field('cb_bulk_add_action', 'cb_bulk_doctors_nonce'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Danh sách dữ liệu</th>
                    <td>
                        <textarea name="cb_bulk_doctors_data" rows="15" style="width: 100%; max-width: 800px; font-family: monospace;" placeholder="Tên Bác Sĩ | Khoa | Chi Nhánh | Link Ảnh | Giới thiệu ngắn | Chi tiết thành tựu | Email | Tên đăng nhập | Mật khẩu&#10;Nguyễn Văn A | Nội khoa | Hà Nội | https://link.jpg | Bác sĩ giỏi[n]10 năm kinh nghiệm | Tốt nghiệp ĐH Y[n]Công tác tại viện 108 | bacsia@gmail.com | bs_nguyenvana | 123456aA@"></textarea>
                        <p class="description"> 
                            - Định dạng: <strong>Tên | Khoa | Chi nhánh | Ảnh | Ngắn | Chi tiết | Email | Tên đăng nhập | Mật khẩu</strong> (Mỗi người 1 dòng).<br>
                            - Nếu nhập <strong>Email</strong>, hệ thống sẽ tự động tạo tài khoản WordPress.<br>
                            - Nếu để trống <strong>Tên đăng nhập</strong>, hệ thống tự tạo từ tên bác sĩ.<br>
                            - Nếu để trống <strong>Mật khẩu</strong>, mật khẩu mặc định sẽ là <code>Bacsi123@</code>.<br>
                            - Để <strong>xuống dòng</strong> trong nội dung, hãy sử dụng ký hiệu <code>[n]</code>.
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button('Bắt đầu nhập dữ liệu'); ?>
        </form>
    </div>
    <?php
}

// Tối ưu giao diện trang chi tiết Bác sĩ (Single Doctor)
add_filter('the_content', 'cb_doctor_custom_content_template');
function cb_doctor_custom_content_template($content) {
    if (is_singular('doctor')) {
        global $post;
        $img_url = get_post_meta($post->ID, '_doctor_image_url', true);
        if (empty($img_url)) $img_url = get_the_post_thumbnail_url($post->ID, 'large');
        if (empty($img_url)) $img_url = 'https://ui-avatars.com/api/?name='.urlencode($post->post_title).'&size=300';

        $terms_specialty = get_the_terms($post->ID, 'specialty');
        $spec_name = ($terms_specialty && !is_wp_error($terms_specialty)) ? $terms_specialty[0]->name : 'Chuyên gia';

        $custom_html = '
        <style>
            .doctor-detail-container {
                display: flex;
                flex-wrap: wrap;
                gap: 30px;
                margin-bottom: 40px;
                font-family: "Inter", sans-serif;
                color: #1a202c !important; /* Chữ đen đậm */
            }
            .doctor-detail-left {
                flex: 1;
                min-width: 300px;
            }
            .doctor-detail-right {
                flex: 2;
                min-width: 350px;
            }
            .doctor-detail-img {
                width: 100%;
                border-radius: 20px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                object-fit: cover;
                border: 5px solid #fff;
            }
            .doctor-detail-right h1 {
                font-size: 36px;
                color: #2b6cb0;
                margin-top: 0;
                margin-bottom: 10px;
            }
            .doctor-detail-badge {
                display: inline-block;
                background: #ebf8ff;
                color: #2b6cb0;
                padding: 5px 15px;
                border-radius: 50px;
                font-weight: 600;
                font-size: 14px;
                margin-bottom: 20px;
            }
            .doctor-detail-content {
                font-size: 19px; /* Tăng cỡ chữ lên chút nữa */
                line-height: 1.8;
                color: #000000 !important; /* Đen tuyệt đối */
                font-weight: 500; /* Tăng độ đậm */
            }
            .doctor-detail-content h3, .doctor-detail-content b, .doctor-detail-content strong {
                color: #000 !important;
                font-size: 22px;
                display: block;
                margin-top: 25px;
                margin-bottom: 10px;
                font-weight: 800; /* Rất đậm cho tiêu đề */
            }
            .doctor-detail-content p, .doctor-detail-content span, .doctor-detail-content div {
                margin-bottom: 15px;
                color: #000 !important;
                font-weight: 500;
            }
        </style>
        <div class="doctor-detail-container">
            <div class="doctor-detail-left">
                <img src="'.esc_url($img_url).'" class="doctor-detail-img" />
            </div>
            <div class="doctor-detail-right">
                <span class="doctor-detail-badge">'.$spec_name.'</span>
                <h1>'.get_the_title().'</h1>
                <div class="doctor-detail-content">
                    '.wpautop(str_replace('[n]', '<br>', $content)).'
                </div>
                <div style="margin-top: 30px;">
                    <a href="'.home_url('/dat-lich/').'?auto_doctor='.$post->ID.'" style="background: #2b6cb0; color: #fff; padding: 15px 30px; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 18px; box-shadow: 0 4px 15px rgba(43,108,176,0.3);">Đặt lịch hẹn ngay với '.get_the_title().'</a>
                </div>
            </div>
        </div>
        ';
        return $custom_html;
    }
    return $content;
}

// Hàm AJAX lấy thông tin chi tiết của 1 bác sĩ để tự động điền form
add_action('wp_ajax_cb_get_doctor_info', 'cb_ajax_get_doctor_info');
add_action('wp_ajax_nopriv_cb_get_doctor_info', 'cb_ajax_get_doctor_info');
function cb_ajax_get_doctor_info() {
    $doctor_id = isset($_POST['doctor_id']) ? intval($_POST['doctor_id']) : 0;
    if (!$doctor_id) wp_send_json_error();

    $branches = get_the_terms($doctor_id, 'clinic_branch');
    $specialties = get_the_terms($doctor_id, 'specialty');

    wp_send_json_success(array(
        'doctor_title' => get_the_title($doctor_id),
        'branch_id'    => ($branches && !is_wp_error($branches)) ? $branches[0]->term_id : 0,
        'specialty_id' => ($specialties && !is_wp_error($specialties)) ? $specialties[0]->term_id : 0
    ));
}

// Hàm AJAX lấy danh sách chuyên khoa có bác sĩ tại chi nhánh cụ thể
add_action('wp_ajax_cb_get_specialties', 'cb_ajax_get_specialties');
add_action('wp_ajax_nopriv_cb_get_specialties', 'cb_ajax_get_specialties');
function cb_ajax_get_specialties() {
    $branch_id = isset($_POST['branch_id']) ? intval($_POST['branch_id']) : 0;
    if (!$branch_id) wp_send_json_error();

    // Lấy tất cả bác sĩ tại chi nhánh này
    $args = array(
        'post_type' => 'doctor',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'clinic_branch',
                'field'    => 'term_id',
                'terms'    => $branch_id,
            ),
        ),
    );
    $doctors = get_posts($args);
    $specialty_ids = array();

    foreach ($doctors as $doc) {
        $terms = get_the_terms($doc->ID, 'specialty');
        if ($terms && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $specialty_ids[] = $term->term_id;
            }
        }
    }

    $specialty_ids = array_unique($specialty_ids);
    $result = array();
    if (!empty($specialty_ids)) {
        foreach ($specialty_ids as $sid) {
            $term = get_term($sid, 'specialty');
            if ($term && !is_wp_error($term)) {
                $result[] = array('id' => $term->term_id, 'name' => $term->name);
            }
        }
    }

    wp_send_json_success($result);
}

/**
 * Redirect logged in users away from login/register pages
 */
/**
 * Auth Protection Logic: 
 * 1. Redirect logged in users away from login/register
 * 2. Redirect guests away from history page
 */
add_action( 'template_redirect', 'clinic_auth_protection_logic' );
function clinic_auth_protection_logic() {
    // 1. Nếu đã đăng nhập mà vào trang đăng nhập/đăng ký -> về trang chủ
    if ( is_user_logged_in() ) {
        if ( is_page('dang-nhap') || is_page('dang-ky') ) {
            wp_safe_redirect( home_url() );
            exit;
        }
    } else {
        // 2. Nếu chưa đăng nhập mà vào trang lịch sử hoặc cài đặt -> về trang đăng nhập
        if ( is_page('lich-su') || is_page('tai-khoan') ) {
            wp_safe_redirect( home_url('/dang-nhap/') );
            exit;
        }
    }
}

/**
 * Hide Admin Bar for non-admins
 */
add_filter( 'show_admin_bar', 'clinic_hide_admin_bar' );
function clinic_hide_admin_bar( $show ) {
    if ( ! current_user_can( 'manage_options' ) ) {
        return false;
    }
    return $show;
}

/**
 * Block Admin Dashboard access for non-admins
 */
add_action( 'admin_init', 'clinic_block_admin_access' );
function clinic_block_admin_access() {
    if ( is_user_logged_in() && ! current_user_can( 'manage_options' ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
        wp_safe_redirect( home_url() );
        exit;
    }
}

/**
 * Shared Auth Styles
 */
add_action('wp_head', 'clinic_auth_styles');
function clinic_auth_styles() {
    if ( is_page('dang-nhap') || is_page('dang-ky') || is_page('tai-khoan') ) {
        ?>
        <style>
            .clinic-auth-page { background: #f7fafc; min-height: 80vh; display: flex; align-items: center; justify-content: center; font-family: 'Montserrat', sans-serif; }
            .clinic-auth-container { max-width: 480px; width: 100%; margin: 40px auto; padding: 50px; background: #fff; border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.05); border: 1px solid #f0f0f0; }
            .clinic-auth-form h3 { text-align: center; color: #1a365d; margin-bottom: 35px; text-transform: uppercase; font-weight: 800; letter-spacing: 2px; font-size: 24px; }
            .clinic-auth-form .input-group { margin-bottom: 25px; }
            .clinic-auth-form label { display: block; margin-bottom: 8px; font-weight: 600; color: #4a5568; font-size: 14px; }
            .clinic-auth-form input[type="text"], .clinic-auth-form input[type="password"], .clinic-auth-form input[type="email"] { 
                width: 100% !important; padding: 16px 20px !important; border: 2px solid #edf2f7 !important; border-radius: 12px !important; 
                font-size: 16px !important; transition: all 0.3s ease !important; background: #f8fafc !important; box-sizing: border-box !important;
            }
            .clinic-auth-form input:focus { border-color: #005086 !important; outline: none !important; background: #fff !important; box-shadow: 0 0 0 4px rgba(0,80,134,0.1) !important; }
            .clinic-auth-btn { 
                width: 100%; padding: 18px; background: #005086; color: #fff; border: none; border-radius: 12px; 
                font-weight: 800; cursor: pointer; transition: all 0.3s; font-size: 17px; text-transform: uppercase; letter-spacing: 1px;
                margin-top: 10px;
            }
            .clinic-auth-btn:hover { background: #003d66; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,80,134,0.2); }
            .clinic-auth-footer { text-align: center; margin-top: 30px; font-size: 15px; color: #718096; }
            .clinic-auth-footer a { color: #005086; font-weight: 700; text-decoration: none; border-bottom: 2px solid transparent; transition: all 0.2s; }
            .clinic-auth-footer a:hover { border-bottom-color: #005086; }
            .clinic-auth-status { margin-bottom: 25px; }
            .clinic-auth-error { 
                background: #fff5f5; color: #c53030; padding: 12px 15px; border-radius: 10px; 
                font-size: 14px; font-weight: 600; border-left: 4px solid #f56565; margin-bottom: 20px; text-align: center;
            }
            .has-error { border-color: #e53935 !important; background: #fff8f8 !important; }
            .cbf-error-msg { color: #e53935; font-size: 12px; margin-top: 5px; display: block; font-weight: 500; }
        </style>
        <?php
    }
}

/**
 * Global Styles to hide specific theme elements
 */
add_action('wp_head', 'clinic_global_hide_elements');
function clinic_global_hide_elements() {
    ?>
    <style>
        /* Ẩn Breadcrumbs và Tiêu đề trang của theme trên tất cả các trang */
        .bradcrumbs, .vw-page-title { display: none !important; }
        
        /* Căn chỉnh lại khoảng cách đầu trang sau khi ẩn tiêu đề */
        #maincontent { padding-top: 20px; }
    </style>
    <?php
}

/**
 * Shortcode for a Premium Services Showcase Grid
 */
function clinic_services_grid_shortcode() {
    $specialties = get_terms(array(
        'taxonomy' => 'specialty',
        'hide_empty' => false,
    ));

    // Map tên chuyên khoa với icon FontAwesome
    $icon_map = array(
        'Nội khoa' => 'fa-stethoscope',
        'Ngoại khoa' => 'fa-scalpel-path',
        'Tim mạch' => 'fa-heart-pulse',
        'Tiêu hóa' => 'fa-stomach',
        'Hô hấp' => 'fa-lungs',
        'Thần kinh' => 'fa-brain',
        'Cơ xương khớp' => 'fa-bone',
        'Nhi khoa' => 'fa-baby',
        'Sản phụ khoa' => 'fa-person-pregnant',
        'Da liễu' => 'fa-hand-dots',
        'Răng Hàm Mặt' => 'fa-tooth',
        'Tai Mũi Họng' => 'fa-ear-listen',
    );

    wp_enqueue_style('google-fonts-showcase', 'https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&display=swap');
    wp_enqueue_style('font-awesome-showcase', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

    ob_start();
    ?>
    <style>
        .cb-services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
            margin: 40px 0;
            font-family: 'Inter', sans-serif;
        }
        .cb-service-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.04);
            border: 1px solid #f0f0f0;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            text-align: left;
        }
        .cb-service-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 5px;
            background: linear-gradient(90deg, #2b6cb0, #4299e1);
            opacity: 0;
            transition: 0.3s;
        }
        .cb-service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(43,108,176,0.12);
            border-color: #ebf8ff;
        }
        .cb-service-card:hover::before {
            opacity: 1;
        }
        .cb-service-icon {
            width: 60px;
            height: 60px;
            background: #ebf8ff;
            color: #2b6cb0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 15px;
            font-size: 24px;
            margin-bottom: 20px;
            transition: 0.3s;
        }
        .cb-service-card:hover .cb-service-icon {
            background: #2b6cb0;
            color: #fff;
            transform: rotate(-5deg);
        }
        .cb-service-card h3 {
            margin: 0 0 12px 0;
            font-size: 22px;
            color: #1a365d;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
        }
        .cb-service-desc {
            color: #718096;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 25px;
            flex-grow: 1;
        }
        .cb-service-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 20px;
            border-top: 1px solid #f7fafc;
            gap: 10px;
        }
        .cb-service-price {
            font-weight: 800;
            color: #e53e3e; /* Đỏ chuyên nghiệp */
            font-size: 20px;
            flex-shrink: 0;
        }
        .cb-service-price span {
            font-size: 11px;
            color: #a0aec0;
            font-weight: 600;
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .cb-service-actions {
            display: flex;
            gap: 8px;
        }
        .cb-btn-book-service, .cb-btn-view-service {
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            font-size: 13px;
            transition: 0.3s;
            display: inline-block;
            text-align: center;
            border: none;
            cursor: pointer;
        }
        .cb-btn-book-service {
            background: #2b6cb0;
            color: #fff;
        }
        .cb-btn-book-service:hover {
            background: #2c5282;
            box-shadow: 0 4px 12px rgba(43,108,176,0.3);
        }
        .cb-btn-view-service {
            background: #f7fafc;
            color: #4a5568;
            border: 1px solid #e2e8f0;
        }
        .cb-btn-view-service:hover {
            background: #edf2f7;
            color: #2b6cb0;
        }

        /* Toolbar Lọc & Tìm kiếm */
        .cb-services-toolbar {
            margin-bottom: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        .cb-services-filter {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 5px;
        }
        .filter-btn {
            padding: 10px 20px;
            border-radius: 50px;
            background: #fff;
            border: 1px solid #e2e8f0;
            color: #4a5568;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s;
            white-space: nowrap;
        }
        .filter-btn.active, .filter-btn:hover {
            background: #2b6cb0;
            color: #fff;
            border-color: #2b6cb0;
        }
        .cb-services-search {
            position: relative;
            min-width: 300px;
        }
        .cb-services-search input {
            width: 100%;
            padding: 12px 20px 12px 45px;
            border-radius: 50px;
            border: 2px solid #e2e8f0;
            font-size: 15px;
            transition: 0.3s;
            outline: none;
        }
        .cb-services-search input:focus {
            border-color: #2b6cb0;
            box-shadow: 0 0 0 4px rgba(43,108,176,0.1);
        }
        .cb-services-search i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
        }

        /* Modal Chi tiết */
        .cb-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.6);
            backdrop-filter: blur(5px);
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .cb-modal-content {
            background-color: #fefefe;
            border-radius: 24px;
            max-width: 800px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            padding: 40px;
            font-family: 'Inter', sans-serif;
        }
        .cb-modal-close {
            position: absolute;
            right: 25px;
            top: 25px;
            font-size: 28px;
            font-weight: bold;
            color: #a0aec0;
            cursor: pointer;
            transition: 0.3s;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #f7fafc;
        }
        .cb-modal-close:hover { color: #e53e3e; background: #fff5f5; }
        .cb-modal-header { display: flex; gap: 30px; margin-bottom: 30px; align-items: start; }
        .cb-modal-icon { width: 80px; height: 80px; background: #ebf8ff; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 36px; color: #2b6cb0; flex-shrink: 0; }
        .cb-modal-title h2 { margin: 0; color: #1a365d; font-size: 32px; font-weight: 800; }
        .cb-modal-title .price-tag { color: #e53e3e; font-size: 20px; font-weight: 700; margin-top: 5px; display: block; }
        .cb-modal-body h4 { color: #2b6cb0; font-size: 18px; margin: 25px 0 10px; font-weight: 700; display: flex; align-items: center; gap: 10px; }
        .cb-modal-body h4 i { font-size: 16px; }
        .cb-modal-body p { color: #4a5568; line-height: 1.8; margin-bottom: 0; }
        .cb-modal-footer { margin-top: 40px; display: flex; justify-content: flex-end; }
        
        @media (max-width: 600px) {
            .cb-modal-header { flex-direction: column; gap: 15px; }
            .cb-modal-content { padding: 25px; }
            .cb-services-toolbar { flex-direction: column; align-items: stretch; }
            .cb-services-search { min-width: 100%; }
        }
    </style>
    
    <!-- Toolbar Lọc & Tìm kiếm -->
    <div class="cb-services-toolbar">
        <div class="cb-services-filter">
            <button class="filter-btn active" data-filter="all">Tất cả dịch vụ</button>
            <button class="filter-btn" data-filter="exam">Khám lâm sàng</button>
            <button class="filter-btn" data-filter="imaging">Chẩn đoán hình ảnh</button>
            <button class="filter-btn" data-filter="test">Xét nghiệm</button>
            <button class="filter-btn" data-filter="package">Gói sức khỏe</button>
        </div>
        <div class="cb-services-search">
            <i class="fas fa-search"></i>
            <input type="text" id="service-search" placeholder="Tìm tên dịch vụ, gói khám...">
        </div>
    </div>

    <div class="cb-services-grid" id="services-grid">
        <?php 
        if (!empty($specialties) && !is_wp_error($specialties)) :
            foreach ($specialties as $spec) :
                $icon_class = 'fa-user-md'; 
                foreach($icon_map as $key => $icon) {
                    if (stripos($spec->name, $key) !== false) {
                        $icon_class = $icon;
                        break;
                    }
                }
                
                // --- PHẦN 1: COPYWRITING - TỰ ĐỘNG ĐỔI TÊN ---
                $display_name = $spec->name;
                if (stripos($display_name, 'Bác sĩ chuyên khoa') !== false) {
                    $display_name = str_ireplace('Bác sĩ chuyên khoa', 'Khám chuyên khoa', $display_name);
                } elseif (stripos($display_name, 'Bác sĩ') !== false) {
                    $display_name = str_ireplace('Bác sĩ', 'Khám', $display_name);
                }
                
                // Phân loại danh mục để lọc (giả lập dựa trên tên)
                $category = 'exam';
                if (stripos($display_name, 'Gói') !== false) $category = 'package';
                elseif (stripos($display_name, 'Siêu âm') !== false || stripos($display_name, 'X-Quang') !== false) $category = 'imaging';
                elseif (stripos($display_name, 'Xét nghiệm') !== false || stripos($display_name, 'Máu') !== false) $category = 'test';

                $price = '200.000đ';
                $price_label = 'Phí dịch vụ từ:';
                if (stripos($spec->name, 'Gói') !== false) {
                    $price = '1.500.000đ';
                    $price_label = 'Giá trọn gói:';
                } elseif (stripos($spec->name, 'Ngoại') !== false) {
                    $price = '250.000đ';
                }
                
                $desc = $spec->description;
                if (empty($desc)) {
                    $desc = 'Dịch vụ ' . $display_name . ' chất lượng cao với đội ngũ bác sĩ giàu kinh nghiệm và trang thiết bị hiện đại.';
                }

                // Thông tin chi tiết giả lập cho Modal
                $steps = "• Đăng ký thông tin\n• Khám lâm sàng với bác sĩ\n• Thực hiện cận lâm sàng (nếu có)\n• Tư vấn kết quả và đơn thuốc";
                $prep = "Vui lòng mang theo hồ sơ bệnh án cũ (nếu có).";
                if ($category == 'test' || $category == 'package') $prep = "Nên nhịn ăn ít nhất 8 tiếng trước khi thực hiện để kết quả chính xác nhất.";
        ?>
            <div class="cb-service-card" data-category="<?php echo $category; ?>" data-name="<?php echo esc_attr(mb_strtolower($display_name)); ?>">
                <div class="cb-service-icon">
                    <i class="fa-solid <?php echo $icon_class; ?>"></i>
                </div>
                <h3><?php echo esc_html($display_name); ?></h3>
                <div class="cb-service-desc">
                    <?php echo wp_trim_words($desc, 22); ?>
                </div>
                <div class="cb-service-footer">
                    <div class="cb-service-price">
                        <span><?php echo $price_label; ?></span>
                        <?php echo $price; ?>
                    </div>
                    <div class="cb-service-actions">
                        <button class="cb-btn-view-service" 
                                data-title="<?php echo esc_attr($display_name); ?>"
                                data-desc="<?php echo esc_attr($desc); ?>"
                                data-price="<?php echo esc_attr($price); ?>"
                                data-steps="<?php echo esc_attr($steps); ?>"
                                data-prep="<?php echo esc_attr($prep); ?>"
                                data-icon="<?php echo $icon_class; ?>">Chi tiết</button>
                        <a href="<?php echo home_url('/dat-lich/'); ?>?auto_specialty=<?php echo $spec->term_id; ?>" class="cb-btn-book-service">Đặt lịch</a>
                    </div>
                </div>
            </div>
        <?php 
            endforeach;
        else:
            echo '<p>Chưa có dữ liệu dịch vụ.</p>';
        endif; 
        ?>
    </div>

    <!-- Modal Xem Chi Tiết -->
    <div id="serviceModal" class="cb-modal">
        <div class="cb-modal-content">
            <span class="cb-modal-close">&times;</span>
            <div class="cb-modal-header">
                <div class="cb-modal-icon" id="modalIcon">
                    <i class="fa-solid fa-user-md"></i>
                </div>
                <div class="cb-modal-title">
                    <h2 id="modalTitle">Tên Dịch Vụ</h2>
                    <span class="price-tag" id="modalPrice">200.000đ</span>
                </div>
            </div>
            <div class="cb-modal-body">
                <p id="modalDesc">Mô tả dịch vụ sẽ hiển thị ở đây.</p>
                
                <h4><i class="fas fa-list-check"></i> Quy trình thực hiện</h4>
                <p id="modalSteps" style="white-space: pre-line;">Các bước thực hiện...</p>
                
                <h4><i class="fas fa-info-circle"></i> Chuẩn bị trước khi khám</h4>
                <p id="modalPrep">Thông tin cần chuẩn bị...</p>
                
                <h4><i class="fas fa-microscope"></i> Trang thiết bị</h4>
                <p>Hệ thống máy móc hiện đại, nhập khẩu từ Mỹ và Nhật Bản, đảm bảo độ chính xác cao nhất.</p>
            </div>
            <div class="cb-modal-footer">
                <a href="<?php echo home_url('/dat-lich/'); ?>" class="cb-btn-book-service" style="padding: 15px 40px; font-size: 16px;">Đặt lịch ngay</a>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Logic Lọc & Tìm kiếm
        const searchInput = document.getElementById('service-search');
        const filterBtns = document.querySelectorAll('.filter-btn');
        const cards = document.querySelectorAll('.cb-service-card');

        function filterServices() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const activeFilter = document.querySelector('.filter-btn.active').dataset.filter;

            cards.forEach(card => {
                const name = card.dataset.name;
                const category = card.dataset.category;
                
                const matchesSearch = name.includes(searchTerm);
                const matchesFilter = activeFilter === 'all' || category === activeFilter;

                if (matchesSearch && matchesFilter) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        searchInput.addEventListener('input', filterServices);

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                filterServices();
            });
        });

        // 2. Logic Modal Chi tiết
        const modal = document.getElementById('serviceModal');
        const closeBtn = document.querySelector('.cb-modal-close');
        const viewBtns = document.querySelectorAll('.cb-btn-view-service');

        viewBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('modalTitle').innerText = this.dataset.title;
                document.getElementById('modalDesc').innerText = this.dataset.desc;
                document.getElementById('modalPrice').innerText = this.dataset.price;
                document.getElementById('modalSteps').innerText = this.dataset.steps;
                document.getElementById('modalPrep').innerText = this.dataset.prep;
                document.getElementById('modalIcon').innerHTML = `<i class="fa-solid ${this.dataset.icon}"></i>`;
                
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden'; // Khóa cuộn trang
            });
        });

        closeBtn.onclick = function() {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('clinic_services', 'clinic_services_grid_shortcode');
function clinic_auth_scripts() {
    if ( is_page('dang-nhap') || is_page('dang-ky') ) {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var forms = document.querySelectorAll('.clinic-auth-form');
            var messages = {
                'full_name': 'Vui lòng nhập họ và tên',
                'user_login': 'Vui lòng nhập tên đăng nhập',
                'user_email': 'Vui lòng nhập địa chỉ email hợp lệ',
                'user_pass': 'Vui lòng nhập mật khẩu',
                'log': 'Vui lòng nhập tên đăng nhập hoặc email',
                'pwd': 'Vui lòng nhập mật khẩu'
            };

            forms.forEach(function(form) {
                form.onsubmit = function(e) {
                    var isValid = true;
                    var firstInvalid = null;
                    
                    form.querySelectorAll('.cbf-error-msg').forEach(function(msg) { msg.remove(); });
                    
                    var inputs = form.querySelectorAll('input[required]');
                    inputs.forEach(function(el) {
                        var val = el.value.trim();
                        var fieldValid = true;
                        
                        if (!val) {
                            fieldValid = false;
                        } else if (el.type === 'email' && !val.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                            fieldValid = false;
                        }

                        if (!fieldValid) {
                            el.classList.add('has-error');
                            isValid = false;
                            if (!firstInvalid) firstInvalid = el;
                            
                            var errorMsg = document.createElement('span');
                            errorMsg.className = 'cbf-error-msg';
                            errorMsg.innerText = messages[el.name] || 'Thông tin này là bắt buộc';
                            el.parentNode.insertBefore(errorMsg, el.nextSibling);
                        } else {
                            el.classList.remove('has-error');
                        }

                        // Xóa lỗi khi nhập lại
                        el.oninput = function() {
                            if (this.value.trim()) {
                                this.classList.remove('has-error');
                                var next = this.nextSibling;
                                if (next && next.classList && next.classList.contains('cbf-error-msg')) {
                                    next.remove();
                                }
                            }
                        };
                    });

                    if (!isValid) {
                        e.preventDefault();
                        if (firstInvalid) firstInvalid.focus();
                    }
                };
            });
        });
        </script>
        <?php
    }
}

/**
 * Shortcode for Custom Login Form
 */
function clinic_login_form_shortcode() {
    if ( is_user_logged_in() ) {
        return '<div class="clinic-auth-container"><p>Bạn đã đăng nhập. <a href="' . wp_logout_url( home_url() ) . '">Đăng xuất</a></p></div>';
    }

    $output = '';
    if ( isset( $_POST['clinic_login_submit'] ) ) {
        $creds = array(
            'user_login'    => sanitize_text_field( $_POST['log'] ),
            'user_password' => $_POST['pwd'],
            'remember'      => isset( $_POST['rememberme'] ),
        );

        $user = wp_signon( $creds, false );

        if ( is_wp_error( $user ) ) {
            $error_text = $user->get_error_message();
            // Làm sạch thông báo lỗi để gọn hơn
            if (strpos($error_text, 'Mật khẩu') !== false) {
                $error_text = 'Mật khẩu không chính xác. Vui lòng thử lại.';
            } elseif (strpos($error_text, 'tên người dùng') !== false) {
                $error_text = 'Tên đăng nhập hoặc Email không tồn tại.';
            }
            $output .= '<div class="clinic-auth-error">' . $error_text . '</div>';

            // Đưa script vào $output thay vì echo trực tiếp
            $output .= '<script>
                if ( window.history.replaceState ) {
                    window.history.replaceState( null, null, window.location.href );
                }
            </script>';
        } else {
            // Đưa script chuyển hướng vào chuỗi return để chạy an toàn
            return '<script>window.location.href="' . home_url() . '";</script>';
        }
    }

    ob_start();
    ?>
    <div class="clinic-auth-page">
        <div class="clinic-auth-container">
            <div class="clinic-auth-status"><?php echo $output; ?></div>
            <form method="post" class="clinic-auth-form" id="clinic-login-form" novalidate>
                <h3>Đăng nhập</h3>
                <div class="input-group">
                    <label>Tài khoản</label>
                    <input type="text" name="log" placeholder="Tên đăng nhập hoặc Email" required>
                </div>
                <div class="input-group">
                    <label>Mật khẩu</label>
                    <input type="password" name="pwd" placeholder="Nhập mật khẩu" required>
                </div>
                <div style="font-size: 14px; margin-bottom: 20px;">
                    <label style="display: flex; align-items: center; cursor: pointer; font-weight: normal;">
                        <input type="checkbox" name="rememberme" style="width: auto !important; margin-right: 8px !important;"> Ghi nhớ đăng nhập
                    </label>
                </div>
                <button type="submit" name="clinic_login_submit" class="clinic-auth-btn">Đăng nhập</button>
                <div class="clinic-auth-footer">
                    <a href="<?php echo wp_lostpassword_url(); ?>" style="color: #718096; font-weight: 500; font-size: 14px;">Quên mật khẩu?</a>
                    <div style="margin-top: 15px;">
                        Chưa có tài khoản? <a href="<?php echo home_url('/dang-ky/'); ?>">Đăng ký ngay</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <style>
        /* Sửa lỗi nhỏ cho checkbox */
        #clinic-login-form input[type="checkbox"] { width: auto !important; height: auto !important; padding: 0 !important; margin: 0 10px 0 0 !important; }
    </style>
    <?php
    return ob_get_clean();
}
add_shortcode( 'clinic_login_form', 'clinic_login_form_shortcode' );
/**
 * Shortcode for Custom Registration Form
 */
function clinic_register_form_shortcode() {
    if ( is_user_logged_in() ) {
        return '<div class="clinic-auth-container"><p>Bạn đã đăng nhập.</p></div>';
    }

    $output = '';
    if ( isset( $_POST['clinic_register_submit'] ) ) {
        $username = sanitize_user( $_POST['user_login'] );
        $email    = sanitize_email( $_POST['user_email'] );
        $password = $_POST['user_pass'];
        $fullname = sanitize_text_field( $_POST['full_name'] );

        $errors = new WP_Error();

        if ( empty( $username ) || empty( $email ) || empty( $password ) ) {
            $errors->add( 'field', 'Vui lòng điền đầy đủ các trường bắt buộc.' );
        }
        if ( username_exists( $username ) ) {
            $errors->add( 'user_name', 'Tên đăng nhập đã tồn tại.' );
        }
        if ( ! is_email( $email ) ) {
            $errors->add( 'email_invalid', 'Địa chỉ Email không hợp lệ.' );
        }
        if ( email_exists( $email ) ) {
            $errors->add( 'email_exists', 'Địa chỉ Email này đã được đăng ký.' );
        }

        if ( empty( $errors->get_error_messages() ) ) {
            $userdata = array(
                'user_login'   => $username,
                'user_pass'    => $password,
                'user_email'   => $email,
                'display_name' => $fullname,
                'role'         => 'subscriber'
            );
            
            $user_id = wp_insert_user( $userdata );

            if ( ! is_wp_error( $user_id ) ) {
                // Tự động đăng nhập sau khi đăng ký thành công
                $creds = array(
                    'user_login'    => $username,
                    'user_password' => $password,
                    'remember'      => true
                );
                wp_signon( $creds, false );

                $output .= '<div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                    <strong>✅ Đăng ký thành công!</strong><br>Hệ thống đang chuyển hướng...
                </div>';
                $output .= '<script>setTimeout(function(){ window.location.href="' . home_url() . '"; }, 2000);</script>';
            } else {
                $output .= '<p style="color:red; text-align:center; font-weight:bold;">❌ Lỗi: ' . $user_id->get_error_message() . '</p>';
            }
        } else {
            $output .= '<div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;">';
            foreach ( $errors->get_error_messages() as $error ) {
                $output .= '• ' . $error . '<br>';
            }
            $output .= '</div>';
        }
    }

    ob_start();
    ?>
    <div class="clinic-auth-page">
        <div class="clinic-auth-container">
            <div class="clinic-auth-status"><?php echo $output; ?></div>
            <form method="post" class="clinic-auth-form" id="clinic-register-form" novalidate>
                <h3>Đăng ký tài khoản</h3>
                <div class="input-group">
                    <label>Họ và tên</label>
                    <input type="text" name="full_name" placeholder="Nhập họ tên đầy đủ" required>
                </div>
                <div class="input-group">
                    <label>Tên đăng nhập</label>
                    <input type="text" name="user_login" placeholder="Sử dụng tên viết liền, không dấu" required>
                </div>
                <div class="input-group">
                    <label>Địa chỉ Email</label>
                    <input type="email" name="user_email" placeholder="example@gmail.com" required>
                </div>
                <div class="input-group">
                    <label>Mật khẩu</label>
                    <input type="password" name="user_pass" placeholder="••••••••" required>
                </div>
                <button type="submit" name="clinic_register_submit" class="clinic-auth-btn">Tham gia ngay</button>
                <div class="clinic-auth-footer">
                    Đã có tài khoản? <a href="<?php echo home_url('/dang-nhap/'); ?>">Đăng nhập tại đây</a>
                </div>
            </form>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'clinic_register_form', 'clinic_register_form_shortcode' );

/**
 * Shortcode for Booking History
 */
function clinic_booking_history_shortcode() {
    if ( ! is_user_logged_in() ) {
        return '<div class="clinic-history-container"><p style="text-align:center;">Vui lòng <a href="' . home_url('/dang-nhap/') . '" style="color:#005086; font-weight:700;">đăng nhập</a> để xem lịch sử đặt lịch của bạn.</p></div>';
    }

    // Enqueue Flatpickr and FontAwesome
    wp_enqueue_style('flatpickr-css', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
    wp_enqueue_script('flatpickr-js', 'https://cdn.jsdelivr.net/npm/flatpickr', array(), null, true);

    $current_user_id = get_current_user_id();
    $args = array(
        'post_type'      => 'appointment',
        'post_status'    => array('pending', 'publish', 'draft', 'private', 'completed'),
        'author'         => $current_user_id,
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC'
    );
    $query = new WP_Query($args);

    ob_start();
    ?>
    <div class="clinic-history-container">
        <h3 style="color: #1a365d; font-weight: 800; text-transform: uppercase; border-bottom: 3px solid #005086; padding-bottom: 10px; display: inline-block; margin-bottom: 30px;">Lịch sử đặt lịch</h3>
        
        <?php if ( $query->have_posts() ) : ?>
            <div style="overflow-x: auto;">
                <table class="clinic-history-table">
                    <thead>
                        <tr>
                            <th>Ngày & Giờ</th>
                            <th>Bác sĩ / Chuyên khoa</th>
                            <th>Bệnh nhân</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ( $query->have_posts() ) : $query->the_post(); 
                            $post_id = get_the_ID();
                            $status = get_post_status();
                            
                            $status_label = 'Chờ xác nhận';
                            $status_class = 'status-pending';
                            
                            if ($status == 'publish') {
                                $status_label = 'Đã xác nhận';
                                $status_class = 'status-confirmed';
                            } elseif ($status == 'completed') {
                                $status_label = 'Đã khám xong';
                                $status_class = 'status-completed';
                            } elseif ($status == 'private') {
                                $status_label = 'Đã từ chối';
                                $status_class = 'status-rejected';
                            } elseif ($status == 'draft') {
                                $status_label = 'Đã hủy';
                                $status_class = 'status-cancelled';
                            }
                            
                            $booking_date = get_post_meta($post_id, '_booking_date', true);
                            $booking_time = get_post_meta($post_id, '_booking_time', true);
                            $doctor = get_post_meta($post_id, '_selected_doctor', true);
                            $specialty = get_post_meta($post_id, '_specialty', true);
                            $p_name = get_post_meta($post_id, '_patient_name', true);

                            // Kiểm tra quyền hủy/đổi lịch khám (Quy tắc 24h)
                            $modify_check = cb_can_modify_appointment($post_id);
                            $can_modify = !is_wp_error($modify_check);
                            $modify_error = is_wp_error($modify_check) ? $modify_check->get_error_message() : '';
                        ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: #2d3748;"><?php echo esc_html($booking_date); ?></div>
                                    <div style="font-size: 12px; color: #718096;"><?php echo esc_html($booking_time); ?></div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: #005086;"><?php echo esc_html($doctor); ?></div>
                                    <div style="font-size: 12px; color: #4a5568;"><?php echo esc_html($specialty); ?></div>
                                </td>
                                <td style="font-size: 14px; color: #4a5568;"><?php echo esc_html($p_name); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <?php echo esc_html($status_label); ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                        <?php if ($can_modify) : ?>
                                            <button class="cb-patient-btn cb-patient-reschedule-btn" data-id="<?php echo $post_id; ?>" data-date="<?php echo esc_attr($booking_date); ?>" data-time="<?php echo esc_attr($booking_time); ?>" style="background: #3182ce; color: #fff; border: none; padding: 6px 12px; border-radius: 6px; font-weight: 700; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; transition: all 0.2s;">
                                                <i class="fas fa-calendar-alt"></i> Đổi lịch
                                            </button>
                                            <button class="cb-patient-btn cb-patient-cancel-btn" data-id="<?php echo $post_id; ?>" style="background: #e53e3e; color: #fff; border: none; padding: 6px 12px; border-radius: 6px; font-weight: 700; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; transition: all 0.2s;">
                                                <i class="fas fa-trash-alt"></i> Hủy
                                            </button>
                                        <?php else : ?>
                                            <?php if ($status === 'completed') : ?>
                                                <?php $has_review = get_post_meta($post_id, '_has_review', true); ?>
                                                <?php if (!$has_review) : ?>
                                                    <button class="cb-patient-btn cb-patient-review-btn" data-id="<?php echo $post_id; ?>" data-doctor="<?php echo esc_attr($doctor); ?>" style="background: #dd6b20; color: #fff; border: none; padding: 6px 12px; border-radius: 6px; font-weight: 700; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; transition: all 0.2s;">
                                                        <i class="fas fa-star"></i> Viết đánh giá
                                                    </button>
                                                <?php else : ?>
                                                    <span style="color: #38a169; font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;" title="Cảm ơn bạn đã đóng góp đánh giá!">
                                                        <i class="fas fa-check-circle"></i> Đã đánh giá
                                                    </span>
                                                <?php endif; ?>
                                            <?php else : ?>
                                                <span style="color: #a0aec0; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;" title="<?php echo esc_attr($modify_error); ?>">
                                                    <i class="fas fa-lock"></i> Khóa
                                                </span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </tbody>
                </table>
            </div>

            <!-- Modal: Đổi lịch hẹn -->
            <div id="cb-modal-reschedule" class="cb-modal">
                <div class="cb-modal-content">
                    <div class="cb-modal-header">
                        <h3>Đổi lịch khám</h3>
                        <span class="cb-modal-close" data-modal="cb-modal-reschedule">&times;</span>
                    </div>
                    <div class="cb-modal-body">
                        <p>Chọn ngày và giờ khám mới của bạn. Sau khi đổi, lịch hẹn sẽ chờ bác sĩ xác nhận lại.</p>
                        <input type="hidden" id="reschedule-app-id" value="">
                        
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; font-weight: 700; margin-bottom: 5px; color: #1a365d;">Ngày khám mới:</label>
                            <input type="text" id="reschedule-new-date" placeholder="Chọn ngày khám mới..." style="width: 100%; border: 1px solid #cbd5e0; border-radius: 8px; padding: 10px; font-family: inherit; font-size: 14px;" readonly>
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; font-weight: 700; margin-bottom: 5px; color: #1a365d;">Giờ khám mới:</label>
                            <select id="reschedule-new-time" style="width: 100%; border: 1px solid #cbd5e0; border-radius: 8px; padding: 10px; font-family: inherit; font-size: 14px; background: #fff; height: auto;">
                                <option value="">-- Chọn khung giờ mới --</option>
                                <?php 
                                $slots_str = get_option('cb_time_slots', "08:00\n08:30\n09:00\n09:30\n10:00\n10:30\n14:00\n14:30\n15:00\n15:30\n16:00");
                                $slots = array_filter(array_map('trim', explode("\n", $slots_str)));
                                foreach ($slots as $slot) : 
                                ?>
                                    <option value="<?php echo esc_attr($slot); ?>"><?php echo esc_html($slot); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="cb-modal-footer">
                        <button class="cb-action-btn cb-btn-complete" id="cb-confirm-reschedule" style="background: #3182ce;">Xác nhận Đổi lịch</button>
                        <button class="cb-action-btn cb-btn-view-notes" style="background: #edf2f7; color: #4a5568;" data-close="cb-modal-reschedule">Hủy</button>
                    </div>
                </div>
            </div>

            <!-- Modal: Viết đánh giá -->
            <div id="cb-modal-review" class="cb-modal">
                <div class="cb-modal-content" style="max-width: 450px;">
                    <div class="cb-modal-header">
                        <h3>Đánh giá Bác sĩ</h3>
                        <span class="cb-modal-close" data-modal="cb-modal-review">&times;</span>
                    </div>
                    <div class="cb-modal-body" style="text-align: center;">
                        <p style="margin-bottom: 5px; font-size: 15px; font-weight: 500;">Bạn đánh giá chất lượng dịch vụ của <strong id="cb-review-doctor-name" style="color: #005086;">Bác sĩ</strong> như thế nào?</p>
                        
                        <!-- Interactive Star Selector -->
                        <div class="cb-stars-selector">
                            <span class="cb-star" data-value="1"><i class="far fa-star"></i></span>
                            <span class="cb-star" data-value="2"><i class="far fa-star"></i></span>
                            <span class="cb-star" data-value="3"><i class="far fa-star"></i></span>
                            <span class="cb-star" data-value="4"><i class="far fa-star"></i></span>
                            <span class="cb-star" data-value="5"><i class="far fa-star"></i></span>
                        </div>
                        <input type="hidden" id="cb-review-rating-value" value="0">
                        <input type="hidden" id="cb-review-app-id" value="">
                        
                        <div style="text-align: left; margin-top: 20px;">
                            <label style="display: block; font-weight: 700; margin-bottom: 8px; color: #1a365d;">Ý kiến nhận xét của bạn:</label>
                            <textarea id="cb-review-text" placeholder="Hãy chia sẻ trải nghiệm khám bệnh của bạn tại phòng khám..." rows="4" style="width: 100%; border: 1px solid #cbd5e0; border-radius: 12px; padding: 12px; font-family: inherit; font-size: 14px; resize: vertical; box-sizing: border-box;"></textarea>
                        </div>
                    </div>
                    <div class="cb-modal-footer">
                        <button class="cb-action-btn" id="cb-confirm-submit-review" style="background: #dd6b20; width: 100%;">Gửi đánh giá ngay</button>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <div style="text-align: center; padding: 40px; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e0;">
                <p style="color: #718096; margin-bottom: 0;">Bạn chưa có lịch hẹn nào được ghi nhận.</p>
                <a href="<?php echo home_url('/dat-lich/'); ?>" style="display: inline-block; margin-top: 15px; color: #005086; font-weight: 700; text-decoration: none;">Đặt lịch ngay &raquo;</a>
            </div>
        <?php endif; ?>
    </div>
    <style>
        .clinic-history-container { max-width: 1000px; margin: 40px auto; padding: 40px; background: #fff; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .clinic-history-table { width: 100%; border-collapse: collapse; min-width: 600px; }
        .clinic-history-table th { text-align: left; padding: 15px; background: #f8fafc; color: #4a5568; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid #edf2f7; }
        .clinic-history-table td { padding: 20px 15px; border-bottom: 1px solid #f0f4f8; vertical-align: middle; }
        .status-badge { display: inline-block; padding: 6px 14px; border-radius: 50px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .status-pending { background: #fefcbf; color: #744210; }
        .status-confirmed { background: #c6f6d5; color: #22543d; }
        .status-completed { background: #e0f2fe; color: #0369a1; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        .status-cancelled { background: #edf2f7; color: #4a5568; }
        @media (max-width: 600px) { .clinic-history-container { padding: 20px; } }

        /* Modal Styles */
        .cb-modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center; backdrop-filter: blur(4px); transition: all 0.3s ease; font-family: inherit; }
        .cb-modal-content { background-color: #fff; margin: auto; padding: 30px; border-radius: 20px; width: 90%; max-width: 500px; box-shadow: 0 20px 40px rgba(0,0,0,0.15); border: 1px solid #edf2f7; animation: cbSlideDown 0.3s ease-out; box-sizing: border-box; }
        .cb-modal-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #edf2f7; padding-bottom: 15px; margin-bottom: 20px; }
        .cb-modal-header h3 { margin: 0; font-size: 20px; font-weight: 800; color: #1a365d; }
        .cb-modal-close { color: #a0aec0; font-size: 28px; font-weight: bold; cursor: pointer; transition: 0.2s; line-height: 1; }
        .cb-modal-close:hover { color: #e53e3e; }
        .cb-modal-body { margin-bottom: 25px; line-height: 1.5; color: #4a5568; }
        .cb-modal-body p { margin-top: 0; margin-bottom: 15px; font-size: 14px; }
        .cb-modal-footer { display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #edf2f7; padding-top: 15px; }
        .cb-action-btn { border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer; transition: all 0.2s; color: #fff; }
        .cb-action-btn:hover { opacity: 0.9; transform: translateY(-1px); }
        .cb-action-btn:active { transform: translateY(0); }
        
        /* Stars Selector CSS */
        .cb-stars-selector { display: flex; gap: 12px; font-size: 36px; cursor: pointer; justify-content: center; margin: 20px 0; }
        .cb-star { color: #cbd5e0; transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .cb-star:hover, .cb-star.hover, .cb-star.selected { color: #ecc94b; transform: scale(1.2); filter: drop-shadow(0 0 5px rgba(236,201,75,0.5)); }
        
        @keyframes cbSlideDown {
            from { opacity: 0; transform: translateY(-50px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    <script>
        jQuery(document).ready(function($) {
            var ajaxurl = '<?php echo esc_url( admin_url('admin-ajax.php') ); ?>';

            // Khởi tạo Flatpickr cho ngày đổi lịch khám
            var flatpickrInterval = setInterval(function() {
                if (typeof flatpickr !== 'undefined') {
                    clearInterval(flatpickrInterval);
                    flatpickr("#reschedule-new-date", {
                        dateFormat: "d/m/Y",
                        minDate: "today",
                        disableMobile: "true"
                    });
                }
            }, 100);

            // Mở modal Đổi lịch
            $(document).on('click', '.cb-patient-reschedule-btn', function() {
                var appId = $(this).data('id');
                var oldDate = $(this).data('date');
                var oldTime = $(this).data('time');
                
                $('#reschedule-app-id').val(appId);
                $('#reschedule-new-date').val(oldDate);
                $('#reschedule-new-time').val(oldTime);
                
                $('#cb-modal-reschedule').css('display', 'flex');
                $('body').css('overflow', 'hidden');
            });

            // Đóng modal khi bấm close hoặc Hủy
            $(document).on('click', '.cb-modal-close, [data-close]', function() {
                var modalId = $(this).data('modal') || $(this).data('close');
                $('#' + modalId).css('display', 'none');
                $('body').css('overflow', 'auto');
            });

            // Đóng modal khi click ra ngoài vùng nội dung modal
            $(window).on('click', function(event) {
                if ($(event.target).hasClass('cb-modal')) {
                    $(event.target).css('display', 'none');
                    $('body').css('overflow', 'auto');
                }
            });

            // Gửi yêu cầu Đổi lịch qua AJAX
            $('#cb-confirm-reschedule').on('click', function() {
                var $btn = $(this);
                var appId = $('#reschedule-app-id').val();
                var newDate = $('#reschedule-new-date').val();
                var newTime = $('#reschedule-new-time').val();
                
                if (!newDate) {
                    alert('Vui lòng chọn ngày khám mới.');
                    return;
                }
                if (!newTime) {
                    alert('Vui lòng chọn khung giờ khám mới.');
                    return;
                }
                
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'cb_patient_reschedule_appointment',
                        appointment_id: appId,
                        new_date: newDate,
                        new_time: newTime
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.data.message);
                            location.reload();
                        } else {
                            alert('Lỗi: ' + response.data.message);
                            $btn.prop('disabled', false).text('Xác nhận Đổi lịch');
                        }
                    },
                    error: function() {
                        alert('Đã xảy ra lỗi kết nối.');
                        $btn.prop('disabled', false).text('Xác nhận Đổi lịch');
                    }
                });
            });

            // Gửi yêu cầu Hủy lịch qua AJAX
            $(document).on('click', '.cb-patient-cancel-btn', function() {
                var $btn = $(this);
                var appId = $btn.data('id');
                
                if (!confirm('Bạn có chắc chắn muốn hủy lịch hẹn này không? Hành động này không thể hoàn tác.')) {
                    return;
                }
                
                var originalHtml = $btn.html();
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Hủy...');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'cb_patient_cancel_appointment',
                        appointment_id: appId
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.data.message);
                            location.reload();
                        } else {
                            alert('Lỗi: ' + response.data.message);
                            $btn.prop('disabled', false).html(originalHtml);
                        }
                    },
                    error: function() {
                        alert('Đã xảy ra lỗi kết nối.');
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                });
            });

            // Mở modal Đánh giá
            $(document).on('click', '.cb-patient-review-btn', function() {
                var appId = $(this).data('id');
                var docName = $(this).data('doctor');
                
                $('#cb-review-app-id').val(appId);
                $('#cb-review-doctor-name').text(docName);
                $('#cb-review-rating-value').val(0);
                $('#cb-review-text').val('');
                
                $('.cb-stars-selector .cb-star').removeClass('selected hover');
                $('.cb-stars-selector .cb-star i').removeClass('fas fa-star').addClass('far fa-star');
                
                $('#cb-modal-review').css('display', 'flex');
                $('body').css('overflow', 'hidden');
            });

            // Hover sao
            $('.cb-stars-selector .cb-star').on('mouseenter', function() {
                var val = $(this).data('value');
                $('.cb-stars-selector .cb-star').each(function() {
                    if ($(this).data('value') <= val) {
                        $(this).addClass('hover');
                    } else {
                        $(this).removeClass('hover');
                    }
                });
            }).on('mouseleave', function() {
                $('.cb-stars-selector .cb-star').removeClass('hover');
            });

            // Click chọn sao
            $('.cb-stars-selector .cb-star').on('click', function() {
                var val = $(this).data('value');
                $('#cb-review-rating-value').val(val);
                
                $('.cb-stars-selector .cb-star').each(function() {
                    if ($(this).data('value') <= val) {
                        $(this).addClass('selected');
                        $(this).find('i').removeClass('far fa-star').addClass('fas fa-star');
                    } else {
                        $(this).removeClass('selected');
                        $(this).find('i').removeClass('fas fa-star').addClass('far fa-star');
                    }
                });
            });

            // AJAX Gửi Đánh giá
            $('#cb-confirm-submit-review').on('click', function() {
                var $btn = $(this);
                var appId = $('#cb-review-app-id').val();
                var rating = $('#cb-review-rating-value').val();
                var content = $('#cb-review-text').val().trim();
                
                if (rating < 1 || rating > 5) {
                    alert('Vui lòng chọn số sao đánh giá (từ 1 đến 5 sao).');
                    return;
                }
                
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang gửi đánh giá...');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'cb_submit_doctor_review',
                        appointment_id: appId,
                        rating: rating,
                        review_content: content
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.data.message);
                            location.reload();
                        } else {
                            alert('Lỗi: ' + response.data.message);
                            $btn.prop('disabled', false).text('Gửi đánh giá ngay');
                        }
                    },
                    error: function() {
                        alert('Đã xảy ra lỗi kết nối.');
                        $btn.prop('disabled', false).text('Gửi đánh giá ngay');
                    }
                });
            });
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('clinic_booking_history', 'clinic_booking_history_shortcode');

/**
 * Shortcode for User Account Settings (Profile & Password)
 */
function clinic_user_settings_shortcode() {
    if ( ! is_user_logged_in() ) {
        return '<div class="clinic-auth-container"><p style="text-align:center;">Vui lòng <a href="' . home_url('/dang-nhap/') . '" style="color:#005086; font-weight:700;">đăng nhập</a> để chỉnh sửa thông tin.</p></div>';
    }

    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    $message = '';
    $error = '';

    // Xử lý cập nhật thông tin
    if ( isset( $_POST['update_profile_submit'] ) ) {
        if ( ! isset( $_POST['profile_nonce'] ) || ! wp_verify_nonce( $_POST['profile_nonce'], 'update_profile_action' ) ) {
            $error = 'Lỗi bảo mật, vui lòng thử lại.';
        } else {
            $display_name = sanitize_text_field( $_POST['display_name'] );
            $user_email = sanitize_email( $_POST['user_email'] );
            $pass1 = $_POST['pass1'];
            $pass2 = $_POST['pass2'];
            
            $update_data = array( 'ID' => $user_id, 'display_name' => $display_name, 'user_email' => $user_email );
            
            // Xử lý đổi mật khẩu nếu có nhập
            if ( ! empty( $pass1 ) ) {
                if ( $pass1 === $pass2 ) {
                    if ( strlen($pass1) < 6 ) {
                        $error = 'Mật khẩu phải có ít nhất 6 ký tự.';
                    } else {
                        $update_data['user_pass'] = $pass1;
                    }
                } else {
                    $error = 'Xác nhận mật khẩu không khớp.';
                }
            }

            if ( empty($error) ) {
                $updated = wp_update_user( $update_data );
                if ( is_wp_error( $updated ) ) {
                    $error = $updated->get_error_message();
                } else {
                    // Cập nhật User Meta (Các trường mở rộng)
                    update_user_meta( $user_id, 'phone_number', sanitize_text_field( $_POST['phone_number'] ) );
                    update_user_meta( $user_id, 'address', sanitize_text_field( $_POST['address'] ) );
                    update_user_meta( $user_id, 'gender', sanitize_text_field( $_POST['gender'] ) );
                    update_user_meta( $user_id, 'birthday', sanitize_text_field( $_POST['birthday'] ) );
                    update_user_meta( $user_id, 'company', sanitize_text_field( $_POST['company'] ) );
                    update_user_meta( $user_id, 'province', sanitize_text_field( $_POST['province'] ) );
                    
                    $message = 'Cập nhật thông tin thành công!';
                }
            }
        }
    }

    // Lấy dữ liệu hiện tại
    $phone = get_user_meta( $user_id, 'phone_number', true );
    $address = get_user_meta( $user_id, 'address', true );
    $gender = get_user_meta( $user_id, 'gender', true );
    $birthday = get_user_meta( $user_id, 'birthday', true );
    $company = get_user_meta( $user_id, 'company', true );
    $province = get_user_meta( $user_id, 'province', true );

    ob_start();
    ?>
    <style>
        .profile-settings-wrapper { display: flex; gap: 50px; max-width: 1100px; margin: 40px auto; font-family: 'Montserrat', sans-serif; background: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); }
        .profile-left { flex: 1; text-align: center; border-right: 1px solid #f0f0f0; padding-right: 50px; }
        .profile-right { flex: 2; }
        
        .avatar-box { width: 160px; height: 160px; margin: 0 auto 20px; border-radius: 50%; overflow: hidden; background: #f8fafc; border: 4px solid #edf2f7; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .avatar-box img { width: 100%; height: 100%; object-fit: cover; }
        .user-meta-info h4 { margin: 10px 0 5px; color: #1a365d; font-weight: 800; font-size: 20px; text-transform: uppercase; letter-spacing: 1px; }
        .user-meta-info p { color: #718096; font-size: 14px; margin-bottom: 25px; }
        
        .profile-actions { display: flex; flex-direction: column; gap: 10px; }
        .btn-profile-sub { width: 100%; padding: 12px; border-radius: 8px; border: 1.5px solid #005086; background: transparent; color: #005086; font-size: 13px; font-weight: 700; cursor: pointer; transition: 0.3s; }
        .btn-profile-sub:hover { background: #005086; color: #fff; }
        .btn-profile-sub.primary { background: #005086; color: #fff; }
        
        .profile-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .profile-input-group { margin-bottom: 20px; }
        .profile-input-group label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 700; color: #2d3748; text-transform: uppercase; letter-spacing: 0.5px; }
        .profile-input-group input, .profile-input-group select { 
            width: 100%; padding: 14px 18px; border: 2px solid #edf2f7; border-radius: 10px; font-size: 15px; background: #f8fafc; box-sizing: border-box; transition: 0.3s;
        }
        .profile-input-group input:focus { border-color: #005086; outline: none; background: #fff; box-shadow: 0 0 0 4px rgba(0,80,134,0.1); }
        
        .section-title { font-size: 15px; font-weight: 800; color: #1a365d; margin: 40px 0 20px; padding-bottom: 10px; border-bottom: 2px solid #edf2f7; text-transform: uppercase; }
        
        #password-section { display: none; margin-top: 20px; padding-top: 20px; border-top: 1px dashed #edf2f7; }

        .btn-update-main { width: 100%; padding: 18px; background: #005086; color: #fff; border: none; border-radius: 10px; font-size: 16px; font-weight: 800; cursor: pointer; transition: 0.3s; text-transform: uppercase; letter-spacing: 1px; }
        .btn-update-main:hover { background: #003d66; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0,80,134,0.2); }

        @media (max-width: 850px) {
            .profile-settings-wrapper { flex-direction: column; padding: 20px; gap: 30px; }
            .profile-left { border-right: none; border-bottom: 1px solid #f0f0f0; padding-right: 0; padding-bottom: 30px; }
            .profile-form-row { grid-template-columns: 1fr; }
        }
    </style>

    <div class="profile-settings-wrapper">
        <!-- CỘT TRÁI -->
        <div class="profile-left">
            <div class="avatar-box">
                <?php echo get_avatar( $user_id, 160 ); ?>
            </div>
            <div class="user-meta-info">
                <h4><?php echo esc_html($current_user->display_name); ?></h4>
                <p><?php echo esc_html($current_user->user_email); ?></p>
            </div>
            <div class="profile-actions">
                <?php 
                // Kiểm tra nếu là bác sĩ thì hiện nút vào Dashboard
                $is_doctor_linked = get_posts(array(
                    'post_type' => 'doctor', 
                    'meta_key' => '_doctor_user_id', 
                    'meta_value' => $user_id,
                    'posts_per_page' => 1
                ));
                if ($is_doctor_linked) : ?>
                    <a href="<?php echo home_url('/dashboard-bac-si/'); ?>" class="btn-profile-sub primary" style="text-decoration:none; text-align:center; display:block; margin-bottom:10px; background:#005086; color:#fff;">VÀO TRANG QUẢN LÝ LỊCH</a>
                <?php endif; ?>
                <button type="button" class="btn-profile-sub">Đổi ảnh đại diện</button>
                <button type="button" class="btn-profile-sub" onclick="clinic_toggle_password()">Đổi mật khẩu</button>
            </div>
        </div>

        <!-- CỘT PHẢI -->
        <div class="profile-right">
            <?php if ( $message ) echo '<div style="background:#f0fff4; color:#276749; padding:15px; border-radius:10px; margin-bottom:20px; font-weight:600; border-left:5px solid #48bb78;">✅ '.$message.'</div>'; ?>
            <?php if ( $error ) echo '<div style="background:#fff5f5; color:#c53030; padding:15px; border-radius:10px; margin-bottom:20px; font-weight:600; border-left:5px solid #f56565;">❌ '.$error.'</div>'; ?>

            <form method="post" novalidate>
                <?php wp_nonce_field( 'update_profile_action', 'profile_nonce' ); ?>
                
                <div class="section-title">Thông tin cơ bản</div>
                <div class="profile-form-row">
                    <div class="profile-input-group">
                        <label>Họ và tên</label>
                        <input type="text" name="display_name" value="<?php echo esc_attr($current_user->display_name); ?>" required>
                    </div>
                    <div class="profile-input-group">
                        <label>Email liên lạc</label>
                        <input type="email" name="user_email" value="<?php echo esc_attr($current_user->user_email); ?>" required>
                    </div>
                </div>

                <div class="profile-form-row">
                    <div class="profile-input-group">
                        <label>Số điện thoại</label>
                        <input type="tel" name="phone_number" value="<?php echo esc_attr($phone); ?>" placeholder="Ví dụ: 0912345678">
                    </div>
                    <div class="profile-input-group">
                        <label>Ngày sinh</label>
                        <input type="date" name="birthday" value="<?php echo esc_attr($birthday); ?>">
                    </div>
                </div>

                <div class="profile-input-group">
                    <label>Địa chỉ hiện tại</label>
                    <input type="text" name="address" value="<?php echo esc_attr($address); ?>" placeholder="Số nhà, tên đường, phường/xã...">
                </div>

                <div class="profile-form-row">
                    <div class="profile-input-group">
                        <label>Giới tính</label>
                        <select name="gender">
                            <option value="Nam" <?php selected($gender, 'Nam'); ?>>Nam</option>
                            <option value="Nữ" <?php selected($gender, 'Nữ'); ?>>Nữ</option>
                            <option value="Khác" <?php selected($gender, 'Khác'); ?>>Khác</option>
                        </select>
                    </div>
                    <div class="profile-input-group">
                        <label>Tỉnh / Thành phố</label>
                        <input type="text" name="province" value="<?php echo esc_attr($province); ?>" placeholder="Ví dụ: Hà Nội">
                    </div>
                </div>

                <div class="profile-input-group">
                    <label>Công ty / Tổ chức</label>
                    <input type="text" name="company" value="<?php echo esc_attr($company); ?>" placeholder="Nơi làm việc (nếu có)">
                </div>

                <div id="password-section">
                    <div class="section-title" style="margin-top: 0;">Đổi mật khẩu mới</div>
                    <div class="profile-form-row">
                        <div class="profile-input-group">
                            <label>Mật khẩu mới</label>
                            <input type="password" name="pass1" placeholder="Nhập mật khẩu mới">
                        </div>
                        <div class="profile-input-group">
                            <label>Xác nhận mật khẩu</label>
                            <input type="password" name="pass2" placeholder="Nhập lại mật khẩu mới">
                        </div>
                    </div>
                </div>

                <button type="submit" name="update_profile_submit" class="btn-update-main">Cập nhật hồ sơ</button>
            </form>
        </div>
    </div>
    <script>
        function clinic_toggle_password() {
            var x = document.getElementById("password-section");
            if (x.style.display === "none" || x.style.display === "") {
                x.style.display = "block";
                x.scrollIntoView({behavior: 'smooth'});
            } else {
                x.style.display = "none";
            }
        }
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode( 'clinic_user_settings', 'clinic_user_settings_shortcode' );

// ==========================================
// DASHBOARD DÀNH RIÊNG CHO BÁC SĨ
// ==========================================

/**
 * Helper: Tìm đường dẫn của trang chứa shortcode chỉ định
 */
function cb_get_shortcode_page_permalink($shortcode) {
    global $wpdb;
    $pages = $wpdb->get_results($wpdb->prepare("
        SELECT ID FROM {$wpdb->posts} 
        WHERE post_type = 'page' 
          AND post_status = 'publish' 
          AND post_content LIKE %s
        LIMIT 1
    ", '%' . $wpdb->esc_like('[' . $shortcode) . '%'));
    
    if (!empty($pages)) {
        return get_permalink($pages[0]->ID);
    }
    return '#';
}

function doctor_dashboard_shortcode() {
    // 1. Kiểm tra đăng nhập
    if (!is_user_logged_in()) {
        $login_page = home_url('/dang-nhap/');
        $redirect = get_permalink();
        echo '<script>window.location.href="' . $login_page . '?redirect_to=' . urlencode($redirect) . '";</script>';
        return '<div style="text-align:center; padding:50px;">Đang chuyển hướng đến trang đăng nhập...</div>';
    }

    $current_user_id = get_current_user_id();

    // 2. Tìm bài viết Bác sĩ liên kết với tài khoản này
    $doctor_posts = get_posts(array(
        'post_type' => 'doctor',
        'meta_query' => array(
            array(
                'key' => '_doctor_user_id',
                'value' => $current_user_id,
            )
        ),
        'posts_per_page' => 1
    ));

    // Nếu không phải là bác sĩ (hoặc chưa được liên kết)
    if (empty($doctor_posts)) {
        return '<div style="max-width: 800px; margin: 50px auto; padding: 40px; background: #ebf8ff; border-radius: 20px; border: 2px dashed #63b3ed; text-align: center; font-family: \'Inter\', sans-serif;">
            <i class="fas fa-user-md" style="font-size: 40px; color: #2b6cb0; margin-bottom: 20px;"></i>
            <h3 style="color: #2b6cb0; margin-top: 0;">Dành cho Bác sĩ</h3>
            <p style="color: #718096;">Tài khoản của bạn chưa được liên kết với hồ sơ Bác sĩ nào trong hệ thống. Vui lòng liên hệ Admin để được hỗ trợ.</p>
        </div>';
    }

    $doctor_id = $doctor_posts[0]->ID;
    $doctor_name = $doctor_posts[0]->post_title;

    $dashboard_page_url = cb_get_shortcode_page_permalink('doctor_dashboard');
    $schedule_page_url = cb_get_shortcode_page_permalink('doctor_schedule_manager');

    // 3. TỰ ĐỘNG CẬP NHẬT ID CHO LỊCH CŨ (Nếu chưa có ID nhưng khớp tên)
    // Việc này giúp các lịch bạn đã đặt trước đó vẫn hiện ra
    global $wpdb;
    $all_my_appointments_raw = $wpdb->get_results($wpdb->prepare("
        SELECT DISTINCT p.* 
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
        WHERE p.post_type = 'appointment'
          AND p.post_status IN ('pending', 'publish', 'private', 'draft', 'completed')
          AND (
            (pm.meta_key = '_doctor_id' AND pm.meta_value = %s)
            OR 
            (pm.meta_key = '_selected_doctor' AND pm.meta_value = %s)
          )
    ", (string)$doctor_id, $doctor_name));

    $all_my_appointments = array();
    if (!empty($all_my_appointments_raw)) {
        foreach ($all_my_appointments_raw as $post_raw) {
            $all_my_appointments[] = new WP_Post($post_raw);
        }
    }

    // Cập nhật ID cho những lịch cũ chưa có ID
    foreach ($all_my_appointments as $app) {
        $existing_id = get_post_meta($app->ID, '_doctor_id', true);
        if (empty($existing_id)) {
            update_post_meta($app->ID, '_doctor_id', (string)$doctor_id);
        }
    }

    // 4. Lấy danh sách lịch hẹn chính thức (sắp xếp theo ngày)
    $appointments_raw = $wpdb->get_results($wpdb->prepare("
        SELECT DISTINCT p.* 
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id
        LEFT JOIN {$wpdb->postmeta} pm2 ON (p.ID = pm2.post_id AND pm2.meta_key = '_booking_date')
        WHERE p.post_type = 'appointment'
          AND p.post_status IN ('pending', 'publish', 'private', 'draft', 'completed')
          AND pm1.meta_key = '_doctor_id'
          AND pm1.meta_value = %s
        ORDER BY pm2.meta_value DESC
    ", (string)$doctor_id));

    $appointments = array();
    $pending_count = 0;
    $confirmed_count = 0;
    $completed_count = 0;
    if (!empty($appointments_raw)) {
        foreach ($appointments_raw as $post_raw) {
            $post_obj = new WP_Post($post_raw);
            $appointments[] = $post_obj;
            if ($post_obj->post_status === 'pending') {
                $pending_count++;
            } elseif ($post_obj->post_status === 'publish') {
                $confirmed_count++;
            } elseif ($post_obj->post_status === 'completed') {
                $completed_count++;
            }
        }
    }

    ob_start();
    ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .doctor-dashboard { font-family: 'Inter', sans-serif; max-width: 1200px; margin: 40px auto; color: #2d3748; }
        .dd-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; background: linear-gradient(135deg, #005086 0%, #2b6cb0 100%); padding: 40px; border-radius: 24px; color: #fff; box-shadow: 0 15px 35px rgba(43,108,176,0.25); }
        .dd-header h2 { margin: 0; font-size: 32px; font-weight: 800; }
        .dd-header p { margin: 8px 0 0; opacity: 0.9; font-size: 16px; }
        
        .dd-nav-menu { display: flex; gap: 15px; margin-bottom: 30px; border-bottom: 2px solid #edf2f7; padding-bottom: 15px; }
        .dd-nav-item { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 12px; font-weight: 700; text-decoration: none !important; font-size: 15px; transition: all 0.2s; color: #718096; background: transparent; }
        .dd-nav-item:hover { color: #2b6cb0; background: #f7fafc; }
        .dd-nav-item.active { color: #2b6cb0; background: #ebf8ff; }
        
        .dd-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 25px; margin-bottom: 40px; }
        .dd-stat-card { background: #fff; padding: 25px; border-radius: 20px; box-shadow: 0 10px 20px rgba(0,0,0,0.03); border: 1px solid #edf2f7; display: flex; align-items: center; gap: 20px; }
        .dd-stat-icon { width: 60px; height: 60px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
        .icon-blue { background: #ebf8ff; color: #2b6cb0; }
        .icon-green { background: #f0fff4; color: #38a169; }
        .icon-yellow { background: #fffaf0; color: #dd6b20; }
        
        .dd-stat-info h3 { margin: 0; font-size: 14px; text-transform: uppercase; color: #718096; letter-spacing: 1px; }
        .dd-stat-info .value { font-size: 28px; font-weight: 800; color: #1a365d; }

        .dd-table-container { background: #fff; border-radius: 24px; overflow: hidden; box-shadow: 0 15px 45px rgba(0,0,0,0.05); border: 1px solid #edf2f7; }
        .dd-table { width: 100%; border-collapse: collapse; text-align: left; }
        .dd-table th { background: #f8fafc; padding: 22px 20px; font-size: 13px; font-weight: 700; text-transform: uppercase; color: #4a5568; border-bottom: 2px solid #edf2f7; letter-spacing: 0.5px; }
        .dd-table td { padding: 22px 20px; border-bottom: 1px solid #f0f4f8; font-size: 15px; }
        .dd-table tr:last-child td { border-bottom: none; }
        .dd-table tr:hover { background: #f7fafc; }
        
        .status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 50px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
        .status-pending { background: #fffaf0; color: #975a16; border: 1px solid #fbd38d; }
        .status-confirmed { background: #f0fff4; color: #276749; border: 1px solid #9ae6b4; }
        .status-completed { background: #ebf8ff; color: #2b6cb0; border: 1px solid #90cdf4; }
        .status-rejected { background: #fff5f5; color: #c53030; border: 1px solid #feb2b2; }
        .status-cancelled { background: #edf2f7; color: #4a5568; border: 1px solid #cbd5e0; }
        
        .dd-tabs { display: flex; gap: 15px; margin-bottom: 25px; border-bottom: 2px solid #edf2f7; padding-bottom: 10px; margin-top: 10px; }
        .dd-tab-btn { background: none; border: none; padding: 10px 20px; font-size: 14px; font-weight: 700; color: #718096; cursor: pointer; position: relative; transition: all 0.2s; border-radius: 8px; }
        .dd-tab-btn:hover { color: #2b6cb0; background: #f7fafc; }
        .dd-tab-btn.active { color: #2b6cb0; background: #ebf8ff; }

        .action-cell { display: flex; gap: 8px; flex-wrap: wrap; }
        .cb-action-btn { border: none; padding: 8px 12px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s ease; line-height: 1.2; }
        .cb-btn-confirm { background: #38a169; color: #fff; }
        .cb-btn-confirm:hover { background: #2f855a; transform: translateY(-1px); }
        .cb-btn-reject { background: #e53e3e; color: #fff; }
        .cb-btn-reject:hover { background: #c53030; transform: translateY(-1px); }
        .cb-btn-complete { background: #3182ce; color: #fff; }
        .cb-btn-complete:hover { background: #2b6cb0; transform: translateY(-1px); }
        .cb-btn-view-notes { background: #4a5568; color: #fff; }
        .cb-btn-view-notes:hover { background: #2d3748; transform: translateY(-1px); }
        .cb-btn-view-reason { background: #dd6b20; color: #fff; }
        .cb-btn-view-reason:hover { background: #c05621; transform: translateY(-1px); }

        /* Modal Styles */
        .cb-modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center; backdrop-filter: blur(4px); transition: all 0.3s ease; }
        .cb-modal-content { background-color: #fff; margin: auto; padding: 30px; border-radius: 20px; width: 90%; max-width: 500px; box-shadow: 0 20px 40px rgba(0,0,0,0.15); border: 1px solid #edf2f7; animation: slideDown 0.3s ease-out; }
        .cb-modal-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #edf2f7; padding-bottom: 15px; margin-bottom: 20px; }
        .cb-modal-header h3 { margin: 0; font-size: 20px; font-weight: 800; color: #1a365d; }
        .cb-modal-close { color: #a0aec0; font-size: 28px; font-weight: bold; cursor: pointer; transition: 0.2s; }
        .cb-modal-close:hover { color: #4a5568; }
        .cb-modal-body { margin-bottom: 25px; line-height: 1.5; color: #4a5568; }
        .cb-modal-body p { margin-top: 0; margin-bottom: 15px; font-size: 14px; }
        .cb-modal-footer { display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #edf2f7; padding-top: 15px; }
        
        @keyframes slideDown {
            from { transform: translateY(-30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .patient-info { display: flex; align-items: center; gap: 15px; }
        .patient-avatar { width: 45px; height: 45px; border-radius: 14px; background: linear-gradient(135deg, #ebf8ff 0%, #bee3f8 100%); color: #2b6cb0; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px; box-shadow: 0 4px 10px rgba(43,108,176,0.1); }
        
        .btn-call { display: inline-flex; align-items: center; gap: 5px; color: #2b6cb0; text-decoration: none; font-weight: 700; transition: 0.2s; }
        .btn-call:hover { color: #2c5282; transform: translateX(3px); }

        @media (max-width: 900px) {
            .dd-header { flex-direction: column; text-align: center; gap: 20px; }
            .dd-table thead { display: none; }
            .dd-table td { display: block; padding: 12px 25px; border: none; text-align: right; position: relative; }
            .dd-table td::before { content: attr(data-label); position: absolute; left: 25px; font-weight: 800; font-size: 11px; text-transform: uppercase; color: #a0aec0; }
            .dd-table tr { display: block; border-bottom: 8px solid #f7fafc; padding: 15px 0; }
            .patient-info { justify-content: flex-end; }
            .action-cell { justify-content: flex-end; }
            .dd-tabs { flex-wrap: wrap; }
        }

    </style>

    <div class="doctor-dashboard">
        <div class="dd-header">
            <div>
                <h2>Bác sĩ: <?php echo esc_html($doctor_name); ?></h2>
                <p><i class="fas fa-check-circle"></i> Tài khoản của bạn đã được xác thực và sẵn sàng nhận lịch.</p>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 14px; opacity: 0.8; font-weight: 600;">NGÀY HÔM NAY</div>
                <div style="font-size: 24px; font-weight: 800;"><?php echo date('d/m/Y'); ?></div>
            </div>
        </div>

        <div class="dd-nav-menu">
            <a href="<?php echo esc_url($dashboard_page_url); ?>" class="dd-nav-item active">
                <i class="fas fa-calendar-check"></i> Lịch Hẹn Bệnh Nhân
            </a>
            <a href="<?php echo esc_url($schedule_page_url); ?>" class="dd-nav-item" <?php if ($schedule_page_url === '#') : ?>onclick="alert('Chưa cấu hình trang Quản lý Lịch. Vui lòng tạo một trang mới trong WordPress Admin và chèn shortcode [doctor_schedule_manager] vào trang đó.'); return false;"<?php endif; ?>>
                <i class="fas fa-user-clock"></i> Cấu hình Lịch & Ngày nghỉ
            </a>
        </div>

        <div class="dd-stats">
            <div class="dd-stat-card">
                <div class="dd-stat-icon icon-yellow">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="dd-stat-info">
                    <h3>Chờ xác nhận</h3>
                    <div class="value"><?php echo $pending_count; ?></div>
                </div>
            </div>
            <div class="dd-stat-card">
                <div class="dd-stat-icon icon-green">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="dd-stat-info">
                    <h3>Đã xác nhận</h3>
                    <div class="value"><?php echo $confirmed_count; ?></div>
                </div>
            </div>
            <div class="dd-stat-card">
                <div class="dd-stat-icon icon-blue">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="dd-stat-info">
                    <h3>Đã khám xong</h3>
                    <div class="value"><?php echo $completed_count; ?></div>
                </div>
            </div>
        </div>

        <!-- Tabs lọc trạng thái -->
        <div class="dd-tabs">
            <button class="dd-tab-btn active" data-status="all">Tất cả (<?php echo count($appointments); ?>)</button>
            <button class="dd-tab-btn" data-status="pending">Chờ xác nhận (<?php echo $pending_count; ?>)</button>
            <button class="dd-tab-btn" data-status="publish">Đã xác nhận (<?php echo $confirmed_count; ?>)</button>
            <button class="dd-tab-btn" data-status="completed">Đã khám xong (<?php echo $completed_count; ?>)</button>
        </div>

        <div class="dd-table-container">
            <?php if (empty($appointments)) : ?>
                <div style="padding: 80px 20px; text-align: center; color: #a0aec0;">
                    <img src="https://cdn-icons-png.flaticon.com/512/1157/1157053.png" style="width: 100px; opacity: 0.2; margin-bottom: 25px;">
                    <p style="font-size: 18px; font-weight: 600;">Chưa có dữ liệu lịch hẹn nào dành cho bạn.</p>
                </div>
            <?php else : ?>
                <table class="dd-table">
                    <thead>
                        <tr>
                            <th>Thông tin bệnh nhân</th>
                            <th>Thời gian hẹn</th>
                            <th>Liên hệ</th>
                            <th>Vấn đề sức khỏe</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $app) : 
                            $patient_name = get_post_meta($app->ID, '_patient_name', true);
                            $patient_phone = get_post_meta($app->ID, '_patient_phone', true);
                            $booking_date = get_post_meta($app->ID, '_booking_date', true);
                            $booking_time = get_post_meta($app->ID, '_booking_time', true);
                            $symptoms = get_post_field('post_content', $app->ID);
                            $status = $app->post_status;
                            
                            $initials = mb_substr($patient_name, 0, 1);
                        ?>
                        <tr data-status="<?php echo esc_attr($status); ?>">
                            <td data-label="Bệnh nhân">
                                <div class="patient-info">
                                    <div class="patient-avatar"><?php echo esc_html($initials); ?></div>
                                    <div>
                                        <strong style="color: #1a365d;"><?php echo esc_html($patient_name); ?></strong><br>
                                        <span style="font-size: 12px; color: #718096;"><?php echo get_post_meta($app->ID, '_patient_gender', true); ?> • <?php echo get_post_meta($app->ID, '_patient_dob', true); ?></span>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Thời gian">
                                <div style="font-weight: 700; color: #2d3748;"><?php echo esc_html($booking_date); ?></div>
                                <div style="color: #2b6cb0; font-size: 13px; font-weight: 700;"><i class="far fa-clock"></i> <?php echo esc_html($booking_time); ?></div>
                            </td>
                            <td data-label="Liên hệ">
                                <a href="tel:<?php echo esc_attr($patient_phone); ?>" class="btn-call">
                                    <i class="fas fa-phone-alt"></i> <?php echo esc_html($patient_phone); ?>
                                </a>
                            </td>
                            <td data-label="Triệu chứng">
                                <div style="max-width: 200px; font-style: italic; color: #4a5568; line-height: 1.4;" title="<?php echo esc_attr($symptoms); ?>">
                                    "<?php echo esc_html(wp_trim_words(str_replace('Triệu chứng: ', '', $symptoms), 15)); ?>"
                                </div>
                            </td>
                            <td data-label="Trạng thái">
                                <?php if ($status === 'pending') : ?>
                                    <span class="status-badge status-pending"><i class="fas fa-hourglass-half"></i> Chờ xác nhận</span>
                                <?php elseif ($status === 'publish') : ?>
                                    <span class="status-badge status-confirmed"><i class="fas fa-check-circle"></i> Đã xác nhận</span>
                                <?php elseif ($status === 'completed') : ?>
                                    <span class="status-badge status-completed"><i class="fas fa-clipboard-check"></i> Đã khám xong</span>
                                <?php elseif ($status === 'private') : ?>
                                    <span class="status-badge status-rejected"><i class="fas fa-times-circle"></i> Đã từ chối</span>
                                <?php elseif ($status === 'draft') : ?>
                                    <span class="status-badge status-cancelled"><i class="fas fa-ban"></i> Đã hủy</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Thao tác">
                                <div class="action-cell">
                                    <?php if ($status === 'pending') : ?>
                                        <button class="cb-action-btn cb-btn-confirm" data-id="<?php echo $app->ID; ?>" title="Xác nhận lịch hẹn">
                                            <i class="fas fa-check"></i> Xác nhận
                                        </button>
                                        <button class="cb-action-btn cb-btn-reject" data-id="<?php echo $app->ID; ?>" title="Từ chối lịch hẹn">
                                            <i class="fas fa-times"></i> Từ chối
                                        </button>
                                    <?php elseif ($status === 'publish') : ?>
                                        <button class="cb-action-btn cb-btn-complete" data-id="<?php echo $app->ID; ?>" title="Đánh dấu đã khám xong">
                                            <i class="fas fa-notes-medical"></i> Hoàn thành
                                        </button>
                                    <?php elseif ($status === 'completed') : ?>
                                        <?php $notes = get_post_meta($app->ID, '_medical_notes', true); ?>
                                        <button class="cb-action-btn cb-btn-view-notes" data-id="<?php echo $app->ID; ?>" data-notes="<?php echo esc_attr($notes); ?>" title="Xem ghi chú y khoa">
                                            <i class="fas fa-eye"></i> Xem ghi chú
                                        </button>
                                    <?php elseif ($status === 'private') : ?>
                                        <?php $reason = get_post_meta($app->ID, '_reject_reason', true); ?>
                                        <button class="cb-action-btn cb-btn-view-reason" data-id="<?php echo $app->ID; ?>" data-reason="<?php echo esc_attr($reason); ?>" title="Xem lý do từ chối">
                                            <i class="fas fa-info-circle"></i> Xem lý do
                                        </button>
                                    <?php else : ?>
                                        <span style="color: #a0aec0;">-</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal: Từ chối lịch hẹn -->
    <div id="cb-modal-reject" class="cb-modal">
        <div class="cb-modal-content">
            <div class="cb-modal-header">
                <h3>Từ chối lịch hẹn</h3>
                <span class="cb-modal-close" data-modal="cb-modal-reject">&times;</span>
            </div>
            <div class="cb-modal-body">
                <p>Vui lòng nhập lý do từ chối lịch hẹn này. Thông báo sẽ được gửi cho bệnh nhân.</p>
                <input type="hidden" id="reject-app-id" value="">
                <textarea id="reject-reason-text" placeholder="Ví dụ: Bác sĩ có ca phẫu thuật đột xuất..." rows="4" style="width: 100%; border: 1px solid #cbd5e0; border-radius: 8px; padding: 12px; font-family: inherit; font-size: 14px; resize: vertical;"></textarea>
            </div>
            <div class="cb-modal-footer">
                <button class="cb-action-btn cb-btn-reject" id="cb-confirm-reject">Xác nhận Từ chối</button>
                <button class="cb-action-btn cb-btn-view-notes" style="background: #edf2f7; color: #4a5568;" data-close="cb-modal-reject">Hủy</button>
            </div>
        </div>
    </div>

    <!-- Modal: Nhập ghi chú y khoa (Đã khám xong) -->
    <div id="cb-modal-complete" class="cb-modal">
        <div class="cb-modal-content">
            <div class="cb-modal-header">
                <h3>Hoàn thành khám bệnh</h3>
                <span class="cb-modal-close" data-modal="cb-modal-complete">&times;</span>
            </div>
            <div class="cb-modal-body">
                <p>Nhập ghi chú y khoa hoặc chỉ định sau khi khám cho bệnh nhân:</p>
                <input type="hidden" id="complete-app-id" value="">
                <textarea id="medical-notes-text" placeholder="Nhập ghi chú y khoa, chẩn đoán, thuốc kê đơn (nếu có)..." rows="6" style="width: 100%; border: 1px solid #cbd5e0; border-radius: 8px; padding: 12px; font-family: inherit; font-size: 14px; resize: vertical;"></textarea>
            </div>
            <div class="cb-modal-footer">
                <button class="cb-action-btn cb-btn-complete" id="cb-confirm-complete">Đã khám xong</button>
                <button class="cb-action-btn cb-btn-view-notes" style="background: #edf2f7; color: #4a5568;" data-close="cb-modal-complete">Hủy</button>
            </div>
        </div>
    </div>

    <!-- Modal: Xem ghi chú y khoa (Readonly) -->
    <div id="cb-modal-view-notes" class="cb-modal">
        <div class="cb-modal-content">
            <div class="cb-modal-header">
                <h3>Ghi chú y khoa</h3>
                <span class="cb-modal-close" data-modal="cb-modal-view-notes">&times;</span>
            </div>
            <div class="cb-modal-body">
                <div id="view-notes-content" style="background: #f7fafc; border: 1px solid #edf2f7; padding: 15px; border-radius: 8px; font-style: italic; white-space: pre-wrap; min-height: 100px;"></div>
            </div>
            <div class="cb-modal-footer">
                <button class="cb-action-btn cb-btn-view-notes" data-close="cb-modal-view-notes">Đóng</button>
            </div>
        </div>
    </div>

    <!-- Modal: Xem lý do từ chối (Readonly) -->
    <div id="cb-modal-view-reason" class="cb-modal">
        <div class="cb-modal-content">
            <div class="cb-modal-header">
                <h3>Lý do từ chối lịch hẹn</h3>
                <span class="cb-modal-close" data-modal="cb-modal-view-reason">&times;</span>
            </div>
            <div class="cb-modal-body">
                <div id="view-reason-content" style="background: #fff5f5; border: 1px solid #feb2b2; color: #c53030; padding: 15px; border-radius: 8px; font-style: italic; white-space: pre-wrap; min-height: 80px;"></div>
            </div>
            <div class="cb-modal-footer">
                <button class="cb-action-btn cb-btn-view-notes" data-close="cb-modal-view-reason">Đóng</button>
            </div>
        </div>
    </div> <!-- Đóng thẻ div modal-view-reason thiếu -->

    <script>
    jQuery(document).ready(function($) {
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';

        // 1. Logic Lọc theo Tabs
        $('.dd-tab-btn').on('click', function() {
            $('.dd-tab-btn').removeClass('active');
            $(this).addClass('active');

            var status = $(this).data('status');
            if (status === 'all') {
                $('.dd-table tbody tr').show();
            } else {
                $('.dd-table tbody tr').hide();
                $('.dd-table tbody tr[data-status="' + status + '"]').show();
            }
        });

        // Helper đóng modal
        function closeModal(modalId) {
            $('#' + modalId).css('display', 'none');
            $('body').css('overflow', 'auto');
        }

        // Helper mở modal
        function openModal(modalId) {
            $('#' + modalId).css('display', 'flex');
            $('body').css('overflow', 'hidden');
        }

        // Đăng ký sự kiện đóng các Modal
        $('.cb-modal-close, [data-close]').on('click', function() {
            var modalId = $(this).data('modal') || $(this).data('close');
            closeModal(modalId);
        });

        // Đóng khi click bên ngoài modal
        $(window).on('click', function(event) {
            if ($(event.target).hasClass('cb-modal')) {
                $(event.target).css('display', 'none');
                $('body').css('overflow', 'auto');
            }
        });

        // 2. Xử lý Xác nhận lịch hẹn (pending -> publish)
        $(document).on('click', '.cb-btn-confirm', function() {
            var $btn = $(this);
            var appId = $btn.data('id');
            
            if (!confirm('Bạn có chắc chắn muốn xác nhận lịch hẹn này?')) return;

            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang duyệt...');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'cb_doctor_confirm_appointment',
                    appointment_id: appId
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        location.reload();
                    } else {
                        alert('Lỗi: ' + response.data.message);
                        $btn.prop('disabled', false).html('<i class="fas fa-check"></i> Xác nhận');
                    }
                },
                error: function() {
                    alert('Đã xảy ra lỗi kết nối.');
                    $btn.prop('disabled', false).html('<i class="fas fa-check"></i> Xác nhận');
                }
            });
        });

        // 3. Xử lý Mở modal Từ chối
        $(document).on('click', '.cb-btn-reject', function() {
            var appId = $(this).data('id');
            $('#reject-app-id').val(appId);
            $('#reject-reason-text').val('');
            openModal('cb-modal-reject');
        });

        // Click Xác nhận Từ chối trong Modal
        $('#cb-confirm-reject').on('click', function() {
            var $btn = $(this);
            var appId = $('#reject-app-id').val();
            var reason = $('#reject-reason-text').val().trim();

            if (!reason) {
                alert('Vui lòng nhập lý do từ chối.');
                return;
            }

            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'cb_doctor_reject_appointment',
                    appointment_id: appId,
                    reject_reason: reason
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        closeModal('cb-modal-reject');
                        location.reload();
                    } else {
                        alert('Lỗi: ' + response.data.message);
                        $btn.prop('disabled', false).text('Xác nhận Từ chối');
                    }
                },
                error: function() {
                    alert('Đã xảy ra lỗi kết nối.');
                    $btn.prop('disabled', false).text('Xác nhận Từ chối');
                }
            });
        });

        // 4. Xử lý Mở modal Đã khám xong
        $(document).on('click', '.cb-btn-complete', function() {
            var appId = $(this).data('id');
            $('#complete-app-id').val(appId);
            $('#medical-notes-text').val('');
            openModal('cb-modal-complete');
        });

        // Click Xác nhận Hoàn thành trong Modal
        $('#cb-confirm-complete').on('click', function() {
            var $btn = $(this);
            var appId = $('#complete-app-id').val();
            var notes = $('#medical-notes-text').val().trim();

            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang lưu...');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'cb_doctor_complete_appointment',
                    appointment_id: appId,
                    medical_notes: notes
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        closeModal('cb-modal-complete');
                        location.reload();
                    } else {
                        alert('Lỗi: ' + response.data.message);
                        $btn.prop('disabled', false).text('Đã khám xong');
                    }
                },
                error: function() {
                    alert('Đã xảy ra lỗi kết nối.');
                    $btn.prop('disabled', false).text('Đã khám xong');
                }
            });
        });

        // 5. Xử lý Xem ghi chú y khoa
        $(document).on('click', '.cb-btn-view-notes', function() {
            var notes = $(this).data('notes') || 'Không có ghi chú nào.';
            $('#view-notes-content').text(notes);
            openModal('cb-modal-view-notes');
        });

        // 6. Xử lý Xem lý do từ chối
        $(document).on('click', '.cb-btn-view-reason', function() {
            var reason = $(this).data('reason') || 'Không có lý do.';
            $('#view-reason-content').text(reason);
            openModal('cb-modal-view-reason');
        });
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('doctor_dashboard', 'doctor_dashboard_shortcode');

/**
 * =========================================================================
 * PHASE 1 - AJAX HANDLERS & HELPERS FOR DOCTOR ACTIONS
 * =========================================================================
 */

/**
 * Kiểm tra xem người dùng hiện tại có quyền thao tác trên lịch hẹn này hay không.
 * Trả về doctor ID nếu hợp lệ, hoặc WP_Error nếu không hợp lệ.
 */
function cb_check_appointment_doctor_permission($appointment_id) {
    if (!is_user_logged_in()) {
        return new WP_Error('not_logged_in', 'Bạn cần đăng nhập để thực hiện thao tác này.');
    }

    $appointment_id = intval($appointment_id);
    if (!$appointment_id || get_post_type($appointment_id) !== 'appointment') {
        return new WP_Error('invalid_appointment', 'Lịch hẹn không hợp lệ.');
    }

    $current_user_id = get_current_user_id();

    // Admin có toàn quyền
    if (current_user_can('administrator')) {
        return get_post_meta($appointment_id, '_doctor_id', true);
    }

    // Lấy thông tin Bác sĩ của user hiện tại
    $doctor_posts = get_posts(array(
        'post_type' => 'doctor',
        'meta_query' => array(
            array(
                'key' => '_doctor_user_id',
                'value' => $current_user_id,
            )
        ),
        'posts_per_page' => 1
    ));

    if (empty($doctor_posts)) {
        return new WP_Error('not_a_doctor', 'Tài khoản của bạn không được liên kết với bác sĩ nào.');
    }

    $doctor_id = $doctor_posts[0]->ID;
    $appointment_doctor_id = get_post_meta($appointment_id, '_doctor_id', true);

    if (intval($appointment_doctor_id) !== intval($doctor_id)) {
        return new WP_Error('unauthorized', 'Bạn không có quyền thao tác trên lịch hẹn của bác sĩ khác.');
    }

    return $doctor_id;
}

/**
 * AJAX: Bác sĩ xác nhận lịch hẹn (pending -> publish)
 */
add_action('wp_ajax_cb_doctor_confirm_appointment', 'cb_ajax_doctor_confirm_appointment');
function cb_ajax_doctor_confirm_appointment() {
    $appointment_id = isset($_POST['appointment_id']) ? intval($_POST['appointment_id']) : 0;
    
    $check = cb_check_appointment_doctor_permission($appointment_id);
    if (is_wp_error($check)) {
        wp_send_json_error(array('message' => $check->get_error_message()));
    }

    $post_status = get_post_status($appointment_id);
    if ($post_status !== 'pending') {
        wp_send_json_error(array('message' => 'Lịch hẹn phải ở trạng thái Chờ xác nhận mới có thể duyệt.'));
    }

    // Cập nhật trạng thái thành publish (Đã xác nhận)
    $update = wp_update_post(array(
        'ID'          => $appointment_id,
        'post_status' => 'publish'
    ));

    if (is_wp_error($update)) {
        wp_send_json_error(array('message' => 'Có lỗi xảy ra khi cập nhật trạng thái.'));
    }

    // Gửi email thông báo xác nhận cho bệnh nhân
    cb_send_appointment_confirmed_email($appointment_id);

    wp_send_json_success(array('message' => 'Xác nhận lịch hẹn thành công.'));
}

/**
 * AJAX: Bác sĩ từ chối lịch hẹn (pending -> private + lý do)
 */
add_action('wp_ajax_cb_doctor_reject_appointment', 'cb_ajax_doctor_reject_appointment');
function cb_ajax_doctor_reject_appointment() {
    $appointment_id = isset($_POST['appointment_id']) ? intval($_POST['appointment_id']) : 0;
    $reject_reason  = isset($_POST['reject_reason']) ? sanitize_text_field($_POST['reject_reason']) : '';

    if (empty($reject_reason)) {
        wp_send_json_error(array('message' => 'Vui lòng cung cấp lý do từ chối.'));
    }
    
    $check = cb_check_appointment_doctor_permission($appointment_id);
    if (is_wp_error($check)) {
        wp_send_json_error(array('message' => $check->get_error_message()));
    }

    $post_status = get_post_status($appointment_id);
    if ($post_status !== 'pending') {
        wp_send_json_error(array('message' => 'Chỉ có thể từ chối lịch hẹn ở trạng thái Chờ xác nhận.'));
    }

    // Cập nhật trạng thái thành private (Từ chối)
    $update = wp_update_post(array(
        'ID'          => $appointment_id,
        'post_status' => 'private'
    ));

    if (is_wp_error($update)) {
        wp_send_json_error(array('message' => 'Có lỗi xảy ra khi cập nhật trạng thái.'));
    }

    // Lưu lý do từ chối
    update_post_meta($appointment_id, '_reject_reason', $reject_reason);

    // Gửi email từ chối cho bệnh nhân kèm lý do
    cb_send_appointment_rejected_email($appointment_id, $reject_reason);

    wp_send_json_success(array('message' => 'Đã từ chối lịch hẹn thành công.'));
}

/**
 * AJAX: Bác sĩ hoàn thành lịch hẹn (publish -> completed + ghi chú y khoa)
 */
add_action('wp_ajax_cb_doctor_complete_appointment', 'cb_ajax_doctor_complete_appointment');
function cb_ajax_doctor_complete_appointment() {
    $appointment_id = isset($_POST['appointment_id']) ? intval($_POST['appointment_id']) : 0;
    $medical_notes  = isset($_POST['medical_notes']) ? sanitize_textarea_field($_POST['medical_notes']) : '';
    
    $check = cb_check_appointment_doctor_permission($appointment_id);
    if (is_wp_error($check)) {
        wp_send_json_error(array('message' => $check->get_error_message()));
    }

    $post_status = get_post_status($appointment_id);
    if ($post_status !== 'publish') {
        wp_send_json_error(array('message' => 'Chỉ có thể đánh giá hoàn thành các lịch hẹn đã xác nhận.'));
    }

    // Cập nhật trạng thái thành completed (Đã khám)
    $update = wp_update_post(array(
        'ID'          => $appointment_id,
        'post_status' => 'completed'
    ));

    if (is_wp_error($update)) {
        wp_send_json_error(array('message' => 'Có lỗi xảy ra khi cập nhật trạng thái.'));
    }

    // Lưu ghi chú y khoa
    update_post_meta($appointment_id, '_medical_notes', $medical_notes);

    wp_send_json_success(array('message' => 'Đã hoàn thành khám và lưu ghi chú thành công.'));
}

/**
 * =========================================================================
 * PHASE 1 - EMAIL NOTIFICATION UTILITIES
 * =========================================================================
 */

/**
 * Hàm chung hỗ trợ gửi email (sử dụng Brevo API nếu có cài đặt, ngược lại dùng wp_mail)
 */
function cb_send_email($to, $subject, $message, $registrant_name = '') {
    $brevo_api_key = get_option('cb_brevo_api_key');
    $brevo_sender_email = get_option('cb_brevo_sender_email');
    if (empty($brevo_sender_email)) {
        $brevo_sender_email = 'no-reply@yourdomain.com';
    }

    if (!empty($brevo_api_key) && $brevo_api_key !== 'ĐIỀN_API_KEY_CỦA_BẠN_VÀO_ĐÂY') {
        $response = wp_remote_post('https://api.brevo.com/v3/smtp/email', array(
            'headers' => array(
                'accept' => 'application/json',
                'api-key' => $brevo_api_key,
                'content-type' => 'application/json'
            ),
            'body' => wp_json_encode(array(
                'sender' => array('name' => 'Phòng Khám', 'email' => $brevo_sender_email),
                'to' => array(array('email' => $to, 'name' => $registrant_name)),
                'subject' => $subject,
                'textContent' => $message 
            )),
            'data_format' => 'body'
        ));

        $response_code = wp_remote_retrieve_response_code($response);
        return ($response_code === 201 || $response_code === 200);
    } else {
        $headers = array('Content-Type: text/plain; charset=UTF-8');
        return wp_mail($to, $subject, $message, $headers);
    }
}

/**
 * Gửi email thông báo cho bệnh nhân khi bác sĩ XÁC NHẬN lịch hẹn
 */
function cb_send_appointment_confirmed_email($appointment_id) {
    $patient_email = get_post_meta($appointment_id, '_patient_email', true);
    if (empty($patient_email)) return false;

    $patient_name  = get_post_meta($appointment_id, '_patient_name', true);
    $booking_date  = get_post_meta($appointment_id, '_booking_date', true);
    $booking_time  = get_post_meta($appointment_id, '_booking_time', true);
    
    // Lấy tên bác sĩ
    $doctor_id = get_post_meta($appointment_id, '_doctor_id', true);
    $doctor_name = $doctor_id ? get_the_title($doctor_id) : get_post_meta($appointment_id, '_selected_doctor', true);

    $subject = 'Lịch hẹn khám của bạn đã được XÁC NHẬN';
    $message = "Chào {$patient_name},\n\n";
    $message .= "Lịch hẹn đặt khám của bạn đã được bác sĩ xác nhận thành công!\n\n";
    $message .= "Thông tin chi tiết lịch khám:\n";
    $message .= "- Bác sĩ khám: {$doctor_name}\n";
    $message .= "- Ngày khám: {$booking_date}\n";
    $message .= "- Khung giờ: {$booking_time}\n\n";
    $message .= "Vui lòng đến phòng khám trước giờ hẹn 15 phút để hoàn tất thủ tục.\n";
    $message .= "Cảm ơn bạn đã tin tưởng hệ thống phòng khám của chúng tôi.\n\n";
    $message .= "Trân trọng,\nHệ thống Phòng khám";

    return cb_send_email($patient_email, $subject, $message, $patient_name);
}

/**
 * Gửi email thông báo cho bệnh nhân khi bác sĩ TỪ CHỐI lịch hẹn
 */
function cb_send_appointment_rejected_email($appointment_id, $reject_reason) {
    $patient_email = get_post_meta($appointment_id, '_patient_email', true);
    if (empty($patient_email)) return false;

    $patient_name  = get_post_meta($appointment_id, '_patient_name', true);
    $booking_date  = get_post_meta($appointment_id, '_booking_date', true);
    $booking_time  = get_post_meta($appointment_id, '_booking_time', true);
    
    // Lấy tên bác sĩ
    $doctor_id = get_post_meta($appointment_id, '_doctor_id', true);
    $doctor_name = $doctor_id ? get_the_title($doctor_id) : get_post_meta($appointment_id, '_selected_doctor', true);

    $subject = 'Thông báo: Lịch hẹn khám của bạn không thể xác nhận';
    $message = "Chào {$patient_name},\n\n";
    $message .= "Chúng tôi rất tiếc khi phải thông báo rằng bác sĩ không thể nhận lịch khám của bạn vào thời gian đã chọn.\n\n";
    $message .= "Thông tin lịch hẹn bị từ chối:\n";
    $message .= "- Bác sĩ khám: {$doctor_name}\n";
    $message .= "- Ngày khám: {$booking_date}\n";
    $message .= "- Khung giờ: {$booking_time}\n";
    $message .= "- Lý do từ chối: {$reject_reason}\n\n";
    $message .= "Bạn có thể truy cập website để đặt lại lịch hẹn vào ngày giờ khác hoặc chọn bác sĩ khác phù hợp.\n";
    $message .= "Mong bạn thông cảm cho sự bất tiện này.\n\n";
    $message .= "Trân trọng,\nHệ thống Phòng khám";

    return cb_send_email($patient_email, $subject, $message, $patient_name);
}

/**
 * =========================================================================
 * PHASE 2 - APPOINTMENT MANAGEMENT HELPERS & AJAX FOR PATIENTS
 * =========================================================================
 */

/**
 * Kiểm tra tính hợp lệ của lịch hẹn trước khi hủy hoặc đổi lịch (Quy tắc 24h & Quyền sở hữu)
 */
function cb_can_modify_appointment($appointment_id) {
    if (!is_user_logged_in()) {
        return new WP_Error('not_logged_in', 'Bạn cần đăng nhập để thực hiện thao tác này.');
    }

    $appointment_id = intval($appointment_id);
    $post = get_post($appointment_id);
    if (!$post || $post->post_type !== 'appointment') {
        return new WP_Error('invalid_appointment', 'Lịch hẹn không tồn tại.');
    }

    $current_user_id = get_current_user_id();

    // Phân quyền: Phải là tác giả của lịch hẹn (hoặc Admin)
    if (intval($post->post_author) !== $current_user_id && !current_user_can('administrator')) {
        return new WP_Error('unauthorized', 'Bạn không có quyền thao tác trên lịch hẹn này.');
    }

    // Kiểm tra trạng thái: chỉ cho phép hủy/đổi nếu trạng thái là pending hoặc publish
    $status = $post->post_status;
    if ($status !== 'pending' && $status !== 'publish') {
        return new WP_Error('invalid_status', 'Chỉ có thể hủy hoặc đổi lịch hẹn chưa diễn ra.');
    }

    // Kiểm tra quy tắc 24 giờ
    $booking_date = get_post_meta($appointment_id, '_booking_date', true);
    $booking_time = get_post_meta($appointment_id, '_booking_time', true);

    if (empty($booking_date)) {
        return new WP_Error('missing_date', 'Không tìm thấy ngày khám để kiểm tra.');
    }

    // Nếu thiếu giờ khám, mặc định lấy 00:00 của ngày đó
    if (empty($booking_time)) {
        $booking_time = '00:00';
    }

    $datetime_str = trim($booking_date) . ' ' . trim($booking_time);
    $booking_datetime = DateTime::createFromFormat('d/m/Y H:i', $datetime_str);

    if (!$booking_datetime) {
        // Fallback thử định dạng ngày d/m/Y
        $booking_datetime = DateTime::createFromFormat('d/m/Y', trim($booking_date));
    }

    if (!$booking_datetime) {
        return new WP_Error('invalid_datetime_format', 'Không thể xác định thời gian lịch hẹn.');
    }

    $booking_timestamp = $booking_datetime->getTimestamp();
    $current_timestamp = current_time('timestamp'); // Sử dụng múi giờ của WordPress

    $time_difference = $booking_timestamp - $current_timestamp;

    // Phải cách thời điểm hiện tại ít nhất 24 giờ (24 * 3600 = 86400 giây)
    if ($time_difference < 86400) {
        return new WP_Error('too_late', 'Bạn chỉ có thể hủy hoặc đổi lịch khám trước giờ hẹn ít nhất 24 tiếng.');
    }

    return true; // Hợp lệ
}

/**
 * AJAX: Bệnh nhân hủy lịch hẹn
 */
add_action('wp_ajax_cb_patient_cancel_appointment', 'cb_ajax_patient_cancel_appointment');
function cb_ajax_patient_cancel_appointment() {
    $appointment_id = isset($_POST['appointment_id']) ? intval($_POST['appointment_id']) : 0;
    
    $check = cb_can_modify_appointment($appointment_id);
    if (is_wp_error($check)) {
        wp_send_json_error(array('message' => $check->get_error_message()));
    }

    // Cập nhật trạng thái thành draft (Đã hủy)
    $update = wp_update_post(array(
        'ID'          => $appointment_id,
        'post_status' => 'draft'
    ));

    if (is_wp_error($update)) {
        wp_send_json_error(array('message' => 'Có lỗi xảy ra khi hủy lịch hẹn.'));
    }

    // Ghi nhận thời gian hủy và người hủy
    update_post_meta($appointment_id, '_cancelled_at', current_time('mysql'));
    update_post_meta($appointment_id, '_cancelled_by', 'patient');

    // Tích hợp thông báo email & webhook
    cb_send_appointment_cancelled_notifications($appointment_id);

    wp_send_json_success(array('message' => 'Hủy lịch hẹn thành công.'));
}

/**
 * AJAX: Bệnh nhân đổi lịch hẹn
 */
add_action('wp_ajax_cb_patient_reschedule_appointment', 'cb_ajax_patient_reschedule_appointment');
function cb_ajax_patient_reschedule_appointment() {
    $appointment_id = isset($_POST['appointment_id']) ? intval($_POST['appointment_id']) : 0;
    $new_date       = isset($_POST['new_date']) ? sanitize_text_field($_POST['new_date']) : '';
    $new_time       = isset($_POST['new_time']) ? sanitize_text_field($_POST['new_time']) : '';

    if (empty($new_date) || empty($new_time)) {
        wp_send_json_error(array('message' => 'Vui lòng chọn ngày và giờ mới.'));
    }
    
    $check = cb_can_modify_appointment($appointment_id);
    if (is_wp_error($check)) {
        wp_send_json_error(array('message' => $check->get_error_message()));
    }

    // Lưu lại thời gian cũ vào meta để ghi nhận lịch sử đổi
    $old_date = get_post_meta($appointment_id, '_booking_date', true);
    $old_time = get_post_meta($appointment_id, '_booking_time', true);

    update_post_meta($appointment_id, '_old_booking_date', $old_date);
    update_post_meta($appointment_id, '_old_booking_time', $old_time);
    update_post_meta($appointment_id, '_rescheduled_at', current_time('mysql'));

    // Cập nhật ngày và giờ khám mới
    update_post_meta($appointment_id, '_booking_date', $new_date);
    update_post_meta($appointment_id, '_booking_time', $new_time);

    // Chuyển trạng thái về pending (Chờ duyệt lại)
    $update = wp_update_post(array(
        'ID'          => $appointment_id,
        'post_status' => 'pending'
    ));

    if (is_wp_error($update)) {
        wp_send_json_error(array('message' => 'Có lỗi xảy ra khi đổi lịch hẹn.'));
    }

    // Gửi email & webhook thông báo
    cb_send_appointment_rescheduled_notifications($appointment_id, $old_date, $old_time);

    wp_send_json_success(array('message' => 'Đổi lịch hẹn thành công! Lịch hẹn của bạn đã được chuyển về trạng thái Chờ duyệt lại.'));
}

/**
 * Helper gửi webhook Discord/Slack thông báo cho Admin
 */
function cb_send_webhook($webhook_title, $embed_title, $fields, $color = 3447003) {
    $webhook_url = get_option('cb_webhook_url');
    if (empty($webhook_url)) return false;

    $webhook_data = array(
        'content' => $webhook_title,
        'embeds' => array(
            array(
                'title'  => $embed_title,
                'color'  => $color,
                'fields' => $fields
            )
        )
    );

    wp_remote_post($webhook_url, array(
        'headers'     => array('Content-Type' => 'application/json'),
        'body'        => wp_json_encode($webhook_data),
        'method'      => 'POST',
        'data_format' => 'body',
        'timeout'     => 15,
        'sslverify'   => false
    ));

    return true;
}

/**
 * Thông báo đổi lịch hẹn (gửi email cho bác sĩ + gọi webhook cho Admin)
 */
function cb_send_appointment_rescheduled_notifications($appointment_id, $old_date, $old_time) {
    $patient_name = get_post_meta($appointment_id, '_patient_name', true);
    $patient_phone = get_post_meta($appointment_id, '_patient_phone', true);
    $new_date = get_post_meta($appointment_id, '_booking_date', true);
    $new_time = get_post_meta($appointment_id, '_booking_time', true);

    $doctor_id = get_post_meta($appointment_id, '_doctor_id', true);
    $doctor_name = $doctor_id ? get_the_title($doctor_id) : get_post_meta($appointment_id, '_selected_doctor', true);
    
    // 1. Gửi email cho Bác sĩ (nếu bác sĩ có email)
    if ($doctor_id) {
        $doctor_user_id = get_post_meta($doctor_id, '_doctor_user_id', true);
        if ($doctor_user_id) {
            $doctor_user = get_userdata($doctor_user_id);
            if ($doctor_user && !empty($doctor_user->user_email)) {
                $subject = "🔔 Thông báo: Bệnh nhân thay đổi thời gian lịch hẹn - {$patient_name}";
                $message = "Kính gửi Bác sĩ {$doctor_name},\n\n";
                $message .= "Bệnh nhân {$patient_name} vừa thay đổi thời gian khám của lịch hẹn (ID: {$appointment_id}).\n\n";
                $message .= "Thông tin thay đổi:\n";
                $message .= "- Thời gian cũ: {$old_time} ngày {$old_date}\n";
                $message .= "- Thời gian mới: {$new_time} ngày {$new_date}\n\n";
                $message .= "Lịch hẹn hiện đang ở trạng thái [Chờ xác nhận lại]. Vui lòng truy cập trang Dashboard Bác sĩ trên website để kiểm tra và duyệt lịch.\n\n";
                $message .= "Trân trọng,\nHệ thống Phòng khám";

                cb_send_email($doctor_user->user_email, $subject, $message, $doctor_name);
            }
        }
    }

    // 2. Gửi Webhook cho Admin
    $fields = array(
        array('name' => 'Mã lịch hẹn', 'value' => '#' . $appointment_id, 'inline' => true),
        array('name' => 'Họ tên bệnh nhân', 'value' => $patient_name, 'inline' => true),
        array('name' => 'Điện thoại', 'value' => $patient_phone, 'inline' => true),
        array('name' => 'Bác sĩ phụ trách', 'value' => $doctor_name, 'inline' => true),
        array('name' => 'Thời gian CŨ', 'value' => $old_time . ' ngày ' . $old_date, 'inline' => false),
        array('name' => 'Thời gian MỚI', 'value' => $new_time . ' ngày ' . $new_date, 'inline' => false),
    );

    cb_send_webhook(
        '🔄 **BỆNH NHÂN YÊU CẦU ĐỔI LỊCH HẸN**',
        'Chi tiết thông tin đổi lịch',
        $fields,
        15105570 // Màu cam (Warning)
    );
}

/**
 * Thông báo hủy lịch hẹn (gửi email cho bác sĩ + gọi webhook cho Admin)
 */
function cb_send_appointment_cancelled_notifications($appointment_id) {
    $patient_name = get_post_meta($appointment_id, '_patient_name', true);
    $patient_phone = get_post_meta($appointment_id, '_patient_phone', true);
    $booking_date = get_post_meta($appointment_id, '_booking_date', true);
    $booking_time = get_post_meta($appointment_id, '_booking_time', true);

    $doctor_id = get_post_meta($appointment_id, '_doctor_id', true);
    $doctor_name = $doctor_id ? get_the_title($doctor_id) : get_post_meta($appointment_id, '_selected_doctor', true);
    
    // 1. Gửi email cho Bác sĩ
    if ($doctor_id) {
        $doctor_user_id = get_post_meta($doctor_id, '_doctor_user_id', true);
        if ($doctor_user_id) {
            $doctor_user = get_userdata($doctor_user_id);
            if ($doctor_user && !empty($doctor_user->user_email)) {
                $subject = "❌ Thông báo: Lịch hẹn khám đã bị HỦY - {$patient_name}";
                $message = "Kính gửi Bác sĩ {$doctor_name},\n\n";
                $message .= "Lịch khám của bệnh nhân {$patient_name} (ID: {$appointment_id}) vào lúc {$booking_time} ngày {$booking_date} đã bị HỦY.\n\n";
                $message .= "Bệnh nhân đã thực hiện yêu cầu hủy lịch này trên hệ thống.\n\n";
                $message .= "Hệ thống tự động giải phóng khung giờ trên để tiếp nhận bệnh nhân khác.\n\n";
                $message .= "Trân trọng,\nHệ thống Phòng khám";

                cb_send_email($doctor_user->user_email, $subject, $message, $doctor_name);
            }
        }
    }

    // 2. Gửi Webhook cho Admin
    $fields = array(
        array('name' => 'Mã lịch hẹn', 'value' => '#' . $appointment_id, 'inline' => true),
        array('name' => 'Họ tên bệnh nhân', 'value' => $patient_name, 'inline' => true),
        array('name' => 'Điện thoại', 'value' => $patient_phone, 'inline' => true),
        array('name' => 'Bác sĩ phụ trách', 'value' => $doctor_name, 'inline' => true),
        array('name' => 'Thời gian khám bị hủy', 'value' => $booking_time . ' ngày ' . $booking_date, 'inline' => false),
    );

    cb_send_webhook(
        '❌ **BỆNH NHÂN HỦY LỊCH HẸN**',
        'Chi tiết thông tin lịch hủy',
        $fields,
        13631488 // Màu đỏ (Danger)
    );
}

/**
 * =========================================================================
 * PHASE 3 - DOCTOR WEEKLY SCHEDULE & DAYS OFF AJAX HANDLERS
 * =========================================================================
 */

add_action('wp_ajax_cb_save_doctor_schedule', 'cb_ajax_save_doctor_schedule');
function cb_ajax_save_doctor_schedule() {
    $doctor_id = isset($_POST['doctor_id']) ? intval($_POST['doctor_id']) : 0;
    if (!$doctor_id) {
        wp_send_json_error(array('message' => 'Bác sĩ không hợp lệ.'));
    }

    $current_user_id = get_current_user_id();
    $doctor_user_id = intval(get_post_meta($doctor_id, '_doctor_user_id', true));
    if ($doctor_user_id !== $current_user_id && !current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Bạn không có quyền thay đổi lịch làm việc của bác sĩ này.'));
    }

    $weekly_schedule = isset($_POST['weekly_schedule']) ? json_decode(wp_unslash($_POST['weekly_schedule']), true) : null;
    $days_off = isset($_POST['days_off']) ? json_decode(wp_unslash($_POST['days_off']), true) : null;

    if (!is_array($weekly_schedule)) {
        wp_send_json_error(array('message' => 'Lịch làm việc tuần không hợp lệ.'));
    }
    if (!is_array($days_off)) {
        wp_send_json_error(array('message' => 'Danh sách ngày nghỉ không hợp lệ.'));
    }

    // Làm sạch dữ liệu
    $sanitized_schedule = array();
    $allowed_days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
    foreach ($weekly_schedule as $day => $data) {
        if (!in_array($day, $allowed_days)) continue;
        $sanitized_schedule[$day] = array(
            'enabled' => isset($data['enabled']) ? filter_var($data['enabled'], FILTER_VALIDATE_BOOLEAN) : false,
            'slots'   => isset($data['slots']) && is_array($data['slots']) ? array_map('sanitize_text_field', $data['slots']) : array()
        );
    }

    $sanitized_days_off = array();
    foreach ($days_off as $date) {
        $date = sanitize_text_field(trim($date));
        if (!empty($date)) {
            $sanitized_days_off[] = $date;
        }
    }

    update_post_meta($doctor_id, '_weekly_schedule', $sanitized_schedule);
    update_post_meta($doctor_id, '_days_off', $sanitized_days_off);

    wp_send_json_success(array('message' => 'Lưu lịch làm việc và ngày nghỉ thành công!'));
}

add_action('wp_ajax_cb_get_doctor_schedule', 'cb_ajax_get_doctor_schedule');
function cb_ajax_get_doctor_schedule() {
    $doctor_id = isset($_POST['doctor_id']) ? intval($_POST['doctor_id']) : 0;
    if (!$doctor_id) {
        wp_send_json_error(array('message' => 'Bác sĩ không hợp lệ.'));
    }

    $current_user_id = get_current_user_id();
    $doctor_user_id = intval(get_post_meta($doctor_id, '_doctor_user_id', true));
    if ($doctor_user_id !== $current_user_id && !current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Bạn không có quyền truy cập lịch làm việc này.'));
    }

    $weekly_schedule = get_post_meta($doctor_id, '_weekly_schedule', true);
    $days_off = get_post_meta($doctor_id, '_days_off', true);

    // Mặc định nếu chưa thiết lập
    if (empty($weekly_schedule)) {
        $weekly_schedule = array();
        $allowed_days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
        $slots_str = get_option('cb_time_slots', "08:00\n08:30\n09:00\n09:30\n10:00\n10:30\n14:00\n14:30\n15:00\n15:30\n16:00");
        $all_slots = array_filter(array_map('trim', explode("\n", $slots_str)));
        
        foreach ($allowed_days as $day) {
            $enabled = !in_array($day, array('saturday', 'sunday'));
            $weekly_schedule[$day] = array(
                'enabled' => $enabled,
                'slots'   => $enabled ? array_values($all_slots) : array()
            );
        }
    }

    if (empty($days_off)) {
        $days_off = array();
    }

    wp_send_json_success(array(
        'weekly_schedule' => $weekly_schedule,
        'days_off'        => $days_off
    ));
}

/**
 * Render interface for Doctor Weekly Schedule & Days Off Manager
 */
function cb_render_doctor_schedule_manager_html($doctor_id) {
    wp_enqueue_style('flatpickr-css', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
    wp_enqueue_script('flatpickr-js', 'https://cdn.jsdelivr.net/npm/flatpickr', array(), null, true);
    
    $slots_str = get_option('cb_time_slots', "08:00\n08:30\n09:00\n09:30\n10:00\n10:30\n14:00\n14:30\n15:00\n15:30\n16:00");
    $all_slots = array_filter(array_map('trim', explode("\n", $slots_str)));
    
    $weekly_schedule = get_post_meta($doctor_id, '_weekly_schedule', true);
    $days_off = get_post_meta($doctor_id, '_days_off', true);
    
    $allowed_days = array(
        'monday'    => 'Thứ Hai',
        'tuesday'   => 'Thứ Ba',
        'wednesday' => 'Thứ Tư',
        'thursday'  => 'Thứ Năm',
        'friday'    => 'Thứ Sáu',
        'saturday'  => 'Thứ Bảy',
        'sunday'    => 'Chủ Nhật'
    );
    
    if (empty($weekly_schedule) || !is_array($weekly_schedule)) {
        $weekly_schedule = array();
        foreach (array_keys($allowed_days) as $day) {
            $enabled = !in_array($day, array('saturday', 'sunday'));
            $weekly_schedule[$day] = array(
                'enabled' => $enabled,
                'slots'   => $enabled ? array_values($all_slots) : array()
            );
        }
    }
    
    if (empty($days_off) || !is_array($days_off)) {
        $days_off = array();
    }
    
    ob_start();
    ?>
    <div class="cb-schedule-manager" data-doctor-id="<?php echo esc_attr($doctor_id); ?>">
        <div style="margin-bottom: 40px; border-bottom: 1px solid #edf2f7; padding-bottom: 20px;">
            <h3 style="margin: 0 0 10px; color: #1a365d; font-size: 24px; font-weight: 800;">Lịch làm việc hàng tuần</h3>
            <p style="margin: 0; color: #718096; font-size: 14px;">Bật/tắt ngày làm việc và tick chọn các khung giờ nhận bệnh nhân của bạn.</p>
        </div>
        
        <div class="cb-weekly-grid">
            <?php foreach ($allowed_days as $day_key => $day_name) : 
                $day_data = isset($weekly_schedule[$day_key]) ? $weekly_schedule[$day_key] : array('enabled' => false, 'slots' => array());
                $is_enabled = isset($day_data['enabled']) ? $day_data['enabled'] : false;
                $active_slots = isset($day_data['slots']) ? $day_data['slots'] : array();
            ?>
                <div class="cb-day-card <?php echo $is_enabled ? 'active' : ''; ?>" data-day="<?php echo esc_attr($day_key); ?>">
                    <div class="cb-day-header">
                        <span class="cb-day-title"><?php echo esc_html($day_name); ?></span>
                        <label class="cb-switch">
                            <input type="checkbox" class="cb-day-toggle" <?php checked($is_enabled); ?>>
                            <span class="cb-slider"></span>
                        </label>
                    </div>
                    
                    <div class="cb-day-body">
                        <div style="font-size: 12px; font-weight: 700; color: #a0aec0; text-transform: uppercase; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center;">
                            <span>Khung giờ khám</span>
                            <span class="cb-select-all-slots" style="color: #3182ce; cursor: pointer; text-transform: none;">Chọn tất cả</span>
                        </div>
                        <div class="cb-slots-grid">
                            <?php foreach ($all_slots as $slot) : 
                                $is_checked = in_array($slot, $active_slots);
                            ?>
                                <label class="cb-slot-checkbox-label <?php echo $is_checked ? 'checked' : ''; ?>">
                                    <input type="checkbox" class="cb-slot-checkbox" value="<?php echo esc_attr($slot); ?>" <?php checked($is_checked); ?> <?php disabled(!$is_enabled); ?>>
                                    <span><?php echo esc_html($slot); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div style="margin: 40px 0; border-bottom: 1px solid #edf2f7; padding-bottom: 20px;">
            <h3 style="margin: 0 0 10px; color: #1a365d; font-size: 24px; font-weight: 800;">Đăng ký ngày nghỉ phép</h3>
            <p style="margin: 0; color: #718096; font-size: 14px;">Chọn các ngày cụ thể bạn muốn nghỉ khám. Hệ thống sẽ tự động chặn không cho đặt lịch vào các ngày này.</p>
        </div>
        
        <div class="cb-daysoff-container">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: start;">
                <div>
                    <label style="display: block; font-weight: 700; color: #4a5568; margin-bottom: 10px; font-size: 14px;">Danh sách ngày nghỉ phép:</label>
                    <div style="position: relative;">
                        <input type="text" id="cb-days-off-picker" value="<?php echo esc_attr(implode(', ', $days_off)); ?>" placeholder="Chọn các ngày nghỉ của bạn..." class="cb-days-off-input" readonly>
                        <i class="fas fa-calendar-alt" style="position: absolute; right: 15px; top: 15px; color: #a0aec0; pointer-events: none;"></i>
                    </div>
                    <p style="font-size: 13px; color: #718096; margin-top: 10px; line-height: 1.5;"><i class="fas fa-info-circle" style="color: #3182ce; margin-right: 5px;"></i> Bạn có thể chọn nhiều ngày nghỉ cùng một lúc trên lịch.</p>
                </div>
                <div class="cb-days-off-badge-list" style="display: flex; gap: 8px; flex-wrap: wrap; padding: 25px; background: #f8fafc; border-radius: 16px; border: 1px solid #edf2f7; min-height: 100px;">
                    <?php if (empty($days_off)) : ?>
                        <span class="cb-no-days-off" style="color: #a0aec0; font-size: 14px; font-style: italic; align-self: center; margin: auto;">Chưa có ngày nghỉ nào được chọn.</span>
                    <?php else : ?>
                        <?php foreach ($days_off as $date) : ?>
                            <span class="cb-date-badge" data-date="<?php echo esc_attr($date); ?>">
                                <?php echo esc_html($date); ?>
                                <span class="cb-remove-date" style="margin-left: 6px; cursor: pointer; color: #e53e3e; font-weight: 800;">&times;</span>
                            </span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 40px; display: flex; justify-content: flex-end; border-top: 1px solid #edf2f7; padding-top: 30px;">
            <button type="button" id="cb-btn-save-schedule" class="cb-save-btn">
                <i class="fas fa-save"></i> Lưu cấu hình lịch làm việc
            </button>
        </div>
    </div>
    
    <style>
        .cb-weekly-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; }
        .cb-day-card { background: #fff; border-radius: 20px; border: 1px solid #edf2f7; box-shadow: 0 4px 15px rgba(0,0,0,0.02); transition: all 0.3s ease; overflow: hidden; }
        .cb-day-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .cb-day-card.active { border-color: #bee3f8; background: #fff; }
        .cb-day-card:not(.active) { opacity: 0.65; background: #f8fafc; }
        
        .cb-day-header { display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 1px solid #edf2f7; background: #fcfcfc; }
        .cb-day-card.active .cb-day-header { background: #ebf8ff; border-bottom-color: #bee3f8; }
        .cb-day-title { font-size: 16px; font-weight: 800; color: #2d3748; }
        .cb-day-card.active .cb-day-title { color: #2b6cb0; }
        
        .cb-day-body { padding: 20px; }
        .cb-slots-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(75px, 1fr)); gap: 10px; margin-top: 10px; }
        
        .cb-slot-checkbox-label { display: flex; align-items: center; justify-content: center; border: 1px solid #cbd5e0; border-radius: 8px; padding: 8px; font-size: 13px; font-weight: 700; color: #4a5568; cursor: pointer; transition: all 0.2s; background: #fff; box-sizing: border-box; }
        .cb-slot-checkbox-label input { display: none; }
        .cb-slot-checkbox-label:hover { border-color: #2b6cb0; color: #2b6cb0; background: #ebf8ff; }
        .cb-slot-checkbox-label.checked { border-color: #3182ce; background: #3182ce; color: #fff; }
        .cb-day-card:not(.active) .cb-slot-checkbox-label { cursor: not-allowed; background: #edf2f7; border-color: #e2e8f0; color: #a0aec0; }
        .cb-day-card:not(.active) .cb-slot-checkbox-label:hover { border-color: #e2e8f0; color: #a0aec0; background: #edf2f7; }
        
        /* iOS-style toggle switch */
        .cb-switch { position: relative; display: inline-block; width: 48px; height: 26px; }
        .cb-switch input { opacity: 0; width: 0; height: 0; }
        .cb-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #cbd5e0; transition: .3s; border-radius: 34px; }
        .cb-slider:before { position: absolute; content: ""; height: 20px; width: 20px; left: 3px; bottom: 3px; background-color: white; transition: .3s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .cb-switch input:checked + .cb-slider { background-color: #3182ce; }
        .cb-switch input:checked + .cb-slider:before { transform: translateX(22px); }
        
        .cb-days-off-input { width: 100%; border: 1px solid #cbd5e0; border-radius: 12px; padding: 15px; font-family: inherit; font-size: 14px; font-weight: 500; cursor: pointer; background: #fff; box-sizing: border-box; }
        .cb-days-off-input:focus { outline: none; border-color: #3182ce; box-shadow: 0 0 0 3px rgba(66,153,225,0.15); }
        
        .cb-date-badge { display: inline-flex; align-items: center; background: #ebf8ff; color: #2b6cb0; font-weight: 700; font-size: 13px; padding: 6px 14px; border-radius: 50px; border: 1px solid #bee3f8; }
        
        .cb-save-btn { background: linear-gradient(135deg, #005086 0%, #2b6cb0 100%); color: #fff; border: none; padding: 15px 30px; border-radius: 12px; font-weight: 700; font-size: 15px; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 14px rgba(43,108,176,0.3); display: inline-flex; align-items: center; gap: 8px; }
        .cb-save-btn:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(43,108,176,0.4); opacity: 0.95; }
        .cb-save-btn:active { transform: translateY(0); }
        .cb-save-btn:disabled { background: #cbd5e0; color: #a0aec0; cursor: not-allowed; box-shadow: none; }
        
        @media (max-width: 768px) {
            .cb-daysoff-container > div { grid-template-columns: 1fr !important; }
        }
    </style>
    
    <script>
        jQuery(document).ready(function($) {
            var ajaxurl = '<?php echo esc_url( admin_url('admin-ajax.php') ); ?>';
            var doctorId = <?php echo intval($doctor_id); ?>;
            
            // Khởi tạo Flatpickr cho Ngày nghỉ (Multi-date)
            var scheduleFlatpickrInterval = setInterval(function() {
                if (typeof flatpickr !== 'undefined') {
                    clearInterval(scheduleFlatpickrInterval);
                    flatpickr("#cb-days-off-picker", {
                        mode: "multiple",
                        dateFormat: "d/m/Y",
                        minDate: "today",
                        disableMobile: "true",
                        onChange: function(selectedDates, dateStr, instance) {
                            updateDateBadges(selectedDates);
                        }
                    });
                }
            }, 100);
            
            // Xử lý bật/tắt Thứ trong tuần
            $(document).on('change', '.cb-day-toggle', function() {
                var $card = $(this).closest('.cb-day-card');
                var isChecked = $(this).is(':checked');
                
                if (isChecked) {
                    $card.addClass('active');
                    $card.find('.cb-slot-checkbox').prop('disabled', false);
                } else {
                    $card.removeClass('active');
                    $card.find('.cb-slot-checkbox').prop('disabled', true);
                }
            });
            
            // Xử lý tick chọn Khung giờ (Thêm visual class checked)
            $(document).on('change', '.cb-slot-checkbox', function() {
                var $label = $(this).closest('.cb-slot-checkbox-label');
                if ($(this).is(':checked')) {
                    $label.addClass('checked');
                } else {
                    $label.removeClass('checked');
                }
            });
            
            // Chọn tất cả khung giờ của ngày đó
            $(document).on('click', '.cb-select-all-slots', function() {
                var $card = $(this).closest('.cb-day-card');
                if (!$card.hasClass('active')) return;
                
                var allChecked = true;
                $card.find('.cb-slot-checkbox').each(function() {
                    if (!$(this).is(':checked')) {
                        allChecked = false;
                        return false;
                    }
                });
                
                $card.find('.cb-slot-checkbox').each(function() {
                    $(this).prop('checked', !allChecked).trigger('change');
                });
            });
            
            // Cập nhật danh sách badges ngày nghỉ
            function updateDateBadges(dates) {
                var $list = $('.cb-days-off-badge-list');
                $list.empty();
                
                if (dates.length === 0) {
                    $list.append('<span class="cb-no-days-off" style="color: #a0aec0; font-size: 14px; font-style: italic; align-self: center; margin: auto;">Chưa có ngày nghỉ nào được chọn.</span>');
                    return;
                }
                
                // Sắp xếp ngày tăng dần
                dates.sort(function(a, b) { return a - b; });
                
                dates.forEach(function(date) {
                    var day = ('0' + date.getDate()).slice(-2);
                    var month = ('0' + (date.getMonth() + 1)).slice(-2);
                    var year = date.getFullYear();
                    var dateStr = day + '/' + month + '/' + year;
                    
                    var badge = $('<span class="cb-date-badge" data-date="' + dateStr + '">' + dateStr + '<span class="cb-remove-date" style="margin-left: 6px; cursor: pointer; color: #e53e3e; font-weight: 800;">&times;</span></span>');
                    $list.append(badge);
                });
            }
            
            // Xóa ngày nghỉ bằng badge close click
            $(document).on('click', '.cb-remove-date', function() {
                var dateToRemove = $(this).parent().data('date');
                var picker = document.querySelector("#cb-days-off-picker")._flatpickr;
                if (!picker) return;
                
                var currentDates = picker.selectedDates;
                var newDates = currentDates.filter(function(date) {
                    var day = ('0' + date.getDate()).slice(-2);
                    var month = ('0' + (date.getMonth() + 1)).slice(-2);
                    var year = date.getFullYear();
                    var dateStr = day + '/' + month + '/' + year;
                    return dateStr !== dateToRemove;
                });
                
                picker.setDate(newDates, true);
            });
            
            // AJAX: Lưu Lịch làm việc & Ngày nghỉ
            $('#cb-btn-save-schedule').on('click', function() {
                var $btn = $(this);
                
                // Thu thập Weekly Schedule
                var weeklySchedule = {};
                $('.cb-day-card').each(function() {
                    var dayKey = $(this).data('day');
                    var enabled = $(this).find('.cb-day-toggle').is(':checked');
                    
                    var slots = [];
                    if (enabled) {
                        $(this).find('.cb-slot-checkbox:checked').each(function() {
                            slots.push($(this).val());
                        });
                    }
                    
                    weeklySchedule[dayKey] = {
                        enabled: enabled,
                        slots: slots
                    };
                });
                
                // Thu thập Days Off
                var daysOff = [];
                var picker = document.querySelector("#cb-days-off-picker")._flatpickr;
                if (picker && picker.selectedDates) {
                    picker.selectedDates.forEach(function(date) {
                        var day = ('0' + date.getDate()).slice(-2);
                        var month = ('0' + (date.getMonth() + 1)).slice(-2);
                        var year = date.getFullYear();
                        daysOff.push(day + '/' + month + '/' + year);
                    });
                }
                
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang lưu...');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'cb_save_doctor_schedule',
                        doctor_id: doctorId,
                        weekly_schedule: JSON.stringify(weeklySchedule),
                        days_off: JSON.stringify(daysOff)
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.data.message);
                        } else {
                            alert('Lỗi: ' + response.data.message);
                        }
                        $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Lưu cấu hình lịch làm việc');
                    },
                    error: function() {
                        alert('Đã xảy ra lỗi kết nối.');
                        $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Lưu cấu hình lịch làm việc');
                    }
                });
            });
        });
    </script>
    <?php
    return ob_get_clean();
}

/**
 * Independent shortcode for Doctor Schedule & Days Off Manager
 */
add_shortcode('doctor_schedule_manager', 'cb_doctor_schedule_manager_shortcode');
function cb_doctor_schedule_manager_shortcode() {
    if (!is_user_logged_in()) {
        return '<div class="clinic-history-container"><p style="text-align:center;">Vui lòng <a href="' . home_url('/dang-nhap/') . '" style="color:#005086; font-weight:700;">đăng nhập</a> để quản lý lịch làm việc.</p></div>';
    }

    $current_user_id = get_current_user_id();
    $doctor_posts = get_posts(array(
        'post_type' => 'doctor',
        'meta_query' => array(
            array(
                'key' => '_doctor_user_id',
                'value' => $current_user_id,
            )
        ),
        'posts_per_page' => 1
    ));

    if (empty($doctor_posts)) {
        return '<div style="max-width: 800px; margin: 50px auto; padding: 40px; background: #ebf8ff; border-radius: 20px; border: 2px dashed #63b3ed; text-align: center; font-family: \'Inter\', sans-serif;">
            <i class="fas fa-user-md" style="font-size: 40px; color: #2b6cb0; margin-bottom: 20px;"></i>
            <h3 style="color: #2b6cb0; margin-top: 0;">Quản lý Lịch làm việc</h3>
            <p style="color: #718096;">Tài khoản của bạn chưa được liên kết với hồ sơ Bác sĩ nào trong hệ thống. Vui lòng liên hệ Admin để được hỗ trợ.</p>
        </div>';
    }

    $doctor_id = $doctor_posts[0]->ID;
    $doctor_name = $doctor_posts[0]->post_title;

    $dashboard_page_url = cb_get_shortcode_page_permalink('doctor_dashboard');
    $schedule_page_url = cb_get_shortcode_page_permalink('doctor_schedule_manager');

    ob_start();
    ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .doctor-dashboard { font-family: 'Inter', sans-serif; max-width: 1200px; margin: 40px auto; color: #2d3748; }
        .dd-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; background: linear-gradient(135deg, #005086 0%, #2b6cb0 100%); padding: 40px; border-radius: 24px; color: #fff; box-shadow: 0 15px 35px rgba(43,108,176,0.25); }
        .dd-header h2 { margin: 0; font-size: 32px; font-weight: 800; }
        .dd-header p { margin: 8px 0 0; opacity: 0.9; font-size: 16px; }
        
        .dd-nav-menu { display: flex; gap: 15px; margin-bottom: 30px; border-bottom: 2px solid #edf2f7; padding-bottom: 15px; }
        .dd-nav-item { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 12px; font-weight: 700; text-decoration: none !important; font-size: 15px; transition: all 0.2s; color: #718096; background: transparent; }
        .dd-nav-item:hover { color: #2b6cb0; background: #f7fafc; }
        .dd-nav-item.active { color: #2b6cb0; background: #ebf8ff; }
        
        .cb-schedule-manager-wrapper { background: #fff; border-radius: 24px; padding: 40px; box-shadow: 0 15px 45px rgba(0,0,0,0.05); border: 1px solid #edf2f7; }
    </style>

    <div class="doctor-dashboard">
        <div class="dd-header">
            <div>
                <h2>Bác sĩ: <?php echo esc_html($doctor_name); ?></h2>
                <p><i class="fas fa-check-circle"></i> Quản lý lịch khám bệnh và cấu hình ngày nghỉ phép.</p>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 14px; opacity: 0.8; font-weight: 600;">NGÀY HÔM NAY</div>
                <div style="font-size: 24px; font-weight: 800;"><?php echo date('d/m/Y'); ?></div>
            </div>
        </div>

        <div class="dd-nav-menu">
            <a href="<?php echo esc_url($dashboard_page_url); ?>" class="dd-nav-item" <?php if ($dashboard_page_url === '#') : ?>onclick="alert('Chưa cấu hình trang Dashboard Bác sĩ. Vui lòng tạo một trang mới trong WordPress Admin và chèn shortcode [doctor_dashboard] vào trang đó.'); return false;"<?php endif; ?>>
                <i class="fas fa-calendar-check"></i> Lịch Hẹn Bệnh Nhân
            </a>
            <a href="<?php echo esc_url($schedule_page_url); ?>" class="dd-nav-item active">
                <i class="fas fa-user-clock"></i> Cấu hình Lịch & Ngày nghỉ
            </a>
        </div>

        <div class="cb-schedule-manager-wrapper">
            <?php echo cb_render_doctor_schedule_manager_html($doctor_id); ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * =========================================================================
 * PHASE 4 - AUTO BLOCK CONFLICTING SLOTS AJAX HANDLERS
 * =========================================================================
 */

add_action('wp_ajax_cb_get_available_slots', 'cb_ajax_get_available_slots');
add_action('wp_ajax_nopriv_cb_get_available_slots', 'cb_ajax_get_available_slots');
function cb_ajax_get_available_slots() {
    $doctor_id    = isset($_POST['doctor_id']) ? intval($_POST['doctor_id']) : 0;
    $booking_date = isset($_POST['booking_date']) ? sanitize_text_field($_POST['booking_date']) : '';

    if (!$doctor_id || empty($booking_date)) {
        wp_send_json_error(array('message' => 'Vui lòng chọn đầy đủ bác sĩ và ngày khám.'));
    }

    // 1. Kiểm tra Ngày nghỉ phép (_days_off)
    $days_off = get_post_meta($doctor_id, '_days_off', true);
    if (is_array($days_off) && in_array($booking_date, $days_off)) {
        wp_send_json_success(array(
            'is_day_off'  => true,
            'is_working'  => false,
            'available'   => array(),
            'unavailable' => array(),
            'message'     => 'Bác sĩ đăng ký nghỉ khám phép vào ngày này.'
        ));
    }

    // 2. Xác định Thứ trong tuần
    $datetime = DateTime::createFromFormat('d/m/Y', $booking_date);
    if (!$datetime) {
        wp_send_json_error(array('message' => 'Định dạng ngày không đúng (d/m/Y).'));
    }

    $weekday = strtolower($datetime->format('l')); // e.g. monday, tuesday...
    
    $vietnamese_days = array(
        'monday'    => 'Thứ Hai',
        'tuesday'   => 'Thứ Ba',
        'wednesday' => 'Thứ Tư',
        'thursday'  => 'Thứ Năm',
        'friday'    => 'Thứ Sáu',
        'saturday'  => 'Thứ Bảy',
        'sunday'    => 'Chủ Nhật'
    );

    // 3. Lấy Lịch làm việc tuần (_weekly_schedule)
    $weekly_schedule = get_post_meta($doctor_id, '_weekly_schedule', true);
    $slots_str = get_option('cb_time_slots', "08:00\n08:30\n09:00\n09:30\n10:00\n10:30\n14:00\n14:30\n15:00\n15:30\n16:00");
    $all_slots = array_filter(array_map('trim', explode("\n", $slots_str)));

    if (empty($weekly_schedule) || !is_array($weekly_schedule)) {
        // Lịch tuần mặc định: Thứ 2 -> Thứ 6 hoạt động, Thứ 7 & CN nghỉ
        $weekly_schedule = array();
        foreach (array_keys($vietnamese_days) as $day) {
            $enabled = !in_array($day, array('saturday', 'sunday'));
            $weekly_schedule[$day] = array(
                'enabled' => $enabled,
                'slots'   => $enabled ? array_values($all_slots) : array()
            );
        }
    }

    $day_config = isset($weekly_schedule[$weekday]) ? $weekly_schedule[$weekday] : array('enabled' => false, 'slots' => array());
    $is_enabled = isset($day_config['enabled']) ? filter_var($day_config['enabled'], FILTER_VALIDATE_BOOLEAN) : false;
    $doctor_slots = isset($day_config['slots']) && is_array($day_config['slots']) ? $day_config['slots'] : array();

    if (!$is_enabled) {
        wp_send_json_success(array(
            'is_day_off'  => false,
            'is_working'  => false,
            'available'   => array(),
            'unavailable' => array(),
            'message'     => 'Bác sĩ không làm việc vào ngày ' . $vietnamese_days[$weekday] . '.'
        ));
    }

    if (empty($doctor_slots)) {
        wp_send_json_success(array(
            'is_day_off'  => false,
            'is_working'  => true,
            'available'   => array(),
            'unavailable' => array(),
            'message'     => 'Bác sĩ không thiết lập khung giờ nhận bệnh nhân trong ngày này.'
        ));
    }

    // 4. Truy vấn các Lịch đã đặt trùng giờ (pending, publish, completed)
    global $wpdb;
    $booked_slots_raw = $wpdb->get_col($wpdb->prepare("
        SELECT DISTINCT pm_time.meta_value 
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm_doc ON p.ID = pm_doc.post_id AND pm_doc.meta_key = '_doctor_id'
        INNER JOIN {$wpdb->postmeta} pm_date ON p.ID = pm_date.post_id AND pm_date.meta_key = '_booking_date'
        INNER JOIN {$wpdb->postmeta} pm_time ON p.ID = pm_time.post_id AND pm_time.meta_key = '_booking_time'
        WHERE p.post_type = 'appointment'
          AND p.post_status IN ('pending', 'publish', 'completed')
          AND pm_doc.meta_value = %s
          AND pm_date.meta_value = %s
    ", (string)$doctor_id, $booking_date));

    $booked_slots = array_map('trim', $booked_slots_raw);

    // 5. Lọc ra danh sách slot khả dụng và không khả dụng
    $available_slots = array();
    $unavailable_slots = array();

    foreach ($doctor_slots as $slot) {
        if (in_array($slot, $booked_slots)) {
            $unavailable_slots[] = $slot;
        } else {
            $available_slots[] = $slot;
        }
    }

    wp_send_json_success(array(
        'is_day_off'  => false,
        'is_working'  => true,
        'available'   => $available_slots,
        'unavailable' => $unavailable_slots
    ));
}

/**
 * =========================================================================
 * PHASE 5 - DOCTOR REVIEWS & RATINGS SYSTEM
 * =========================================================================
 */

/**
 * Helper: Tính toán lại điểm số đánh giá trung bình và tổng số lượt đánh giá của Bác sĩ
 */
function cb_update_doctor_rating_cache($doctor_id) {
    global $wpdb;
    
    // Lấy tất cả các review của bác sĩ này
    $reviews = $wpdb->get_results($wpdb->prepare("
        SELECT pm.post_id, pm2.meta_value as rating 
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->postmeta} pm2 ON pm.post_id = pm2.post_id AND pm2.meta_key = '_rating'
        INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
        WHERE pm.meta_key = '_doctor_id' 
          AND pm.meta_value = %d
          AND p.post_status = 'publish'
    ", $doctor_id));
    
    $total_rating = 0;
    $count = count($reviews);
    
    if ($count > 0) {
        foreach ($reviews as $rev) {
            $total_rating += floatval($rev->rating);
        }
        $avg_rating = round($total_rating / $count, 1);
    } else {
        $avg_rating = 0;
    }
    
    update_post_meta($doctor_id, '_average_rating', $avg_rating);
    update_post_meta($doctor_id, '_review_count', $count);
}

/**
 * AJAX: Gửi đánh giá cho Bác sĩ từ Bệnh nhân
 */
add_action('wp_ajax_cb_submit_doctor_review', 'cb_ajax_submit_doctor_review');
function cb_ajax_submit_doctor_review() {
    $appointment_id = isset($_POST['appointment_id']) ? intval($_POST['appointment_id']) : 0;
    $rating         = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $content        = isset($_POST['review_content']) ? sanitize_textarea_field($_POST['review_content']) : '';

    if (!$appointment_id || $rating < 1 || $rating > 5) {
        wp_send_json_error(array('message' => 'Dữ liệu đánh giá không hợp lệ.'));
    }

    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Bạn cần đăng nhập để gửi đánh giá.'));
    }

    $current_user_id = get_current_user_id();
    $appointment = get_post($appointment_id);

    if (!$appointment || $appointment->post_type !== 'appointment') {
        wp_send_json_error(array('message' => 'Lịch hẹn không tồn tại.'));
    }

    // Kiểm tra quyền sở hữu cuộc hẹn (ngoại trừ admin)
    if (!current_user_can('administrator') && intval($appointment->post_author) !== $current_user_id) {
        wp_send_json_error(array('message' => 'Bạn không có quyền đánh giá lịch hẹn này.'));
    }

    // Phải là completed
    if ($appointment->post_status !== 'completed') {
        wp_send_json_error(array('message' => 'Chỉ có thể đánh giá ca khám đã hoàn thành.'));
    }

    // Kiểm tra xem đã đánh giá chưa
    $already_reviewed = get_post_meta($appointment_id, '_has_review', true);
    if ($already_reviewed) {
        wp_send_json_error(array('message' => 'Lịch khám này đã được bạn đánh giá trước đó.'));
    }

    $doctor_id = get_post_meta($appointment_id, '_doctor_id', true);
    if (!$doctor_id) {
        wp_send_json_error(array('message' => 'Không tìm thấy thông tin bác sĩ liên kết.'));
    }

    $patient_name = get_post_meta($appointment_id, '_patient_name', true);
    if (empty($patient_name)) {
        $patient_name = 'Bệnh nhân ẩn danh';
    }

    // Tạo bài viết review mới
    $review_data = array(
        'post_title'   => 'Đánh giá cho ' . get_the_title($doctor_id) . ' - Lịch #' . $appointment_id,
        'post_content' => $content,
        'post_status'  => 'publish',
        'post_type'    => 'review',
        'post_author'  => $current_user_id,
    );

    $review_id = wp_insert_post($review_data);

    if (is_wp_error($review_id) || !$review_id) {
        wp_send_json_error(array('message' => 'Không thể lưu đánh giá của bạn lúc này.'));
    }

    // Lưu các postmeta liên quan
    update_post_meta($review_id, '_doctor_id', $doctor_id);
    update_post_meta($review_id, '_rating', $rating);
    update_post_meta($review_id, '_appointment_id', $appointment_id);
    update_post_meta($review_id, '_patient_name', $patient_name);

    // Đánh dấu cuộc hẹn đã có review
    update_post_meta($appointment_id, '_has_review', '1');
    update_post_meta($appointment_id, '_review_id', $review_id);

    // Tính toán lại xếp hạng bác sĩ
    cb_update_doctor_rating_cache($doctor_id);

    wp_send_json_success(array(
        'message' => 'Gửi đánh giá thành công! Cảm ơn ý kiến đóng góp của bạn.',
        'rating'  => $rating
    ));
}

/**
 * Shortcode: Hiển thị danh sách Đội ngũ Bác sĩ kèm đánh giá sao uy tín
 * Shortcode: [clinic_doctors_list]
 */
add_shortcode('clinic_doctors_list', 'cb_clinic_doctors_list_shortcode');
function cb_clinic_doctors_list_shortcode() {
    global $wpdb;
    
    $doctors = get_posts(array(
        'post_type'   => 'doctor',
        'numberposts' => -1,
        'post_status' => 'publish',
    ));

    if (empty($doctors)) {
        return '<div style="text-align: center; padding: 40px; color: #718096; font-family: \'Inter\', sans-serif;">Chưa có dữ liệu bác sĩ.</div>';
    }

    // Sắp xếp bác sĩ dựa trên số sao trung bình thực tế hoặc ảo uy tín
    usort($doctors, function($a, $b) {
        $r_a = get_post_meta($a->ID, '_average_rating', true);
        if (empty($r_a)) {
            $mod_a = $a->ID % 3;
            $r_a = ($mod_a == 0) ? 5.0 : (($mod_a == 1) ? 4.9 : 4.8);
        } else {
            $r_a = floatval($r_a);
        }
        
        $r_b = get_post_meta($b->ID, '_average_rating', true);
        if (empty($r_b)) {
            $mod_b = $b->ID % 3;
            $r_b = ($mod_b == 0) ? 5.0 : (($mod_b == 1) ? 4.9 : 4.8);
        } else {
            $r_b = floatval($r_b);
        }
        
        if ($r_a == $r_b) {
            return $b->ID - $a->ID;
        }
        return ($r_b < $r_a) ? -1 : 1;
    });

    ob_start();
    ?>
    <div class="cb-doctors-home-section">
        <h2 class="cb-doctors-title-home">Bác sĩ tư vấn khám bệnh qua video</h2>
        <div class="cb-doctors-grid">
            <?php foreach ($doctors as $doc) : 
                // Link ảnh đại diện
                $img_url = get_post_meta($doc->ID, '_doctor_image_url', true);
                if (empty($img_url)) $img_url = get_the_post_thumbnail_url($doc->ID, 'medium');
                if (empty($img_url)) $img_url = 'https://ui-avatars.com/api/?name='.urlencode($doc->post_title).'&background=ebf8ff&color=2b6cb0&size=200';
                
                // Tách chuyên khoa
                $specialty = '';
                $terms = wp_get_post_terms($doc->ID, 'specialty');
                if (!is_wp_error($terms) && !empty($terms)) {
                    $specialty = $terms[0]->name;
                } else {
                    $specialty = 'Bác sĩ chuyên khoa';
                }

                // Tính số sao trung bình thực tế hoặc ảo uy tín
                $avg_rating = get_post_meta($doc->ID, '_average_rating', true);
                if (empty($avg_rating) || floatval($avg_rating) <= 0) {
                    $mod = $doc->ID % 3;
                    $avg_rating = ($mod == 0) ? '5.0' : (($mod == 1) ? '4.9' : '4.8');
                } else {
                    $avg_rating = number_format(floatval($avg_rating), 1);
                }

                // Tính lượt khám (completed + fake offset)
                $completed_count = $wpdb->get_var($wpdb->prepare("
                    SELECT COUNT(*) FROM {$wpdb->posts} p
                    INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
                    WHERE p.post_type = 'appointment'
                      AND p.post_status = 'completed'
                      AND pm.meta_key = '_doctor_id'
                      AND pm.meta_value = %d
                ", $doc->ID));
                
                $fake_offset = 35 + ($doc->ID % 145);
                $total_visits = intval($completed_count) + $fake_offset;

                // Giá khám & Chức danh
                $price = get_post_meta($doc->ID, '_doctor_price', true);
                if (empty($price)) {
                    $price = '200.000đ';
                }
                
                $title_custom = get_post_meta($doc->ID, '_doctor_title_custom', true);
                if (empty($title_custom)) {
                    $title_custom = 'Bác sĩ Chuyên Khoa';
                }

                // Tách Tiền tố & Tên riêng
                $title = $doc->post_title;
                $prefix = '';
                $name = $title;
                $prefixes = array('BS CKII.', 'BS CKI.', 'BS CkII.', 'BS CkI.', 'BS.', 'ThS. BS.', 'Bác sĩ', 'ThS.BS.', 'TS.BS.', 'PGS.TS.BS.', 'GS.TS.BS.');
                foreach ($prefixes as $p) {
                    if (mb_stripos($title, $p) === 0) {
                        $prefix = $p;
                        $name = trim(mb_substr($title, mb_strlen($p)));
                        break;
                    }
                }
                if (empty($prefix)) {
                    if (preg_match('/^(BS|Bác sĩ)\b/i', $title, $matches)) {
                        $prefix = $matches[0];
                        $name = trim(substr($title, strlen($prefix)));
                    }
                }
            ?>
                <div class="cb-doctor-card-showcase">
                    <!-- 1. Ảnh đại diện bo tròn -->
                    <div class="cb-doc-avatar-container">
                        <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($doc->post_title); ?>" class="cb-doc-avatar">
                    </div>

                    <!-- 2. Thanh trạng thái Đánh giá & Lượt khám -->
                    <div class="cb-doc-status-bar">
                        <div class="cb-status-left">
                            <span class="cb-label">Đánh giá:</span> <span class="cb-val"><?php echo esc_html($avg_rating); ?></span> <i class="fa-solid fa-star cb-star-icon"></i>
                        </div>
                        <div class="cb-status-right">
                            <span class="cb-label">Lượt khám:</span> <span class="cb-val"><?php echo esc_html($total_visits); ?></span> <i class="fa-solid fa-user cb-user-icon"></i>
                        </div>
                    </div>

                    <!-- 3. Chức vụ & Họ tên -->
                    <div class="cb-doc-name-section">
                        <div class="cb-doc-prefix"><?php echo esc_html($prefix ? $prefix : 'Bác sĩ'); ?></div>
                        <h4 class="cb-doc-name"><?php echo esc_html($name); ?></h4>
                    </div>

                    <!-- 4. Chi tiết Chuyên khoa, Giá khám, Chức vụ cụ thể -->
                    <div class="cb-doc-details-list">
                        <div class="cb-doc-detail-item">
                            <i class="fa-solid fa-stethoscope"></i>
                            <span class="cb-detail-txt"><?php echo esc_html($specialty); ?></span>
                        </div>
                        <div class="cb-doc-detail-item">
                            <i class="fa-solid fa-circle-info"></i>
                            <span class="cb-detail-txt"><?php echo esc_html($price); ?></span>
                        </div>
                        <div class="cb-doc-detail-item">
                            <i class="fa-solid fa-hospital"></i>
                            <span class="cb-detail-txt"><?php echo esc_html($title_custom); ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <style>
        .cb-doctors-home-section {
            width: 100%;
            max-width: 1200px;
            margin: 40px auto;
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
            padding: 0 15px;
        }
        .cb-doctors-title-home {
            text-align: center;
            font-size: 28px;
            font-weight: 800;
            color: #0f2d59;
            margin-bottom: 35px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            line-height: 1.3;
        }
        .cb-doctors-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            width: 100%;
        }
        .cb-doctor-card-showcase {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #edf2f7;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.04);
            padding: 24px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
            box-sizing: border-box;
        }
        .cb-doctor-card-showcase:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 35px rgba(15, 45, 89, 0.08);
            border-color: #cbd5e1;
        }
        .cb-doc-avatar-container {
            width: 125px;
            height: 125px;
            border-radius: 50%;
            overflow: hidden;
            margin-bottom: 18px;
            border: 4px solid #f0f7ff;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.06);
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f8fafc;
        }
        .cb-doc-avatar {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .cb-doc-status-bar {
            display: flex;
            justify-content: space-between;
            width: 100%;
            padding: 10px 12px;
            background: #f1f5f9;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 18px;
            box-sizing: border-box;
        }
        .cb-status-left, .cb-status-right {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .cb-status-left .cb-label, .cb-status-right .cb-label {
            color: #64748b;
            font-weight: 500;
        }
        .cb-status-left .cb-val, .cb-status-right .cb-val {
            color: #1e293b;
            font-weight: 700;
        }
        .cb-star-icon {
            color: #ecc94b;
            font-size: 14px;
        }
        .cb-user-icon {
            color: #f97316;
            font-size: 13px;
        }
        .cb-doc-name-section {
            width: 100%;
            text-align: left;
            margin-bottom: 15px;
            padding-left: 4px;
        }
        .cb-doc-prefix {
            font-size: 13px;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .cb-doc-name {
            font-size: 19px;
            font-weight: 800;
            color: #0f2d59;
            margin: 0;
            line-height: 1.3;
        }
        .cb-doc-details-list {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 10px;
            border-top: 1px dashed #e2e8f0;
            padding-top: 15px;
            box-sizing: border-box;
        }
        .cb-doc-detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #334155;
        }
        .cb-doc-detail-item i {
            color: #64748b;
            font-size: 15px;
            width: 18px;
            text-align: center;
        }
        .cb-detail-txt {
            font-weight: 500;
            line-height: 1.4;
        }

        @media (max-width: 1024px) {
            .cb-doctors-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
        }
        @media (max-width: 640px) {
            .cb-doctors-grid {
                grid-template-columns: 1fr;
            }
            .cb-doctors-title-home {
                font-size: 22px;
                margin-bottom: 25px;
            }
        }
    </style>
    <?php
    return ob_get_clean();
}
?>
