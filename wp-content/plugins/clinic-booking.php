<?php
/*
Plugin Name: Hệ thống Đặt lịch Khám
Description: Plugin hỗ trợ đặt lịch khám bệnh và nhắc hẹn tự động qua Email/SMS.
Version: 1.0
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
        'supports' => array( 'title', 'editor', 'thumbnail' ), // Hỗ trợ ảnh đại diện
        'menu_icon' => 'dashicons-groups',
    );
    register_post_type( 'doctor', $args );
    add_theme_support( 'post-thumbnails' ); // Kích hoạt tính năng ảnh đại diện
}
add_action( 'init', 'create_doctor_post_type' );

// Tạo Shortcode hiển thị Form đặt lịch
function clinic_booking_form_shortcode() {
    ob_start(); // Bắt đầu lưu bộ đệm đầu ra

    // Xử lý dữ liệu khi người dùng bấm nút "Xác nhận Đặt lịch"
    if ( isset( $_POST['submit_booking'] ) ) {
        // Làm sạch dữ liệu đầu vào để bảo mật
        $patient_name  = sanitize_text_field( $_POST['patient_name'] );
        $patient_phone = sanitize_text_field( $_POST['patient_phone'] );
        $patient_email = sanitize_email( $_POST['patient_email'] );
        $booking_date  = sanitize_text_field( $_POST['booking_date'] );
        $symptoms      = sanitize_textarea_field( $_POST['symptoms'] );
        $selected_doctor = isset($_POST['selected_doctor']) ? sanitize_text_field($_POST['selected_doctor']) : 'Không chỉ định';

        // Cấu trúc mảng dữ liệu để tạo một "Cuộc hẹn" mới trong Database
        $appointment_data = array(
            'post_title'   => 'Lịch khám: ' . $patient_name . ' - ' . $booking_date,
            'post_content' => 'Triệu chứng: ' . $symptoms,
            'post_status'  => 'pending', // Trạng thái chờ xác nhận
            'post_type'    => 'appointment', // Đúng với Custom Post Type đã tạo
        );

        // Chèn dữ liệu vào bảng wp_posts
        $post_id = wp_insert_post( $appointment_data );

        if ( $post_id ) {
            // Lưu các thông tin phụ vào Custom Fields
            update_post_meta( $post_id, '_patient_phone', $patient_phone );
            update_post_meta( $post_id, '_patient_email', $patient_email );
            update_post_meta( $post_id, '_booking_date', $booking_date );
            update_post_meta( $post_id, '_selected_doctor', $selected_doctor );
            
            // BẮT ĐẦU PHẦN GỬI EMAIL TỰ ĐỘNG
            $to = $patient_email; // Gửi đến email khách hàng vừa nhập
            
            // Lấy email admin từ Cài đặt, nếu chưa cài thì lấy email mặc định của web
            $admin_email = get_option('cb_admin_email');
            if (empty($admin_email)) {
                $admin_email = get_option('admin_email');
            }
            
            $subject = 'Xác nhận đặt lịch khám thành công';
            $admin_subject = '🎉 CÓ LỊCH KHÁM MỚI TỪ: ' . $patient_name;
            
            // Xây dựng nội dung email gửi cho KHÁCH HÀNG
            $message = "Chào " . $patient_name . ",\n\n";
            $message .= "Cảm ơn bạn đã đặt lịch khám. Hệ thống đã ghi nhận thông tin của bạn chi tiết như sau:\n\n";
            $message .= "- Ngày khám mong muốn: " . $booking_date . "\n";
            $message .= "- Bác sĩ yêu cầu: " . $selected_doctor . "\n";
            $message .= "- Số điện thoại liên hệ: " . $patient_phone . "\n";
            $message .= "- Triệu chứng/Ghi chú: " . $symptoms . "\n\n";
            $message .= "Vui lòng giữ điện thoại, bộ phận Lễ tân của chúng tôi sẽ sớm liên hệ lại để chốt giờ khám chính xác cho bạn.\n\n";
            $message .= "Trân trọng,\nHệ thống Phòng khám";

            // Xây dựng nội dung email gửi cho ADMIN
            $admin_message = "Hệ thống vừa nhận được một đăng ký lịch khám mới:\n\n";
            $admin_message .= "- Họ và tên: " . $patient_name . "\n";
            $admin_message .= "- Số điện thoại: " . $patient_phone . "\n";
            $admin_message .= "- Email khách: " . $patient_email . "\n";
            $admin_message .= "- Ngày muốn khám: " . $booking_date . "\n";
            $admin_message .= "- Bác sĩ yêu cầu: " . $selected_doctor . "\n";
            $admin_message .= "- Triệu chứng/Ghi chú: " . $symptoms . "\n\n";
            $admin_message .= "Vui lòng đăng nhập vào quản trị website để xem chi tiết hoặc gọi ngay cho khách.";

            // --- TÍCH HỢP BREVO API ---
            $brevo_api_key = get_option('cb_brevo_api_key');
            $brevo_sender_email = get_option('cb_brevo_sender_email');
            if (empty($brevo_sender_email)) {
                $brevo_sender_email = 'no-reply@yourdomain.com';
            }

            // Nếu người dùng có điền API Key trong phần Cài đặt
            if (!empty($brevo_api_key) && $brevo_api_key !== 'ĐIỀN_API_KEY_CỦA_BẠN_VÀO_ĐÂY') {
                // 1. Gửi email cho Khách hàng
                $response = wp_remote_post('https://api.brevo.com/v3/smtp/email', array(
                    'headers' => array(
                        'accept' => 'application/json',
                        'api-key' => $brevo_api_key,
                        'content-type' => 'application/json'
                    ),
                    'body' => wp_json_encode(array(
                        'sender' => array('name' => 'Phòng Khám', 'email' => $brevo_sender_email),
                        'to' => array(array('email' => $to, 'name' => $patient_name)),
                        'subject' => $subject,
                        'textContent' => $message 
                    )),
                    'data_format' => 'body'
                ));
                
                // 2. Gửi email thông báo cho Admin
                wp_remote_post('https://api.brevo.com/v3/smtp/email', array(
                    'headers' => array(
                        'accept' => 'application/json',
                        'api-key' => $brevo_api_key,
                        'content-type' => 'application/json'
                    ),
                    'body' => wp_json_encode(array(
                        'sender' => array('name' => 'Hệ thống Đặt Lịch', 'email' => $brevo_sender_email),
                        'to' => array(array('email' => $admin_email, 'name' => 'Admin')),
                        'subject' => $admin_subject,
                        'textContent' => $admin_message 
                    )),
                    'data_format' => 'body'
                ));

                $response_code = wp_remote_retrieve_response_code($response);
                $mail_sent = ($response_code === 201 || $response_code === 200);
            } else {
                // Nếu chưa điền API Key, fallback về wp_mail mặc định
                $headers = array('Content-Type: text/plain; charset=UTF-8');
                
                // Gửi cho khách
                $mail_sent = wp_mail( $to, $subject, $message, $headers );
                // Gửi cho Admin
                wp_mail( $admin_email, $admin_subject, $admin_message, $headers );
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
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            border: 1px solid #f0f0f0;
        }
        .clinic-premium-form h3 {
            text-align: center;
            color: #1a365d;
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 30px;
            margin-top: 0;
        }
        .cbf-group {
            margin-bottom: 22px;
        }
        .cbf-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #4a5568;
            font-size: 14px;
        }
        .cbf-group input, 
        .cbf-group textarea,
        .cbf-group select {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            box-sizing: border-box;
            font-size: 15px;
            color: #2d3748;
            background: #f8fafc;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
            appearance: none;
            -webkit-appearance: none;
        }
        .cbf-group input:focus, 
        .cbf-group textarea:focus,
        .cbf-group select:focus {
            border-color: #3182ce;
            background: #ffffff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.15);
        }
        .cbf-submit {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #3182ce 0%, #2b6cb0 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(49, 130, 206, 0.3);
            margin-top: 10px;
        }
        .cbf-submit:hover {
            background: linear-gradient(135deg, #2b6cb0 0%, #2c5282 100%);
            box-shadow: 0 6px 15px rgba(49, 130, 206, 0.4);
            transform: translateY(-2px);
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
            <h3>✨ Đặt Lịch Khám Bệnh</h3>
            <form method="post" action="">
                <div class="cbf-group">
                    <label for="patient_name">Họ và tên (*)</label>
                    <input type="text" name="patient_name" id="patient_name" placeholder="Ví dụ: Nguyễn Văn A" required>
                </div>
                
                <div class="cbf-group">
                    <label for="patient_phone">Số điện thoại (*)</label>
                    <input type="tel" name="patient_phone" id="patient_phone" placeholder="Nhập số điện thoại của bạn" required>
                </div>

                <div class="cbf-group">
                    <label for="patient_email">Email (Để nhận thông báo)</label>
                    <input type="email" name="patient_email" id="patient_email" placeholder="email@example.com" required>
                </div>

                <div class="cbf-group">
                    <label for="booking_date">Ngày khám mong muốn (*)</label>
                    <input type="text" name="booking_date" id="booking_date" placeholder="Nhấp để chọn ngày khám" required autocomplete="off">
                </div>

                <div class="cbf-group">
                    <label for="selected_doctor">Bác sĩ yêu cầu (Không bắt buộc)</label>
                    <select name="selected_doctor" id="selected_doctor" style="background-image: url('data:image/svg+xml;utf8,<svg fill=%22%234a5568%22 height=%2224%22 viewBox=%220 0 24 24%22 width=%2224%22 xmlns=%22http://www.w3.org/2000/svg%22><path d=%22M7 10l5 5 5-5z%22/><path d=%22M0 0h24v24H0z%22 fill=%22none%22/></svg>'); background-repeat: no-repeat; background-position-x: 98%; background-position-y: center;">
                        <option value="Không chỉ định">-- Chọn Bác sĩ (Để trống nếu không rõ) --</option>
                        <?php
                        if ($doctors_list) {
                            foreach ($doctors_list as $doc) {
                                echo '<option value="' . esc_attr($doc->post_title) . '">' . esc_html($doc->post_title) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="cbf-group">
                    <label for="symptoms">Mô tả triệu chứng/Ghi chú</label>
                    <textarea name="symptoms" id="symptoms" rows="4" placeholder="Mô tả ngắn gọn vấn đề bạn đang gặp phải..."></textarea>
                </div>

                <button type="submit" name="submit_booking" class="cbf-submit">Xác nhận Đặt lịch ngay</button>
            </form>
        </div>

        <!-- CỘT 2: DANH SÁCH BÁC SĨ -->
        <div class="clinic-doctors-list">
            <h3 style="color: #1a365d; font-size: 24px; margin-bottom: 5px; margin-top: 0; font-family: 'Inter', sans-serif;">👨‍⚕️ Đội ngũ Bác sĩ</h3>
            <p style="color: #718096; margin-bottom: 20px; font-family: 'Inter', sans-serif;">Các chuyên gia hàng đầu sẽ đồng hành cùng sức khỏe của bạn.</p>
            
            <?php
            if ($doctors_list) {
                foreach ($doctors_list as $doctor) {
                    $img_url = get_post_meta($doctor->ID, '_doctor_image_url', true);
                    if (empty($img_url)) {
                        $img_url = get_the_post_thumbnail_url($doctor->ID, 'thumbnail');
                    }
                    if (empty($img_url)) {
                        // Ảnh mặc định nếu bác sĩ chưa có ảnh
                        $img_url = 'https://ui-avatars.com/api/?name='.urlencode($doctor->post_title).'&background=ebf8ff&color=2b6cb0&size=200';
                    }
                    ?>
                    <div class="doctor-card">
                        <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($doctor->post_title); ?>" class="doctor-avatar">
                        <div class="doctor-info">
                            <h4><?php echo esc_html($doctor->post_title); ?></h4>
                            <p><?php echo wp_kses_post(wp_trim_words($doctor->post_content, 25, '...')); ?></p>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div style="padding: 20px; background: #fff; border-radius: 10px; border: 1px dashed #cbd5e0; color: #718096; text-align: center; font-family: Inter, sans-serif;">Chưa có dữ liệu bác sĩ. Vui lòng thêm bác sĩ trong trang quản trị (Menu Bác sĩ).</div>';
            }
            ?>
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
                }
            }, 100);
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
    $patient_phone = get_post_meta( $post->ID, '_patient_phone', true );
    $patient_email = get_post_meta( $post->ID, '_patient_email', true );
    $booking_date  = get_post_meta( $post->ID, '_booking_date', true );
    $selected_doctor = get_post_meta( $post->ID, '_selected_doctor', true );

    wp_nonce_field( 'clinic_booking_save_meta', 'clinic_booking_meta_nonce' );

    echo '<div style="display: flex; flex-direction: column; gap: 10px;">';
    
    echo '<label for="patient_phone"><strong>Số điện thoại:</strong></label>';
    echo '<input type="text" id="patient_phone" name="patient_phone" value="' . esc_attr( $patient_phone ) . '" style="width: 100%;">';

    echo '<label for="patient_email"><strong>Email:</strong></label>';
    echo '<input type="email" id="patient_email" name="patient_email" value="' . esc_attr( $patient_email ) . '" style="width: 100%;">';

    echo '<label for="booking_date"><strong>Ngày khám:</strong></label>';
    echo '<input type="date" id="booking_date" name="booking_date" value="' . esc_attr( $booking_date ) . '" style="width: 100%;">';
    
    echo '<label for="selected_doctor"><strong>Bác sĩ yêu cầu:</strong></label>';
    echo '<input type="text" id="selected_doctor" name="selected_doctor" value="' . esc_attr( $selected_doctor ) . '" style="width: 100%;">';
    
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

    if ( isset( $_POST['patient_phone'] ) ) {
        update_post_meta( $post_id, '_patient_phone', sanitize_text_field( $_POST['patient_phone'] ) );
    }
    if ( isset( $_POST['patient_email'] ) ) {
        update_post_meta( $post_id, '_patient_email', sanitize_email( $_POST['patient_email'] ) );
    }
    if ( isset( $_POST['booking_date'] ) ) {
        update_post_meta( $post_id, '_booking_date', sanitize_text_field( $_POST['booking_date'] ) );
    }
    if ( isset( $_POST['selected_doctor'] ) ) {
        update_post_meta( $post_id, '_selected_doctor', sanitize_text_field( $_POST['selected_doctor'] ) );
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
}

add_action('admin_init', 'cb_register_settings');
function cb_register_settings() {
    register_setting('cb_settings_group', 'cb_admin_email');
    register_setting('cb_settings_group', 'cb_brevo_api_key');
    register_setting('cb_settings_group', 'cb_brevo_sender_email');
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
                    <th scope="row">Email Quản Trị (Nhận thông báo)</th>
                    <td>
                        <input type="email" name="cb_admin_email" value="<?php echo esc_attr(get_option('cb_admin_email', get_option('admin_email'))); ?>" style="width: 350px;" />
                        <p class="description">Email sẽ nhận được thông báo mỗi khi có khách hàng đặt lịch mới. Mặc định là email của quản trị viên.</p>
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
            </table>
            <?php submit_button('Lưu cấu hình'); ?>
        </form>
    </div>
    <?php
}
?>