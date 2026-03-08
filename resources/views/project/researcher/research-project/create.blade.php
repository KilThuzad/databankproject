@extends('project.myapp')

@section('title', 'Add New Research Project')

@section('content')

<link rel="stylesheet" href="{{ asset('css/researher/style.css') }}">

<div class="container my-4">

    <h1 class="h3 mb-2 fw-bold text-dark">Add New Research Project</h1>
    <p class="text-muted mb-4">Fill in the details below to create a new research project</p>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="projectForm" action="{{ route('userresearchproject.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return false;">
        @csrf

        {{-- Project Information --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-danger text-white fw-bold">Project Information</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                    <select name="category" class="form-select" required>
                        <option value="">Select category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->name }}" {{ old('category') == $category->name ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Upload File <span class="text-danger">*</span></label>
                    <input type="file" name="file_path" class="form-control" required>
                </div>
            </div>
        </div>

        {{-- Team Assignment (hidden initially) --}}
        <div class="card mb-4 shadow-sm" id="teamAssignment" style="display:none;">
            <div class="card-header bg-danger text-white fw-bold">Team Assignment</div>
            <div class="card-body">
                {{-- Leader --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Project Leader</label>
                    <select name="leader_id" id="leaderSelect" class="form-select">
                        <option value="">(Default: You)</option>
                        @foreach($researchers as $researcher)
                            <option value="{{ $researcher->id }}">
                                {{ $researcher->firstname }} {{ $researcher->lastname }}
                                @if($researcher->id == auth()->id()) (You) @endif
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted d-block">
                        If no leader is selected, you will automatically become the leader. <br>
                        If another user is selected as leader, you will automatically be added as a team member.
                    </small>
                </div>

                {{-- Members --}}
                <label class="form-label fw-bold mt-3">Team Members (optional)</label>
                <div class="border rounded p-3" style="max-height: 250px; overflow-y: auto">
                    <div class="row">
                        @foreach($researchers as $researcher)
                            @if($researcher->id != auth()->id())
                                <div class="col-md-6 mb-2 team-member" data-id="{{ $researcher->id }}">
                                    <div class="form-check">
                                        <input type="checkbox" name="member_ids[]" value="{{ $researcher->id }}" class="form-check-input">
                                        <label class="form-check-label">
                                            {{ $researcher->firstname }} {{ $researcher->lastname }}
                                        </label>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Buttons --}}
        <div class="text-end">
            <a href="{{ route('userresearchproject.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="button" id="submitBtn" class="btn btn-danger px-4">Submit Project</button>
        </div>
    </form>
</div>
{{-- Modal --}}
<div class="modal fade" id="teamModal" tabindex="-1" aria-labelledby="teamModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-top">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="teamModalLabel">Add Team Members?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Do you want to add team members to this project?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="noTeamBtn">No</button>
        <button type="button" class="btn btn-danger" id="yesTeamBtn">Yes</button>
      </div>
    </div>
  </div>
</div>


{{-- JS --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const leaderSelect = document.getElementById('leaderSelect');
    const members = document.querySelectorAll('.team-member');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('projectForm');
    const teamSection = document.getElementById('teamAssignment');
    const submitterId = '{{ auth()->id() }}';

    const teamModal = new bootstrap.Modal(document.getElementById('teamModal'));

    leaderSelect.addEventListener('change', function () {
        members.forEach(m => {
            const checkbox = m.querySelector('input');
            if (checkbox.value === leaderSelect.value) {
                checkbox.checked = false;
                checkbox.disabled = true;
                m.style.display = 'none';
            } else {
                checkbox.disabled = false;
                m.style.display = 'block';
            }
        });
    });

    submitBtn.addEventListener('click', function (e) {
        e.preventDefault();

        if (teamSection.style.display === 'none') {
            teamModal.show();
        } else {
            form.removeAttribute('onsubmit');
            form.submit();
        }
    });

    document.getElementById('yesTeamBtn').addEventListener('click', function () {
        teamSection.style.display = 'block';
        teamModal.hide();
        teamSection.scrollIntoView({ behavior: 'smooth' });
    });

    document.getElementById('noTeamBtn').addEventListener('click', function () {
        let leader = leaderSelect.value || submitterId;

        document.querySelectorAll('input[name="member_ids[]"][type="hidden"]').forEach(h => h.remove());
        document.querySelectorAll('input[name="leader_id"][type="hidden"]').forEach(h => h.remove());

        const leaderInput = document.createElement('input');
        leaderInput.type = 'hidden';
        leaderInput.name = 'leader_id';
        leaderInput.value = leader;
        form.appendChild(leaderInput);

        if (leader != submitterId) {
            const memberInput = document.createElement('input');
            memberInput.type = 'hidden';
            memberInput.name = 'member_ids[]';
            memberInput.value = submitterId;
            form.appendChild(memberInput);
        }

        teamModal.hide();
        form.removeAttribute('onsubmit');
        form.submit();
    });
});
</script>
@endsection
