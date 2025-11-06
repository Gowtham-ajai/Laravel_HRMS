
<div class="container mt-4">
    <h3 class="mb-4">Attendance for {{ $employee->first_name }} {{ $employee->last_name }}</h3>

    <form method="POST" action="{{ route('employees.attendance.store', $employee->id) }}">
        @csrf
        <div class="mb-3">
            <label>Date</label>
            <input type="date" name="date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Check In</label>
            <input type="time" name="check_in" class="form-control">
        </div>
        <div class="mb-3">
            <label>Check Out</label>
            <input type="time" name="check_out" class="form-control">
        </div>
        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control" required>
                <option value="Present">Present</option>
                <option value="Absent">Absent</option>
                <option value="Late">Late</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Save Attendance</button>
    </form>

    <h5 class="mt-5">Previous Records</h5>
    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th>Date</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employee->attendances as $att)
            <tr>
                <td>{{ $att->date }}</td>
                <td>{{ $att->check_in ?? '-' }}</td>
                <td>{{ $att->check_out ?? '-' }}</td>
                <td>{{ $att->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

