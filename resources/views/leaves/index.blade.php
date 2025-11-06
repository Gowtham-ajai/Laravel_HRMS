<div class="container mt-4">
    <h2 class="mb-4">
        <i class="bi bi-calendar-check me-2"></i>Leave Requests
    </h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered" id="hrLeavesTable">
        <thead class="table-dark">
            <tr>
                <th>Employee</th>
                <th>Type</th>
                <th>Start</th>
                <th>End</th>
                <th>Remarks</th>
                <th>Applicant Type</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($leaves as $leave)
            <tr id="leaveRow-{{ $leave->id }}">
                <td>{{ $leave->employee->first_name }} {{ $leave->employee->last_name }}</td>
                <br>
                <span class="badge bg-info">Employee</span>
                <td>{{ $leave->leave_type }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}</td>
                <td>{{ $leave->remarks ?? '-' }}</td>
                <td>
                    @if($leave->employee->register->role === 'hr')
                        <span class="badge bg-info">HR Leave</span>
                    @else
                        <span class="badge bg-secondary">Employee Leave</span>
                    @endif
                </td>
                <td class="status-cell">
                    <span class="badge 
                        @if($leave->status == 'Pending') bg-warning
                        @elseif($leave->status == 'Approved') bg-success
                        @else bg-danger @endif">
                        {{ $leave->status }}
                    </span>
                </td>
                <td>
                    @if($leave->status == 'Pending')
                        <button class="btn btn-success btn-sm approveBtn" data-id="{{ $leave->id }}">
                            <i class="bi bi-check-circle me-1"></i>Approve</button>
                        <button class="btn btn-danger btn-sm rejectBtn" data-id="{{ $leave->id }}">
                            <i class="bi bi-x-circle me-1"></i>Reject</button>
                    @else
                        <span class="text-muted">
                            @if($leave->status == 'Approved')
                                <i class="bi bi-check-circle text-success me-1"></i>Approved
                            @else
                                <i class="bi bi-x-circle text-danger me-1"></i>Rejected
                            @endif
                        </span>
                    @endif
                </td>
            </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <i class="bi bi-inbox display-4 text-muted d-block mb-2"></i>
                        <span class="text-muted">No HR leave requests found</span>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {

        // Approve leave
        $(document).on('click', '.approveBtn', function() {
            const leaveId = $(this).data('id');
            updateLeaveStatus(leaveId, 'Approved');
        });

        // Reject leave
        $(document).on('click', '.rejectBtn', function() {
            const leaveId = $(this).data('id');
            updateLeaveStatus(leaveId, 'Rejected');
        });

        function updateLeaveStatus(leaveId, status) {
            $.ajax({
                url: "{{ route('hr.updateLeaveStatus') }}",
                type: "POST",
                data: {
                    leave_id: leaveId,
                    status: status,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        const row = $("#leaveRow-" + response.leave_id);
                        
                        // Update status text + badge color dynamically
                        const badge = row.find('.status-cell span');
                        badge.text(response.new_status);
                        
                        badge.removeClass('bg-warning bg-success bg-danger');
                        if (response.new_status === 'Approved') badge.addClass('bg-success');
                        else if (response.new_status === 'Rejected') badge.addClass('bg-danger');
                        else badge.addClass('bg-warning');

                        // Hide action buttons after approval/rejection
                        row.find('td:last').html('<span class="text-muted">Processed</span>');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 403) {
                        alert(xhr.responseJSON.message || 'You are not authorized to perform this action!');
                    } else {
                        alert("Something went wrong while updating status!");
                    }
                }
            });
        }

    });
</script>