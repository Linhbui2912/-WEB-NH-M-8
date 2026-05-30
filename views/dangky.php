<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký - PawsConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">    
    <style>
        body {
            background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
            min-height: 100vh;
        }
        .register-card {
            border: none;
            border-radius: 1.25rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
        .btn-pet {
            background-color: black;
            color: white;
            border: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-pet:hover {
            background-color: #ff5252;
            color: white;
            transform: translateY(-1px);
        }
        .brand-logo {
            font-size: 3rem;
            color: #ff6b6b;
        }
        .hover-underline:hover {
            text-decoration: underline !important;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center py-5">

    <div class="container" style="max-width: 480px;">
        <?php if(isset($_GET['msg']) && $_GET['msg'] == "captcha-failed"): ?>
            <div id="captcha-alert" class="alert alert-danger text-center mb-4 rounded-3 shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                Mã xác thực không chính xác! Vui lòng nhập lại.
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == "register-fail"): ?>
            <div id="error-alert" class="alert alert-danger text-center mb-4 rounded-3 shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                Đăng ký thất bại. Tên tài khoản đã tồn tại!
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['msg']) && $_GET['msg'] == "system-error"): ?>
            <div id="system-alert" class="alert alert-danger text-center mb-4 rounded-3 shadow-sm" role="alert">
                <i class="bi bi-gear-fill me-2"></i>
                Hệ thống đang bận hoặc gặp sự cố kỹ thuật. Vui lòng thử lại sau!
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == "password-mismatch"): ?>
            <div id="mismatch-alert" class="alert alert-warning text-center mb-4 rounded-3 shadow-sm" role="alert">
                <i class="bi bi-shield-exclamation me-2"></i>
                Mật khẩu xác nhận không trùng khớp!
            </div>
        <?php endif; ?>

        <div class="card register-card p-4 p-md-5 bg-white">
            <div class="text-center mb-4">
                <div class="brand-logo mb-2">
                    <i class="bi bi-paw-fill"></i>
                </div>
                <h2 class="fw-bold text-dark mb-1">Tạo Tài Khoản</h2>
                <p class="text-muted small">Gia nhập cộng đồng thú cưng PawsConnect</p>
            </div>

            <form method="post" action="../controllers/xulydangky.php" id="registerForm">
                
                <div class="mb-3">
                    <label class="form-label text-secondary small fw-semibold">Tên hiển thị (Họ tên):</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-secondary border-end-0">
                            <i class="bi bi-card-heading"></i>
                        </span>
                        <input type="text" name="fullname" class="form-control bg-light border-start-0 input-field" placeholder="Nhập tên hiển thị của bạn..." required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small fw-semibold">Tên tài khoản (Username):</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-secondary border-end-0">
                            <i class="bi bi-person"></i>
                        </span>
                        <input type="text" name="username" id="username" class="form-control bg-light border-start-0 input-field" placeholder="Nhập tên tài khoản ..." required>
                    </div>
                    
                    <div id="username-ajax-error" class="mt-1" style="display: none;"></div>
                </div>              

                <div class="mb-3">
                    <label class="form-label text-secondary small fw-semibold">Mật khẩu:</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-secondary border-end-0">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input type="password" name="password" id="password" minlength="6" class="form-control bg-light border-start-0 input-field" placeholder="Tối thiểu 6 ký tự..." required>
                    </div>
                    <div id="password-length-error" class="form-text text-danger small mt-1" style="display: none;">
                        Mật khẩu phải chứa ít nhất 6 ký tự!
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-secondary small fw-semibold">Xác nhận mật khẩu:</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-secondary border-end-0">
                            <i class="bi bi-shield-check"></i>
                        </span>
                        <input type="password" name="re-password" id="re-password" class="form-control bg-light border-start-0 input-field" placeholder="Nhập lại mật khẩu..." required>
                    </div>
                    <div id="password-error-text" class="form-text text-danger small mt-1" style="display: none;">
                        Mật khẩu xác nhận chưa trùng khớp!
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label text-secondary small fw-semibold">Mã xác thực CAPTCHA:</label>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <img src="../controllers/captcha.php" id="captcha-img" class="rounded-2 border" style="width: 160px; height: 45px; cursor: pointer;" title="Click để đổi mã">
                        <button type="button" id="btn-refresh-captcha" class="btn btn-sm btn-outline-secondary py-2">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-secondary border-end-0">
                            <i class="bi bi-shield-lock"></i>
                        </span>
                        <input type="text" name="txt_captcha" class="form-control bg-light border-start-0 input-field" placeholder="Nhập các ký tự trong hình..." required autocomplete="off">
                    </div>
                </div>
                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-pet py-2.5 rounded-3">
                        <i class="bi bi-person-plus-fill me-2"></i>Đăng Ký Tài Khoản
                    </button>
                </div>

                <div class="text-center mt-4 border-top pt-3">
                    <span class="small text-muted">Đã có tài khoản PawsConnect? </span>
                    <a href="dangnhap.php" class="text-decoration-none small text-danger fw-semibold hover-underline">
                        Đăng nhập ngay
                    </a>
                </div>                
                
                <div class="text-center">
                    <button type="button" id="btn-clear" class="btn btn-link btn-sm text-secondary text-decoration-none">
                        <i class="bi bi-eraser me-1"></i>Xóa form
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const inputs = document.querySelectorAll('.input-field');
            const captchaAlert = document.getElementById('captcha-alert');
            const errorAlert = document.getElementById('error-alert');
            const systemAlert = document.getElementById('system-alert');
            const mismatchAlert = document.getElementById('mismatch-alert');
            const btnClear = document.getElementById('btn-clear');
            
            const registerForm = document.getElementById('registerForm');
            const password = document.getElementById('password');
            const rePassword = document.getElementById('re-password');
            const passwordErrorText = document.getElementById('password-error-text');

            const passwordLengthError = document.getElementById('password-length-error');

            // Hàm ẩn toàn bộ các khối alert thông báo
            function hideAlerts() {
                if (captchaAlert) captchaAlert.style.display = 'none';
                if (errorAlert) errorAlert.style.display = 'none';
                if (mismatchAlert) mismatchAlert.style.display = 'none';
                if(systemAlert) systemAlert.style.display = 'none';
                passwordErrorText.style.display = 'none';
            }

            // 1. Tự động ẩn thông báo lỗi khi người dùng bắt đầu gõ lại dữ liệu mới
            inputs.forEach(input => {
                input.addEventListener('input', () => {
                    hideAlerts();
                });            
            });
            // 2. Kiểm tra độ dài mật khẩu khi người dùng đang gõ 
        password.addEventListener('input', function() {
            if (password.value.length < 6 && password.value !== '') {
                passwordLengthError.style.display = 'block';
            } else {
                passwordLengthError.style.display = 'none';
            }
        });
            // 3. Kiểm tra trùng khớp mật khẩu ngay khi người dùng đang gõ 
            rePassword.addEventListener('input', function() {
                if (password.value !== rePassword.value && rePassword.value !== '') {
                    passwordErrorText.style.display = 'block';
                } else {
                    passwordErrorText.style.display = 'none';
                }
            });

            // 4. Ngăn không cho submit form nếu mật khẩu gõ lại bị lệch
            if (registerForm) {
                registerForm.addEventListener('submit', function(event) {
                    if (password.value !== rePassword.value) {
                        event.preventDefault(); // Chặn gửi dữ liệu đi
                        passwordErrorText.style.display = 'block';
                        rePassword.focus();
                    }
                });
            }

            // 5. Xử lý logic nút "Xóa form"
            if (btnClear) {
                btnClear.addEventListener('click', function() {
                    inputs.forEach(input => {
                        input.value = '';
                    });
                    hideAlerts();
                });
            }
            // Kiểm tra Username bằng XMLHttpRequest khi rời khỏi ô nhập 
        const usernameInput = document.getElementById('username');
        const usernameAjaxError = document.getElementById('username-ajax-error');
        let isUsernameValid = true; // Biến cờ chặn submit form nếu lỗi

        usernameInput.addEventListener('blur', function() {
            const username = usernameInput.value.trim();
            
            if (username === '') {
                usernameAjaxError.innerHTML = '';
                usernameAjaxError.style.display = 'none';
                isUsernameValid = true;
                return;
            }

            // Khởi tạo đối tượng yêu cầu chạy ngầm 
            const xhr = new XMLHttpRequest();
            
            // Thiết lập phương thức GET đến file xử lý kèm tham số username trên URL
            xhr.open('GET', '../controllers/check_username.php?username=' + encodeURIComponent(username), true);
            
            // Lắng nghe phản hồi từ Server
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) { // Khi request hoàn thành
                    if (xhr.status === 200) { // Nếu kết nối thành công tốt đẹp
                        
                        // // Lấy kết quả trả về từ XMTML
                        const response = xhr.responseText;
                        
                        // Nếu Server có trả về nội dung (tức là dính chữ báo lỗi "Tên tài khoản này đã...")
                        if (xhr.responseText.trim() !== "") {
                            //Lấy nguyên khối XHTML nhận được gán thẳng vào innerHTML của cái hộp báo lỗi
                            usernameAjaxError.innerHTML = response;
                            usernameAjaxError.style.display = 'block';
                            usernameInput.classList.add('is-invalid'); // Thêm viền đỏ của Bootstrap
                            isUsernameValid = false; // Đánh dấu lỗi, không cho gửi form
                        } else {
                            // Nếu Server trả về chuỗi rỗng (tức là tài khoản hợp lệ, không trùng)
                            usernameAjaxError.innerHTML = '<span class="text-success small"><i class="bi bi-check-circle-fill me-1"></i> Tên tài khoản hợp lệ</span>'; 
                            usernameAjaxError.style.display = 'block';
                            usernameInput.classList.remove('is-invalid');
                            isUsernameValid = true;
                        }
                    } else {
                        console.error('Lỗi kiểm tra hệ thống. Mã trạng thái:', xhr.status);
                    }
                }
            };            
            // Bắn request đi ngầm ngầm dưới nền
            xhr.send();
        });
        // Khi người dùng đang gõ lại vào ô username thì tạm thời ẩn viền đỏ và thông báo đi
        usernameInput.addEventListener('input', function() {
            usernameAjaxError.style.display = 'none';
            usernameInput.classList.remove('is-invalid');
            isUsernameValid = true; 
        });
        const captchaImg = document.getElementById('captcha-img');
        const btnRefreshCaptcha = document.getElementById('btn-refresh-captcha');

        function reloadCaptcha() {
            if (captchaImg) {
                // Thêm rand= để ép trình duyệt load ảnh mới, không lấy từ bộ nhớ đệm
                captchaImg.src = '../controllers/captcha.php?rand=' + Math.random();
            }
        }
        if (captchaImg) captchaImg.addEventListener('click', reloadCaptcha);
        if (btnRefreshCaptcha) btnRefreshCaptcha.addEventListener('click', reloadCaptcha);              
        
    });        
    </script>
</body>
</html>