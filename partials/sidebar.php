<?php

declare(strict_types=1);

/** @var string $activeNav */
/** @var bool $isRoot */
/** @var string $assetPrefix */

$homeHref = $isRoot ? 'pages/homepage.php' : 'homepage.php';

$pages = static fn (string $file): string => $isRoot ? 'pages/' . $file : $file;

$icon = static fn (string $name): string => $assetPrefix . 'assets/icon/' . $name;

$navItem = static function (string $href, string $img, string $tooltip, string $key, ?string $extraClass = null) use ($activeNav): void {
    $cls = 'nav-icon' . ($activeNav === $key ? ' active' : '');
    if ($extraClass) {
        $cls .= ' ' . $extraClass;
    }
    echo '<a href="' . h($href) . '" class="' . h($cls) . '" data-bs-toggle="tooltip" data-bs-title="' . h($tooltip) . '">';
    echo '<img src="' . h($img) . '" alt="' . h($tooltip) . '">';
    echo '</a>';
};
?>
<aside class="left-sidebar col-2 col-md-1">
  <a class="sidebar-logo mb-4" href="<?= h($homeHref) ?>" data-bs-toggle="tooltip" data-bs-title="Trang chủ PawConnect">
    <img src="<?= h($icon('PawsConnect.png')) ?>" alt="PawConnect Logo">
  </a>

  <nav class="sidebar-nav">
    <?php $navItem($homeHref, $icon('home_5973558.png'), 'Home', 'home'); ?>
    <?php $navItem($pages('search.php'), $icon('search.png'), 'Search', 'search'); ?>
    <?php $navItem($pages('discover.php'), $icon('discovery_12028921.png'), 'Discover', 'discover'); ?>
    <?php $navItem($pages('create-post.php'), $icon('add.png'), 'Create New Post', 'create'); ?>
    <?php $navItem($pages('profile.php'), $icon('user.png'), 'User Account', 'profile'); ?>
  </nav>

  <a href="<?= h($pages('settings.php')) ?>" class="nav-icon settings-icon<?= $activeNav === 'settings' ? ' active' : '' ?>" data-bs-toggle="tooltip" data-bs-title="Settings">
    <img src="<?= h($icon('setting.png')) ?>" alt="Settings">
  </a>
</aside>
