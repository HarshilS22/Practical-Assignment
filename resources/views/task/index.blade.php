@extends('layouts.layout')

@section('main-content')
    <div class="main-panel">
        <div class="col-lg-12 grid-margin stretch-card">

            <div class="card">
                <div class="card-body">
                    <div class="template-demo">
                        <button id="btn-create" type="button" data-bs-toggle="modal"
                                data-bs-target="#taskModal" class="btn btn-gradient-success btn-fw">Add Task
                        </button>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 pt-3">
                            <select id="statusFilter" class="form-control">
                                <option value="">All Status</option>
                                <option value="to_do">To Do</option>
                                <option value="in_progress">In Progress</option>
                                <option value="done">Done</option>
                            </select>
                        </div>
                    </div>

                    <table id="myTable" class="table table-striped nowrap" style="width:100%">
                        <thead>
                        <tr>
                            <th>User</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Updated At</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="taskModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskModalLabel">Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="task-form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Title<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="title" id="title"
                                       autocomplete="new-password" autocorrect="off" autocapitalize="off"
                                       spellcheck="false" placeholder="Enter Title" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Description<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="description" autocomplete="new-password"
                                       autocorrect="off" autocapitalize="off" spellcheck="false"
                                       placeholder="Enter Description"
                                       id="description" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Due Date<span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="due_date"
                                       placeholder="Enter Due Date" id="due_date" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">Select</option>
                                    <option value="to_do">To Do</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="done">Done</option>
                                </select>
                            </div>
                        </div>
                        <hr/>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="contact-save-btn">Save</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <script src="/js/task.js"></script>
    @section('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                window.taskTable = new DataTable('#myTable', {
                    responsive  : true,
                    searching   : false,
                    lengthChange: false,
                    order       : [[3, 'asc']],
                    ajax        : {
                        url : "{{ route('tasks.list') }}",
                        data: function (d) {
                            d.status = document.getElementById('statusFilter').value;
                        }
                    },
                    language: {
                        emptyTable : "No tasks found for this status",
                        zeroRecords: "No tasks found for this status"
                    },
                    columns     : [
                        { data: 'user' },
                        { data: 'title' },
                        { data: 'description' },
                        { data: 'due_date' },
                        {
                            data  : 'status',
                            render: function (data) {
                                let badgeClass = 'secondary';
                                if (data === 'to_do') badgeClass = 'warning';
                                if (data === 'in_progress') badgeClass = 'info';
                                if (data === 'done') badgeClass = 'success';

                                return `<span class="badge bg-${badgeClass}">
                                    ${data.replace('_', ' ').toUpperCase()}
                                </span>`;
                            }
                        },
                        { data: 'updated_at' },
                        { data: 'action', orderable: false, searchable: false }
                    ]
                });


                document.getElementById('statusFilter').addEventListener('change', function () {
                    window.taskTable.ajax.reload();

                });
            });
        </script>
    @endsection

@endsection
