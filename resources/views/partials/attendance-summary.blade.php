<div class="card shadow-sm border-0" id="attendance-summary">
    <div class="card-header text-white" 
         style="background: linear-gradient(90deg, #1e3c72, #2a5298);">
        <h5 class="mb-0 fw-bold">Today’s Attendance</h5>
    </div>

    <div class="card-body">
        @php
            $checkIn  = $todayAttendance->check_in;
            $checkOut = $todayAttendance->check_out;
            $breaks   = $todayAttendance->breaks ?? [];

            // ✅ Safely handle break detection
            $lastBreak = !empty($breaks) ? end($breaks) : null;
            $isOnBreak = false;

            if ($lastBreak) {
                if (is_array($lastBreak)) {
                    $isOnBreak = (isset($lastBreak['break_in']) && !isset($lastBreak['break_out'])) 
                                 || (isset($lastBreak['break_out']) && is_null($lastBreak['break_out']));
                } elseif (is_object($lastBreak)) {
                    $isOnBreak = property_exists($lastBreak, 'break_in') && 
                                (!property_exists($lastBreak, 'break_out') || is_null($lastBreak->break_out));
                }
            }
        @endphp

        <div class="d-flex flex-wrap gap-2 mb-3">
            <button class="btn btn-success attendance-btn" id="checkInBtn"
                data-action="checkin"
                @if($checkIn) disabled @endif>
                Check In
            </button>

            <button class="btn btn-warning attendance-btn" id="breakInBtn"
                data-action="breakin"
                @if(!$checkIn || $isOnBreak || $checkOut) disabled @endif>
                Break In
            </button>

            <button class="btn btn-info attendance-btn" id="breakOutBtn"
                data-action="breakout"
                @if(!$isOnBreak || $checkOut) disabled @endif>
                Break Out
            </button>

            <button class="btn btn-danger attendance-btn" id="checkOutBtn"
                data-action="checkout"
                @if(!$checkIn || $checkOut || $isOnBreak) disabled @endif>
                Check Out
            </button>
        </div>

        <div class="small text-muted mb-2">
            <p>Check In: {{ $checkIn ? \Carbon\Carbon::parse($checkIn)->format('h:i A') : '—' }}</p>
            <p>Check Out: {{ $checkOut ? \Carbon\Carbon::parse($checkOut)->format('h:i A') : '—' }}</p>
            <p>Status:
                <span class="badge
                    @if($todayAttendance->status == 'Present') bg-success
                    @elseif($todayAttendance->status == 'Half Day') bg-warning
                    @elseif($todayAttendance->status == 'Absent') bg-danger
                    @else bg-secondary @endif">
                    {{ $todayAttendance->status ?? 'Not Checked In' }}
                </span>
            </p>
            <p>Total Working Hours: <span id="totalWorkHours">{{ $todayAttendance->total_work_hours ? gmdate('H:i:s', $todayAttendance->total_work_hours) : '00:00:00' }}</span></p>
            <p>Break Duration: <span id="breakTimer">{{ $todayAttendance->total_break_seconds ? gmdate('H:i:s', $todayAttendance->total_break_seconds) : '00:00:00' }}</span></p>

        </div>
    </div>
</div>

<!-- ✅ Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    $('.attendance-btn').on('click', function() {
        let action = $(this).data('action');
        let button = $(this);
        button.prop('disabled', true); // prevent double-click

        $.ajax({
            url: "{{ route('attendance.action') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                action: action
            },
            success: function(response) {
                if (response.html) {
                    $('#attendance-summary').html(response.html);
                }
                // Optionally add Toastr success message here
            },
            error: function(xhr) {
                alert(xhr.responseJSON?.error || "Something went wrong!");
                button.prop('disabled', false);
            }
        });
    });
});
</script>

