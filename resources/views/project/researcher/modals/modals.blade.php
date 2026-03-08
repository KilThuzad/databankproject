{{-- ================= Add Member Modal ================= --}}
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('research_projects.addMember', $project->id) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header modal-header-gradient">
                    <h5 class="modal-title" id="addMemberModalLabel"><i class="fas fa-user-plus me-2"></i>Add Researcher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Select researchers to add to the project:</p>
                    <div class="list-group">
                        @foreach($availableMembers as $member)
                            <label class="list-group-item">
                                <input type="checkbox" name="members[]" value="{{ $member->id }}" class="form-check-input member-checkbox me-2">
                                {{ $member->firstname }} {{ $member->lastname }} ({{ $member->email }})
                            </label>
                        @endforeach
                        @if(count($availableMembers) === 0)
                            <p class="text-muted">No available members to add.</p>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="addMemberBtn" class="btn btn-primary-custom" disabled>Add Selected</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ================= Change Member Modal ================= --}}
<div class="modal fade" id="changeMemberModal" tabindex="-1" aria-labelledby="changeMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="changeMemberForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header modal-header-gradient">
                    <h5 class="modal-title" id="changeMemberModalLabel"><i class="fas fa-user-edit me-2"></i>Change Member Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="member_id" id="changeMemberId">
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" name="role" id="changeMemberRole" required>
                            <option value="member">Member</option>
                            <option value="leader">Leader</option>
                        </select>
                        <small class="text-danger mt-1 d-block">Changing leader will require selecting a new leader.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-custom">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ================= Edit Comment Modal ================= --}}
<div class="modal fade" id="editCommentModal" tabindex="-1" aria-labelledby="editCommentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editCommentForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header modal-header-gradient">
                    <h5 class="modal-title" id="editCommentModalLabel"><i class="fas fa-edit me-2"></i>Edit Comment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editCommentTextarea" class="form-label">Comment</label>
                        <textarea class="form-control" name="comment" id="editCommentTextarea" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-custom">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ================= Document Viewer Modal ================= --}}
<div class="modal fade" id="documentViewerModal" tabindex="-1" aria-labelledby="documentViewerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-gradient">
                <h5 class="modal-title" id="documentViewerModalLabel"><i class="far fa-file-alt me-2"></i>Project Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <iframe id="documentViewerFrame" src="" frameborder="0" style="width: 100%; height: 80vh;"></iframe>
            </div>
        </div>
    </div>
</div>

{{-- ================= SCRIPT TO HANDLE MODALS ================= --}}
<script>
function prepareDocumentViewer(projectId, fileUrl) {
    const iframe = document.getElementById('documentViewerFrame');
    iframe.src = fileUrl;
}

// Populate Change Member Modal dynamically
const changeMemberModal = document.getElementById('changeMemberModal');
changeMemberModal.addEventListener('show.bs.modal', function(event){
    const button = event.relatedTarget;
    const memberId = button.getAttribute('data-member-id');
    const role = button.getAttribute('data-member-role');

    document.getElementById('changeMemberId').value = memberId;
    document.getElementById('changeMemberRole').value = role;

    const form = document.getElementById('changeMemberForm');
    form.action = `/research-projects/{{ $project->id }}/change-member/${memberId}`;
});
</script>
