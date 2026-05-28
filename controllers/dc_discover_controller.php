<?php
session_start();

if (!isset($_SESSION['maNguoiDung'])) {
    header("Location: ../views/dangnhap.php");
    exit();
}

require_once "../modules/db_module.php";
require_once "../models/dc_discover_model.php";

$link = null;
taoKetNoi($link);

$model = new DiscoverModel();
$danhSachBaiDang = $model->layDanhSachBaiDang($link);

giaiPhongBoNho($link, null);

require_once "../views/dc_discover_view.php";
?>