@extends('project.staff.layout.app')

@section('title', 'Project Details')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/project-view.css') }}">

<div class="container-fluid my-4">

{{-- Alerts --}}
@php
    $flashSuccess = session('success') ?? session('status') ?? session('message') ?? null;
    $flashError   = session('error') ?? session('danger') ?? null;
@endphp

@if($flashSuccess)
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="fas fa-check-circle me-2 fs-5"></i>
        <div>{{ $flashSuccess }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($flashError)
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="fas fa-exclamation-triangle me-2 fs-5"></i>
        <div>{{ $flashError }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

    {{-- Main Card --}}
    <div class="card shadow-sm border-0 rounded-4">

        {{-- Header --}}
        <div class="card-header bg-light border-bottom">
            <h4 class="mb-0 fw-bold text-dark">
                <i class="fas fa-folder-open text-danger me-2"></i> Project Details
            </h4>
        </div>

        <div class="card-body">

            {{-- Title & Status --}}
            <div class="row mb-4 align-items-center">
                <div class="col-md-8">
                    <h3 class="fw-bold mb-1">{{ $project->title }}</h3>
                    <p class="text-muted mb-0">{{ $project->description }}</p>
                </div>

                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    @php
                        $statusMap = [
                            'approved' => ['APPROVED', 'success'],
                            'pending' => ['PENDING', 'secondary'],
                            'in_progress' => ['IN PROGRESS', 'secondary'],
                        ];
                        [$label, $color] = $statusMap[$project->is_staff_approved] ?? ['UNKNOWN', 'dark'];
                    @endphp

                    <span class="badge rounded-pill px-3 py-2 bg-{{ $color }}-subtle text-{{ $color }}" id="statusBadge">
                        {{ $label }}
                    </span>

                    {{-- Status Update Buttons --}}
                    <div class="mt-2" id="actionButtons">
                        @if($project->is_staff_approved === 'pending')
                            <button class="btn btn-sm btn-success" id="confirmApprovalBtn">
                                <i class="fas fa-check me-1"></i> Confirm
                            </button>
                        @elseif($project->is_staff_approved === 'approved')
                            <a href="{{ route('staffreviewproject.assignReviewerForm', $project->id) }}" class="btn btn-sm btn-info" id="assignReviewerBtn">
                                <i class="fas fa-user-plus me-1"></i> Assign Reviewer
                            </a>
                        @endif
                    </div>

                    <div class="mt-2 text-muted small">
                        <i class="fas fa-calendar-alt me-1"></i>
                        <strong>Due:</strong>
                            {{ optional($project->deadline)->format('M d, Y h:i A') ?? 'No Due Date' }}
                    </div>
                </div>
            </div>

            <div class="row g-4">

                {{-- Project Information --}}
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-header bg-light fw-semibold">
                            Project Information
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-4 text-muted">Category</dt>
                                <dd class="col-sm-8 fw-semibold">
                                    {{ $project->category ?? 'Uncategorized' }}
                                </dd>

                                <dt class="col-sm-4 text-muted">Submitted By</dt>
                                <dd class="col-sm-8">
                                    {{ $project->user?->firstname }} {{ $project->user?->lastname }}
                                </dd>

                                <dt class="col-sm-4 text-muted">Document</dt>
                                <dd class="col-sm-8">
                                    <div class="d-flex flex-column gap-2">
                                        @if($project->file_path)
                                            <div class="d-flex align-items-center justify-content-between mb-2 p-2 border rounded bg-light">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="fas fa-file text-primary"></i>
                                                    @php
                                                        $fullName = basename($project->file_path);
                                                        $maxLength = 25;

                                                        if (strlen($fullName) > $maxLength) {
                                                                
                                                            $displayName = substr($fullName, 0, 12) . '...' . substr($fullName, -10);
                                                            } else {
                                                                $displayName = $fullName;
                                                            }
                                                    @endphp
                                                    <span class="small">{{ $displayName }}</span>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <div class="d-flex gap-2">
                                            @if($project->file_path)
                                                <a href="{{ route('research_projects.download', $project->id) }}"
                                                   class="btn btn-sm btn-outline-secondary flex-fill">
                                                    <i class="fas fa-download me-1"></i> Download Document
                                                </a>
                                            @endif
                                            
                                            <button class="btn btn-sm btn-outline-primary flex-fill"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#uploadDocumentModal">
                                                <i class="fas fa-upload me-1"></i> 
                                                {{ $project->file_path ? 'Replace Document' : 'Upload Document' }}
                                            </button>
                                        </div>
                                        
                                        @if(!$project->file_path)
                                            <div class="text-muted small">
                                                <i class="fas fa-info-circle me-1"></i>
                                                No document has been uploaded yet
                                            </div>
                                        @endif
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                {{-- Project Team --}}
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-semibold">Project Team</h5>
                        </div>

                        <div class="card-body team-members">
                            @foreach($project->team as $member)
                                <div class="member-info p-3 border rounded-3 mb-2 bg-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-semibold">
                                                {{ $member->firstname }} {{ $member->lastname }}
                                            </div>
                                            <div class="text-muted small">
                                                <i class="fas fa-envelope me-1"></i> {{ $member->email }}
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-light border text-dark">
                                                {{ ucfirst($member->pivot->role) }}
                                            </span>

                                        
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Upload Document Modal --}}
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('staffresearchproject.uploadDocument', $project->id) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  id="uploadDocumentForm">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="uploadDocumentModalLabel">
                        <i class="fas fa-upload me-2"></i>
                        {{ $project->file_path ? 'Replace Document' : 'Upload Document' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    @if($project->file_path)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Uploading a new document will replace the existing one.
                        </div>

                        <div class="current-file-info mb-3 p-3 border rounded bg-light">
                            <h6 class="fw-semibold">Current Document:</h6>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-file text-primary"></i>
                                <span class="small">{{ basename($project->file_path) }}</span>
                                <a href="{{ route('research_projects.download', $project->id) }}"
                                   class="btn btn-sm btn-outline-primary ms-auto">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Upload Area --}}
                    <div class="file-upload-area border rounded p-5 text-center" id="fileUploadArea">
                        <div class="upload-icon mb-3">
                            <i class="fas fa-cloud-upload-alt fa-3x text-primary"></i>
                        </div>

                        <h5 class="mb-2">Drop file here or click to upload</h5>
                        <p class="text-muted mb-3">
                            PDF, DOC, DOCX, JPG, PNG (Max 10MB)
                        </p>

                        <input type="file"
                               class="d-none"
                               id="documentFile"
                               name="document"
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               required>

                        <label for="documentFile" class="btn btn-primary">
                            <i class="fas fa-folder-open me-2"></i> Browse File
                        </label>

                        <div class="mt-3 d-none" id="selectedFileInfo">
                            <div class="alert alert-success d-flex align-items-center justify-content-between">
                                <div>
                                    <i class="fas fa-check-circle me-2"></i>
                                    <span id="fileName"></span>
                                    <small class="d-block text-muted" id="fileSize"></small>
                                </div>
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="clearFileSelection()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-check mt-3">
                        <input class="form-check-input"
                               type="checkbox"
                               name="notify_team"
                               id="notifyTeam"
                               value="1"
                               checked>
                        <label class="form-check-label" for="notifyTeam">
                            <i class="fas fa-bell me-1"></i> Notify team members about document update
                        </label>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="uploadBtn" disabled>
                        <i class="fas fa-upload me-2"></i>
                        {{ $project->file_path ? 'Replace Document' : 'Upload Document' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- Confirm Approval Modal --}}
<div class="modal fade" id="confirmApprovalModal" tabindex="-1" aria-labelledby="confirmApprovalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('staffresearchproject.updateApprovalStatus', $project->id) }}" method="POST" id="approvalForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="approved">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmApprovalModalLabel">Confirm Approval</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to approve this project?</p>
                    <p class="text-muted small">Once approved, you can assign a reviewer to evaluate the project.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Yes, Approve Project</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Update Status Modal --}}
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('staffresearchproject.updateStatus', $project->id) }}" method="POST" id="updateStatusForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" id="statusInput">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStatusModalLabel">Update Project Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to update the project status to <strong id="statusText"></strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Yes, Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Styles --}}
<style>
.card {
    border-radius: 12px;
}
.card-header {
    font-weight: 600;
}
.member-actions {
    display: none;
}
.team-members.edit-mode .member-actions {
    display: flex;
}
.member-info:hover {
    background-color: #f8f9fa;
}
.badge {
    font-size: 0.75rem;
    font-weight: 600;
}
.file-upload-area {
    border: 2px dashed #dee2e6;
    transition: all 0.3s ease;
    cursor: pointer;
}
.file-upload-area:hover {
    border-color: #0d6efd;
    background-color: #f8f9fa;
}
.file-upload-area.dragover {
    border-color: #0d6efd;
    background-color: #e7f1ff;
}
.selected-file {
    animation: fadeIn 0.3s ease;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.btn.flex-fill {
    flex: 1 1 0;
    min-width: 0;
}
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // File upload handling
    const fileInput = document.getElementById('documentFile');
    const uploadBtn = document.getElementById('uploadBtn');
    const selectedFileInfo = document.getElementById('selectedFileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const uploadArea = document.getElementById('fileUploadArea');
    
    uploadArea.addEventListener('click', function(e) {
        if (!e.target.closest('label') && !e.target.closest('.btn-outline-danger')) {
            fileInput.click();
        }
    });
    
    fileInput.addEventListener('change', function() {
        handleFileSelection(this.files[0]);
    });
    
    function handleFileSelection(file) {
        if (file) {
            const isValidFormat = validateFileFormat(file);
            const isValidSize = validateFileSize(file);
            
            if (isValidFormat && isValidSize) {
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                selectedFileInfo.classList.remove('d-none');
                uploadBtn.disabled = false;
            } else if (!isValidFormat) {
                alert('Please select a valid file format (PDF, DOC, DOCX, JPG, PNG)');
                fileInput.value = '';
                clearFileSelection();
            } else if (!isValidSize) {
                alert('File size exceeds 10MB limit. Please select a smaller file.');
                fileInput.value = '';
                clearFileSelection();
            }
        }
    }
    
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('dragover');
        
        if (e.dataTransfer.files.length > 0) {
            const file = e.dataTransfer.files[0];
            fileInput.files = e.dataTransfer.files;
            handleFileSelection(file);
        }
    });
    
    const uploadForm = document.getElementById('uploadDocumentForm');
    uploadForm.addEventListener('submit', function(e) {
        if (!fileInput.files.length) {
            e.preventDefault();
            alert('Please select a file to upload');
            return false;
        }
        
        const originalText = uploadBtn.innerHTML;
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Uploading...';
        uploadBtn.disabled = true;
        
        return true;
    });
    
    const updateStatusModal = document.getElementById('updateStatusModal');
    if (updateStatusModal) {
        updateStatusModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const status = button.getAttribute('data-status');

            const statusInput = document.getElementById('statusInput');
            const statusText = document.getElementById('statusText');

            statusInput.value = status;

            const statusLabels = {
                pending: 'Pending',
                approved: 'Approved',
                declined: 'Declined'
            };

            statusText.textContent = statusLabels[status] || status;
        });
    }

    const confirmApprovalBtn = document.getElementById('confirmApprovalBtn');
    if (confirmApprovalBtn) {
        confirmApprovalBtn.addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('confirmApprovalModal'));
            modal.show();
        });
    }

    const approvalForm = document.getElementById('approvalForm');
    if (approvalForm) {
        approvalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';
            submitBtn.disabled = true;
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    _method: 'PUT',
                    status: 'approved'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const statusBadge = document.getElementById('statusBadge');
                    statusBadge.textContent = 'APPROVED';
                    statusBadge.className = 'badge rounded-pill px-3 py-2 bg-success-subtle text-success';
                    
                    const actionButtons = document.getElementById('actionButtons');
                    actionButtons.innerHTML = `
                        <a href="{{ route('staffreviewproject.assignReviewerForm', $project->id) }}" class="btn btn-sm btn-info" id="assignReviewerBtn">
                            <i class="fas fa-user-plus me-1"></i> Assign Reviewer
                        </a>
                    `;
                    
                    const modal = bootstrap.Modal.getInstance(document.getElementById('confirmApprovalModal'));
                    modal.hide();
                    
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});

function validateFileFormat(file) {
    const validExtensions = ['.pdf', '.doc', '.docx', '.jpg', '.jpeg', '.png'];
    const fileName = file.name.toLowerCase();
    return validExtensions.some(ext => fileName.endsWith(ext));
}

function validateFileSize(file) {
    const maxSize = 10 * 1024 * 1024; // 10MB in bytes
    return file.size <= maxSize;
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function clearFileSelection() {
    const fileInput = document.getElementById('documentFile');
    const selectedFileInfo = document.getElementById('selectedFileInfo');
    const uploadBtn = document.getElementById('uploadBtn');
    
    fileInput.value = '';
    selectedFileInfo.classList.add('d-none');
    uploadBtn.disabled = true;
}

// Initialize Bootstrap components
if (typeof bootstrap !== 'undefined') {
    // Initialize tooltips if any
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}
</script>
@endpush