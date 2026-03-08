@extends('project.staff.layout.app')

@section('title', 'Event Calendar')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid p-4">
    <div class="card shadow-sm rounded-3">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Event Calendar</h3>
            <a href="{{ route('staffevents.index') }}" class="btn btn-light btn-sm">
                <i class="fas fa-list me-1"></i> List View
            </a>
        </div>
        <div class="card-body">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">Event Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="eventForm">
                    @csrf
                    <input type="hidden" id="eventId">
                    <div class="mb-3">
                        <label for="title" class="form-label">Event Title</label>
                        <input type="text" class="form-control" id="title" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="start" class="form-label">Start Date/Time</label>
                            <input type="datetime-local" class="form-control" id="start" required>
                        </div>
                        <div class="col-md-6">
                            <label for="end" class="form-label">End Date/Time</label>
                            <input type="datetime-local" class="form-control" id="end">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" rows="3"></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="allDay">
                                <label class="form-check-label" for="allDay">All Day Event</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="color" class="form-label">Event Color</label>
                            <input type="color" class="form-control form-control-color" id="color" value="#3c8dbc">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger me-auto" id="deleteBtn">
                    <i class="fas fa-trash-alt me-1"></i> Delete
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Close
                </button>
                <button type="button" class="btn btn-primary" id="saveBtn">
                    <i class="fas fa-save me-1"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Delete Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this event?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<style>
    #calendar {
        background-color: #fff;
        border-radius: 0.5rem;
        padding: 10px;
        box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,0.1);
    }
    .fc .fc-toolbar-title {
        font-weight: 600;
    }
    .fc-button {
        background-color: #4e73df !important;
        border-color: #4e73df !important;
        color: #fff !important;
    }
    .fc-event {
        border-radius: 0.35rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .fc-event:hover {
        opacity: 0.85;
        transform: translateY(-1px);
    }
    .modal-content {
        border: none;
    }
    .btn-close {
        font-size: 1.2rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    toastr.options = { closeButton: true, progressBar: true, positionClass: 'toast-top-right', timeOut: 3000 };
    
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' },
        events: '{{ route("calendar.events") }}',
        selectable: true,
        editable: true,
        dayMaxEvents: true,
        select: function(info) { openModal({ start: info.startStr, end: info.endStr, allDay: info.allDay }); },
        eventClick: function(info) { openModal(info.event); },
        eventDrop: function(info) { updateEvent(info.event); },
        eventResize: function(info) { updateEvent(info.event); },
        eventDidMount: function(info) {
            if (info.event.extendedProps.color) {
                info.el.style.backgroundColor = info.event.extendedProps.color;
                info.el.style.borderColor = info.event.extendedProps.color;
            }
        }
    });
    calendar.render();

    const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
    const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
    const eventForm = document.getElementById('eventForm');
    const deleteBtn = document.getElementById('deleteBtn');
    let currentEventId = null;

    function openModal(event) {
        if(event.id) {
            currentEventId = event.id;
            document.getElementById('eventId').value = event.id;
            document.getElementById('title').value = event.title || '';
            document.getElementById('start').value = moment(event.start).format('YYYY-MM-DDTHH:mm');
            document.getElementById('end').value = event.end ? moment(event.end).format('YYYY-MM-DDTHH:mm') : '';
            document.getElementById('description').value = event.extendedProps.description || '';
            document.getElementById('allDay').checked = event.allDay || false;
            document.getElementById('color').value = event.extendedProps.color || '#3c8dbc';
            deleteBtn.style.display = 'block';
            document.getElementById('modalTitle').textContent = 'Edit Event';
        } else {
            currentEventId = null;
            eventForm.reset();
            document.getElementById('start').value = event.start || '';
            document.getElementById('end').value = event.end || '';
            document.getElementById('allDay').checked = event.allDay || false;
            document.getElementById('color').value = '#3c8dbc';
            deleteBtn.style.display = 'none';
            document.getElementById('modalTitle').textContent = 'Create New Event';
        }
        eventModal.show();
    }

    document.getElementById('saveBtn').addEventListener('click', function() {
        const eventData = {
            title: document.getElementById('title').value,
            start: document.getElementById('start').value,
            end: document.getElementById('end').value || null,
            description: document.getElementById('description').value,
            allDay: document.getElementById('allDay').checked,
            color: document.getElementById('color').value
        };
        if(!eventData.title){ toastr.error('Title is required'); return; }
        const url = currentEventId ? `/events/${currentEventId}` : '/events';
        const method = currentEventId ? 'put' : 'post';
        axios[method](url, eventData)
            .then(()=> { calendar.refetchEvents(); eventModal.hide(); toastr.success(`Event ${currentEventId ? 'updated' : 'created'} successfully`); })
            .catch(()=> toastr.error('An error occurred'));
    });

    deleteBtn.addEventListener('click', ()=> { eventModal.hide(); confirmDeleteModal.show(); });

    document.getElementById('confirmDeleteBtn').addEventListener('click', ()=>{
        axios.delete(`/events/${currentEventId}`)
            .then(()=> { calendar.refetchEvents(); confirmDeleteModal.hide(); toastr.success('Event deleted successfully'); })
            .catch(()=> { toastr.error('Error deleting event'); confirmDeleteModal.hide(); eventModal.show(); });
    });

    function updateEvent(event){
        axios.put(`/events/${event.id}`, {
            title: event.title,
            start: event.startStr,
            end: event.endStr,
            allDay: event.allDay
        }).then(()=> toastr.success('Event updated successfully'))
        .catch(()=> { toastr.error('Error updating event'); event.revert(); });
    }
});
</script>
@endpush
