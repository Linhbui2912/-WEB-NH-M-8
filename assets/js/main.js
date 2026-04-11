const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
tooltipTriggerList.forEach((el) => {
  new bootstrap.Tooltip(el, {
    trigger: "hover focus",
    placement: "auto"
  });
});

const likeButtons = document.querySelectorAll(".paw-like-btn");
likeButtons.forEach((button) => {
  button.addEventListener("click", () => {
    const icon = button.querySelector("img");
    const isLiked = button.dataset.liked === "true";
    const iconWhite = icon.dataset.iconWhite;
    const iconLiked = icon.dataset.iconLiked;

    if (!iconWhite || !iconLiked) {
      return;
    }

    if (isLiked) {
      icon.src = iconWhite;
      button.dataset.liked = "false";
    } else {
      icon.src = iconLiked;
      button.dataset.liked = "true";
    }
  });
});

const commentStore = {
  "post-1": [
    { user: "InuShiba_lalala", text: "Bé mèo xinh quá nha!" },
    { user: "PetLover_95", text: "Ánh sáng ảnh này đẹp thật." }
  ],
  "post-2": [
    { user: "Meobeo_3123", text: "Hai bé đáng yêu quá trời." }
  ]
};

let activePostId = null;

const commentButtons = document.querySelectorAll(".open-comments-btn");
const commentsList = document.getElementById("commentsList");
const commentInput = document.getElementById("commentInput");
const submitCommentBtn = document.getElementById("submitCommentBtn");

function renderComments(postId) {
  if (!commentsList) return;
  const comments = commentStore[postId] || [];
  commentsList.innerHTML = "";

  if (comments.length === 0) {
    commentsList.innerHTML = '<li class="comment-empty">Chưa có bình luận nào.</li>';
    return;
  }

  comments.forEach((comment) => {
    const li = document.createElement("li");
    li.innerHTML = `<span class="comment-user">${comment.user}</span>${comment.text}`;
    commentsList.appendChild(li);
  });
}

commentButtons.forEach((button) => {
  button.addEventListener("click", () => {
    activePostId = button.dataset.postId;
    renderComments(activePostId);
    if (commentInput) {
      commentInput.value = "";
      commentInput.focus();
    }
  });
});

function submitComment() {
  if (!activePostId || !commentInput) return;
  const text = commentInput.value.trim();
  if (!text) return;

  if (!commentStore[activePostId]) {
    commentStore[activePostId] = [];
  }
  commentStore[activePostId].push({
    user: "you",
    text
  });

  commentInput.value = "";
  renderComments(activePostId);
}

if (submitCommentBtn) {
  submitCommentBtn.addEventListener("click", submitComment);
}

if (commentInput) {
  commentInput.addEventListener("keydown", (event) => {
    if (event.key === "Enter") {
      event.preventDefault();
      submitComment();
    }
  });
}
