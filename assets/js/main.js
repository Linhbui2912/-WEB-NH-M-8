const apiBaseRaw = document.body.dataset.apiBase ?? "";

function apiUrl(file) {
  const base = apiBaseRaw.endsWith("/") ? apiBaseRaw.slice(0, -1) : apiBaseRaw;
  return `${base ? `${base}/` : ""}api/${file}`;
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

const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
tooltipTriggerList.forEach((el) => {
  new bootstrap.Tooltip(el, {
    trigger: "hover focus",
    placement: "auto",
  });
});

document.querySelectorAll(".paw-like-btn").forEach((button) => {
  button.addEventListener("click", async () => {
    const postId = postIdFromElement(button);
    if (!postId) return;

    try {
      const res = await fetch(apiUrl("like.php"), {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ post_id: postId }),
      });
      const data = await res.json();
      if (!data.ok) return;

      button.dataset.liked = data.liked ? "true" : "false";
      const icon = button.querySelector("img");
      if (icon) {
        icon.src = data.liked
          ? icon.dataset.iconLiked || "./assets/icon/pawheart.png"
          : icon.dataset.iconWhite || "./assets/icon/footprint.png";
      }
    } catch {
      /* ignore */
    }
  });
});

document.querySelectorAll(".save-post-btn").forEach((button) => {
  button.addEventListener("click", async () => {
    const postId = postIdFromElement(button);
    if (!postId) return;

    try {
      const res = await fetch(apiUrl("save.php"), {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ post_id: postId }),
      });
      const data = await res.json();
      if (!data.ok) return;

      button.dataset.saved = data.saved ? "true" : "false";
      button.classList.toggle("is-saved", Boolean(data.saved));
      const icon = button.querySelector("img");
      if (icon) {
        icon.src = data.saved
          ? icon.dataset.iconSaved || "./assets/icon/saved.png"
          : icon.dataset.iconSave || "./assets/icon/save.png";
      }
    } catch {
      /* ignore */
    }
  });
});

let activePostId = null;

const commentsList = document.getElementById("commentsList");
const commentInput = document.getElementById("commentInput");
const submitCommentBtn = document.getElementById("submitCommentBtn");

function renderComments(items) {
  if (!commentsList) return;
  commentsList.innerHTML = "";

  if (!items.length) {
    commentsList.innerHTML = '<li class="comment-empty">Chưa có bình luận nào.</li>';
    return;
  }

  items.forEach((comment) => {
    const li = document.createElement("li");
    const user = escapeHtml(comment.username ?? "");
    const body = escapeHtml(comment.body ?? "");
    li.innerHTML = `<span class="comment-user">${user}</span>${body}`;
    commentsList.appendChild(li);
  });
}

async function loadComments(postId) {
  if (!commentsList) return;
  commentsList.innerHTML = '<li class="comment-empty">Đang tải...</li>';
  try {
    const res = await fetch(`${apiUrl("comments.php")}?post_id=${encodeURIComponent(postId)}`);
    const data = await res.json();
    if (!data.ok) {
      commentsList.innerHTML = '<li class="comment-empty">Không tải được bình luận.</li>';
      return;
    }
    renderComments(data.comments ?? []);
  } catch {
    commentsList.innerHTML = '<li class="comment-empty">Không tải được bình luận.</li>';
  }
}

document.querySelectorAll(".open-comments-btn").forEach((button) => {
  button.addEventListener("click", () => {
    activePostId = postIdFromElement(button);
    if (!activePostId) return;

    if (commentInput) {
      commentInput.disabled = false;
      commentInput.value = "";
      commentInput.focus();
    }
    if (submitCommentBtn) submitCommentBtn.disabled = false;

    void loadComments(activePostId);
  });
});

async function submitComment() {
  if (!activePostId || !commentInput) return;
  const text = commentInput.value.trim();
  if (!text) return;

  try {
    const res = await fetch(apiUrl("comments.php"), {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ post_id: activePostId, body: text }),
    });
    const data = await res.json();
    if (!data.ok) return;

    commentInput.value = "";
    renderComments(data.comments ?? []);
  } catch {
    /* ignore */
  }
}

if (submitCommentBtn) {
  submitCommentBtn.addEventListener("click", () => {
    void submitComment();
  });
}

if (commentInput) {
  commentInput.addEventListener("keydown", (event) => {
    if (event.key === "Enter") {
      event.preventDefault();
      void submitComment();
    }
  });
}
