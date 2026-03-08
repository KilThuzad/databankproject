<div class="modal fade" id="changeMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <form action="{{ route('research_projects.changeMember', $project->id) }}" method="POST">
                @csrf

                <input type="hidden" name="old_member_id" id="oldMemberId">
                <input type="hidden" name="member_role" id="memberRole">

                {{-- Header --}}
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-user-edit text-danger me-2"></i>
                        Change Team Member
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body">

                    <div class="alert alert-info small" id="roleFilterInfo">
                        Select a new member for this role.
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
                            <tbody id="membersTableBody">
                                @foreach($availableMembers as $newMember)
                                    <tr class="member-row" data-role="{{ $newMember->role }}">
                                        <td>{{ $newMember->firstname }}</td>
                                        <td>{{ $newMember->lastname }}</td>
                                        <td>{{ $newMember->email }}</td>
                                        <td>
                                            <span class="badge bg-light border text-dark">
                                                {{ $newMember->role }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio"
                                                   name="selected_member"
                                                   value="{{ $newMember->id }}">
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
                    <button type="submit" class="btn btn-danger px-4">
                        Confirm Change
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
