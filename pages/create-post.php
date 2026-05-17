<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $caption = trim((string) ($_POST['caption'] ?? ''));
    if ($caption === '') {
        $error = 'Vui lòng nhập mô tả bài đăng.';
    }

    if ($error === '' && (!isset($_FILES['image']) || !is_array($_FILES['image']))) {
        $error = 'Vui lòng chọn ảnh.';
    }

    if ($error === '' && is_array($_FILES['image'])) {
        $file = $_FILES['image'];
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $error = 'Upload ảnh không thành công.';
        } else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = $finfo ? finfo_file($finfo, (string) $file['tmp_name']) : false;
            if ($finfo) {
                finfo_close($finfo);
            }

            $map = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
            ];

            if (!is_string($mime) || !isset($map[$mime])) {
                $error = 'Chỉ chấp nhận JPG, PNG hoặc WEBP.';
            } else {
                $ext = $map[$mime];
                $name = uniqid('upload_', true) . '.' . $ext;
                $destDir = __DIR__ . '/../assets/uploads/';
                if (!is_dir($destDir)) {
                    mkdir($destDir, 0775, true);
                }
                $destPath = $destDir . $name;
                if (!move_uploaded_file((string) $file['tmp_name'], $destPath)) {
                    $error = 'Không lưu được file upload.';
                } else {
                    $relative = 'assets/uploads/' . $name;
                    try {
                        $pdo = Database::connection();
                        $repo = new PostRepository($pdo);
                        $repo->create((int) $_SESSION['user_id'], $relative, $caption);
                        header('Location: homepage.php');
                        exit;
                    } catch (Throwable $e) {
                        $error = 'Không ghi được CSDL. Kiểm tra kết nối và script SQL.';
                    }
                }
            }
        }
    }
}

$isRoot = false;
$activeNav = 'create';
$assetPrefix = '../';
$apiBase = '../';
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PawConnect - Create Post</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body data-api-base="<?= h($apiBase) ?>">
  <div class="container-fluid px-0">
    <div class="row g-0">
      <?php require __DIR__ . '/../partials/sidebar.php'; ?>

      <main class="feed-wrapper col">
        <section class="feed-column">
          <div class="post-card">
            <h1 class="h5 mb-3">Tạo bài đăng</h1>

            <?php if ($error !== ''): ?>
              <div class="alert alert-danger"><?= h($error) ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data" class="vstack gap-3">
              <div>
                <label class="form-label">Ảnh</label>
                <input class="form-control" type="file" name="image" accept="image/*" required>
              </div>
              <div>
                <label class="form-label">Mô tả</label>
                <textarea class="form-control" name="caption" rows="4" required placeholder="Viết caption cho bài đăng..."></textarea>
              </div>
              <button type="submit" class="btn btn-dark">Đăng bài</button>
            </form>
          </div>
        </section>
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/main.js"></script>
</body>
</html>
