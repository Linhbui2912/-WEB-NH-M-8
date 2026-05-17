<?php

declare(strict_types=1);

/** @var array<string,mixed> $post */
/** @var string $assetPrefix */
/** @var string $rootDir */

$postId = (string) ($post['id'] ?? '');
$userId = (string) ($post['user_id'] ?? '');
$liked = ((int) ($post['liked'] ?? 0)) === 1;
$saved = ((int) ($post['saved'] ?? 0)) === 1;

$pawRoot = $rootDir ?? dirname(__DIR__);
$assets = $assetPrefix . 'assets/';
$postSrc = paw_feed_post_src((string) ($post['post_file'] ?? ''), $pawRoot);
$avatarSrc = paw_feed_avatar_src((string) ($post['avatar_file'] ?? ''), $pawRoot);
$postImageUrl = $assetPrefix . ltrim($postSrc, './');
$avatarUrl = $assetPrefix . ltrim($avatarSrc, './');
$when = time_ago((string) ($post['created_at'] ?? ''));
$profileHref = 'profile.php?id=' . rawurlencode($userId);
?>
<article class="post-card" data-post-id="<?= h($postId) ?>">
  <header class="post-header">
    <a href="<?= h($profileHref) ?>" class="avatar-link">
      <img class="avatar" src="<?= h($avatarUrl) ?>" alt="Avatar">
    </a>
    <div>
      <h2 class="username">
        <a href="<?= h($profileHref) ?>"><?= h((string) ($post['username'] ?? '')) ?></a>
      </h2>
      <?php if ($when !== ''): ?>
      <p class="post-time"><?= h($when) ?></p>
      <?php endif; ?>
    </div>
    <button type="button" class="btn post-more" aria-label="More">•••</button>
  </header>

  <img class="post-image" src="<?= h($postImageUrl) ?>" alt="Bài đăng thú cưng">

  <div class="post-actions">
    <button type="button"
      class="icon-btn paw-like-btn"
      data-liked="<?= $liked ? 'true' : 'false' ?>"
      aria-label="Like post">
      <img
        src="<?= h($assets) ?>icon/<?= $liked ? 'pawheart.png' : 'footprint.png' ?>"
        data-icon-white="<?= h($assets) ?>icon/footprint.png"
        data-icon-liked="<?= h($assets) ?>icon/pawheart.png"
        alt="Like">
    </button>
    <button type="button"
      class="icon-btn open-comments-btn"
      data-post-id="<?= h($postId) ?>"
      data-bs-toggle="modal"
      data-bs-target="#commentsModal"
      aria-label="Comment">
      <img src="<?= h($assets) ?>icon/message.png" alt="Comment">
    </button>
    <button type="button"
      class="icon-btn save-post-btn<?= $saved ? ' is-saved' : '' ?>"
      data-saved="<?= $saved ? 'true' : 'false' ?>"
      aria-label="Lưu bài viết">
      <img
        src="<?= h($assets) ?>icon/<?= $saved ? 'saved.png' : 'save.png' ?>"
        data-icon-save="<?= h($assets) ?>icon/save.png"
        data-icon-saved="<?= h($assets) ?>icon/saved.png"
        alt="Lưu">
    </button>
  </div>

  <div class="post-caption">
    <p class="mb-1">
      <strong><a href="<?= h($profileHref) ?>" class="caption-user-link"><?= h((string) ($post['username'] ?? '')) ?></a></strong>
    </p>
    <p class="mb-0"><?= nl2br(h((string) ($post['caption'] ?? ''))) ?></p>
  </div>
</article>
