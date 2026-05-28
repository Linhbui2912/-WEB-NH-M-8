<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - PawsConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">    
    <style>
        body {
            background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
            min-height: 100vh;
        }
        .login-card {
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

    <div class="container" style="max-width: 450px;">
        
        <?php if(isset($_GET['msg']) && $_GET['msg'] == "login-fail"): ?>
            <div id="error-alert" class="alert alert-danger text-center mb-4 rounded-3 shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                Username hoặc Password không đúng. Vui lòng kiểm tra lại!
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == "login-required"): ?>
            <div id="required-alert" class="alert alert-warning text-center mb-4 rounded-3 shadow-sm" role="alert">
                <i class="bi bi-shield-lock-fill me-2"></i>
                Vui lòng đăng nhập tài khoản để tiếp tục!
            </div>
        <?php endif; ?>

        <div class="card login-card p-4 p-md-5 bg-white">
            <div class="text-center mb-4">
                <div class="brand-logo mb-2">
                    <i class="bi bi-paw-fill"></i>
                </div>
                <h2 class="fw-bold text-dark mb-1">PawsConnect</h2>
                <p class="text-muted small">Mạng xã hội kết nối cộng đồng thú cưng</p>
            </div>

            <form method="post" action="../controllers/xulydangnhap.php">
                
                <div class="mb-3">
                    <label class="form-label text-secondary small fw-semibold">Tên tài khoản:</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-secondary border-end-0">
                            <i class="bi bi-person"></i>
                        </span>
                        <input type="text" name="username" class="form-control bg-light border-start-0 input-field" placeholder="Nhập tên tài khoản..." required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-secondary small fw-semibold">Mật khẩu:</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-secondary border-end-0">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input type="password" name="password" class="form-control bg-light border-start-0 input-field" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-pet py-2.5 rounded-3">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Đăng Nhập
                    </button>
                </div>
                <div class="text-center mt-4 border-top pt-3">
                <span class="small text-muted">Chưa có tài khoản PawsConnect? </span>
                <a href="dangky.php" class="text-decoration-none small text-danger fw-semibold hover-underline">
                Đăng ký ngay
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
        // Chọn tất cả các ô input có class "input-field"
        const inputs = document.querySelectorAll('.input-field');
        
        // Chọn các khối alert thông báo lỗi
        const errorAlert = document.getElementById('error-alert');
        const requiredAlert = document.getElementById('required-alert');
        // Chọn nút xóa form
        const btnClear = document.getElementById('btn-clear');
        //Hàm ẩn các thông báo alert
        function hideAlerts() {
            if (errorAlert) errorAlert.style.display = 'none';
            if (requiredAlert) requiredAlert.style.display = 'none';
        }
        // Lặp qua từng ô nhập liệu để lắng nghe hành động của người dùng
        inputs.forEach(input => {
            // Sự kiện 'input' kích hoạt ngay khi người dùng gõ phím hoặc click thay đổi dữ liệu
            input.addEventListener('input', () => {
                // Nếu khối thông báo đăng nhập sai đang tồn tại thì ẩn nó đi
                if (errorAlert) {
                    errorAlert.style.display = 'none';
                }
                // Tương tự cho thông báo yêu cầu đăng nhập
                if (requiredAlert) {
                    requiredAlert.style.display = 'none';
                }
            });            
        });
        // Xử lý sự kiện click vào nút "Xóa form"
            if (btnClear) {
            btnClear.addEventListener('click', function() {
                // Ép tất cả các ô nhập liệu về rỗng (Xóa sạch hoàn toàn)
                inputs.forEach(input => {
                    input.value = '';
                });
                // Đồng thời ẩn luôn các thông báo lỗi cho giao diện sạch sẽ
                hideAlerts();
            });
        }
        });
    </script>
</body>
</html>