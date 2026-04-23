<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function clinic_booking_form_shortcode() {
    ob_start();

    // Xử lý dữ liệu khi người dùng bấm nút "Xác nhận Đặt lịch"
    if ( isset( $_POST['submit_booking'] ) ) {
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

        $appointment_data = array(
            'post_title'   => 'Lịch khám: ' . $patient_name . ' - ' . $booking_date . ' ' . $booking_time,
            'post_content' => 'Triệu chứng: ' . $symptoms,
            'post_status'  => 'pending',
            'post_type'    => 'appointment',
        );

        $post_id = wp_insert_post( $appointment_data );

        if ( $post_id ) {
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
            
            // WEBHOOK
            $webhook_url = get_option('cb_webhook_url');
            if (!empty($webhook_url)) {
                $webhook_data = array(
                    'content' => '🔔 **CÓ LỊCH ĐẶT KHÁM MỚI**',
                    'embeds' => array(
                        array(
                            'title' => 'Chi tiết thông tin đăng ký',
                            'color' => 3447003,
                            'fields' => array(
                                array('name' => 'Người đăng ký', 'value' => $registrant_name, 'inline' => true),
                                array('name' => 'Điện thoại', 'value' => $patient_phone, 'inline' => true),
                                array('name' => 'Người khám', 'value' => $patient_name . ' (' . $patient_gender . ')', 'inline' => true),
                                array('name' => 'Thời gian', 'value' => $booking_time . ' ngày ' . $booking_date, 'inline' => false),
                            )
                        )
                    )
                );
                wp_remote_post($webhook_url, array(
                    'headers' => array('Content-Type' => 'application/json'),
                    'body'    => wp_json_encode($webhook_data),
                    'method'  => 'POST',
                    'timeout' => 15,
                    'sslverify' => false
                ));
            }

            // EMAIL (Brevo fallback wp_mail)
            $to = $patient_email;
            $subject = 'Xác nhận đặt lịch khám thành công';
            $message = "Chào " . $registrant_name . ",\n\nCảm ơn bạn đã đặt lịch khám tại hệ thống của chúng tôi.\nThời gian: " . $booking_date . " lúc " . $booking_time . ".\nBộ phận lễ tân sẽ sớm liên hệ lại.";
            
            $brevo_api_key = get_option('cb_brevo_api_key');
            if (!empty($brevo_api_key) && $brevo_api_key !== 'ĐIỀN_API_KEY_CỦA_BẠN_VÀO_ĐÂY') {
                wp_remote_post('https://api.brevo.com/v3/smtp/email', array(
                    'headers' => array('api-key' => $brevo_api_key, 'content-type' => 'application/json'),
                    'body' => wp_json_encode(array(
                        'sender' => array('name' => 'Phòng Khám', 'email' => get_option('cb_brevo_sender_email', get_option('admin_email'))),
                        'to' => array(array('email' => $to, 'name' => $registrant_name)),
                        'subject' => $subject,
                        'textContent' => $message 
                    ))
                ));
            } else {
                wp_mail( $to, $subject, $message );
            }

            echo '<p style="color: green; font-weight: bold; margin-bottom: 15px;">✅ Đặt lịch thành công! Chúng tôi sẽ liên hệ lại sớm.</p>';
            echo '<script>if(window.history.replaceState){window.history.replaceState(null,null,window.location.href);}</script>';
        }
    }

    $times_opt = get_option('cb_time_slots', "08:00\n09:00\n10:00\n14:00\n15:00\n16:00");
    $times = array_map('trim', explode("\n", $times_opt));
    $branches = get_terms(array('taxonomy' => 'clinic_branch', 'hide_empty' => false));
    $specialties_terms = get_terms(array('taxonomy' => 'specialty', 'hide_empty' => false));
    $doctors_list = get_posts(array('post_type' => 'doctor', 'numberposts' => -1, 'post_status' => 'publish'));

    wp_enqueue_style('flatpickr-css', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
    wp_enqueue_script('flatpickr-js', 'https://cdn.jsdelivr.net/npm/flatpickr', array(), null, true);
    ?>
    <style>
        .clinic-booking-container, .clinic-booking-container * { font-family: 'Montserrat', 'Be Vietnam Pro', sans-serif !important; }
        .clinic-booking-container { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; max-width: 1100px; margin: 40px auto; align-items: start; }
        @media (max-width: 768px) { .clinic-booking-container { grid-template-columns: 1fr; } }
        .clinic-premium-form { background: #fff; padding: 25px 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 3px solid #005086; }
        .clinic-premium-form h3 { color: #ff5722; font-size: 20px; font-weight: 700; margin-bottom: 20px; margin-top: 0; text-transform: uppercase; }
        .cbf-group { margin-bottom: 15px; }
        .cbf-group-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px; }
        .cbf-group input, .cbf-group select, .cbf-group textarea, .cbf-group-row input, .cbf-group-row select { width: 100%; padding: 12px 15px; border: 1px solid #dcdcdc; border-radius: 6px; font-size: 14px; }
        .has-error { border-color: #e53935 !important; background: #fff8f8 !important; }
        .cbf-btn-primary { background: #5b9bd5; color: #fff; border: none; padding: 12px 25px; border-radius: 6px; cursor: pointer; font-weight: 600; }
        .cbf-btn-secondary { background: #005086; color: #fff; border: none; padding: 12px 25px; border-radius: 6px; cursor: pointer; }
        .doctor-card { display: flex; background: #fff; padding: 20px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: 1px solid #f0f0f0; margin-bottom: 20px; }
        .doctor-avatar { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-right: 20px; border: 3px solid #ebf8ff; flex-shrink: 0; }
        .doctor-info h4 { margin: 0 0 5px 0; color: #2b6cb0; font-size: 20px; }
        .cb-pagination button { padding: 5px 12px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; }
    </style>

    <div class="clinic-booking-container">
        <div class="clinic-premium-form">
            <h3>ĐẶT LỊCH HẸN KHÁM</h3>
            <form method="post" action="" id="clinic-booking-form">
                <div id="cbf-step-1">
                    <div class="cbf-group">
                        <select name="clinic" id="clinic" required>
                            <option value="" data-id="">Chọn chi nhánh / phòng khám</option>
                            <?php foreach($branches as $b) echo '<option value="'.$b->name.'" data-id="'.$b->term_id.'">'.$b->name.'</option>'; ?>
                        </select>
                    </div>
                    <div class="cbf-group">
                        <select name="specialty" id="specialty" required>
                            <option value="" data-id="">Chọn chuyên khoa</option>
                            <?php foreach($specialties_terms as $s) echo '<option value="'.$s->name.'" data-id="'.$s->term_id.'">'.$s->name.'</option>'; ?>
                        </select>
                    </div>
                    <div class="cbf-group">
                        <select name="selected_doctor" id="selected_doctor" required>
                            <option value="">Chọn bác sĩ</option>
                            <?php foreach($doctors_list as $doc) echo '<option value="'.$doc->post_title.'">'.$doc->post_title.'</option>'; ?>
                        </select>
                    </div>
                    <div class="cbf-group-row">
                        <input type="text" name="booking_date" id="booking_date" placeholder="Ngày khám" required>
                        <select name="booking_time" id="booking_time" required>
                            <option value="">Giờ</option>
                            <?php foreach($times as $t) if(trim($t)) echo '<option value="'.$t.'">'.$t.'</option>'; ?>
                        </select>
                    </div>
                    <button type="button" class="cbf-btn-primary" id="btn-next">Tiếp theo</button>
                </div>
                <div id="cbf-step-2" style="display:none;">
                    <div class="cbf-group"><input type="text" name="registrant_name" placeholder="Họ tên người đăng ký" required></div>
                    <div class="cbf-group-row">
                        <input type="tel" name="patient_phone" placeholder="Điện thoại" required>
                        <input type="email" name="patient_email" placeholder="Email" required>
                    </div>
                    <div class="cbf-group"><input type="text" name="patient_name" placeholder="Họ tên người khám" required></div>
                    <div class="cbf-group-row">
                        <input type="text" name="patient_dob" id="patient_dob" placeholder="Ngày sinh" required>
                        <div style="display:flex; gap:10px; align-items:center;">
                            <label><input type="radio" name="patient_gender" value="Nam" checked> Nam</label>
                            <label><input type="radio" name="patient_gender" value="Nữ"> Nữ</label>
                        </div>
                    </div>
                    <div class="cbf-group"><textarea name="symptoms" rows="4" placeholder="Lời nhắn / Triệu chứng"></textarea></div>
                    <div style="display:flex; justify-content: space-between;">
                        <button type="button" class="cbf-btn-secondary" id="btn-back">Quay lại</button>
                        <button type="submit" name="submit_booking" class="cbf-btn-primary">Xác nhận</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="clinic-doctors-list">
            <h3 style="color: #1a365d; font-size: 24px; margin: 0 0 15px 0;">Đội ngũ Bác sĩ</h3>
            <div id="cb-doctors-display">
                <?php
                if ($doctors_list) {
                    $count = 0;
                    foreach ($doctors_list as $doctor) {
                        $count++; $display = ($count > 4) ? 'none' : 'flex';
                        $img_url = get_post_meta($doctor->ID, '_doctor_image_url', true) ?: get_the_post_thumbnail_url($doctor->ID, 'thumbnail') ?: 'https://ui-avatars.com/api/?name='.urlencode($doctor->post_title);
                        echo '<div class="doctor-card" style="display: '.$display.';">
                            <img src="'.$img_url.'" class="doctor-avatar">
                            <div class="doctor-info">
                                <h4>'.$doctor->post_title.'</h4>
                                <p>'.wp_trim_words($doctor->post_content, 20).'</p>
                                <a href="'.get_permalink($doctor->ID).'" target="_blank" style="color:#2b6cb0; font-size:13px; font-weight:600;">Xem chi tiết →</a>
                            </div>
                        </div>';
                    }
                    if ($count > 4) {
                        echo '<div class="cb-pagination" style="display:flex; gap:5px; justify-content:center;">';
                        for ($i = 1; $i <= ceil($count/4); $i++) echo '<button class="page-num" data-page="'.$i.'" style="background:'.($i==1?'#2b6cb0':'#edf2f7').'; color:'.($i==1?'#fff':'#2b6cb0').';">'.$i.'</button>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if(typeof flatpickr !== 'undefined') {
            flatpickr("#booking_date", { dateFormat: "d/m/Y", minDate: "today" });
            flatpickr("#patient_dob", { dateFormat: "d/m/Y" });
        }
        var step1 = document.getElementById('cbf-step-1'), step2 = document.getElementById('cbf-step-2');
        document.getElementById('btn-next').onclick = function() { step1.style.display='none'; step2.style.display='block'; };
        document.getElementById('btn-back').onclick = function() { step1.style.display='block'; step2.style.display='none'; };

        var clinic = document.getElementById('clinic'), spec = document.getElementById('specialty'), doc = document.getElementById('selected_doctor'), display = document.getElementById('cb-doctors-display');
        
        function updateDoctors(callback) {
            var b_id = clinic.options[clinic.selectedIndex].getAttribute('data-id'), s_id = spec.options[spec.selectedIndex].getAttribute('data-id');
            display.style.opacity = '0.5';
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (this.status === 200) {
                    var res = JSON.parse(this.responseText);
                    display.style.opacity = '1';
                    doc.innerHTML = '<option value="">Chọn bác sĩ</option>';
                    if(res.success) {
                        res.data.doctors.forEach(function(d){ doc.innerHTML += '<option value="'+d.title+'">'+d.title+'</option>'; });
                        display.innerHTML = res.data.html;
                        initPagination();
                        if (typeof callback === 'function') callback();
                    }
                }
            };
            xhr.send('action=cb_get_doctors&branch_id='+(b_id||'')+'&specialty_id='+(s_id||''));
        }

        function updateSpecs(callback) {
            var b_id = clinic.options[clinic.selectedIndex].getAttribute('data-id');
            spec.innerHTML = '<option value="">Đang tải...</option>';
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (this.status === 200) {
                    var res = JSON.parse(this.responseText);
                    spec.innerHTML = '<option value="">Chọn chuyên khoa</option>';
                    if(res.success) {
                        res.data.forEach(function(s){ spec.innerHTML += '<option value="'+s.name+'" data-id="'+s.id+'">'+s.name+'</option>'; });
                        updateDoctors(callback); // Truyền tiếp callback xuống cấp dưới
                    }
                }
            };
            xhr.send('action=cb_get_specialties&branch_id='+b_id);
        }

        function initPagination() {
            document.querySelectorAll('.page-num').forEach(function(btn){
                btn.onclick = function() {
                    var p = this.getAttribute('data-page'), cards = document.querySelectorAll('.doctor-card');
                    cards.forEach(function(c, i){ c.style.display = (Math.ceil((i+1)/4) == p) ? 'flex' : 'none'; });
                    document.querySelectorAll('.page-num').forEach(function(b){ b.style.background='#edf2f7'; b.style.color='#2b6cb0'; });
                    this.style.background='#2b6cb0'; this.style.color='#fff';
                };
            });
        }
        clinic.onchange = updateSpecs; spec.onchange = updateDoctors; initPagination();

        // Auto select từ URL (?auto_doctor=ID) - Fix logic chuỗi AJAX
        var params = new URLSearchParams(window.location.search), autoId = params.get('auto_doctor');
        if(autoId) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if(this.status === 200) {
                    var res = JSON.parse(this.responseText);
                    if(res.success && res.data) {
                        // 1. Chọn Chi nhánh
                        for(var i=0; i<clinic.options.length; i++) {
                            if(clinic.options[i].getAttribute('data-id') == res.data.branch_id) {
                                clinic.selectedIndex = i;
                                break;
                            }
                        }

                        // 2. Chạy update Chuyên khoa và ĐỢI nó xong
                        updateSpecs(function() {
                            // 3. Sau khi Chuyên khoa đã load xong, chọn đúng chuyên khoa
                            for(var i=0; i<spec.options.length; i++) {
                                if(spec.options[i].getAttribute('data-id') == res.data.specialty_id) {
                                    spec.selectedIndex = i;
                                    break;
                                }
                            }

                            // 4. Chạy update Bác sĩ và ĐỢI nó xong
                            updateDoctors(function() {
                                // 5. Sau khi Bác sĩ đã load xong, chọn đúng tên bác sĩ
                                for(var i=0; i<doc.options.length; i++) {
                                    if(doc.options[i].value == res.data.doctor_title) {
                                        doc.selectedIndex = i;
                                        break;
                                    }
                                }
                                // 6. Cuộn xuống form
                                window.scrollTo({ top: document.getElementById('clinic-booking-form').offsetTop - 100, behavior: 'smooth' });
                            });
                        });
                    }
                }
            };
            xhr.send('action=cb_get_doctor_info&doctor_id='+autoId);
        }
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('clinic_booking_form', 'clinic_booking_form_shortcode');
