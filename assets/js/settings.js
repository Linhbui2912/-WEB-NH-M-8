(function () {
    'use strict';

    const MODAL_SETTINGS_ID = 'settingsMenuModal';
    const MODAL_LOGOUT_ID   = 'logoutConfirmModal';
    console.log('settings.js loaded!');

    function buildModalsHtml() {
        return `
        <div class="modal fade" id="${MODAL_SETTINGS_ID}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow">
                    <div class="modal-body p-4">
                        <h5 class="text-center fw-bold mb-4">Cài đặt</h5>
                        <div class="d-grid gap-2">
                            <button type="button"
                                    class="btn btn-outline-danger rounded-3 py-2"
                                    id="btnOpenLogoutConfirm">Đăng xuất</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="${MODAL_LOGOUT_ID}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 rounded-4">
                    <div class="modal-body p-4 text-center">
                        <p class="mb-4">Bạn có chắc muốn đăng xuất?</p>
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="../controllers/xulydangxuat.php"
                                   class="btn btn-dark w-100 rounded-3">Có</a>
                            </div>
                            <div class="col-6">
                                <button type="button"
                                        class="btn btn-secondary w-100 rounded-3"
                                        data-bs-dismiss="modal">Không</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    }

    function init() {
        const triggerBtn = document.getElementById('btnOpenSettings');
        if (!triggerBtn) return;

        if (!document.getElementById(MODAL_SETTINGS_ID)) {
            const tmp = document.createElement('div');
            tmp.innerHTML = buildModalsHtml();
            while (tmp.firstChild) document.body.appendChild(tmp.firstChild);
        }

        const settingsModalEl = document.getElementById(MODAL_SETTINGS_ID);
        const logoutModalEl   = document.getElementById(MODAL_LOGOUT_ID);

        if (typeof bootstrap === 'undefined') return;

        const settingsModal = bootstrap.Modal.getInstance(settingsModalEl)
            || new bootstrap.Modal(settingsModalEl);
        const logoutModal = bootstrap.Modal.getInstance(logoutModalEl)
            || new bootstrap.Modal(logoutModalEl);

        triggerBtn.addEventListener('click', () => settingsModal.show());

        const logoutBtn = document.getElementById('btnOpenLogoutConfirm');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', () => {
                settingsModal.hide();
                settingsModalEl.addEventListener('hidden.bs.modal', function onHidden() {
                    settingsModalEl.removeEventListener('hidden.bs.modal', onHidden);
                    logoutModal.show();
                });
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();