@extends('project.app')

@section('title', 'Project Details')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/admin/project-view.css') }}">
<div class="container-fluid my-4">

    {{-- Alerts --}}
    @foreach (['success','error'] as $msg)
        @if(session($msg))
            <div class="alert alert-{{ $msg === 'success' ? 'success' : 'danger' }} alert-dismissible fade show">
                {{ session($msg) }}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    <div class="card shadow-sm border-0">

        {{-- HEADER --}}
        <div class="card-header project-header">
            <h3 class="mb-0">Project Details</h3>
        </div>

        <div class="card-body">

            {{-- TITLE --}}
            <div class="row mb-4">
                <div class="col-md-8">
                    <h2 class="fw-bold">{{ $project->title }}</h2>
                    <p class="text-muted mb-0">{{ $project->description }}</p>
                </div>

                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <span class="badge badge-status
                        @class([
                            'bg-success' => $project->status === 'complete',
                            'bg-warning text-dark' => in_array($project->status,['in_progress','needs_revision']),
                            'bg-secondary' => !in_array($project->status,['complete','in_progress','needs_revision'])
                        ])">
                        {{ ucfirst(str_replace('_',' ',$project->status)) }}
                    </span>

                    <div class="small text-muted mt-2">
                        <div><i class="far fa-calendar"></i> Created: {{ $project->created_at->format('d M Y') }}</div>
                        <div><i class="fas fa-sync-alt"></i> Updated: {{ $project->updated_at->format('d M Y') }}</div>
                        <div>
                            <i class="fas fa-clock"></i>
                            Deadline:
                            {{ $project->deadline
                                ? \Carbon\Carbon::parse($project->deadline)->format('d M Y • h:i A')
                                : 'Not set'
                            }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- INFO + TEAM --}}
            <div class="row g-4">

                {{-- PROJECT INFO --}}
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light fw-semibold">
                            Project Information
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Category:</dt>
                                <dd class="col-sm-8">{{ $project->category ?? 'N/A' }}</dd>

                                <dt class="col-sm-4">Document:</dt>
                                <dd class="col-sm-8 doc-actions">
                                    <!-- <button class="btn btn-sm btn-dark-red"
                                            data-bs-toggle="modal"
                                            data-bs-target="#documentViewerModal"
                                            onclick="prepareDocumentViewer('{{ asset('storage/'.$project->file_path) }}')">
                                        <i class="far fa-eye"></i> View
                                    </button> -->

                                    <a href="{{ route('research_projects.download',$project->id) }}"
                                       class="btn btn-sm btn-secondary">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                {{-- TEAM --}}
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light fw-semibold">
                            Project Team
                        </div>

                        <div class="card-body">
                            @foreach($project->team as $member)
                                <div class="member-card p-3 rounded mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ ucfirst($member->pivot->role) }}</strong><br>
                                            {{ $member->firstname }} {{ $member->lastname }}
                                            <div class="small text-muted">{{ $member->email }}</div>
                                        </div>

                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge {{ $member->pivot->role === 'leader' ? 'bg-danger' : 'bg-secondary' }}">
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

{{-- DOCUMENT VIEWER --}}
<div class="modal fade" id="documentViewerModal">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">Document Viewer</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="viewerContainer" style="height:80vh;"></div>
            </div>
        </div>
    </div>
</div>

{{-- JS --}}
<script src="https://cdn.syncfusion.com/ej2/26.2.4/dist/ej2.min.js"></script>
<script>
function prepareDocumentViewer(fileUrl){
    const container=document.getElementById('viewerContainer');
    container.innerHTML='';
    const ext=fileUrl.split('.').pop().toLowerCase();

    if(ext==='pdf'){
        new ej.pdfviewer.PdfViewer({documentPath:fileUrl}).appendTo(container);
    }else{
        container.innerHTML=`<iframe src="https://docs.google.com/gview?url=${encodeURIComponent(fileUrl)}&embedded=true"
            style="width:100%;height:100%;border:none"></iframe>`;
    }
}
</script>

@endsection
