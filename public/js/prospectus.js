document.querySelectorAll("[data-panel-toggle]").forEach(btn => {
    btn.addEventListener("click", (e) => {
        e.preventDefault();
        const selector = btn.getAttribute("data-panel-toggle");
        const target = document.querySelector(selector);
        if (!target) return;
        target.classList.toggle("hidden");
        // Clear any Bootstrap collapse leftovers that can freeze mid-animation
        target.classList.remove("collapsing", "collapse", "show");
        target.style.height = "";
        target.style.overflow = "";
    });
});

function openDeleteModal(courseId, courseCode) {
    const form = document.getElementById('deleteForm');
    form.action = `/prospectus/course/${courseId}`;
    document.getElementById('deleteMessage').innerText =
        `Are you sure you want to delete course "${courseCode}"?`;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

function openEditModal(courseId, code, name) {
    const form = document.getElementById('editForm');
    form.action = `/prospectus/course/${courseId}`;
    document.getElementById('editCourseCode').value = code;
    document.getElementById('editCourseName').value = name;
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

document.addEventListener("DOMContentLoaded", () => {
    const openId = new URLSearchParams(window.location.search).get('open');
    if (openId) {
        const openEl = document.getElementById(openId);
        if (openEl) {
            openEl.classList.remove('hidden', 'collapsing', 'collapse');
            openEl.classList.remove('show');
            openEl.style.height = '';
            openEl.style.overflow = '';
        }
    }

    const editForm = document.getElementById('editForm');
    const deleteForm = document.getElementById('deleteForm');

    // Helper: show spinner
    function toggleLoading(button, loading) {
        const spinner = button.querySelector('.spinner');
        const text = button.querySelector('.btn-text');
        if (loading) {
            spinner.classList.remove('hidden');
            text.classList.add('hidden');
            button.disabled = true;
        } else {
            spinner.classList.add('hidden');
            text.classList.remove('hidden');
            button.disabled = false;
        }
    }

    // Helper: show success modal
    function showToast(message, type = "success") {
        const container = document.getElementById("toastContainer");

        const toast = document.createElement("div");
        toast.className = `px-4 py-2 rounded-lg shadow-lg text-white flex items-center justify-between w-64 animate-slide-in`;
        toast.style.backgroundColor = type === "success" ? "#16a34a" : "#dc2626"; // green or red
        toast.innerHTML = `
            <span>${message}</span>
            <button class="ml-2 text-white font-bold focus:outline-none">×</button>
        `;

        // remove on click
        toast.querySelector("button").addEventListener("click", () => toast.remove());

        // auto remove
        setTimeout(() => {
            toast.classList.remove("animate-slide-in");
            toast.classList.add("animate-fade-out");
            setTimeout(() => toast.remove(), 500);
        }, 2000);

        container.appendChild(toast);
    }

    // 🔹 Animations
    const style = document.createElement("style");
    style.innerHTML = `
        @keyframes slideIn { from { transform: translateX(100%); opacity:0; } to { transform: translateX(0); opacity:1; } }
        @keyframes fadeOut { from { opacity:1; } to { opacity:0; } }

        .animate-slide-in { animation: slideIn 0.4s ease-out; }
        .animate-fade-out { animation: fadeOut 0.5s forwards; }
    `;
    document.head.appendChild(style);


    // ✅ Handle Edit (AJAX)
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
                li.outerHTML = updatedItem;
                closeEditModal();
                showToast("Course Updated ✅");
            } else {
                alert('Error updating course');
            }
        });
    }

    // ✅ Handle Delete (AJAX)
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
                showToast("Course Deleted 🗑️");
            } else {
                alert('Error deleting course');
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
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            toggleLoading(btn, false);

            if (response.ok) {
                let newItem = await response.text();
                let ul = this.closest('#year-' + yearId).querySelector('ul');
                let emptyMsg = ul.querySelector('.text-gray-500');
                if (emptyMsg) emptyMsg.remove();
                ul.insertAdjacentHTML('beforeend', newItem);
                this.reset();
                showToast("Course Added ✅");
            } else {
                alert('Error adding course');
            }
        });
    });


    // 🔹 Open Program Edit Modal
    window.openProgramEditModal = function (programId, collegeId, programCode, programName) {
        const modal = document.getElementById("editProgramModal");
        const form = document.getElementById("editProgramForm");
        form.action = `/prospectus/program/${programId}`;
        document.getElementById("editProgramCollege").value = collegeId || '';
        document.getElementById("editProgramCode").value = programCode;
        document.getElementById("editProgramName").value = programName;
        modal.classList.remove("hidden");
    };

    // 🔹 Close
    window.closeProgramEditModal = function () {
        document.getElementById("editProgramModal").classList.add("hidden");
    };

    // 🔹 Submit Handler
    const editProgramForm = document.getElementById("editProgramForm");
    if (editProgramForm) {
        editProgramForm.addEventListener("submit", async function (e) {
            e.preventDefault();
            const btn = document.getElementById("editProgramBtn");
            toggleLoading(btn, true);

            let response = await fetch(this.action, {
                method: 'POST', // Laravel spoofing still works
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
                document.getElementById("program-name-" + data.id).textContent =
                    `${data.program_code} — ${data.program_name}`;

                closeProgramEditModal();
                showToast("Course updated");
            } else {
                showToast("Error updating course", "error");
            }
        });
    }
    // 🔹 Open Delete Modal
    window.openProgramDeleteModal = function(programId, programCode) {
        const modal = document.getElementById("deleteProgramModal");
        const form = document.getElementById("deleteProgramForm");
        form.action = `/prospectus/program/${programId}`;
        document.getElementById("deleteProgramCode").textContent = programCode;
        modal.classList.remove("hidden");
    };
    
    // 🔹 Close
    window.closeProgramDeleteModal = function() {
        document.getElementById("deleteProgramModal").classList.add("hidden");
    };
    
    // 🔹 Submit Handler
    const deleteProgramForm = document.getElementById("deleteProgramForm");
    if (deleteProgramForm) {
        deleteProgramForm.addEventListener("submit", async function(e) {
            e.preventDefault();
            const btn = document.getElementById("deleteProgramBtn");
            toggleLoading(btn, true);
    
            let response = await fetch(this.action, {
                method: 'POST', // Laravel method spoofing
                body: new FormData(this),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
    
            toggleLoading(btn, false);
    
            if (response.ok) {
                let data = await response.json();
                document.getElementById("program-block-" + data.id)?.remove();
    
                closeProgramDeleteModal();
                showToast("Course deleted");
            } else {
                showToast("Error deleting course", "error");
            }
        });
    }

    window.openCollegeEditModal = function (collegeId, collegeName) {
        const modal = document.getElementById("editCollegeModal");
        const form = document.getElementById("editCollegeForm");
        form.action = `/prospectus/college/${collegeId}`;
        document.getElementById("editCollegeName").value = collegeName;
        modal.classList.remove("hidden");
    };

    window.closeCollegeEditModal = function () {
        document.getElementById("editCollegeModal").classList.add("hidden");
    };

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

    window.openCollegeDeleteModal = function (collegeId, collegeName) {
        const modal = document.getElementById("deleteCollegeModal");
        const form = document.getElementById("deleteCollegeForm");
        form.action = `/prospectus/college/${collegeId}`;
        document.getElementById("deleteCollegeName").textContent = collegeName;
        modal.classList.remove("hidden");
    };

    window.closeCollegeDeleteModal = function () {
        document.getElementById("deleteCollegeModal").classList.add("hidden");
    };

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
                document.getElementById("college-block-" + data.id)?.remove();
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