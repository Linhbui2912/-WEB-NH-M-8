<?php

declare(strict_types=1);

/** @var string $activeNav */
/** @var string $assetPrefix */

$icon = static fn (string $name): string => $assetPrefix . 'assets/icon/' . $name;

$navItem = static function (string $href, string $img, string $tooltip, string $key) use ($activeNav, $icon): void {
    $cls = 'nav-icon' . ($activeNav === $key ? ' active' : '');
    echo '<a href="' . hp_h($href) . '" class="' . hp_h($cls) . '" data-bs-toggle="tooltip" data-bs-title="' . hp_h($tooltip) . '">';
    echo '<img src="' . hp_h($icon($img)) . '" alt="' . hp_h($tooltip) . '">';
    echo '</a>';
};
?>
<aside class="left-sidebar col-2 col-md-1">
  <a class="sidebar-logo mb-4" href="homepage.php" data-bs-toggle="tooltip" data-bs-title="Trang chủ PawConnect">
    <img src="<?= hp_h($icon('PawsConnect.png')) ?>" alt="PawConnect Logo">
  </a>

  <nav class="sidebar-nav">
    <?php $navItem('homepage.php', 'home_5973558.png', 'Home', 'home'); ?>
    <?php $navItem('search.php', 'search.png', 'Search', 'search'); ?>
    <?php $navItem('discover.php', 'discovery_12028921.png', 'Discover', 'discover'); ?>
    <?php $navItem('create-post.php', 'add.png', 'Create New Post', 'create'); ?>
    <?php $navItem('profile.php', 'user.png', 'User Account', 'profile'); ?>
  </nav>

  <a href="settings.php" class="nav-icon settings-icon<?= $activeNav === 'settings' ? ' active' : '' ?>" data-bs-toggle="tooltip" data-bs-title="Settings">
    <img src="<?= hp_h($icon('setting.png')) ?>" alt="Settings">
  </a>
</aside>
