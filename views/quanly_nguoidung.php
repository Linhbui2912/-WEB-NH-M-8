<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người dùng - PawConnect Admin</title>
    <link rel="stylesheet" href="../assets/css/admin_style.css">
</head>
<body>

    <?php require_once __DIR__ . '/admin_sidebar.php'; ?>

    <div class="admin-main-content">
        <h1 class="page-title">Quản lý người dùng</h1>
        
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã người dùng</th>
                        <th>Tên người dùng</th>
                        <th>Trạng thái</th>
                        <th>Người báo cáo</th>
                        <th>Lý do báo cáo</th>
                        <th>Ngày báo cáo</th>
                        <th>Xử lý</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($danhSachNguoiDung)): ?>
                        <?php foreach ($danhSachNguoiDung as $user): ?>
                            <tr class="<?= $user['trangThai'] === 'bi_khoa' ? 'row-alert' : '' ?>">
                                
                                <td class="text-blue">
                                    <a href="../views/profile.php?id=<?= htmlspecialchars($user['maNguoiDung']) ?>" target="_blank" style="text-decoration: none; color: inherit;">
                                        <?= htmlspecialchars($user['maNguoiDung']) ?>
                                    </a>
                                </td>
                                
                                <td><?= htmlspecialchars($user['tenDangNhap']) ?></td>
                                
                                <td class="<?= $user['trangThai'] === 'bi_khoa' ? 'status-locked' : 'status-active' ?>">
                                    <?= $user['trangThai'] === 'hoat_dong' ? 'Hoạt động' : 'Bị khóa' ?>
                                </td>
                                
                                <td><?= htmlspecialchars($user['nguoiBaoCao']) ?></td>
                                <td><?= htmlspecialchars($user['lyDoBaoCao']) ?></td>
                                <td><?= htmlspecialchars($user['ngayBaoCao']) ?></td>
                                
                                <td class="action-buttons">
                                    <form action="../controllers/admin_user_controller.php" method="POST" style="margin: 0;">
                                        <input type="hidden" name="action" value="toggle_status">
                                        <input type="hidden" name="maNguoiDung" value="<?= htmlspecialchars($user['maNguoiDung']) ?>">
                                        <input type="hidden" name="trangThaiHienTai" value="<?= htmlspecialchars($user['trangThai']) ?>">
                                        
                                        <?php if ($user['trangThai'] === 'hoat_dong'): ?>
                                            <button type="submit" class="btn-lock">Khóa</button>
                                        <?php else: ?>
                                            <button type="submit" class="btn-unlock">Mở khóa</button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 30px; color: #666;">
                                Hiện tại không có người dùng nào bị báo cáo.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
