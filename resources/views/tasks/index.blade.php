<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 600px;
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
            text-decoration: line-through;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">To-Do List</h2>

        <!-- Display Validation Errors -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="input-group mb-3">
            <input type="text" id="task-title" class="form-control" placeholder="Enter a new task">
            <button id="add-task" class="btn btn-success">Add Task</button>
        </div>

        <ul id="task-list" class="list-group">
            @foreach($tasks as $task)
                <li class="list-group-item d-flex justify-content-between align-items-center {{ $task->is_completed ? 'completed-task' : '' }}">
                    <span>{{ $task->title }}</span>
                    <div>
                        @if (!$task->is_completed)
                            <button class="btn btn-primary btn-sm mark-complete" data-id="{{ $task->id }}">Mark as Complete</button>
                        @endif
                        <button class="btn btn-danger btn-sm delete-task" data-id="{{ $task->id }}">Delete</button>
                    </div>
                </li>
            @endforeach
        </ul>
        <a href="/tasks/all" class="btn btn-primary mt-3">Show All Tasks</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#add-task').click(function() {
                let title = $('#task-title').val().trim();
                if (title === '') {
                    Swal.fire('Error', 'Task title cannot be empty', 'error');
                    return;
                }

                $.ajax({
                    url: '/tasks',
                    type: 'POST',
                    data: {
                        title: title,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        if (data.success) {
                            Swal.fire('Success', 'Task added successfully', 'success');
                            $('#task-list').append(`
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>${title}</span>
                                    <div>
                                        <button class="btn btn-primary btn-sm mark-complete" data-id="${data.id}">Mark as Complete</button>
                                        <button class="btn btn-danger btn-sm delete-task" data-id="${data.id}">Delete</button>
                                    </div>
                                </li>
                            `);
                            $('#task-title').val('');
                        } else {
                            Swal.fire('Error', 'Failed to add task', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Error', xhr.responseText, 'error');
                    }
                });
            });

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
