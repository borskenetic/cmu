document.addEventListener("DOMContentLoaded", () => {
    const openId = new URLSearchParams(window.location.search).get('open');
    if (openId) {
        const openEl = document.getElementById(openId);
        if (openEl) openEl.classList.add('is-open');
    }

    const editForm = document.getElementById('editForm');
    const deleteForm = document.getElementById('deleteForm');

    function toggleLoading(button, loading) {
        if (!button) return;
        const spinner = button.querySelector('.spinner');
        const text = button.querySelector('.btn-text');
        if (loading) {
            if (spinner) spinner.classList.remove('hidden');
            if (text) text.classList.add('hidden');
            button.disabled = true;
        } else {
            if (spinner) spinner.classList.add('hidden');
            if (text) text.classList.remove('hidden');
            button.disabled = false;
        }
    }

    function showToast(message, type = "success") {
        const container = document.getElementById("toastContainer");
        if (!container) return;

        const toast = document.createElement("div");
        toast.className = "px-4 py-2 rounded-lg shadow-lg text-white flex items-center justify-between w-64";
        toast.style.backgroundColor = type === "success" ? "#16a34a" : "#dc2626";
        toast.innerHTML = `<span>${message}</span><button type="button" class="ml-2 text-white font-bold">×</button>`;
        toast.querySelector("button").addEventListener("click", () => toast.remove());
        setTimeout(() => toast.remove(), 2500);
        container.appendChild(toast);
    }

    if (editForm) {
        editForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const btn = document.getElementById('editBtn');
            toggleLoading(btn, true);

            let response = await fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            toggleLoading(btn, false);

            if (response.ok) {
                let updatedItem = await response.text();
                let courseId = this.action.split('/').pop();
                let li = document.getElementById('course-' + courseId);
                if (li) li.outerHTML = updatedItem;
                closeEditModal();
                showToast("Subject updated");
            } else {
                showToast("Error updating subject", "error");
            }
        });
    }

    if (deleteForm) {
        deleteForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const btn = document.getElementById('deleteBtn');
            toggleLoading(btn, true);

            let response = await fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            toggleLoading(btn, false);

            if (response.ok) {
                let courseId = this.action.split('/').pop();
                let li = document.getElementById('course-' + courseId);
                if (li) li.remove();
                closeDeleteModal();
                showToast("Subject deleted");
            } else {
                showToast("Error deleting subject", "error");
            }
        });
    }

    document.querySelectorAll('.add-course-form').forEach(form => {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            toggleLoading(btn, true);

            let formData = new FormData(this);
            let yearId = this.dataset.year;

            let response = await fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            toggleLoading(btn, false);

            if (response.ok) {
                let newItem = await response.text();
                let yearPanel = document.getElementById('year-' + yearId);
                let ul = yearPanel ? yearPanel.querySelector('ul') : null;
                if (ul) {
                    let emptyMsg = ul.querySelector('.text-gray-500');
                    if (emptyMsg) emptyMsg.remove();
                    ul.insertAdjacentHTML('beforeend', newItem);
                }
                this.reset();
                showToast("Subject added");
            } else {
                showToast("Error adding subject", "error");
            }
        });
    });

    const editProgramForm = document.getElementById("editProgramForm");
    if (editProgramForm) {
        editProgramForm.addEventListener("submit", async function (e) {
            e.preventDefault();
            const btn = document.getElementById("editProgramBtn");
            toggleLoading(btn, true);

            let response = await fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            toggleLoading(btn, false);

            if (response.ok) {
                let data = await response.json();
                if (data.college_changed) {
                    window.location.reload();
                    return;
                }
                const label = document.getElementById("program-name-" + data.id);
                if (label) label.textContent = `${data.program_code} — ${data.program_name}`;
                closeProgramEditModal();
                showToast("Course updated");
            } else {
                showToast("Error updating course", "error");
            }
        });
    }

    const deleteProgramForm = document.getElementById("deleteProgramForm");
    if (deleteProgramForm) {
        deleteProgramForm.addEventListener("submit", async function (e) {
            e.preventDefault();
            const btn = document.getElementById("deleteProgramBtn");
            toggleLoading(btn, true);

            let response = await fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            toggleLoading(btn, false);

            if (response.ok) {
                let data = await response.json();
                const block = document.getElementById("program-block-" + data.id);
                if (block) block.remove();
                closeProgramDeleteModal();
                showToast("Course deleted");
            } else {
                showToast("Error deleting course", "error");
            }
        });
    }

    const editCollegeForm = document.getElementById("editCollegeForm");
    if (editCollegeForm) {
        editCollegeForm.addEventListener("submit", async function (e) {
            e.preventDefault();
            const btn = document.getElementById("editCollegeBtn");
            toggleLoading(btn, true);

            let response = await fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            toggleLoading(btn, false);

            if (response.ok) {
                let data = await response.json();
                const label = document.getElementById("college-name-" + data.id);
                if (label) label.textContent = data.name;
                closeCollegeEditModal();
                showToast("College updated");
            } else {
                showToast("Error updating college", "error");
            }
        });
    }

    const deleteCollegeForm = document.getElementById("deleteCollegeForm");
    if (deleteCollegeForm) {
        deleteCollegeForm.addEventListener("submit", async function (e) {
            e.preventDefault();
            const btn = document.getElementById("deleteCollegeBtn");
            toggleLoading(btn, true);

            let response = await fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            toggleLoading(btn, false);

            if (response.ok) {
                let data = await response.json();
                const block = document.getElementById("college-block-" + data.id);
                if (block) block.remove();
                closeCollegeDeleteModal();
                showToast("College deleted");
            } else {
                let message = "Error deleting college";
                try {
                    const data = await response.json();
                    if (data.message) message = data.message;
                } catch (err) {}
                showToast(message, "error");
            }
        });
    }
});
