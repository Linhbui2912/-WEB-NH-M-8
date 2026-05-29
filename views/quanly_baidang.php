<?php

declare(strict_types=1);

session_start();

if (!isset($_SESSION['maNguoiDung']) || ($_SESSION['maQuyen'] ?? '') !== '1') {
    header('Location: ../views/dangnhap.php?msg=login-required');
    exit;
}

require_once __DIR__ . '/../modules/db_module.php';
require_once __DIR__ . '/../models/homepage_helpers.php';
require_once __DIR__ . '/../models/admin_post_report_model.php';

$link = null;
taoKetNoi($link);
$reports = PostReportModel::fetchAll($link);
giaiPhongBoNho($link, true);

// Nhãn trạng thái hiển thị
function trangThaiLabel(string $tt): string
{
    return match ($tt) {
        'cho_duyet'        => '<span style="color:#e07b00;font-weight:600">Chờ duyệt</span>',
        'da_xoa_bai_dang'  => '<span style="color:#888">Đã xóa</span>',
        'tu_choi_bao_cao'  => '<span style="color:#d9534f;font-weight:600">Đã từ chối</span>',
        default            => hp_h($tt),
    };
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý bài đăng - PawConnect Admin</title>
    <link rel="stylesheet" href="../assets/css/admin_style.css">
</head>
<body>


<?php require __DIR__ . '/admin_sidebar.php'; ?>

<div class="admin-main-content">
    <h1 class="page-title">Quản lý bài đăng</h1>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Mã bài đăng</th>
                    <th>Người báo cáo</th>
                    <th>Lý do báo cáo</th>
                    <th>Ngày báo cáo</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($reports) === 0): ?>
                    <tr>
                        <td colspan="6" style="text-align:center;color:#888">Không có báo cáo nào.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reports as $r): ?>
                        <?php $isAlert = $r['trangThai'] === 'cho_duyet'; ?>
                        <tr class="<?= $isAlert ? 'row-alert' : '' ?>">
                            <td>
                                <a href="#"
                                   class="text-blue post-link"
                                   data-ma-bai-dang="<?= hp_h((string) $r['maBaiDang']) ?>"
                                   data-ma-bao-cao="<?= hp_h((string) $r['maBaoCao']) ?>">
                                    <?= hp_h((string) $r['maBaiDang']) ?>
                                </a>
                            </td>
                            <td><?= hp_h((string) $r['tenNguoiBaoCao']) ?></td>
                            <td><?= hp_h((string) $r['lyDoBaoCao']) ?></td>
                            <td><?= hp_h(date('d/m/Y', strtotime((string) $r['thoiGianBaoCao']))) ?></td>
                            <td><?= trangThaiLabel((string) $r['trangThai']) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($r['trangThai'] !== 'da_xoa_bai_dang'): ?>
                                        <button class="btn-lock btn-delete"
                                                data-ma-bai-dang="<?= hp_h((string) $r['maBaiDang']) ?>"
                                                data-ma-bao-cao="<?= hp_h((string) $r['maBaoCao']) ?>">
                                            Xóa
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($r['trangThai'] === 'cho_duyet'): ?>
                                        <button class="btn-unlock btn-reject"
                                                data-ma-bao-cao="<?= hp_h((string) $r['maBaoCao']) ?>">
                                            Từ chối
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ── Modal chi tiết bài đăng ── -->
<div id="postDetailOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:2000;align-items:center;justify-content:center;">
    <div id="postDetailCard" style="background:#fff;border-radius:12px;overflow:hidden;display:flex;max-width:860px;width:95%;max-height:90vh;box-shadow:0 8px 32px rgba(0,0,0,.2);">

        <!-- Ảnh bên trái -->
        <div style="flex:1;background:#000;display:flex;align-items:center;justify-content:center;min-height:400px;">
            <img id="pdImage" src="" alt="Ảnh bài đăng" style="max-width:100%;max-height:80vh;object-fit:contain;">
        </div>

        <!-- Nội dung bên phải -->
        <div style="width:320px;display:flex;flex-direction:column;border-left:1px solid #eee;">

            <!-- Header -->
            <div style="display:flex;align-items:center;gap:10px;padding:14px 16px;border-bottom:1px solid #eee;">
                <img id="pdAvatar" src="" alt="" style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                <div>
                    <div id="pdUsername" style="font-weight:700;font-size:14px;"></div>
                    <div id="pdTime" style="font-size:12px;color:#888;"></div>
                </div>
                <button id="pdClose" style="margin-left:auto;background:none;border:none;font-size:20px;cursor:pointer;color:#666">✕</button>
            </div>

            <!-- Body: caption + comments -->
            <div style="flex:1;overflow-y:auto;padding:14px 16px;">
                <p id="pdCaption" style="font-size:14px;margin-bottom:12px;"></p>
                <ul id="pdComments" style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:10px;"></ul>
            </div>

            <!-- Footer: paw count -->
            <div style="padding:12px 16px;border-top:1px solid #eee;">
                <div style="display:flex;align-items:center;gap:6px;">
                    <span style="font-size:20px;">🐾</span>
                    <span id="pdPawCount" style="font-weight:700;font-size:14px;"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Spinner loading -->
<div id="pdLoading" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:3000;align-items:center;justify-content:center;">
    <div style="width:48px;height:48px;border:5px solid #fff;border-top-color:transparent;border-radius:50%;animation:spin .8s linear infinite;"></div>
</div>
<style>@keyframes spin{to{transform:rotate(360deg)}}</style>

<script>
const API = '../controllers/admin_post_report_controller.php';

// ── Mở card chi tiết ─────────────────────────────────────────────────────
document.querySelectorAll('.post-link').forEach(link => {
    link.addEventListener('click', async e => {
        e.preventDefault();
        const maBaiDang = link.dataset.maBaiDang;
        showLoading(true);
        try {
            const res  = await fetch(`${API}?action=detail&maBaiDang=${encodeURIComponent(maBaiDang)}`);
            const data = await res.json();
            if (!data.ok) { alert('Không tải được bài đăng.'); return; }
            renderDetail(data.post);
            showOverlay(true);
        } catch { alert('Lỗi mạng.'); }
        finally { showLoading(false); }
    });
});

function renderDetail(post) {
    document.getElementById('pdImage').src    = post.anhBaiDang || '';
    document.getElementById('pdAvatar').src   = post.avatar || '';
    document.getElementById('pdUsername').textContent = post.tenDangNhap || '';
    document.getElementById('pdTime').textContent     = post.thoiGian || '';
    document.getElementById('pdCaption').textContent  = post.noiDung || '';
    document.getElementById('pdPawCount').textContent = `${post.paw_count} lượt paw`;

    const ul = document.getElementById('pdComments');
    if (!post.comments || post.comments.length === 0) {
        ul.innerHTML = '<li style="color:#aaa;font-size:13px">Chưa có bình luận.</li>';
        return;
    }
    ul.innerHTML = post.comments.map(c => `
        <li style="display:flex;gap:8px;align-items:flex-start">
            <img src="${esc(c.avatar)}" style="width:28px;height:28px;border-radius:50%;object-fit:cover;flex-shrink:0">
            <div style="font-size:13px">
                <strong>${esc(c.tenDangNhap)}</strong>
                <span style="margin-left:4px">${esc(c.noiDung)}</span>
                <div style="color:#aaa;font-size:11px;margin-top:2px">${esc(c.thoiGian)}</div>
            </div>
        </li>`).join('');
}

document.getElementById('pdClose').addEventListener('click', () => showOverlay(false));
document.getElementById('postDetailOverlay').addEventListener('click', e => {
    if (e.target === document.getElementById('postDetailOverlay')) showOverlay(false);
});

// ── Xóa bài đăng ─────────────────────────────────────────────────────────
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', async () => {
        if (!confirm('Xóa bài đăng này và cập nhật trạng thái báo cáo?')) return;
        const { maBaiDang, maBaoCao } = btn.dataset;
        showLoading(true);
        try {
            const res  = await fetch(API, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete', maBaiDang, maBaoCao }),
            });
            const data = await res.json();
            if (data.ok) location.reload();
            else alert('Xóa thất bại.');
        } catch { alert('Lỗi mạng.'); }
        finally { showLoading(false); }
    });
});

// ── Từ chối báo cáo ───────────────────────────────────────────────────────
document.querySelectorAll('.btn-reject').forEach(btn => {
    btn.addEventListener('click', async () => {
        if (!confirm('Từ chối báo cáo này?')) return;
        const { maBaoCao } = btn.dataset;
        showLoading(true);
        try {
            const res  = await fetch(API, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'reject', maBaoCao }),
            });
            const data = await res.json();
            if (data.ok) location.reload();
            else alert('Thao tác thất bại.');
        } catch { alert('Lỗi mạng.'); }
        finally { showLoading(false); }
    });
});

// ── Helpers ───────────────────────────────────────────────────────────────
function showOverlay(show) {
    const el = document.getElementById('postDetailOverlay');
    el.style.display = show ? 'flex' : 'none';
}
function showLoading(show) {
    const el = document.getElementById('pdLoading');
    el.style.display = show ? 'flex' : 'none';
}
function esc(str) {
    return String(str ?? '')
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
</body>
</html>