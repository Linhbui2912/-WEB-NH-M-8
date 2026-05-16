document.addEventListener('DOMContentLoaded', () => {
  const viewerId = document.body.dataset.viewerId || 'U002';
  const profileId = document.body.dataset.profileId || '';

  initPostDetailModal(viewerId);
  initFollowFeatures(viewerId, profileId);
});

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text ?? '';
  return div.innerHTML;
}

async function toggleFollow(followerId, targetId, action = 'toggle') {
  const formData = new FormData();
  formData.append('maNguoiTheoDoi', followerId);
  formData.append('maNguoiDuocTheoDoi', targetId);
  formData.append('action', action);

  const res = await fetch('../api/toggle-follow.php', { method: 'POST', body: formData });
  const data = await res.json();
  if (!data.success) {
    throw new Error(data.message || 'Không cập nhật được trạng thái theo dõi.');
  }
  return data;
}

function updateFollowCounts(targetId, data) {
  const followerEl = document.getElementById('followerCount');
  const followingEl = document.getElementById('followingCount');
  const pageProfileId = document.body.dataset.profileId || '';
  const viewer = viewerIdFromPage();

  if (followerEl && targetId === pageProfileId) {
    followerEl.textContent = String(data.targetFollowerCount);
  }
  if (followingEl && viewer === pageProfileId) {
    followingEl.textContent = String(data.followerFollowingCount);
  }
}

function viewerIdFromPage() {
  return document.body.dataset.viewerId || 'U002';
}

function followButtonLabel(user) {
  if (user.viewerFollows) {
    return 'Đang theo dõi';
  }
  if (user.followsViewer) {
    return 'Theo dõi lại';
  }
  return 'Theo dõi';
}

function initFollowFeatures(viewerId, profileId) {
  const followListModalEl = document.getElementById('followListModal');
  const followListTitle = document.getElementById('followListTitle');
  const followListBody = document.getElementById('followListBody');
  const btnFollow = document.getElementById('btnFollow');

  let followListModal = null;
  if (followListModalEl) {
    followListModal = new bootstrap.Modal(followListModalEl);
  }

  async function openFollowList(type) {
    if (!followListModal || !profileId) return;

    followListModal.show();
    followListTitle.textContent = type === 'followers' ? 'Người theo dõi' : 'Đang theo dõi';
    followListBody.innerHTML = `
      <div class="follow-list-loading text-center py-4">
        <div class="spinner-border spinner-border-sm text-secondary" role="status"></div>
      </div>`;

    try {
      const res = await fetch(
        `../api/follow-list.php?profile=${encodeURIComponent(profileId)}&type=${encodeURIComponent(type)}&viewer=${encodeURIComponent(viewerId)}`
      );
      const data = await res.json();
      if (!data.success) {
        followListBody.innerHTML = `<p class="text-muted text-center py-4 mb-0">${escapeHtml(data.message)}</p>`;
        return;
      }

      followListTitle.textContent = data.title;

      if (!data.users.length) {
        followListBody.innerHTML = '<p class="text-muted text-center py-4 mb-0">Chưa có ai trong danh sách này.</p>';
        return;
      }

      followListBody.innerHTML = `<ul class="follow-list list-unstyled mb-0">${data.users
        .map((user) => {
          const isSelf = user.maNguoiDung === viewerId;
          const btnClass = user.viewerFollows ? 'following' : '';
          const actionBtn = isSelf
            ? ''
            : `<button type="button"
                  class="btn btn-sm btn-follow-list ${btnClass}"
                  data-user-id="${escapeHtml(user.maNguoiDung)}"
                  data-following="${user.viewerFollows ? '1' : '0'}">
                  ${escapeHtml(followButtonLabel(user))}
               </button>`;

          return `
            <li class="follow-list-item">
              <a href="${escapeHtml(user.profileUrl)}&viewer=${encodeURIComponent(viewerId)}" class="follow-list-user">
                <img src="${escapeHtml(user.avatar)}" alt="" class="follow-list-avatar" />
                <div class="follow-list-meta">
                  <span class="follow-list-username">${escapeHtml(user.tenDangNhap)}</span>
                  <span class="follow-list-name">${escapeHtml(user.tenHienThi)}</span>
                </div>
              </a>
              ${actionBtn}
            </li>`;
        })
        .join('')}</ul>`;

      followListBody.querySelectorAll('.btn-follow-list').forEach((btn) => {
        btn.addEventListener('click', async () => {
          const targetId = btn.dataset.userId;
          const wasFollowing = btn.dataset.following === '1';
          btn.disabled = true;

          try {
            const result = await toggleFollow(viewerId, targetId, wasFollowing ? 'unfollow' : 'follow');
            btn.dataset.following = result.following ? '1' : '0';
            btn.textContent = result.following ? 'Đang theo dõi' : 'Theo dõi';
            btn.classList.toggle('following', result.following);
            updateFollowCounts(targetId, result);

            if (targetId === profileId && btnFollow) {
              btnFollow.dataset.following = result.following ? '1' : '0';
              btnFollow.textContent = result.following ? 'Đang theo dõi' : 'Theo dõi';
              btnFollow.classList.toggle('following', result.following);
            }
          } catch (err) {
            alert(err.message || 'Lỗi khi theo dõi.');
          } finally {
            btn.disabled = false;
          }
        });
      });
    } catch (err) {
      followListBody.innerHTML = '<p class="text-muted text-center py-4 mb-0">Không tải được danh sách.</p>';
    }
  }

  document.getElementById('btnShowFollowers')?.addEventListener('click', () => openFollowList('followers'));
  document.getElementById('btnShowFollowing')?.addEventListener('click', () => openFollowList('following'));
  document.querySelectorAll('.profile-stat-btn[data-follow-type]').forEach((btn) => {
    btn.addEventListener('click', () => openFollowList(btn.dataset.followType));
  });

  btnFollow?.addEventListener('click', async () => {
    const targetId = btnFollow.dataset.targetId;
    if (!targetId) return;

    const wasFollowing = btnFollow.dataset.following === '1';
    btnFollow.disabled = true;

    try {
      const result = await toggleFollow(viewerId, targetId, wasFollowing ? 'unfollow' : 'follow');
      btnFollow.dataset.following = result.following ? '1' : '0';
      btnFollow.textContent = result.following ? 'Đang theo dõi' : 'Theo dõi';
      btnFollow.classList.toggle('following', result.following);
      updateFollowCounts(targetId, result);
    } catch (err) {
      alert(err.message || 'Lỗi khi theo dõi.');
    } finally {
      btnFollow.disabled = false;
    }
  });
}

function initPostDetailModal(viewerId) {
  const modalEl = document.getElementById('postDetailModal');
  if (!modalEl) return;

  const modal = new bootstrap.Modal(modalEl);
  const loadingEl = document.getElementById('postDetailLoading');
  const imageEl = document.getElementById('postDetailImage');
  const avatarEl = document.getElementById('postDetailAvatar');
  const usernameEl = document.getElementById('postDetailUsername');
  const timeEl = document.getElementById('postDetailTime');
  const captionEl = document.getElementById('postDetailCaption');
  const commentsEl = document.getElementById('postDetailComments');
  const likesEl = document.getElementById('postDetailLikes');
  const commentForm = document.getElementById('commentForm');
  const commentInput = document.getElementById('commentInput');
  const commentPostId = document.getElementById('commentPostId');
  const submitBtn = commentForm?.querySelector('.post-comment-submit');

  let currentPostId = null;

  function setLoading(show) {
    if (loadingEl) loadingEl.classList.toggle('d-none', !show);
  }

  function renderComments(comments) {
    if (!commentsEl) return;
    if (!comments.length) {
      commentsEl.innerHTML = '<li class="text-muted small">Chưa có bình luận. Hãy là người đầu tiên!</li>';
      return;
    }
    commentsEl.innerHTML = comments
      .map(
        (c) => `
      <li>
        <img class="comment-avatar" src="${escapeHtml(c.avatar)}" alt="" />
        <div class="comment-body">
          <div>
            <strong>${escapeHtml(c.tenDangNhap)}</strong>
            <span>${escapeHtml(c.noiDung)}</span>
          </div>
          <span class="comment-time">${escapeHtml(c.thoiGian)}</span>
        </div>
      </li>`
      )
      .join('');
  }

  async function openPost(maBaiDang) {
    currentPostId = maBaiDang;
    commentPostId.value = maBaiDang;
    commentInput.value = '';
    submitBtn.disabled = true;
    modal.show();
    setLoading(true);

    try {
      const res = await fetch(
        `../api/post-detail.php?maBaiDang=${encodeURIComponent(maBaiDang)}&viewer=${encodeURIComponent(viewerId)}`
      );
      const data = await res.json();
      if (!data.success) {
        alert(data.message || 'Không tải được bài đăng.');
        modal.hide();
        return;
      }

      const post = data.post;
      imageEl.src = post.anhBaiDang;
      imageEl.alt = post.noiDung || 'Bài đăng';
      avatarEl.src = post.avatar;
      usernameEl.textContent = post.tenDangNhap;
      usernameEl.href = post.profileUrl;
      timeEl.textContent = post.thoiGian;
      captionEl.innerHTML = post.noiDung
        ? `<strong>${escapeHtml(post.tenDangNhap)}</strong> ${escapeHtml(post.noiDung)}`
        : '';
      likesEl.innerHTML = `<strong>${post.soPhanUng}</strong> lượt paw`;
      renderComments(data.comments);

      const body = document.getElementById('postDetailBody');
      if (body) body.scrollTop = 0;
    } catch (err) {
      alert('Lỗi mạng. Kiểm tra server PHP và kết nối CSDL.');
      modal.hide();
    } finally {
      setLoading(false);
    }
  }

  document.querySelectorAll('.post-item[data-post-id]').forEach((btn) => {
    btn.addEventListener('click', () => openPost(btn.dataset.postId));
  });

  commentInput?.addEventListener('input', () => {
    submitBtn.disabled = !commentInput.value.trim();
  });

  commentForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const noiDung = commentInput.value.trim();
    if (!noiDung || !currentPostId) return;

    submitBtn.disabled = true;
    const formData = new FormData();
    formData.append('maBaiDang', currentPostId);
    formData.append('noiDung', noiDung);
    formData.append('maNguoiDung', viewerId);

    try {
      const res = await fetch('../api/add-comment.php', { method: 'POST', body: formData });
      const data = await res.json();
      if (!data.success) {
        alert(data.message || 'Không gửi được bình luận.');
        submitBtn.disabled = false;
        return;
      }
      appendComment(data.comment);
      commentInput.value = '';
    } catch (err) {
      alert('Lỗi khi gửi bình luận.');
      submitBtn.disabled = false;
    }
  });

  function appendComment(c) {
    if (!commentsEl) return;
    const empty = commentsEl.querySelector('.text-muted');
    if (empty) empty.remove();

    const li = document.createElement('li');
    li.innerHTML = `
      <img class="comment-avatar" src="${escapeHtml(c.avatar)}" alt="" />
      <div class="comment-body">
        <div>
          <strong>${escapeHtml(c.tenDangNhap)}</strong>
          <span>${escapeHtml(c.noiDung)}</span>
        </div>
        <span class="comment-time">${escapeHtml(c.thoiGian)}</span>
      </div>`;
    commentsEl.appendChild(li);
    commentsEl.parentElement?.scrollTo({ top: commentsEl.scrollHeight, behavior: 'smooth' });
  }

  modalEl.addEventListener('hidden.bs.modal', () => {
    currentPostId = null;
    imageEl.src = '';
  });
}
