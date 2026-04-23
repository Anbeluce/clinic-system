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
        'has_archive' => true,
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
                                        echo '<option value="' . esc_attr($doc->post_title) . '" data-doctor-id="' . esc_attr($doc->ID) . '">' . esc_html($doc->post_title) . '</option>';
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
                        <div class="doctor-card" style="display: <?php echo $display; ?>;">
                            <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($doctor->post_title); ?>" class="doctor-avatar">
                            <div class="doctor-info">
                                <h4><?php echo esc_html($doctor->post_title); ?></h4>
                                <div class="doctor-excerpt" style="color: #718096; font-size: 14px; line-height: 1.5; margin-bottom: 10px;">
                                    <?php 
                                        $excerpt = get_the_excerpt($doctor->ID);
                                        echo wp_kses_post($excerpt ? $excerpt : wp_trim_words($doctor->post_content, 20)); 
                                    ?>
                                </div>
                                <a href="<?php echo get_permalink($doctor->ID); ?>" target="_blank" style="color: #2b6cb0; font-size: 13px; font-weight: 600; text-decoration: none;">Xem chi tiết →</a>
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
            var flatpickrInterval = setInterval(function() {
                if (typeof flatpickr !== 'undefined') {
                    clearInterval(flatpickrInterval);
                    flatpickr("#booking_date", {
                        dateFormat: "d/m/Y",
                        minDate: "today",
                        disableMobile: "true" 
                    });
                    flatpickr("#patient_dob", {
                        dateFormat: "d/m/Y",
                        disableMobile: "true" 
                    });
                }
            }, 100);

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
    wp_nonce_field( 'doctor_save_meta', 'doctor_meta_nonce' );
    echo '<div style="display: flex; flex-direction: column; gap: 10px;">';
    echo '<label for="doctor_image_url"><strong>Link ảnh đại diện (URL trực tiếp):</strong></label>';
    echo '<input type="url" id="doctor_image_url" name="doctor_image_url" placeholder="Ví dụ: https://i.imgur.com/anh-bac-si.jpg" value="' . esc_attr( $doctor_image_url ) . '" style="width: 100%;">';
    echo '<p style="color: #666; font-size: 13px;">Dán trực tiếp link ảnh vào đây để không phải upload lên thư viện ảnh nặng máy.</p>';
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
        }
        .cb-service-price {
            font-weight: 700;
            color: #2d3748;
            font-size: 16px;
        }
        .cb-service-price span {
            font-size: 12px;
            color: #a0aec0;
            font-weight: 500;
            display: block;
        }
        .cb-btn-book-service {
            background: #f7fafc;
            color: #2b6cb0;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            transition: 0.3s;
        }
        .cb-service-card:hover .cb-btn-book-service {
            background: #2b6cb0;
            color: #fff;
        }
    </style>
    
    <div class="cb-services-grid">
        <?php 
        if (!empty($specialties) && !is_wp_error($specialties)) :
            foreach ($specialties as $spec) :
                $icon_class = 'fa-user-md'; // Default
                foreach($icon_map as $key => $icon) {
                    if (stripos($spec->name, $key) !== false) {
                        $icon_class = $icon;
                        break;
                    }
                }
                
                // Giả lập giá và mô tả (có thể lấy từ term meta nếu bạn đã cài)
                $price = '200.000đ';
                if (stripos($spec->name, 'Ngoại') !== false) $price = '250.000đ';
                
                $desc = $spec->description;
                if (empty($desc)) {
                    $desc = 'Dịch vụ khám và điều trị chuyên sâu chuyên khoa ' . $spec->name . ' với đội ngũ bác sĩ hàng đầu.';
                }
        ?>
            <div class="cb-service-card">
                <div class="cb-service-icon">
                    <i class="fa-solid <?php echo $icon_class; ?>"></i>
                </div>
                <h3><?php echo esc_html($spec->name); ?></h3>
                <div class="cb-service-desc">
                    <?php echo wp_trim_words($desc, 25); ?>
                </div>
                <div class="cb-service-footer">
                    <div class="cb-service-price">
                        <span>Phí khám từ:</span>
                        <?php echo $price; ?>
                    </div>
                    <a href="<?php echo home_url('/dat-lich/'); ?>?auto_specialty=<?php echo $spec->term_id; ?>" class="cb-btn-book-service">Đặt lịch ngay</a>
                </div>
            </div>
        <?php 
            endforeach;
        else:
            echo '<p>Chưa có dữ liệu dịch vụ chuyên khoa.</p>';
        endif; 
        ?>
    </div>
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

            // Ngăn chặn hộp thoại "Resubmit form" khi reload trang
            echo '<script>
                if ( window.history.replaceState ) {
                    window.history.replaceState( null, null, window.location.href );
                }
            </script>';
        } else {
            echo '<script>window.location.href="' . home_url() . '";</script>';
            exit;
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
                    <a href="' . wp_lostpassword_url() . '" style="color: #718096; font-weight: 500; font-size: 14px;">Quên mật khẩu?</a>
                    <div style="margin-top: 15px;">
                        Chưa có tài khoản? <a href="' . home_url('/dang-ky/') . '">Đăng ký ngay</a>
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

    $current_user_id = get_current_user_id();
    $args = array(
        'post_type'      => 'appointment',
        'post_status'    => array('pending', 'publish', 'draft', 'private'),
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
                            } elseif ($status == 'draft' || $status == 'private') {
                                $status_label = 'Đã hủy';
                                $status_class = 'status-cancelled';
                            }
                            
                            $booking_date = get_post_meta($post_id, '_booking_date', true);
                            $booking_time = get_post_meta($post_id, '_booking_time', true);
                            $doctor = get_post_meta($post_id, '_selected_doctor', true);
                            $specialty = get_post_meta($post_id, '_specialty', true);
                            $p_name = get_post_meta($post_id, '_patient_name', true);
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
                            </tr>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </tbody>
                </table>
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
        .status-cancelled { background: #fed7d7; color: #822727; }
        @media (max-width: 600px) { .clinic-history-container { padding: 20px; } }
    </style>
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

    // 3. TỰ ĐỘNG CẬP NHẬT ID CHO LỊCH CŨ (Nếu chưa có ID nhưng khớp tên)
    // Việc này giúp các lịch bạn đã đặt trước đó vẫn hiện ra
    $all_my_appointments = get_posts(array(
        'post_type' => 'appointment',
        'posts_per_page' => -1,
        'post_status' => array('pending', 'publish', 'private', 'draft'),
        'meta_query' => array(
            'relation' => 'OR',
            // Trường hợp 1: Đã có ID chính xác
            array(
                'key' => '_doctor_id',
                'value' => (string)$doctor_id,
            ),
            // Trường hợp 2: Chưa có ID nhưng tên bác sĩ khớp (dành cho lịch cũ)
            array(
                'key' => '_selected_doctor',
                'value' => $doctor_name,
            )
        )
    ));

    // Cập nhật ID cho những lịch cũ chưa có ID
    foreach ($all_my_appointments as $app) {
        $existing_id = get_post_meta($app->ID, '_doctor_id', true);
        if (empty($existing_id)) {
            update_post_meta($app->ID, '_doctor_id', (string)$doctor_id);
        }
    }

    // 4. Lấy danh sách lịch hẹn chính thức (sắp xếp theo ngày)
    $appointments = get_posts(array(
        'post_type' => 'appointment',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => '_doctor_id',
                'value' => (string)$doctor_id,
            )
        ),
        'post_status' => array('pending', 'publish', 'private', 'draft'),
        'orderby' => 'meta_value',
        'meta_key' => '_booking_date',
        'order' => 'DESC'
    ));

    ob_start();
    ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .doctor-dashboard { font-family: 'Inter', sans-serif; max-width: 1200px; margin: 40px auto; color: #2d3748; }
        .dd-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; background: linear-gradient(135deg, #005086 0%, #2b6cb0 100%); padding: 40px; border-radius: 24px; color: #fff; box-shadow: 0 15px 35px rgba(43,108,176,0.25); }
        .dd-header h2 { margin: 0; font-size: 32px; font-weight: 800; }
        .dd-header p { margin: 8px 0 0; opacity: 0.9; font-size: 16px; }
        
        .dd-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 25px; margin-bottom: 40px; }
        .dd-stat-card { background: #fff; padding: 25px; border-radius: 20px; box-shadow: 0 10px 20px rgba(0,0,0,0.03); border: 1px solid #edf2f7; display: flex; align-items: center; gap: 20px; }
        .dd-stat-icon { width: 60px; height: 60px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
        .icon-blue { background: #ebf8ff; color: #2b6cb0; }
        .icon-green { background: #f0fff4; color: #38a169; }
        
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

        <div class="dd-stats">
            <div class="dd-stat-card">
                <div class="dd-stat-icon icon-blue">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="dd-stat-info">
                    <h3>Tổng số ca khám</h3>
                    <div class="value"><?php echo count($appointments); ?></div>
                </div>
            </div>
            <div class="dd-stat-card">
                <div class="dd-stat-icon icon-green">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div class="dd-stat-info">
                    <h3>Lịch khám mới nhất</h3>
                    <div class="value">
                        <?php 
                            $latest_count = 0;
                            foreach($appointments as $app) {
                                if ($app->post_status === 'pending') $latest_count++;
                            }
                            echo $latest_count;
                        ?>
                    </div>
                </div>
            </div>
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
                        <tr>
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
                                <div style="max-width: 250px; font-style: italic; color: #4a5568; line-height: 1.4;" title="<?php echo esc_attr($symptoms); ?>">
                                    "<?php echo esc_html(wp_trim_words(str_replace('Triệu chứng: ', '', $symptoms), 15)); ?>"
                                </div>
                            </td>
                            <td data-label="Trạng thái">
                                <?php if ($status === 'pending') : ?>
                                    <span class="status-badge status-pending"><i class="fas fa-hourglass-half"></i> Chờ khám</span>
                                <?php else : ?>
                                    <span class="status-badge status-confirmed"><i class="fas fa-check-double"></i> Đã khám</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('doctor_dashboard', 'doctor_dashboard_shortcode');
?>
