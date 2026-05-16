<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

$isRoot = false;
$activeNav = 'discover';
$assetPrefix = '../';
$apiBase = '../';
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PawConnect - Discover</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body data-api-base="<?= h($apiBase) ?>">
  <div class="container-fluid px-0">
    <div class="row g-0">
      <?php require __DIR__ . '/../partials/sidebar.php'; ?>

      <main class="feed-wrapper col">
        <section class="feed-column">
          <div class="post-card text-center">
            <h1 class="h5 mb-2">Discover</h1>
            <p class="text-secondary mb-0">Trang Discover — nhóm có thể nối gợi ý / khám phá từ CSDL tại đây.</p>
          </div>
        </section>
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/main.js"></script>
</body>
</html>
