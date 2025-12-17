(function ($) {
    "use strict";

    /* ================= GLOBAL CSRF (SAFE) ================= */
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    /* ================= UI HELPERS ================= */
    const ui = {
        toast(message, type = "success") {
            const id = "toast-" + Date.now();
            const $toast = $(`
                <div id="${id}" class="toast text-bg-${type} position-fixed"
                     style="top:20px; right:20px; z-index:1200;">
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button class="btn-close btn-close-white me-2 m-auto"
                                data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `).appendTo("body");

            const bsToast = new bootstrap.Toast($toast[0]);
            bsToast.show();

            setTimeout(() => {
                bsToast.hide();
                $toast.remove();
            }, 4000);
        },

        clearErrors($form) {
            $form.find(".is-invalid").removeClass("is-invalid");
            $form.find(".invalid-feedback").remove();
        },

        fieldError($el, message) {
            $el.addClass("is-invalid");
            $el.after(`<div class="invalid-feedback">${message}</div>`);
        }
    };

    /* ================= API ================= */
    const api = {
        get(url) {
            return $.getJSON(url);
        },
        post(url, data) {
            return $.ajax({
                url,
                method: "POST",
                data
            });
        }
    };

    /* ================= TASK FORM ================= */
    function bindTaskForm() {
        const $form = $("#task-form");

        $form.on("submit", function (e) {
            e.preventDefault();
            ui.clearErrors($form);

            const id = $form.find('[name="id"]').val();
            const title = $.trim($form.find('[name="title"]').val());
            const description = $.trim($form.find('[name="description"]').val());
            const dueDate = $form.find('[name="due_date"]').val();
            const status = $form.find('[name="status"]').val();

            let hasError = false;

            if (!title) {
                ui.fieldError($form.find('[name="title"]'), "Title is required");
                hasError = true;
            }
            if (!description) {
                ui.fieldError($form.find('[name="description"]'), "Description is required");
                hasError = true;
            }
            if (!dueDate) {
                ui.fieldError($form.find('[name="due_date"]'), "Due date is required");
                hasError = true;
            }
            if (!status) {
                ui.fieldError($form.find('[name="status"]'), "Status is required");
                hasError = true;
            }

            if (hasError) return;

            const data = {
                title,
                description,
                due_date: dueDate,
                status
            };

            let url = "/tasks";
            if (id) {
                url = `/tasks/${id}`;
                data._method = "PUT";
            }

            api.post(url, data)
                .done((res) => {
                    ui.toast(res.message || "Task saved successfully");

                    bootstrap.Modal.getInstance(
                        document.getElementById("taskModal")
                    ).hide();

                    $form[0].reset();
                    $form.find('[name="id"]').val("");

                    if (window.taskTable) {
                        window.taskTable.ajax.reload();
                    }
                })
                .fail((xhr) => {
                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        Object.entries(xhr.responseJSON.errors).forEach(([key, msgs]) => {
                            ui.fieldError(
                                $form.find(`[name="${key}"]`),
                                msgs[0]
                            );
                        });
                    } else {
                        ui.toast("Something went wrong", "danger");
                    }
                });
        });
    }

    /* ================= EDIT TASK ================= */
    window.editTask = function (id) {
        api.get(`/tasks/${id}`)
            .done((res) => {
                const t = res.data;
                const $form = $("#task-form");

                ui.clearErrors($form);
                $form[0].reset();

                $form.find('[name="id"]').val(t.id);
                $form.find('[name="title"]').val(t.title);
                $form.find('[name="description"]').val(t.description);
                $form.find('[name="due_date"]').val(t.due_date);
                $form.find('[name="status"]').val(t.status);
                const modalEl = document.getElementById('taskModal');
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            })
            .fail(() => ui.toast("Failed to load task", "danger"));
    };

    /* ================= DELETE TASK ================= */
    window.deleteTask = function (id) {
        if (!confirm("Are you sure you want to delete this task?")) return;

        api.post(`/tasks/${id}`, { _method: "DELETE" })
            .done((res) => {
                ui.toast(res.message || "Task deleted");
                if (window.taskTable) {
                    window.taskTable.ajax.reload();
                }
            })
            .fail(() => ui.toast("Delete failed", "danger"));
    };

    /* ================= INIT ================= */
    $(function () {
        bindTaskForm();

        $("#btn-create").on("click", function () {
            const $form = $("#task-form");
            ui.clearErrors($form);
            $form[0].reset();
            $form.find('[name="id"]').val("");
        });
    });

})(jQuery);
