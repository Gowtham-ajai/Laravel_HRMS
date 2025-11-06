@extends('layouts.master')

@section('title', 'Contact Messages')

@section('content')

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow border-0">
                <div class="card-header d-flex justify-content-between align-items-center text-white" 
                     style="background: linear-gradient(90deg, #1e3c72, #2a5298);">
                    <h5 class="mb-0">
                        <i class="bi bi-envelope me-2"></i>Contact Messages
                        @if($unreadCount > 0)
                            <span class="badge bg-danger ms-2">{{ $unreadCount }} unread</span>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    @if($messages->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <h4 class="text-muted mt-3">No messages yet</h4>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>From</th>
                                        <th>Email</th>
                                        <th>Subject</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($messages as $message)
                                        <tr class="{{ $message->is_read ? '' : 'table-active' }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($message->employee)
                                                        <img src="{{ asset('storage/'.$message->employee->photo) }}" 
                                                             alt="{{ $message->name }}" 
                                                             class="rounded-circle me-2" 
                                                             style="width: 32px; height: 32px; object-fit: cover;">
                                                        <div>
                                                            <strong>{{ $message->name }}</strong>
                                                            <small class="d-block text-muted">Employee</small>
                                                        </div>
                                                    @else
                                                        <div>
                                                            <strong>{{ $message->name }}</strong>
                                                            <small class="d-block text-muted">Visitor</small>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>{{ $message->email }}</td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 200px;">
                                                    {{ $message->subject }}
                                                </div>
                                            </td>
                                            <td>{{ $message->created_at->format('M d, Y h:i A') }}</td>
                                            <td>
                                                @if($message->is_read)
                                                    <span class="badge bg-success">Read</span>
                                                @else
                                                    <span class="badge bg-warning">Unread</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.contact.message.show', $message->id) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            onclick="confirmDelete({{ $message->id }})">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this message? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(messageId) {
    const form = document.getElementById('deleteForm');
    form.action = `/admin/contact-messages/${messageId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

@endsection