<div class="modal fade" id="addMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <form action="{{ route('research_projects.addMember', $project->id) }}" method="POST">
                @csrf

                {{-- Header --}}
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-user-plus text-danger me-2"></i>
                        Add Project Members
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body">

                    <div class="alert alert-warning small">
                        You may select up to <strong>two (2)</strong> members only.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Firstname</th>
                                    <th>Lastname</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th class="text-center">Select</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($availableMembers as $available)
                                    <tr>
                                        <td>{{ $available->firstname }}</td>
                                        <td>{{ $available->lastname }}</td>
                                        <td>{{ $available->email }}</td>
                                        <td>
                                            <span class="badge bg-light border text-dark">
                                                {{ $available->role }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox"
                                                   name="selected_members[]"
                                                   value="{{ $available->id }}"
                                                   class="member-checkbox">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-danger px-4" id="addMemberBtn" disabled>
                        Add Member(s)
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
