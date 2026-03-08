@extends('project.staff.layout.app')

@section('title', 'Event Calendar')

@section('content')
<div class="container-fluid p-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Event Calendar</h1>
                <a href="{{ route('events.create') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus"></i> Add Event
                </a>
            </div>
        </div>
        <div class="card-body">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Event Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Event Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="eventForm">
                    @csrf
                    <input type="hidden" id="eventId">
                    <div class="form-group">
                        <label for="title">Event Title</label>
                        <input type="text" class="form-control" id="title" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="start">Start Date/Time</label>
                            <input type="datetime-local" class="form-control" id="start" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="end">End Date/Time</label>
                            <input type="datetime-local" class="form-control" id="end">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" rows="3"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="project_id">Project</label>
                            <select class="form-control" id="project_id">
                                <option value="">Select Project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="category_id">Category</label>
                            <select class="form-control" id="category_id">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="allDay">
                                <label class="form-check-label" for="allDay">All Day Event</label>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="color">Event Color</label>
                            <input type="color" class="form-control" id="color" value="#3c8dbc">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="deleteBtn">Delete</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveBtn">Save Event</button>
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
        border-radius: 0.25rem;
    }
    .fc-header-toolbar {
        padding: 1rem;
        margin-bottom: 0.5rem !important;
    }
    .fc-button {
        background-color: #4e73df !important;
        border-color: #4e73df !important;
        color: white !important;
    }
    .fc-event {
        cursor: pointer;
    }
    @media (max-width: 768px) {
        .fc-header-toolbar {
            flex-direction: column;
            align-items: flex-start;
        }
        .fc-toolbar-chunk {
            margin-bottom: 0.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Toastr
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right'
    };

    // Initialize Calendar
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: '{{ route("calendar.events") }}',
        selectable: true,
        selectMirror: true,
        navLinks: true,
        editable: true,
        dayMaxEvents: true,
        select: function(info) {
            openModal({
                start: info.startStr,
                end: info.endStr,
                allDay: info.allDay
            });
        },
        eventClick: function(info) {
            openModal(info.event);
        },
        eventDrop: function(info) {
            updateEvent(info.event);
        },
        eventResize: function(info) {
            updateEvent(info.event);
        }
    });
    calendar.render();

    // Modal and Form Handling
    const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
    const eventForm = document.getElementById('eventForm');
    const deleteBtn = document.getElementById('deleteBtn');

    function openModal(event) {
        const form = eventForm;
        if (event.id) {
            // Existing event
            document.getElementById('eventId').value = event.id;
            document.getElementById('title').value = event.title;
            document.getElementById('start').value = formatDateTime(event.start);
            document.getElementById('end').value = event.end ? formatDateTime(event.end) : '';
            document.getElementById('description').value = event.extendedProps.description || '';
            document.getElementById('allDay').checked = event.allDay;
            document.getElementById('color').value = event.backgroundColor || '#3c8dbc';
            document.getElementById('project_id').value = event.extendedProps.project_id || '';
            document.getElementById('category_id').value = event.extendedProps.category_id || '';
            
            deleteBtn.style.display = 'block';
            document.getElementById('modalTitle').textContent = 'Edit Event';
        } else {
            // New event
            form.reset();
            document.getElementById('start').value = formatDateTime(event.start);
            document.getElementById('end').value = event.end ? formatDateTime(event.end) : '';
            document.getElementById('allDay').checked = event.allDay;
            
            deleteBtn.style.display = 'none';
            document.getElementById('modalTitle').textContent = 'Add New Event';
        }
        eventModal.show();
    }

    function formatDateTime(date) {
        return date.replace(/T.*$/, '') + 'T' + date.match(/T(\d{2}:\d{2})/)[1];
    }

    document.getElementById('saveBtn').addEventListener('click', function() {
        const form = eventForm;
        const eventId = document.getElementById('eventId').value;
        const eventData = {
            title: document.getElementById('title').value,
            start: document.getElementById('start').value,
            end: document.getElementById('end').value,
            description: document.getElementById('description').value,
            allDay: document.getElementById('allDay').checked,
            color: document.getElementById('color').value,
            project_id: document.getElementById('project_id').value || null,
            category_id: document.getElementById('category_id').value || null
        };

        if (!eventData.title) {
            toastr.error('Title is required');
            return;
        }

        const url = eventId ? `/events/${eventId}` : '/events';
        const method = eventId ? 'put' : 'post';

        axios[method](url, eventData)
            .then(response => {
                calendar.refetchEvents();
                eventModal.hide();
                toastr.success(`Event ${eventId ? 'updated' : 'created'} successfully`);
            })
            .catch(error => {
                console.error(error);
                toastr.error('An error occurred');
            });
    });

    deleteBtn.addEventListener('click', function() {
        if (confirm('Are you sure you want to delete this event?')) {
            const eventId = document.getElementById('eventId').value;
            axios.delete(`/events/${eventId}`)
                .then(response => {
                    calendar.refetchEvents();
                    eventModal.hide();
                    toastr.success('Event deleted successfully');
                })
                .catch(error => {
                    console.error(error);
                    toastr.error('Error deleting event');
                });
        }
    });

    function updateEvent(event) {
        const eventData = {
            title: event.title,
            start: event.startStr,
            end: event.endStr,
            allDay: event.allDay,
            color: event.backgroundColor
        };

        axios.put(`/events/${event.id}`, eventData)
            .catch(error => {
                console.error(error);
                toastr.error('Error updating event');
                event.revert();
            });
    }
});
</script>
@endpush