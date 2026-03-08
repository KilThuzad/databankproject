document.addEventListener('DOMContentLoaded', function () {
    const editTeamBtn = document.getElementById('editTeamBtn');
    const teamContainer = document.querySelector('.team-members');

    if (editTeamBtn && teamContainer) {
        const actionBtns = teamContainer.querySelectorAll('.member-actions');

        actionBtns.forEach(btn => btn.style.display = 'none');

        editTeamBtn.addEventListener('click', function () {
            teamContainer.classList.toggle('edit-mode');

            if (teamContainer.classList.contains('edit-mode')) {
                editTeamBtn.innerHTML = '<i class="fas fa-times me-1"></i> Done Editing';
                editTeamBtn.classList.replace('btn-outline-primary', 'btn-primary');

                actionBtns.forEach(btn => btn.style.display = 'flex');
            } else {
                editTeamBtn.innerHTML = '<i class="fas fa-edit me-1"></i> Edit Team';
                editTeamBtn.classList.replace('btn-primary', 'btn-outline-primary');

                actionBtns.forEach(btn => btn.style.display = 'none');
            }
        });
    } 
 });



document.addEventListener('DOMContentLoaded', function () {

    const changeMemberModal = document.getElementById('changeMemberModal');
    if (changeMemberModal) {
        changeMemberModal.addEventListener('show.bs.modal', function (event) {
            let button = event.relatedTarget;
            let memberId = button.getAttribute('data-member-id');
            let memberRole = button.getAttribute('data-member-role');

            document.getElementById('oldMemberId').value = memberId;
            document.getElementById('memberRole').value = memberRole;

            const rows = document.querySelectorAll('#membersTableBody .member-row');
            rows.forEach(row => {
                let role = row.getAttribute('data-role');
                if (memberRole === 'adviser' && role === 'reviewer') {
                    row.style.display = '';
                } else if (memberRole !== 'adviser' && role === 'researcher') {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            const roleFilterInfo = document.getElementById('roleFilterInfo');
            if (memberRole === 'adviser') {
                roleFilterInfo.innerHTML = "Only reviewers are available for selection when replacing an adviser.";
            } else {
                roleFilterInfo.innerHTML = "Select a new member for this role.";
            }
        });
    }

    /**
     * --- DOCUMENT VIEWER ---
     */
    window.prepareDocumentViewer = function (projectId, fileUrl) {
        const fileExt = fileUrl.split('.').pop().toLowerCase();

        document.getElementById('pdfViewer').style.display = 'none';
        document.getElementById('externalViewer').style.display = 'none';
        document.getElementById('unsupportedViewer').style.display = 'none';

        if (fileExt === 'pdf') {
            document.getElementById('pdfViewer').style.display = 'block';
            document.getElementById('pdfFrame').src = fileUrl;
        } else if (['docx', 'doc', 'ppt', 'pptx', 'xls', 'xlsx'].includes(fileExt)) {
            document.getElementById('externalViewer').style.display = 'block';
            document.getElementById('externalFrame').src =
                `https://docs.google.com/viewer?url=${encodeURIComponent(fileUrl)}&embedded=true`;
        } else {
            document.getElementById('unsupportedViewer').style.display = 'block';
        }
    };

    /**
     * --- MEMBER SELECTION (LIMIT 2) ---
     */
    const checkboxes = document.querySelectorAll(".member-checkbox");
    const addButton = document.getElementById("addMemberBtn");

    function updateButtonState() {
        const checked = document.querySelectorAll(".member-checkbox:checked");
        if (addButton) addButton.disabled = (checked.length === 0);
    }

    if (checkboxes.length > 0) {
        checkboxes.forEach(cb => {
            cb.addEventListener("change", function () {
                const checked = document.querySelectorAll(".member-checkbox:checked");
                if (checked.length > 2) {
                    this.checked = false;
                    alert("You can only select up to 2 members.");
                }
                updateButtonState();
            });
        });
        updateButtonState();
    }

    /**
     * --- AUTO-HIDE ALERT ---
     */
    const alert = document.querySelector('.alert.show');
    if (alert) {
        setTimeout(() => {
            alert.classList.remove('show');
            setTimeout(() => {
                location.reload();
            }, 500);
        }, 3000);
    }

});
