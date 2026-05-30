<?php

declare(strict_types=1);

/** @var string $activeNav */
$icon = static fn (string $name): string => asset_url('icon/' . $name);
$currentPage = basename($_SERVER['PHP_SELF']);
$isHomePage = str_contains($_SERVER['REQUEST_URI'], 'homepage');
?>
<aside class="left-sidebar col-2 col-md-1">
  <a class="sidebar-logo mb-4" href="../views/homepage.php" data-bs-toggle="tooltip" data-bs-title="PawConnect">
    <img src="<?= h($icon('PawsConnect.png')) ?>" alt="PawConnect Logo" />
  </a>
  <nav class="sidebar-nav">
   <a href="../views/homepage.php" class="nav-icon<?= $isHomePage ? ' active' : '' ?>">
  <img src="<?= h($icon('home_5973558.png')) ?>" alt="Home" />
</a>
    <a href="../controllers/SearchController.php" class="nav-icon" data-bs-toggle="tooltip" data-bs-title="Search">
      <img src="<?= h($icon('search.png')) ?>" alt="Search" />
    </a>
    <a href="../controllers/dc_discover_controller.php" class="nav-icon" data-bs-toggle="tooltip" data-bs-title="Discover">
      <img src="<?= h($icon('discovery_12028921.png')) ?>" alt="Discover" />
    </a>
    <a href="../views/create-post.php" class="nav-icon" data-bs-toggle="tooltip" data-bs-title="Create">
      <img src="<?= h($icon('add.png')) ?>" alt="Create" />
    </a>
<a href="../views/profile.php" class="nav-icon<?= !$isHomePage ? ' active' : '' ?>">
  <img src="<?= h($icon('user.png')) ?>" alt="Account" />
</a>
  </nav>
  <button type="button" id="btnOpenSettings" class="nav-icon settings-icon border-0 bg-transparent p-0" data-bs-toggle="tooltip" data-bs-title="Settings">
    <img src="<?= h($icon('setting.png')) ?>" alt="Settings" />
  </button>
</aside>

