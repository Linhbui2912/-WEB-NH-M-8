document.addEventListener('DOMContentLoaded', () => {
  const viewerId = document.body.dataset.viewerId || '';
  const profileId = document.body.dataset.profileId || '';
  const isOwnProfile = document.body.dataset.isOwnProfile === '1';
  const apiBase = document.body.dataset.apiBase || '../controllers/';

  initPostDetailModal(viewerId, apiBase);
  initFollowFeatures(viewerId, profileId, apiBase);
  initReportModals(apiBase);
   document.getElementById('btnOpenEditProfile')?.addEventListener('click', () => {
    const editModalEl = document.getElementById('editProfileModal');
    if (!editModalEl) return;
    const editModal = bootstrap.Modal.getInstance(editModalEl)
      || new bootstrap.Modal(editModalEl);
    editModal.show();
  });
});

function apiUrl(path, apiBase) {
  return `${apiBase}${path}`;
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text ?? '';
  return div.innerHTML;
}

function showToast(message, type = 'success') {
  const wrap = document.getElementById('profileToastWrap');
  if (!wrap) return;
  const id = `toast-${Date.now()}`;
  const bg = type === 'success' ? 'text-bg-success' : 'text-bg-danger';
  wrap.insertAdjacentHTML(
    'beforeend',
    `<div id="${id}" class="toast align-items-center ${bg} border-0" role="alert">
      <div class="d-flex"><div class="toast-body">${escapeHtml(message)}</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>`
  );
  const el = document.getElementById(id);
  const toast = new bootstrap.Toast(el, { delay: 3500 });
  toast.show();
  el.addEventListener('hidden.bs.toast', () => el.remove());
}

async function toggleFollow(followerId, targetId, action, apiBase) {
  const formData = new FormData();
  formData.append('maNguoiTheoDoi', followerId);
  formData.append('maNguoiDuocTheoDoi', targetId);
  formData.append('action', action);
  const res = await fetch(apiUrl('profile_ajax.php?action=toggle-follow', apiBase), {
    method: 'POST',
    body: formData,
  });
  const data = await res.json();
  if (!data.success) throw new Error(data.message || 'Lỗi theo dõi.');
  return data;
}



function initReportModals(apiBase) {
  setupReportModal({
    openBtnId: 'btnReportPost',
    modalId: 'reportPostModal',
    listId: 'reportPostReasons',
    submitId: 'btnSubmitReportPost',
    buildPayload: (reason) => {
      const fd = new FormData();
      fd.append('action', 'report-post');
      fd.append('maBaiDang', currentReportPostId || '');
      fd.append('lyDoBaoCao', reason);
      return fd;
    },
    apiBase,
    onSuccess: () => {
      bootstrap.Modal.getInstance(document.getElementById('reportPostModal'))?.hide();
    },
  });

  setupReportModal({
    openBtnId: 'btnReportAccount',
    modalId: 'reportAccountModal',
    listId: 'reportAccountReasons',
    submitId: 'btnSubmitReportAccount',
    buildPayload: (reason) => {
      const fd = new FormData();
      fd.append('action', 'report-account');
      fd.append('maNguoiBiBaoCao', document.body.dataset.profileId || '');
      fd.append('lyDoBaoCao', reason);
      return fd;
    },
    apiBase,
    onSuccess: () => {
      bootstrap.Modal.getInstance(document.getElementById('reportAccountModal'))?.hide();
    },
  });
}

let currentReportPostId = null;

function setupReportModal({ openBtnId, modalId, listId, submitId, buildPayload, apiBase, onSuccess }) {
  const modalEl = document.getElementById(modalId);
  const listEl = document.getElementById(listId);
  const submitBtn = document.getElementById(submitId);
  if (!modalEl || !listEl || !submitBtn) return;

  const modal = new bootstrap.Modal(modalEl, { backdrop: true });
  let selectedReason = '';

  const openBtn = document.getElementById(openBtnId);
  if (openBtn) {
    openBtn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      selectedReason = '';
      submitBtn.disabled = true;
      listEl.querySelectorAll('.profile-report-reason-btn').forEach((b) => b.classList.remove('selected'));
      modal.show();
    });
  }

  listEl.querySelectorAll('.profile-report-reason-btn').forEach((btn) => {
    btn.addEventListener('click', () => {
      listEl.querySelectorAll('.profile-report-reason-btn').forEach((b) => b.classList.remove('selected'));
      btn.classList.add('selected');
      selectedReason = btn.dataset.reason || '';
      submitBtn.disabled = !selectedReason;
    });
  });

  submitBtn.addEventListener('click', async () => {
    if (!selectedReason) return;
    submitBtn.disabled = true;
    try {
      const res = await fetch(apiUrl('report_controller.php', apiBase), {
        method: 'POST',
        body: buildPayload(selectedReason),
      });
      const data = await res.json();
      if (!data.success) {
        showToast(data.message || 'Gửi báo cáo thất bại.', 'danger');
        return;
      }
      showToast(data.message || 'Đã gửi báo cáo.');
      onSuccess?.();
    } catch {
      showToast('Lỗi mạng khi gửi báo cáo.', 'danger');
    } finally {
      submitBtn.disabled = false;
    }
  });
}

function initFollowFeatures(viewerId, profileId, apiBase) {
  const followListModalEl = document.getElementById('followListModal');
  const followListTitle = document.getElementById('followListTitle');
  const followListBody = document.getElementById('followListBody');
  const btnFollow = document.getElementById('btnFollow');
  let followListModal = followListModalEl ? new bootstrap.Modal(followListModalEl) : null;

  async function openFollowList(type) {
    if (!followListModal || !profileId) return;
    followListModal.show();
    followListTitle.textContent = type === 'followers' ? 'Người theo dõi' : 'Đang theo dõi';
    followListBody.innerHTML =
      '<div class="text-center py-4"><div class="spinner-border spinner-border-sm"></div></div>';

    try {
      const res = await fetch(
        apiUrl(
          `profile_ajax.php?action=follow-list&profile=${encodeURIComponent(profileId)}&type=${encodeURIComponent(type)}`,
          apiBase
        )
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
      followListBody.innerHTML = `<ul class="list-unstyled mb-0">${data.users
        .map((user) => {
          const isSelf = user.maNguoiDung === viewerId;
          const btnClass = user.viewerFollows ? 'following' : '';
          const actionBtn = isSelf
            ? ''
            : `<button type="button" class="btn btn-sm btn-follow-list ${btnClass}"
                  data-user-id="${escapeHtml(user.maNguoiDung)}" data-following="${user.viewerFollows ? '1' : '0'}">
                  ${user.viewerFollows ? 'Đang theo dõi' : 'Theo dõi'}
               </button>`;
          return `<li class="follow-list-item">
          <a href="profile.php?user=${encodeURIComponent(user.tenDangNhap)}" class="follow-list-user">        <img src="${escapeHtml(user.avatar)}" alt="" class="follow-list-avatar" />
        <div class="follow-list-meta">
            <span class="follow-list-username">${escapeHtml(user.tenDangNhap)}</span>
            <span class="follow-list-name">${escapeHtml(user.tenHienThi)}</span>
        </div>
    </a>${actionBtn}</li>`;
        })
        .join('')}</ul>`;

      followListBody.querySelectorAll('.btn-follow-list').forEach((btn) => {
        btn.addEventListener('click', async () => {
          const targetId = btn.dataset.userId;
          const wasFollowing = btn.dataset.following === '1';
          btn.disabled = true;
          try {
            const result = await toggleFollow(viewerId, targetId, wasFollowing ? 'unfollow' : 'follow', apiBase);
            btn.dataset.following = result.following ? '1' : '0';
            btn.textContent = result.following ? 'Đang theo dõi' : 'Theo dõi';
            btn.classList.toggle('following', result.following);
            if (targetId === profileId && btnFollow) {
              btnFollow.dataset.following = result.following ? '1' : '0';
              btnFollow.textContent = result.following ? 'Đang theo dõi' : 'Theo dõi';
              btnFollow.classList.toggle('following', result.following);
            }
            document.querySelectorAll('#followerCount, #followerCountMobile').forEach(el => {
              el.textContent = String(result.targetFollowerCount);
            });
          } catch (err) {
            alert(err.message);
          } finally {
            btn.disabled = false;
          }
        });
      });
    } catch {
      followListBody.innerHTML = '<p class="text-muted text-center py-4 mb-0">Không tải được danh sách.</p>';
    }
  }

  document.getElementById('btnShowFollowers')?.addEventListener('click', () => openFollowList('followers'));
  document.getElementById('btnShowFollowing')?.addEventListener('click', () => openFollowList('following'));
  document.getElementById('btnShowFollowersMobile')?.addEventListener('click', () => openFollowList('followers'));
  document.getElementById('btnShowFollowingMobile')?.addEventListener('click', () => openFollowList('following'));

  btnFollow?.addEventListener('click', async () => {
    const targetId = btnFollow.dataset.targetId;
    if (!targetId) return;
    const wasFollowing = btnFollow.dataset.following === '1';
    btnFollow.disabled = true;
    try {
      const result = await toggleFollow(viewerId, targetId, wasFollowing ? 'unfollow' : 'follow', apiBase);
      btnFollow.dataset.following = result.following ? '1' : '0';
      btnFollow.textContent = result.following ? 'Đang theo dõi' : 'Theo dõi';
      btnFollow.classList.toggle('following', result.following);
      document.querySelectorAll('#followerCount, #followerCountMobile').forEach(el => {
        el.textContent = String(result.targetFollowerCount);
      });
    } catch (err) {
      alert(err.message);
    } finally {
      btnFollow.disabled = false;
    }
  });
}

function initPostDetailModal(viewerId, apiBase) {
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
  const btnLike = document.getElementById('btnLikePost');
  const likeImg = btnLike?.querySelector('img');

  let currentPostId = null;

  function setLoading(show) {
    loadingEl?.classList.toggle('d-none', !show);
  }

  function setLikeUi(liked, count) {
    btnLike?.classList.toggle('liked', liked);
    if (likeImg) {
      likeImg.src = liked ? likeImg.dataset.iconLiked : likeImg.dataset.iconDefault;
    }
    if (likesEl) likesEl.innerHTML = `<strong>${count}</strong> lượt paw`;
  }

  function renderComments(comments) {
    if (!commentsEl) return;
    if (!comments.length) {
      commentsEl.innerHTML = '<li class="text-muted small">Chưa có bình luận. Hãy là người đầu tiên!</li>';
      return;
    }
    commentsEl.innerHTML = comments
      .map(
        (c) => `<li>
        <img class="comment-avatar" src="${escapeHtml(c.avatar)}" alt="" />
        <div class="comment-body">
          <div><strong>${escapeHtml(c.tenDangNhap)}</strong> <span>${escapeHtml(c.noiDung)}</span></div>
          <span class="comment-time">${escapeHtml(c.thoiGian)}</span>
        </div></li>`
      )
      .join('');
  }

  async function openPost(maBaiDang) {
    currentPostId = maBaiDang;
    currentReportPostId = maBaiDang;
    commentPostId.value = maBaiDang;
    commentInput.value = '';
    submitBtn.disabled = true;
    modal.show();
    setLoading(true);

    try {
      const res = await fetch(
        apiUrl(`profile_ajax.php?action=post-detail&maBaiDang=${encodeURIComponent(maBaiDang)}`, apiBase)
      );
      const data = await res.json();
      if (!data.success) {
        alert(data.message || 'Không tải được bài đăng.');
        modal.hide();
        return;
      }
      const post = data.post;
      imageEl.src = post.anhBaiDang;
      avatarEl.src = post.avatar;
      usernameEl.textContent = post.tenDangNhap;
      usernameEl.href = post.profileUrl;
      timeEl.textContent = post.thoiGian;
      captionEl.innerHTML = post.noiDung
        ? `<strong>${escapeHtml(post.tenDangNhap)}</strong> ${escapeHtml(post.noiDung)}`
        : '';
      setLikeUi(!!post.daThich, post.soPhanUng);
      renderComments(data.comments);
      document.getElementById('postDetailBody')?.scrollTo(0, 0);
    } catch {
      alert('Lỗi mạng.');
      modal.hide();
    } finally {
      setLoading(false);
    }
  }

  document.querySelectorAll('.post-item[data-post-id]').forEach((btn) => {
    btn.addEventListener('click', () => openPost(btn.dataset.postId));
  });

  document.getElementById('btnFocusComment')?.addEventListener('click', () => {
    commentInput?.focus();
  });

  btnLike?.addEventListener('click', async () => {
    if (!currentPostId) return;
    const formData = new FormData();
    formData.append('maBaiDang', currentPostId);
    try {
      const res = await fetch(apiUrl('profile_ajax.php?action=toggle-like', apiBase), {
        method: 'POST',
        body: formData,
      });
      const data = await res.json();
      if (data.success) setLikeUi(data.liked, data.soPhanUng);
    } catch {
      showToast('Không cập nhật được lượt paw.', 'danger');
    }
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
    try {
      const res = await fetch(apiUrl('profile_ajax.php?action=add-comment', apiBase), {
        method: 'POST',
        body: formData,
      });
      const data = await res.json();
      if (!data.success) {
        alert(data.message || 'Không gửi được bình luận.');
        submitBtn.disabled = false;
        return;
      }
      const empty = commentsEl.querySelector('.text-muted');
      if (empty) empty.remove();
      const li = document.createElement('li');
      li.innerHTML = `<img class="comment-avatar" src="${escapeHtml(data.comment.avatar)}" alt="" />
        <div class="comment-body"><div><strong>${escapeHtml(data.comment.tenDangNhap)}</strong>
        <span>${escapeHtml(data.comment.noiDung)}</span></div>
        <span class="comment-time">${escapeHtml(data.comment.thoiGian)}</span></div>`;
      commentsEl.appendChild(li);
      commentInput.value = '';
    } catch {
      alert('Lỗi khi gửi bình luận.');
      submitBtn.disabled = false;
    }
  });

  modalEl.addEventListener('hidden.bs.modal', () => {
    currentPostId = null;
    currentReportPostId = null;
    imageEl.src = '';
  });
}