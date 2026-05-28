const apiControllers = (document.body.dataset.apiControllers || "../controllers/").replace(/\/?$/, "/");
const viewerId = document.body.dataset.viewerId || "";

function apiUrl(controllerFile) {
  return `${apiControllers}${controllerFile}`;
}

function escapeHtml(text) {
  return String(text)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

function postIdFromElement(el) {
  const card = el?.closest?.(".post-card");
  const id = String(card?.dataset?.postId ?? el?.dataset?.postId ?? "").trim();
  return id || null;
}

function setLikeButtonState(button, liked) {
  if (!button) return;
  button.dataset.liked = liked ? "true" : "false";
  button.classList.toggle("liked", Boolean(liked));
  const icon = button.querySelector("img");
  if (icon) {
    icon.src = liked
      ? icon.dataset.iconLiked || "../assets/icon/pawheart.png"
      : icon.dataset.iconWhite || "../assets/icon/footprint.png";
  }
}

function updatePawCountLabels(postId, count) {
  document.querySelectorAll(`.post-card[data-post-id="${postId}"] [data-paw-count]`).forEach((el) => {
    el.textContent = `${count} lượt paw`;
  });
  const detailLikes = document.getElementById("postDetailLikes");
  if (detailLikes && currentDetailPostId === postId) {
    detailLikes.innerHTML = `<strong>${count}</strong> lượt paw`;
  }
}

async function toggleLike(postId, buttons) {
  const res = await fetch(apiUrl("LikeController.php"), {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ post_id: postId }),
  });
  const data = await res.json();
  if (!data.ok) return null;
  buttons.forEach((btn) => setLikeButtonState(btn, data.liked));
  updatePawCountLabels(postId, data.like_count);
  return data;
}

document.querySelectorAll(".post-card .paw-like-btn").forEach((button) => {
  button.addEventListener("click", async (event) => {
    event.stopPropagation();
    const postId = postIdFromElement(button);
    if (!postId) return;
    const card = button.closest(".post-card");
    const modalBtn = currentDetailPostId === postId ? document.getElementById("postDetailLikeBtn") : null;
    const targets = modalBtn ? [button, modalBtn] : [button];
    try {
      await toggleLike(postId, targets);
    } catch {
      /* ignore */
    }
  });
});

const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
tooltipTriggerList.forEach((el) => {
  new bootstrap.Tooltip(el, { trigger: "hover focus", placement: "auto" });
});

let currentDetailPostId = null;
let selectedReportReason = "";
let reportReasons = [];

const postDetailModalEl = document.getElementById("postDetailModal");
const reportModalEl = document.getElementById("reportModal");
const postDetailModal = postDetailModalEl ? new bootstrap.Modal(postDetailModalEl) : null;
const reportModal = reportModalEl ? new bootstrap.Modal(reportModalEl) : null;

const postDetailLoading = document.getElementById("postDetailLoading");
const postDetailImage = document.getElementById("postDetailImage");
const postDetailAvatar = document.getElementById("postDetailAvatar");
const postDetailUsername = document.getElementById("postDetailUsername");
const postDetailCaption = document.getElementById("postDetailCaption");
const postDetailComments = document.getElementById("postDetailComments");
const postDetailLikes = document.getElementById("postDetailLikes");
const postDetailCommentInput = document.getElementById("postDetailCommentInput");
const postDetailCommentForm = document.getElementById("postDetailCommentForm");
const postDetailPostId = document.getElementById("postDetailPostId");
const postDetailLikeBtn = document.getElementById("postDetailLikeBtn");
const postDetailCommentFocusBtn = document.getElementById("postDetailCommentFocusBtn");
const postDetailReportBtn = document.getElementById("postDetailReportBtn");
const postDetailSubmitBtn = postDetailCommentForm?.querySelector(".post-comment-submit");

function setDetailLoading(show) {
  if (postDetailLoading) {
    postDetailLoading.classList.toggle("d-none", !show);
  }
}

function renderDetailComments(comments) {
  if (!postDetailComments) return;
  if (!comments.length) {
    postDetailComments.innerHTML = '<li class="text-muted small">Chưa có bình luận. Hãy là người đầu tiên!</li>';
    return;
  }
  postDetailComments.innerHTML = comments
    .map(
      (c) => `
      <li>
        <img class="comment-avatar" src="${escapeHtml(c.avatar)}" alt="" />
        <div class="comment-body">
          <div>
            <strong>${escapeHtml(c.tenDangNhap || c.username || "")}</strong>
            <span>${escapeHtml(c.noiDung || c.body || "")}</span>
          </div>
          <span class="comment-time">${escapeHtml(c.thoiGian || c.time || "")}</span>
        </div>
      </li>`
    )
    .join("");
}

async function openPostDetail(postId, focusComment = false) {
  if (!postDetailModal || !postId) return;
  currentDetailPostId = postId;
  if (postDetailPostId) postDetailPostId.value = postId;
  if (postDetailCommentInput) {
    postDetailCommentInput.value = "";
    if (postDetailSubmitBtn) postDetailSubmitBtn.disabled = true;
  }

  postDetailModal.show();
  setDetailLoading(true);

  try {
    const res = await fetch(
      `${apiUrl("PostDetailController.php")}?post_id=${encodeURIComponent(postId)}`
    );
    const data = await res.json();
    if (!data.ok) {
      alert("Không tải được bài đăng.");
      postDetailModal.hide();
      return;
    }

    const post = data.post;
    if (postDetailImage) {
      postDetailImage.src = post.anhBaiDang;
      postDetailImage.alt = post.noiDung || "Bài đăng";
    }
    if (postDetailAvatar) postDetailAvatar.src = post.avatar;
    if (postDetailUsername) {
      postDetailUsername.textContent = post.tenDangNhap;
      postDetailUsername.href = post.profileUrl;
    }
    if (postDetailCaption) {
      postDetailCaption.innerHTML = post.noiDung
        ? `<strong>${escapeHtml(post.tenDangNhap)}</strong> ${escapeHtml(post.noiDung)}`
        : "";
    }
    if (postDetailLikes) {
      postDetailLikes.innerHTML = `<strong>${post.paw_count}</strong> lượt paw`;
    }
    setLikeButtonState(postDetailLikeBtn, post.liked);
    renderDetailComments(data.comments || []);
    updatePawCountLabels(postId, post.paw_count);

    const body = document.getElementById("postDetailBody");
    if (body) body.scrollTop = 0;

    if (focusComment && postDetailCommentInput) {
      setTimeout(() => postDetailCommentInput.focus(), 250);
    }
  } catch {
    alert("Lỗi mạng khi tải chi tiết bài đăng.");
    postDetailModal.hide();
  } finally {
    setDetailLoading(false);
  }
}

document.querySelectorAll(".open-post-detail-btn").forEach((button) => {
  button.addEventListener("click", (event) => {
    event.preventDefault();
    const postId = button.dataset.postId || postIdFromElement(button);
    if (!postId) return;
    const focusComment = button.classList.contains("open-comments-focus");
    void openPostDetail(postId, focusComment);
  });
});

postDetailCommentFocusBtn?.addEventListener("click", () => {
  postDetailCommentInput?.focus();
});

postDetailCommentInput?.addEventListener("input", () => {
  if (postDetailSubmitBtn) {
    postDetailSubmitBtn.disabled = !postDetailCommentInput.value.trim();
  }
});

postDetailCommentForm?.addEventListener("submit", async (event) => {
  event.preventDefault();
  const text = postDetailCommentInput?.value.trim();
  if (!text || !currentDetailPostId) return;

  if (postDetailSubmitBtn) postDetailSubmitBtn.disabled = true;
  try {
    const res = await fetch(apiUrl("CommentController.php"), {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ post_id: currentDetailPostId, body: text }),
    });
    const data = await res.json();
    if (!data.ok) return;
    if (postDetailCommentInput) postDetailCommentInput.value = "";
    renderDetailComments(
      (data.comments || []).map((c) => ({
        tenDangNhap: c.username,
        noiDung: c.body,
        avatar: c.avatar,
        thoiGian: c.time,
      }))
    );
  } catch {
    /* ignore */
  } finally {
    if (postDetailSubmitBtn) {
      postDetailSubmitBtn.disabled = !postDetailCommentInput?.value.trim();
    }
  }
});

postDetailLikeBtn?.addEventListener("click", async (event) => {
  event.stopPropagation();
  if (!currentDetailPostId) return;
  const cardBtn = document.querySelector(
    `.post-card[data-post-id="${currentDetailPostId}"] .paw-like-btn`
  );
  const targets = cardBtn ? [postDetailLikeBtn, cardBtn] : [postDetailLikeBtn];
  try {
    await toggleLike(currentDetailPostId, targets);
  } catch {
    /* ignore */
  }
});

async function loadReportReasons() {
  if (reportReasons.length) return reportReasons;
  const res = await fetch(apiUrl("ReportController.php"));
  const data = await res.json();
  if (data.ok && Array.isArray(data.reasons)) {
    reportReasons = data.reasons;
  }
  return reportReasons;
}

function renderReportReasons() {
  const list = document.getElementById("reportReasonList");
  const submitBtn = document.getElementById("reportSubmitBtn");
  if (!list) return;
  selectedReportReason = "";
  if (submitBtn) submitBtn.disabled = true;

  list.innerHTML = reportReasons
    .map(
      (reason, index) => `
      <li>
        <button type="button" class="report-reason-btn" data-reason-index="${index}">
          ${escapeHtml(reason)}
        </button>
      </li>`
    )
    .join("");

  list.querySelectorAll(".report-reason-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      list.querySelectorAll(".report-reason-btn").forEach((b) => b.classList.remove("selected"));
      btn.classList.add("selected");
      selectedReportReason = reportReasons[Number.parseInt(btn.dataset.reasonIndex || "0", 10)] || "";
      if (submitBtn) submitBtn.disabled = selectedReportReason === "";
    });
  });
}

postDetailReportBtn?.addEventListener("click", async () => {
  if (!currentDetailPostId) return;
  await loadReportReasons();
  renderReportReasons();
  reportModal?.show();
});

document.getElementById("reportSubmitBtn")?.addEventListener("click", async () => {
  if (!currentDetailPostId || !selectedReportReason) return;
  const submitBtn = document.getElementById("reportSubmitBtn");
  if (submitBtn) submitBtn.disabled = true;
  try {
    const res = await fetch(apiUrl("ReportController.php"), {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ post_id: currentDetailPostId, reason: selectedReportReason }),
    });
    const data = await res.json();
    if (data.ok) {
      reportModal?.hide();
      alert(data.message || "Đã gửi báo cáo.");
    }
  } catch {
    /* ignore */
  } finally {
    if (submitBtn) submitBtn.disabled = false;
  }
});
