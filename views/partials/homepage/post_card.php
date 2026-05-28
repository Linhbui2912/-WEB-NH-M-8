<?php

declare(strict_types=1);

/** @var array<string,mixed> $post */
/** @var string $assetPrefix */

$postId = (string) ($post['id'] ?? '');
$userId = (string) ($post['user_id'] ?? '');
$liked = ((int) ($post['liked'] ?? 0)) === 1;
$pawCount = (int) ($post['paw_count'] ?? 0);

// // ĐÚNG - dùng đúng key từ FeedModel
// $postId = (string) ($post['maBaiDang'] ?? '');
// $userId = (string) ($post['maNguoiDung'] ?? '');
// $liked  = ((int) ($post['liked'] ?? 0)) === 1;
// $pawCount = (int) ($post['paw_count'] ?? 0);

// caption:
// $post['noiDung'] thay vì $post['caption']
// $post['tenDangNhap'] thay vì $post['username']

$assets = $assetPrefix . 'assets/';
$postImageUrl = hp_post_image_url((string) ($post['post_file'] ?? ''), $assetPrefix);
$avatarUrl = hp_avatar_url((string) ($post['avatar_file'] ?? ''), $assetPrefix);
$profileHref = 'profile.php?id=' . rawurlencode($userId);
?>
<article class="post-card" data-post-id="<?= hp_h($postId) ?>">
  <header class="post-header">
    <a href="<?= hp_h($profileHref) ?>" class="avatar-link">
      <img class="avatar" src="<?= hp_h($avatarUrl) ?>" alt="Avatar">
    </a>
    <div>
      <h2 class="username">
        <a href="<?= hp_h($profileHref) ?>"><?= hp_h((string) ($post['username'] ?? '')) ?></a>
      </h2>
    </div>
    <button type="button" class="btn post-more" aria-label="More">•••</button>
  </header>

  <button type="button" class="post-image-btn open-post-detail-btn" data-post-id="<?= hp_h($postId) ?>" aria-label="Xem chi tiết bài đăng">
    <img class="post-image" src="<?= hp_h($postImageUrl) ?>" alt="Bài đăng thú cưng">
  </button>

  <div class="post-actions">
    <button type="button"
      class="icon-btn paw-like-btn<?= $liked ? ' liked' : '' ?>"
      data-liked="<?= $liked ? 'true' : 'false' ?>"
      aria-label="Like post">
      <img
        src="<?= hp_h($assets) ?>icon/<?= $liked ? 'pawheart.png' : 'footprint.png' ?>"
        data-icon-white="<?= hp_h($assets) ?>icon/footprint.png"
        data-icon-liked="<?= hp_h($assets) ?>icon/pawheart.png"
        alt="Like">
    </button>
    <button type="button"
      class="icon-btn open-post-detail-btn open-comments-focus"
      data-post-id="<?= hp_h($postId) ?>"
      aria-label="Comment">
      <img src="<?= hp_h($assets) ?>icon/message.png" alt="Comment">
    </button>
  </div>

  <p class="post-paw-count mb-1" data-paw-count><?= hp_h((string) $pawCount) ?> lượt paw</p>

  <div class="post-caption">
    <p class="mb-1">
      <strong><a href="<?= hp_h($profileHref) ?>" class="caption-user-link"><?= hp_h((string) ($post['username'] ?? '')) ?></a></strong>
    </p>
    <p class="mb-0"><?= nl2br(hp_h((string) ($post['caption'] ?? ''))) ?></p>
  </div>
</article>
