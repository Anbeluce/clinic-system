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
    ob_start(); // Bắt đầu lưu bộ đệm đầu ra

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
        );

        // Chèn dữ liệu vào bảng wp_posts
        $post_id = wp_insert_post( $appointment_data );

        if ( $post_id ) {
            // Lưu các thông tin phụ vào Custom Fields
            update_post_meta( $post_id, '_clinic', $clinic );
            update_post_meta( $post_id, '_specialty', $specialty );
            update_post_meta( $post_id, '_selected_doctor', $selected_doctor );
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
            $message .= "- Họ tên người khám: " . $patient_name . " (" . $patient_gender . ", sinh ngày: " . $patient_dob . ")\n";
            $message .= "- Số điện thoại liên hệ: " . $patient_phone . "\n";
            $message .= "- Triệu chứng/Ghi chú: " . $symptoms . "\n\n";
            $message .= "Vui lòng giữ điện thoại, bộ phận Lễ tân của chúng tôi sẽ sớm liên hệ lại để chốt giờ khám chính xác cho bạn.\n\n";
            $message .= "Trân trọng,\nHệ thống Phòng khám";

            // Xây dựng nội dung (text) chung để gửi cho Webhook
            $admin_message = "Hệ thống vừa nhận được một đăng ký lịch khám mới:\n\n";
            $admin_message .= "- Người đăng ký: " . $registrant_name . "\n";
            $admin_message .= "- Điện thoại: " . $patient_phone . "\n";
            $admin_message .= "- Email: " . $patient_email . "\n";
            $admin_message .= "- Người khám: " . $patient_name . " (" . $patient_gender . ", sinh ngày: " . $patient_dob . ")\n";
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
                                array('name' => 'Người khám', 'value' => $patient_name . ' (' . $patient_gender . ')', 'inline' => true),
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
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
    wp_enqueue_style('flatpickr-css', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
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
                                        echo '<option value="' . esc_attr($doc->post_title) . '">' . esc_html($doc->post_title) . '</option>';
                                    }
                                }
                                ?>
                            </select>
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
                                    doctorSelect.appendChild(opt);
                                });
                                // 2. Cập nhật Danh sách Card bên phải
                                if(doctorsDisplay) doctorsDisplay.innerHTML = res.data.html;
                                initPagination(); // Gán lại sự kiện phân trang
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

            // Tự động chọn Bác sĩ từ URL (?auto_doctor=ID)
            function autoSelectDoctorFromURL() {
                var urlParams = new URLSearchParams(window.location.search);
                var autoDoctorId = urlParams.get('auto_doctor');
                
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
                                            break;
                                        }
                                    }
                                    // Cuộn xuống form đặt lịch
                                    var formEl = document.getElementById('clinic-booking-form');
                                    if(formEl) formEl.scrollIntoView({ behavior: 'smooth' });
                                });
                            }
                        }
                    };
                    xhr.send('action=cb_get_doctor_info&doctor_id=' + autoDoctorId);
                }
            }
            autoSelectDoctorFromURL();

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

    // Meta box cho bác sĩ
    add_meta_box(
        'doctor_details',
        'Ảnh Đại Diện (Bằng Link URL)',
        'doctor_meta_box_html',
        'doctor',
        'normal',
        'high'
    );
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
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    if ( isset( $_POST['doctor_image_url'] ) ) {
        update_post_meta( $post_id, '_doctor_image_url', sanitize_url( $_POST['doctor_image_url'] ) );
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
                        <textarea name="cb_bulk_doctors_data" rows="15" style="width: 100%; max-width: 800px; font-family: monospace;" placeholder="Tên Bác Sĩ | Khoa | Chi Nhánh | Link Ảnh | Giới thiệu ngắn | Chi tiết thành tựu&#10;Nguyễn Văn A | Nội khoa | Hà Nội | https://link.jpg | Bác sĩ giỏi[n]10 năm kinh nghiệm | Tốt nghiệp ĐH Y[n]Công tác tại viện 108"></textarea>
                        <p class="description"> 
                            - Định dạng: <strong>Tên | Khoa | Chi nhánh | Ảnh | Ngắn | Chi tiết</strong> (Mỗi người 1 dòng).<br>
                            - Để <strong>xuống dòng</strong> trong nội dung, hãy sử dụng ký hiệu <code>[n]</code>.<br>
                            - Ví dụ: <code>Tốt nghiệp ĐH Y [n] Công tác tại viện 108</code> sẽ hiển thị thành 2 dòng.
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
?>