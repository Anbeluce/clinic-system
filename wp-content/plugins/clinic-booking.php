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
            
            // BẮT ĐẦU PHẦN GỬI EMAIL TỰ ĐỘNG
            $to = $patient_email; // Gửi đến email khách hàng vừa nhập
            $subject = 'Xác nhận đặt lịch khám thành công';
            
            // Xây dựng nội dung email
            $message = "Chào " . $patient_name . ",\n\n";
            $message .= "Cảm ơn bạn đã đặt lịch khám. Hệ thống đã ghi nhận thông tin của bạn chi tiết như sau:\n\n";
            $message .= "- Ngày khám mong muốn: " . $booking_date . "\n";
            $message .= "- Số điện thoại liên hệ: " . $patient_phone . "\n";
            $message .= "- Triệu chứng/Ghi chú: " . $symptoms . "\n\n";
            $message .= "Vui lòng giữ điện thoại, bộ phận Lễ tân của chúng tôi sẽ sớm liên hệ lại để chốt giờ khám chính xác cho bạn.\n\n";
            $message .= "Trân trọng,\nHệ thống Phòng khám";

            // Định dạng email (hỗ trợ tiếng Việt có dấu)
            $headers = array('Content-Type: text/plain; charset=UTF-8');

            // Thực thi hàm gửi mail của WordPress
            $mail_sent = wp_mail( $to, $subject, $message, $headers );

            if ( $mail_sent ) {
                echo '<p style="color: green; font-weight: bold; margin-bottom: 15px;">✅ Đặt lịch thành công! Một email xác nhận đã được gửi đến bạn.</p>';
            } else {
                echo '<p style="color: green; font-weight: bold; margin-bottom: 15px;">✅ Đặt lịch thành công! (Lưu ý: Không thể gửi email xác nhận lúc này do cấu hình máy chủ).</p>';
            }
            // KẾT THÚC PHẦN GỬI EMAIL
            
        } else {
            echo '<p style="color: red; font-weight: bold; margin-bottom: 15px;">❌ Có lỗi xảy ra trong quá trình hệ thống ghi nhận, vui lòng thử lại.</p>';
        }
    }

    // Giao diện HTML của Form
    ?>
    <form method="post" action="" style="max-width: 500px; display: flex; flex-direction: column; gap: 15px; font-family: sans-serif;">
        <div>
            <label for="patient_name"><b>Họ và tên (*):</b></label><br>
            <input type="text" name="patient_name" id="patient_name" required style="width: 100%; padding: 8px;">
        </div>
        
        <div>
            <label for="patient_phone"><b>Số điện thoại (*):</b></label><br>
            <input type="tel" name="patient_phone" id="patient_phone" required style="width: 100%; padding: 8px;">
        </div>

        <div>
            <label for="patient_email"><b>Email (Để nhận thông báo):</b></label><br>
            <input type="email" name="patient_email" id="patient_email" required style="width: 100%; padding: 8px;">
        </div>

        <div>
            <label for="booking_date"><b>Ngày khám mong muốn (*):</b></label><br>
            <input type="date" name="booking_date" id="booking_date" required style="width: 100%; padding: 8px;">
        </div>

        <div>
            <label for="symptoms"><b>Mô tả triệu chứng/Ghi chú:</b></label><br>
            <textarea name="symptoms" id="symptoms" rows="4" style="width: 100%; padding: 8px;"></textarea>
        </div>

        <button type="submit" name="submit_booking" style="padding: 12px; background-color: #0073aa; color: white; border: none; cursor: pointer; font-weight: bold; font-size: 16px;">Xác nhận Đặt lịch</button>
    </form>
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
}

function clinic_booking_meta_box_html( $post ) {
    $patient_phone = get_post_meta( $post->ID, '_patient_phone', true );
    $patient_email = get_post_meta( $post->ID, '_patient_email', true );
    $booking_date  = get_post_meta( $post->ID, '_booking_date', true );

    wp_nonce_field( 'clinic_booking_save_meta', 'clinic_booking_meta_nonce' );

    echo '<div style="display: flex; flex-direction: column; gap: 10px;">';
    
    echo '<label for="patient_phone"><strong>Số điện thoại:</strong></label>';
    echo '<input type="text" id="patient_phone" name="patient_phone" value="' . esc_attr( $patient_phone ) . '" style="width: 100%;">';

    echo '<label for="patient_email"><strong>Email:</strong></label>';
    echo '<input type="email" id="patient_email" name="patient_email" value="' . esc_attr( $patient_email ) . '" style="width: 100%;">';

    echo '<label for="booking_date"><strong>Ngày khám:</strong></label>';
    echo '<input type="date" id="booking_date" name="booking_date" value="' . esc_attr( $booking_date ) . '" style="width: 100%;">';
    
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
}
?>