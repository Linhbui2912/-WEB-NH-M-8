<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/homepage_helpers.php'; 
$activeNav = 'search'; $assetPrefix = '../'; 
/**
 * @var string $keyword
 * @var list<array<string,mixed>> $results
 * @var bool   $searched
 * @var string $activeNav
 * @var string $assetPrefix
 * @var string|null $viewerId
 * @var string $projectUrl   — absolute path từ domain root, vd: /PawsConnect
 */

// Helper tạo URL tuyệt đối trong view này
$url = static fn(string $path): string => h($projectUrl . '/' . ltrim($path, '/'));
$icon = static fn(string $name): string => h($projectUrl . '/assets/icon/' . $name);
$NO_AVATAR = $projectUrl . '/assets/icon/user.png';
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PawConnect – Tìm kiếm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
   <link rel="stylesheet" href="<?= hp_h($assetPrefix) ?>assets/css/homepage.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
      body { background: #efefef; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }

       
        .settings-icon {
            display: flex; align-items: center; justify-content: center;
            width: 44px; height: 44px; border-radius: 50%;
            background: none; border: none; cursor: pointer;
            transition: background .15s; margin-bottom: 8px;
        }
        .settings-icon img { width: 26px; height: 26px; }
        .settings-icon:hover { background: #f5f5f5; }

        .search-main {
            margin-left: 72px; width: 380px;
             min-height: 100vh; border-right: 1px solid #ffff;
            padding: 16px;
        }

        .search-bar-wrap {
            display: flex; align-items: center;
            gap: 10px; margin-bottom: 16px;
        }
        .search-icon-stub {
            flex-shrink: 0; width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
        }
        .search-icon-stub img { width: 22px; height: 22px; opacity: .55; }

        .search-bar-inner {
            flex: 1; display: flex; align-items: center;
           background: #ffff; border-radius: 24px;
            padding: 0 14px; gap: 8px;
        }
        .search-bar-inner input {
            flex: 1; border: none; outline: none;
            background: transparent; font-size: 14px;
            color: #262626; padding: 10px 0;
        }
        .search-bar-inner input::placeholder { color: #8e8e8e; }
        .btn-clear-search {
            background: none; border: none; padding: 0;
            color: #8e8e8e; font-size: 14px; cursor: pointer;
            display: none; line-height: 1; flex-shrink: 0;
        }
        .btn-clear-search:hover { color: #262626; }

        .result-item {
            display: flex; align-items: center; gap: 12px;
            padding: 8px 4px; border-radius: 8px;
            text-decoration: none; color: #262626; transition: background .12s;
        }
        .result-item:hover { background: #f8f8f8; }
        .result-avatar {
            width: 48px; height: 48px; border-radius: 50%;
            object-fit: cover; border: 1px solid #efefef; flex-shrink: 0;
        }
        .result-display-name { font-weight: 600; font-size: 14px; line-height: 1.3; color: #262626; }
        .result-username { font-size: 13px; color: #8e8e8e; line-height: 1.3; }
        .hint-text { padding: 24px 4px; font-size: 14px; color: #8e8e8e; }
        .search-spinner { display: none; padding: 20px 4px; }
        .search-spinner.show { display: block; }
    </style>
</head>
<body data-viewer-id="<?= h((string)($viewerId ?? '')) ?>" data-is-own-profile="1" data-api-base="<?= h($projectUrl) ?>/controllers/">

<!-- Sidebar -->
<?php require __DIR__ . '/partials/homepage/sidebar.php'; ?>

<!-- Search panel -->
<div class="search-main">
    <div class="search-bar-wrap">
        <div class="search-icon-stub">
            <img src="<?= $icon('search.png') ?>" alt="" />
        </div>
        <div class="search-bar-inner">
            <input
                type="text"
                id="searchInput"
                placeholder="Tìm kiếm tại đây"
                value="<?= h($keyword) ?>"
                autocomplete="off"
                autofocus
            />
            <button type="button" class="btn-clear-search" id="btnClear">✕</button>
        </div>
    </div>

    <div class="search-spinner" id="searchSpinner">
        <div class="spinner-border spinner-border-sm text-secondary" role="status"></div>
    </div>

    <div id="searchResults">
        <?php if ($searched && count($results) > 0): ?>
            <?php foreach ($results as $r): ?>
                <a href="<?= h($projectUrl . '/views/profile.php?user=' . rawurlencode($r['tenDangNhap'])) ?>"
                   class="result-item">
                    <img class="result-avatar"
                         src="<?= h(profile_image_url($r['anhDaiDien'])) ?>"
                         alt="<?= h($r['tenHienThi']) ?>"
                         onerror="this.src='<?= h($NO_AVATAR) ?>'" />
                    <div>
                        <div class="result-display-name"><?= h($r['tenHienThi']) ?></div>
                        <div class="result-username"><?= h($r['tenDangNhap']) ?></div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php elseif ($searched): ?>
            <p class="hint-text">Không tìm thấy người dùng.</p>
        <?php endif; ?>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
 <script src="../assets/js/settings.js"></script>
<script>
const searchInput = document.getElementById('searchInput');
const btnClear    = document.getElementById('btnClear');
const resultsBox  = document.getElementById('searchResults');
const spinner     = document.getElementById('searchSpinner');
const SEARCH_URL  = <?= json_encode($projectUrl . '/controllers/SearchController.php') ?>;
const NO_AVATAR   = <?= json_encode($NO_AVATAR) ?>;

let debounce = null;

function updateClear() {
    btnClear.style.display = searchInput.value.trim() ? 'block' : 'none';
}

function escHtml(s) {
    const d = document.createElement('div');
    d.textContent = s ?? '';
    return d.innerHTML;
}

function renderUsers(users) {
    if (!users.length) {
        resultsBox.innerHTML = `<p class="hint-text">Không tìm thấy người dùng.</p>`;
        return;
    }
    resultsBox.innerHTML = users.map(u => `
        <a href="${escHtml(u.profileUrl)}" class="result-item">
            <img class="result-avatar"
                 src="${escHtml(u.avatar)}"
                 alt="${escHtml(u.tenHienThi)}"
                 onerror="this.src='${NO_AVATAR}'" />
            <div>
                <div class="result-display-name">${escHtml(u.tenHienThi)}</div>
                <div class="result-username">${escHtml(u.tenDangNhap)}</div>
            </div>
        </a>`).join('');
}

async function doSearch(kw) {
    if (!kw.trim()) {
        resultsBox.innerHTML = '';
        spinner.classList.remove('show');
        return;
    }
    spinner.classList.add('show');
    resultsBox.innerHTML = '';
    try {
        const r    = await fetch(`${SEARCH_URL}?q=${encodeURIComponent(kw)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await r.json();
        if (data.ok) renderUsers(data.users);
    } catch {
        resultsBox.innerHTML = `<p class="hint-text">Lỗi kết nối, thử lại.</p>`;
    } finally {
        spinner.classList.remove('show');
    }
}

searchInput.addEventListener('input', () => {
    updateClear();
    clearTimeout(debounce);
    debounce = setTimeout(() => doSearch(searchInput.value.trim()), 280);
});

btnClear.addEventListener('click', () => {
    searchInput.value = '';
    updateClear();
    resultsBox.innerHTML = '';
    searchInput.focus();
});

updateClear();
</script>
</body>
</html>
