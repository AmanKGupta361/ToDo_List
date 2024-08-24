<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Tasks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 800px;
            margin-top: 50px;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .list-group-item {
            transition: background-color 0.3s ease;
        }
        .list-group-item:hover {
            background-color: #f1f1f1;
        }
        .completed-task {
            /* text-decoration: line-through; */
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">All Tasks</h2>

        <ul class="nav nav-tabs mb-3" id="task-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="all-tasks-tab" data-bs-toggle="tab" data-bs-target="#all-tasks" type="button" role="tab" aria-controls="all-tasks" aria-selected="true">All Tasks</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="completed-tasks-tab" data-bs-toggle="tab" data-bs-target="#completed-tasks" type="button" role="tab" aria-controls="completed-tasks" aria-selected="false">Completed Tasks</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pending-tasks-tab" data-bs-toggle="tab" data-bs-target="#pending-tasks" type="button" role="tab" aria-controls="pending-tasks" aria-selected="false">Pending Tasks</button>
            </li>
        </ul>

        <div class="tab-content" id="task-tabs-content">
            <div class="tab-pane fade show active" id="all-tasks" role="tabpanel" aria-labelledby="all-tasks-tab">
                <ul class="list-group">
                    @foreach($tasks as $task)
                        <li class="list-group-item d-flex justify-content-between align-items-center {{ $task->is_completed ? 'completed-task' : '' }}">
                            <span>{{ $task->title }}</span>
                            <div>
                                @if (!$task->is_completed)
                                    <button class="btn btn-primary btn-sm mark-complete" data-id="{{ $task->id }}">Mark as Complete</button>
                                @else
                                    <button class="btn btn-success btn-sm " data-id="{{ $task->id }}">Completed</button>
                                @endif
                                <button class="btn btn-danger btn-sm delete-task" data-id="{{ $task->id }}">Delete</button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="tab-pane fade" id="completed-tasks" role="tabpanel" aria-labelledby="completed-tasks-tab">
                <ul class="list-group">
                    @foreach($completedTasks as $task)
                        <li class="list-group-item d-flex justify-content-between align-items-center completed-task">
                            <span>{{ $task->title }}</span>
                            <div>
                                <button class="btn btn-success btn-sm " data-id="{{ $task->id }}">Completed</button>
                                <button class="btn btn-danger btn-sm delete-task" data-id="{{ $task->id }}">Delete</button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="tab-pane fade" id="pending-tasks" role="tabpanel" aria-labelledby="pending-tasks-tab">
                <ul class="list-group">
                    @foreach($pendingTasks as $task)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $task->title }}</span>
                            <div>
                                <button class="btn btn-primary btn-sm mark-complete" data-id="{{ $task->id }}">Mark as Complete</button>
                                <button class="btn btn-danger btn-sm delete-task" data-id="{{ $task->id }}">Delete</button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <a href="/" class="btn btn-primary mt-3">Back to Home</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $(document).on('click', '.mark-complete', function() {
                let id = $(this).data('id');
                $.ajax({
                    url: `/tasks/${id}`,
                    type: 'PATCH',
                    data: {_token: '{{ csrf_token() }}'},
                    success: function(data) {
                        if (data.success) {
                            Swal.fire('Success', 'Task marked as complete', 'success');
                            location.reload();
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Error', xhr.responseText, 'error');
                    }
                });
            });

            $(document).on('click', '.delete-task', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/tasks/${id}`,
                            type: 'DELETE',
                            data: {_token: '{{ csrf_token() }}'},
                            success: function(data) {
                                if (data.success) {
                                    Swal.fire('Deleted!', 'Task has been deleted.', 'success');
                                    location.reload();
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire('Error', xhr.responseText, 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
