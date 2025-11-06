<div class="card shadow-sm border-0 mb-3">
    <div class="card-header text-white" 
         style="background: linear-gradient(90deg, #1e3c72, #2a5298);">
        <h5 class="mb-0 fw-bold">Apply Leave</h5>
    </div>

    <div class="card-body">
        <form id="applyLeaveForm">
            @csrf
            <div class="mb-2">
                <label for="leave_type">Leave Type</label>
                <select name="leave_type" id="leave_type" class="form-control" required>
                    <option value="Sick">Sick</option>
                    <option value="Casual">Casual</option>
                    <option value="Annual">Annual</option>
                    <option value="Unpaid">Unpaid</option>
                </select>
            </div>

            <div class="mb-2">
                <label for="start_date">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control" required>
            </div>

            <div class="mb-2">
                <label for="end_date">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control" required>
            </div>

            <div class="mb-2">
                <label for="remarks">Remarks</label>
                <textarea name="remarks" id="remarks" class="form-control" rows="3"></textarea>
            </div>

            <button type="submit" 
                class="btn border-0 text-white fw-semibold px-4 py-2 rounded-pill"
                style="background: linear-gradient(90deg, #1e3c72, #2a5298); transition: 0.3s;">
                Apply Leave
            </button>

        </form>

        <div id="successMessage" class="alert alert-success mt-3 d-none">
            Leave applied successfully!
        </div>
    </div>
</div>


<!-- Leave History -->
<div class="card shadow-sm border-0">
    <div class="card-header text-white"
         style="background: linear-gradient(90deg, #1e3c72, #2a5298);">
        <h5 class="mb-0 fw-bold">My Leaves</h5>
    </div>

    <div class="card-body">
        <table class="table table-bordered" id="leavesTable">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Status</th>
                    <th>Remarks</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leaves as $leave)
                    <tr id="leave-{{ $leave->id }}">
                        <td>{{ $leave->leave_type }}</td>
                        <td>{{ $leave->start_date }}</td>
                        <td>{{ $leave->end_date }}</td>
                        <td>
                            <span class="badge 
                                @if($leave->status == 'Pending') bg-warning
                                @elseif($leave->status == 'Approved') bg-success
                                @else bg-danger
                                @endif">{{ $leave->status }}</span>
                        </td>
                        <td>{{ $leave->remarks }}</td>
                        <td>—</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


<!-- AJAX Script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#applyLeaveForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: "{{ route('employee.applyLeave') }}",
            method: "POST",
            data: $(this).serialize(),
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            success: function(response) {
                if (response.success) {
                    alert(response.message);

                    // Add new leave instantly in table
                    let newRow = `
                        <tr>
                            <td>${response.leave.leave_type}</td>
                            <td>${response.leave.start_date}</td>
                            <td>${response.leave.end_date}</td>
                            <td>${response.leave.status}</td>
                            <td>${response.leave.remarks ?? ''}</td>
                        </tr>
                    `;
                    $('#leavesTable tbody').prepend(newRow);

                    // Reset the form
                    $('#applyLeaveForm')[0].reset();
                }
            },
            error: function(xhr) {
                alert('Error submitting leave! Please try again.');
            }
        });
    });
});
</script>

<script>
$(document).ready(function() {
    // Hover glow effect
    $('button[type="submit"]').hover(
        function() {
            $(this).css('box-shadow', '0 0 12px rgba(30, 60, 114, 0.6)');
        },
        function() {
            $(this).css('box-shadow', 'none');
        }
    );
});
</script>

