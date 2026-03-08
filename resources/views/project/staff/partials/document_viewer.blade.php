<div class="modal fade" id="documentViewerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-file-alt text-danger me-2"></i> Document Viewer
                </h5>

                <div class="ms-auto d-flex gap-2">
                    <a href="{{ route('research_projects.download', $project->id) }}"
                       class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-download me-1"></i> Download
                    </a>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
            </div>

            <div class="modal-body p-0">
                <div id="viewerContainer" style="height: 80vh;">

                    {{-- PDF Viewer --}}
                    <div id="pdfViewer" style="display:none; width:100%; height:100%;">
                        <iframe id="pdfFrame"
                                style="width:100%; height:100%; border:none;"></iframe>
                    </div>

                    {{-- External Viewer --}}
                    <div id="externalViewer" style="display:none; width:100%; height:100%;">
                        <iframe id="externalFrame"
                                style="width:100%; height:100%; border:none;"></iframe>
                    </div>

                    {{-- Unsupported --}}
                    <div id="unsupportedViewer"
                         class="d-flex flex-column justify-content-center align-items-center h-100 text-center"
                         style="display:none;">
                        <h5 class="fw-bold mb-2">Preview not available</h5>
                        <p class="text-muted mb-3">
                            This file type cannot be previewed. Please download the file.
                        </p>
                        <a href="{{ route('research_projects.download', $project->id) }}"
                           class="btn btn-danger">
                            <i class="fas fa-download me-1"></i> Download File
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
