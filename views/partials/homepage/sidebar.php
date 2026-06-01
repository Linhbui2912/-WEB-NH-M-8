<?php

declare(strict_types=1);

/** @var string $activeNav */
/** @var string $assetPrefix */

/**
 * Component: Sidebar
 * @var string $activeNav
 */

// Tự động gọi hàm lấy URL gốc từ file helper (Kết quả sẽ là http://localhost:3000)
if (!isset($projectUrl) && function_exists('project_base_url')) {
    $projectUrl = project_base_url();
}

// Tạo helper sinh URL tuyệt đối (Nếu không có $projectUrl thì dùng fallback dấu / ở đầu)
$baseUrl = isset($projectUrl) ? rtrim($projectUrl, '/') : '';
$url     = static fn(string $path): string => hp_h($baseUrl . '/' . ltrim($path, '/'));
$icon    = static fn(string $name): string => hp_h($baseUrl . '/assets/icon/' . $name);
?>
<aside class="left-sidebar col-2 col-md-1">
 <a class="sidebar-logo" href="<?= $url('views/homepage.php') ?>" data-bs-toggle="tooltip" data-bs-title="Trang chủ PawConnect">
        <img src="<?= $icon('PawsConnect.png') ?>" alt="PawConnect" />
  </a>

  <nav class="sidebar-nav">
    <a href="<?= $url('views/homepage.php') ?>" class="nav-icon<?= $activeNav === 'home' ? ' active' : '' ?>" data-bs-toggle="tooltip" data-bs-title="Home">
            <img src="<?= $icon('home_5973558.png') ?>" alt="Home" />
        </a>
        
        <a href="<?= $url('controllers/SearchController.php') ?>" class="nav-icon<?= $activeNav === 'search' ? ' active' : '' ?>" data-bs-toggle="tooltip" data-bs-title="Search">
            <img src="<?= $icon('search.png') ?>" alt="Search" />
        </a>
        
        <a href="<?= $url('controllers/dc_discover_controller.php') ?>" class="nav-icon<?= $activeNav === 'discover' ? ' active' : '' ?>" data-bs-toggle="tooltip" data-bs-title="Discover">
            <img src="<?= $icon('discovery_12028921.png') ?>" alt="Discover" />
        </a>
        
        <a href="<?= $url('views/create-post.php') ?>" class="nav-icon<?= $activeNav === 'create' ? ' active' : '' ?>" data-bs-toggle="tooltip" data-bs-title="Create">
            <img src="<?= $icon('add.png') ?>" alt="Create" />
        </a>
        
        <a href="<?= $url('views/profile.php') ?>" class="nav-icon<?= $activeNav === 'profile' ? ' active' : '' ?>" data-bs-toggle="tooltip" data-bs-title="Profile">
            <img src="<?= $icon('user.png') ?>" alt="Profile" />
        </a>
  </nav>

       <button type="button" id="btnOpenSettings" class="nav-icon settings-icon<?= $activeNav === 'settings' ? ' active' : '' ?>" data-bs-toggle="tooltip" data-bs-title="Settings" style="background: none; border: none; padding: 0; cursor: pointer; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;">
        <img src="<?= $icon('setting.png') ?>" alt="Settings" style="width: 26px; height: 26px; object-fit: contain;" />
    </button>
    </button>
</aside>
