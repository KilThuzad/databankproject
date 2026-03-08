@extends('project.researcher.layout.app')

@section('title', 'Event Calendar')

@section('content')

<link rel="stylesheet" href="{{ asset('css/researcher/style.css') }}">
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid p-4">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2" style="color: #333; font-weight: 700;">Event Calendar</h1>
            <p class="text-muted mb-0">View and manage events in calendar format</p>
        </div>
        <div>
            <a href="{{ route('userevents.index') }}" class="btn" style="background: #e74a3b; color: white; border: none; border-radius: 6px; padding: 0.5rem 1rem;">
                <i class="fas fa-list me-2"></i> List View
            </a>
        </div>
    </div>

    {{-- Calendar Card --}}
    <div class="card shadow-sm" style="border: none; border-radius: 8px;">
        <div class="card-header border-0 p-3" style="background: linear-gradient(135deg, #e74a3b 0%, #c0392b 100%); border-radius: 8px 8px 0 0;">
            <h2 class="h5 mb-0 d-inline-flex align-items-center" style="color: white; font-weight: 600;">
                <i class="fas fa-calendar-alt me-2"></i>
                Event Schedule
            </h2>
        </div>
        <div class="card-body p-3">
            <div id="calendar"></div>
        </div>
    </div>
</div>

{{-- Event Modal --}}
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border: none; border-radius: 8px; overflow: hidden;">
            <div class="modal-header border-0 p-3" style="background: linear-gradient(135deg, #e74a3b 0%, #c0392b 100%);">
                <h5 class="modal-title text-white" id="modalTitle">Event Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="eventForm">
                    @csrf
                    <input type="hidden" id="eventId">
                    <div class="mb-3">
                        <label for="title" class="form-label" style="color: #333; font-weight: 500;">Event Title</label>
                        <input type="text" class="form-control" id="title" required 
                               style="border: 1px solid #ddd; border-radius: 6px; padding: 0.5rem 0.75rem;">
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="start" class="form-label" style="color: #333; font-weight: 500;">Start Date/Time</label>
                            <input type="datetime-local" class="form-control" id="start" required 
                                   style="border: 1px solid #ddd; border-radius: 6px; padding: 0.5rem 0.75rem;">
                        </div>
                        <div class="col-md-6">
                            <label for="end" class="form-label" style="color: #333; font-weight: 500;">End Date/Time</label>
                            <input type="datetime-local" class="form-control" id="end" 
                                   style="border: 1px solid #ddd; border-radius: 6px; padding: 0.5rem 0.75rem;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label" style="color: #333; font-weight: 500;">Description</label>
                        <textarea class="form-control" id="description" rows="3" 
                                  style="border: 1px solid #ddd; border-radius: 6px; padding: 0.5rem 0.75rem;"></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="allDay" 
                                       style="border-color: #e74a3b;">
                                <label class="form-check-label" for="allDay" style="color: #666;">All Day Event</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="color" class="form-label" style="color: #333; font-weight: 500;">Event Color</label>
                            <div class="d-flex align-items-center">
                                <input type="color" class="form-control-color" id="color" value="#e74a3b" 
                                       style="width: 40px; height: 40px; border-radius: 6px; border: 1px solid #ddd; cursor: pointer;">
                                <span class="ms-2 small text-muted">Click to change</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 p-3" style="background: #f8f9fa;">
                <button type="button" class="btn btn-sm" id="deleteBtn"
                        style="background: #dc3545; color: white; border: none; border-radius: 6px; padding: 0.375rem 0.75rem; display: none;">
                    <i class="fas fa-trash me-1"></i> Delete
                </button>
                <div class="ms-auto">
                    <button type="button" class="btn btn-sm me-2" data-bs-dismiss="modal"
                            style="background: #6c757d; color: white; border: none; border-radius: 6px; padding: 0.375rem 0.75rem;">
                        <i class="fas fa-times me-1"></i> Close
                    </button>
                    <button type="button" class="btn btn-sm" id="saveBtn"
                            style="background: #e74a3b; color: white; border: none; border-radius: 6px; padding: 0.375rem 0.75rem;">
                        <i class="fas fa-save me-1"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border: none; border-radius: 8px; overflow: hidden;">
            <div class="modal-header border-0 p-3" style="background: #dc3545;">
                <h5 class="modal-title text-white">Confirm Deletion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3" style="color: #dc3545;"></i>
                    <h6 class="mb-2" style="color: #333; font-weight: 600;">Are you sure you want to delete this event?</h6>
                    <p class="text-muted mb-0">This action cannot be undone.</p>
                </div>
            </div>
            <div class="modal-footer border-0 p-3" style="background: #f8f9fa;">
                <button type="button" class="btn btn-sm me-2" data-bs-dismiss="modal"
                        style="background: #6c757d; color: white; border: none; border-radius: 6px; padding: 0.375rem 0.75rem;">
                    Cancel
                </button>
                <button type="button" class="btn btn-sm" id="confirmDeleteBtn"
                        style="background: #dc3545; color: white; border: none; border-radius: 6px; padding: 0.375rem 0.75rem;">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>
    
@endsection


@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 3000,
        escapeHtml: true
    };

    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        themeSystem: 'standard',
        events: '{{ route("calendar.events") }}',
        selectable: true,
        selectMirror: true,
        navLinks: true,
        editable: true,
        dayMaxEvents: true,
        nowIndicator: true,
        firstDay: 1, 
        dayHeaderFormat: { weekday: 'short' },
        buttonText: {
            today: 'Today',
            month: 'Month',
            week: 'Week',
            day: 'Day'
        },
        
       
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
        },
        
        eventDidMount: function(info) {
     
            if (info.event.extendedProps.color) {
                info.el.style.backgroundColor = info.event.extendedProps.color;
                info.el.style.borderColor = info.event.extendedProps.color;
            }
            
           
            if (info.event.extendedProps.description) {
                info.el.title = info.event.extendedProps.description;
            }
        },
        
        datesSet: function(info) {
            document.querySelector('.fc-toolbar-title').style.color = '#333';
            document.querySelector('.fc-toolbar-title').style.fontWeight = '600';
        }
    });
    
    calendar.render();


    const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
    const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
    const eventForm = document.getElementById('eventForm');
    const deleteBtn = document.getElementById('deleteBtn');
    let currentEventId = null;

 
    function openModal(event) {
        const form = eventForm;
        if (event.id) {
            currentEventId = event.id;
            document.getElementById('eventId').value = event.id;
            document.getElementById('title').value = event.title || '';
            document.getElementById('start').value = formatDateTime(event.start);
            document.getElementById('end').value = event.end ? formatDateTime(event.end) : '';
            document.getElementById('description').value = event.extendedProps.description || '';
            document.getElementById('allDay').checked = event.allDay || false;
            document.getElementById('color').value = event.extendedProps.color || '#e74a3b';
            
            deleteBtn.style.display = 'block';
            document.getElementById('modalTitle').textContent = 'Edit Event';
        } else {
            currentEventId = null;
            form.reset();
            document.getElementById('start').value = event.start ? formatDateTime(event.start) : '';
            document.getElementById('end').value = event.end ? formatDateTime(event.end) : '';
            document.getElementById('allDay').checked = event.allDay || false;
            document.getElementById('color').value = '#e74a3b';
            
            deleteBtn.style.display = 'none';
            document.getElementById('modalTitle').textContent = 'Create New Event';
        }
        eventModal.show();
    }

  
    function formatDateTime(date) {
        return moment(date).format('YYYY-MM-DDTHH:mm');
    }


    document.getElementById('saveBtn').addEventListener('click', function() {
        const form = eventForm;
        const eventId = document.getElementById('eventId').value;
        const eventData = {
            title: document.getElementById('title').value,
            start: document.getElementById('start').value,
            end: document.getElementById('end').value || null,
            description: document.getElementById('description').value,
            allDay: document.getElementById('allDay').checked,
            color: document.getElementById('color').value
        };

        if (!eventData.title) {
            toastr.error('Title is required');
            return;
        }

        const url = eventId ? `/events/${eventId}` : '/events';
        const method = eventId ? 'put' : 'post';

        axios({
            method: method,
            url: url,
            data: eventData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            calendar.refetchEvents();
            eventModal.hide();
            toastr.success(`Event ${eventId ? 'updated' : 'created'} successfully`);
        })
        .catch(error => {
            console.error(error);
            toastr.error('An error occurred while saving the event');
        });
    });


    deleteBtn.addEventListener('click', function() {
        eventModal.hide();
        confirmDeleteModal.show();
    });


    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        axios.delete(`/events/${currentEventId}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            calendar.refetchEvents();
            confirmDeleteModal.hide();
            toastr.success('Event deleted successfully');
        })
        .catch(error => {
            console.error(error);
            toastr.error('Error deleting event');
            confirmDeleteModal.hide();
            eventModal.show();
        });
    });


    function updateEvent(event) {
        const eventData = {
            title: event.title,
            start: event.startStr,
            end: event.endStr,
            allDay: event.allDay
        };

        axios.put(`/events/${event.id}`, eventData, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            toastr.success('Event updated successfully');
        })
        .catch(error => {
            console.error(error);
            toastr.error('Error updating event');
            event.revert();
        });
    }

    document.querySelectorAll('#eventForm input, #eventForm textarea').forEach(input => {
        input.addEventListener('invalid', function(e) {
            e.preventDefault();
            this.style.borderColor = '#dc3545';
            toastr.error('Please fill in all required fields');
        });
        
        input.addEventListener('input', function() {
            this.style.borderColor = '#ddd';
        });
    });
});
</script>
@endpush