<?php

declare(strict_types=1);

/**
 * Thư viện mysqli kiểu thủ tục (theo mẫu thường dùng trong lớp PHP).
 *
 * ---------------------------------------------------------------------------
 * Một số hàm đọc dòng từ mysqli_result ($result là kết quả SELECT):
 *
 * (1) mysqli_fetch_assoc($result)
 *     → Mảng kết hợp: key là TÊN CỘT (vd: $row['username']).
 *
 * (2) mysqli_fetch_array($result [, $mode])
 *     → Mảng có cả chỉ số số và tên cột (mặc định MYSQLI_BOTH).
 *     Ví dụ: mysqli_fetch_array($result, MYSQLI_ASSOC) giống assoc.
 *
 * (3) mysqli_fetch_object($result)
 *     → Trả về object stdClass: $obj->username.
 *
 * (4) mysqli_fetch_row($result)
 *     → Mảng chỉ số 0,1,2... theo thứ tự cột trong SELECT.
 *
 * (5) mysqli_fetch_lengths($result)
 *     → Độ dài (byte) của từng cột của DÒNG VỪA ĐỌC (gọi sau một lần fetch).
 *
 * (6) mysqli_fetch_field($result)
 *     → Thông tin meta của “cột tiếp theo” trong tập kết quả (tên bảng, kiểu,...).
 *
 * Trên trang chủ (pages/homepage.php) ta dùng mysqli_fetch_assoc trong vòng while.
 * ---------------------------------------------------------------------------
 */

/**
 * @return mysqli
 */
function taoKetNoi(): mysqli
{
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    mysqli_set_charset($conn, DB_CHARSET);

    return $conn;
}

/**
 * Đóng kết nối MySQL.
 */
function dongKetNoi(mysqli $conn): void
{
    mysqli_close($conn);
}

/**
 * SELECT — trả về mysqli_result (nhớ mysqli_free_result sau khi xong).
 *
 * @return mysqli_result
 */
function chayTruyVanTraVeDL(mysqli $conn, string $sql): mysqli_result
{
    $result = mysqli_query($conn, $sql);
    if (!$result instanceof mysqli_result) {
        throw new RuntimeException('Truy vấn không trả về dữ liệu.');
    }

    return $result;
}

/**
 * INSERT / UPDATE / DELETE — không dùng để đọc hàng.
 */
function chayTruyVanKhongTraVeDL(mysqli $conn, string $sql): bool
{
    if (mysqli_query($conn, $sql) === false) {
        throw new RuntimeException(mysqli_error($conn));
    }

    return true;
}

/**
 * Giải phóng bộ nhớ của tập kết quả SELECT.
 */
function giaiPhongBoNho(?mysqli_result $result): void
{
    if ($result instanceof mysqli_result) {
        mysqli_free_result($result);
    }
}
