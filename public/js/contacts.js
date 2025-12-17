// public/js/contacts.js
// Modular, refactored contacts UI (jQuery + Bootstrap).
// Exposes globals: openEdit, deleteContact, openMergeModal

(function ($) {
    "use strict";

    // ---------- Config ----------
    const CSRF_TOKEN = $('meta[name="csrf-token"]').attr("content") || "";

    // ---------- UI helpers ----------
    const ui = {
        toast(message, type = "success") {
            const id = "toast-" + Date.now();
            const $t = $(`
        <div id="${id}" class="toast align-items-center text-bg-${type} border-0 position-fixed" role="alert" aria-live="assertive" aria-atomic="true"
             style="top:20px; right:20px; z-index:1200;">
          <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
        </div>
      `).appendTo("body");
            const bs = new bootstrap.Toast(document.getElementById(id));
            bs.show();
            setTimeout(() => {
                bs.hide();
                $t.remove();
            }, 4500);
        },

        clearFormErrors($form) {
            $form.find(".is-invalid").removeClass("is-invalid");
            $form.find(".invalid-feedback").remove();
            $form.find(".form-errors-global").remove();
        },

        setFieldError($el, message) {
            $el.addClass("is-invalid");
            const $fb = $('<div class="invalid-feedback"></div>').text(message);
            $el.parent().find(".invalid-feedback").remove();
            $el.parent().append($fb);
        },

        formatStorageUrl(path) {
            if (!path) return "";
            if (/^https?:\/\//i.test(path)) return path;
            if (path.startsWith("storage/")) return "/" + path;
            return "/storage/" + path;
        },
    };

    // ---------- Validation helpers ----------
    const validators = {
        isValidEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        },
        isValidPhone(phone) {
            if (/[^0-9\s-]/.test(phone)) {
                return false;
            }
            const digits = phone.replace(/\D/g, "");
            return digits.length === 10;
        },
    };

    // ---------- API wrapper ----------
    const api = {
        get(url, data = {}, opts = {}) {
            return $.ajax(
                Object.assign(
                    {
                        url,
                        method: "GET",
                        dataType: "json",
                        data,
                    },
                    opts
                )
            );
        },
        post(url, data = {}, opts = {}) {
            return $.ajax(
                Object.assign(
                    {
                        url,
                        method: "POST",
                        dataType: "json",
                        data,
                        headers: { "X-CSRF-TOKEN": CSRF_TOKEN },
                    },
                    opts
                )
            );
        },
        ajaxForm(url, formData, opts = {}) {
            return $.ajax(
                Object.assign(
                    {
                        url,
                        method: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: "json",
                        headers: { "X-CSRF-TOKEN": CSRF_TOKEN },
                    },
                    opts
                )
            );
        },
    };

    // ---------- Table rendering ----------
    const table = {
        buildFromJson(paged) {
            let html =
                '<table class="table table-striped table-bordered"><thead><tr>';
            html +=
                "<th>ID</th><th>Name</th><th>Primary Email</th><th>Primary Phone</th><th>Gender</th><th>Status</th><th>Actions</th>";
            html += "</tr></thead><tbody>";

            (paged.data || []).forEach((c) => {
                html += `<tr data-id="${c.id}">`;
                html += `<td>${c.id}</td>`;
                html += `<td>${c.name || ""}</td>`;
                html += `<td>${c.email || ""}`;
                if (c.emails && c.emails.length)
                    html += `<br><small class="text-muted">+ ${c.emails.length} more</small>`;
                html += `</td>`;
                html += `<td>${c.phone || ""}`;
                if (c.phones && c.phones.length)
                    html += `<br><small class="text-muted">+ ${c.phones.length} more</small>`;
                html += `</td>`;
                html += `<td>${c.gender || ""}</td>`;
                html += `<td>`;

                if (c.is_merged) {
                    html += `<span class="badge bg-warning text-dark mt-1">
                Merged into ${c.merged_into_name || ""}
             </span>`;
                } else {
                    html += `<button class="btn btn-sm btn-warning" onclick="openMergeModal(${c.id})">
                Merge
             </button>`;
                }

                html += `</td>`;
                html += `<td>`;
                html += `<button class="btn btn-sm btn-primary btn-edit" data-id="${c.id}">Edit</button> `;
                if (!c.is_merged) {
                    html += `<button class="btn btn-sm btn-danger btn-delete" data-id="${c.id}">Delete</button> `;
                }

                // html += `<button class="btn btn-sm btn-warning btn-merge" data-id="${c.id}">Merge</button>`;
                html += `</td>`;
                html += `</tr>`;
            });

            html += "</tbody></table>";
            $("#contacts-table").html(html);
            table.attachHandlers();
        },

        attachHandlers() {
            const $table = $("#contacts-table");
            $table
                .find(".btn-edit")
                .off("click")
                .on("click", function () {
                    const id = $(this).data("id");
                    if (id && window.openEdit) window.openEdit(id);
                });
            $table
                .find(".btn-delete")
                .off("click")
                .on("click", function () {
                    const id = $(this).data("id");
                    if (id && window.deleteContact) window.deleteContact(id);
                });
            $table
                .find(".btn-merge")
                .off("click")
                .on("click", function () {
                    const id = $(this).data("id");
                    if (id && window.openMergeModal) window.openMergeModal(id);
                });
        },

        refresh(filters = {}) {
            api.get("/contacts", filters)
                .done((res) => {
                    // if blade partial html returned
                    if (typeof res === "string" && res.trim().startsWith("<")) {
                        $("#contacts-table").html(res);
                        return;
                    }
                    if (res && res.data) {
                        table.buildFromJson(res.data);
                        return;
                    }
                    $("#contacts-table").html(res);
                })
                .fail(() => ui.toast("Failed to refresh contacts", "danger"));
        },
    };

    // ---------- Safe template clone + delegated remove handlers ----------
    // Delegated handlers for removing dynamic rows (works for any row inserted later)
    $(document).on("click", ".remove-email", function (e) {
        e.preventDefault();
        $(this).closest(".email-row").remove();
    });
    $(document).on("click", ".remove-phone", function (e) {
        e.preventDefault();
        $(this).closest(".phone-row").remove();
    });
    $(document).on("click", ".remove-custom", function (e) {
        e.preventDefault();
        $(this).closest(".custom-field-row").remove();
    });

    // helper to clone a template safely returning a jQuery element
    function cloneTemplateAsElement($tpl) {
        // prefer template.content.firstElementChild when available (produces an Element)
        const frag = $tpl.prop("content");
        if (frag && frag.firstElementChild) {
            return $(frag.firstElementChild.cloneNode(true));
        }
        // fallback: try using innerHTML
        const html = $tpl.html();
        return $(html);
    }

    // ---------- Dynamic rows (emails/phones/custom) ----------
    function wireDynamicRows($scope) {
        // add email
        $scope
            .find("#btn-add-email")
            .off("click.wire")
            .on("click.wire", function (e) {
                e.preventDefault();
                const $tpl = $scope.find("#tpl-email");
                const $node = cloneTemplateAsElement($tpl);
                $scope.find("#emails-wrapper").append($node);
                // delegated remove handler will handle .remove-email
            });

        // add phone
        $scope
            .find("#btn-add-phone")
            .off("click.wire")
            .on("click.wire", function (e) {
                e.preventDefault();
                const $tpl = $scope.find("#tpl-phone");
                const $node = cloneTemplateAsElement($tpl);
                $scope.find("#phones-wrapper").append($node);
            });

        // add custom field
        $scope
            .find("#btn-add-custom")
            .off("click.wire")
            .on("click.wire", function (e) {
                e.preventDefault();
                const $tpl = $scope.find("#tpl-custom");
                const $node = cloneTemplateAsElement($tpl);
                $scope.find("#custom-fields-wrapper").append($node);
            });

        // ensure existing rows (if any) still have remove buttons working — delegation covers them,
        // but we keep this to handle cases where UI needs immediate local binding
        // (no-op if delegation already covers everything)
    }

    function appendIndexedCustomFields($form, fd) {
        $form
            .find("#custom-fields-wrapper .custom-field-row")
            .each(function (i) {
                const $row = $(this);
                const key =
                    $row.find('input[name="custom_fields[][key]"]').val() || "";
                const value =
                    $row.find('input[name="custom_fields[][value]"]').val() ||
                    "";
                fd.append(`custom_fields[${i}][key]`, key);
                fd.append(`custom_fields[${i}][value]`, value);
            });
    }

    // ---------- Form (create/update) ----------
    function bindContactForm() {
        const $form = $("#contact-form");

        $form.off("submit").on("submit", function (e) {
            e.preventDefault();
            ui.clearFormErrors($form);

            // Basic client-side validation
            const name = $.trim($form.find('[name="name"]').val());
            const email = $.trim($form.find('[name="email"]').val());
            const phone = $.trim($form.find('[name="phone"]').val());
            let hasError = false;

            if (!name) {
                ui.setFieldError(
                    $form.find('[name="name"]'),
                    "Name is required"
                );
                hasError = true;
            }
            if (!validators.isValidEmail(email)) {
                ui.setFieldError($form.find('[name="email"]'), "Invalid email");
                hasError = true;
            }
            if (!validators.isValidPhone(phone)) {
                ui.setFieldError(
                    $form.find('[name="phone"]'),
                    "Invalid phone number"
                );
                hasError = true;
            }

            // file check
            const profileEl = $form.find('[name="profile_image"]')[0];
            if (profileEl && profileEl.files && profileEl.files[0]) {
                if (profileEl.files[0].size > 2 * 1024 * 1024) {
                    ui.setFieldError(
                        $form.find('[name="profile_image"]'),
                        "Profile image must be <= 2MB"
                    );
                    hasError = true;
                }
            }

            if (hasError) return;

            // Build form data but re-index custom_fields
            const baseFd = new FormData($form[0]);
            const fd = new FormData();
            for (const pair of baseFd.entries()) {
                const nameKey = pair[0];
                if (!nameKey.startsWith("custom_fields"))
                    fd.append(nameKey, pair[1]);
            }
            appendIndexedCustomFields($form, fd);

            // detect edit mode
            const id = $.trim($form.find('[name="id"]').val());
            let url = "/contacts";
            const opts = {
                beforeSend: () =>
                    $form.find('[type="submit"]').attr("disabled", true),
                complete: () =>
                    $form.find('[type="submit"]').attr("disabled", false),
            };

            if (id) {
                url = "/contacts/" + id;
                fd.append("_method", "POST"); // update using POST + server expects method override (keep same logic)
            }

            api.ajaxForm(url, fd, opts)
                .done((res) => {
                    if (res && res.success) {
                        ui.toast(res.message || "Contact saved");
                        const modalEl = document.getElementById("contactModal");
                        const bsModal = bootstrap.Modal.getInstance(modalEl);
                        if (bsModal) bsModal.hide();
                        $form[0].reset();
                        $form
                            .find(
                                "#emails-wrapper,#phones-wrapper,#custom-fields-wrapper"
                            )
                            .empty();
                        $form.find('[name="id"]').val("");
                        table.refresh();
                    } else {
                        ui.toast("Unexpected server response", "danger");
                        console.log(res);
                    }
                })
                .fail((xhr) => {
                    if (
                        xhr.status === 422 &&
                        xhr.responseJSON &&
                        xhr.responseJSON.errors
                    ) {
                        const errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach((key) => {
                            const msg = errors[key][0];
                            if (/^custom_fields\.\d+\.(key|value)$/.test(key)) {
                                const parts = key.split(".");
                                const idx = parseInt(parts[1], 10);
                                const field = parts[2];
                                const $row = $form
                                    .find(
                                        "#custom-fields-wrapper .custom-field-row"
                                    )
                                    .eq(idx);
                                if ($row.length) {
                                    if (field === "key")
                                        ui.setFieldError(
                                            $row
                                                .find(
                                                    'input[name="custom_fields[][key]"]'
                                                )
                                                .first(),
                                            msg
                                        );
                                    else
                                        ui.setFieldError(
                                            $row
                                                .find(
                                                    'input[name="custom_fields[][value]"]'
                                                )
                                                .first(),
                                            msg
                                        );
                                    return;
                                }
                            }

                            let $el = $form.find('[name="' + key + '"]');
                            if ($el.length === 0) {
                                const base = key.replace(/\.\d+/g, "");
                                $el = $form.find('[name^="' + base + '"]');
                            }
                            if ($el.length) ui.setFieldError($el.first(), msg);
                            else {
                                let $global = $form.find(".form-errors-global");
                                if ($global.length === 0) {
                                    $global = $(
                                        '<div class="form-errors-global text-danger mb-2"></div>'
                                    );
                                    $form.prepend($global);
                                }
                                $global.append($("<div>").text(msg));
                            }
                        });
                    } else {
                        ui.toast("Server error — please try again", "danger");
                        console.error(xhr);
                    }
                });
        });
    }

    // ---------- Edit ----------
    function openEditHandler(id) {
        if (!id) return;
        api.get("/contacts/" + id)
            .done((res) => {
                if (!res || !res.data) {
                    ui.toast("Failed to load contact", "danger");
                    return;
                }
                const d = res.data;
                const modalEl = document.getElementById("contactModal");
                if (!modalEl) {
                    ui.toast("Contact modal not found", "danger");
                    return;
                }
                const $modal = $(modalEl);
                const $form = $modal.find("#contact-form");
                ui.clearFormErrors($form);
                $form[0].reset();
                $form
                    .find(
                        "#emails-wrapper,#phones-wrapper,#custom-fields-wrapper"
                    )
                    .empty();

                $form.find('[name="id"]').val(d.id || "");
                $form.find('[name="name"]').val(d.name || "");
                $form.find('[name="email"]').val(d.email || "");
                $form.find('[name="phone"]').val(d.phone || "");
                $form.find('[name="gender"]').val(d.gender || "");

                // profile preview
                if ($form.find("#profile-preview").length === 0)
                    $form
                        .find('[name="profile_image"]')
                        .after('<div id="profile-preview" class="mt-2"></div>');
                const $pp = $form.find("#profile-preview").empty();
                if (d.profile_image) {
                    const url = ui.formatStorageUrl(d.profile_image);
                    $pp.append(
                        `<div><a href="${url}" target="_blank">Current profile: ${url
                            .split("/")
                            .pop()}</a></div>`
                    );
                    $pp.append(
                        `<div><label class="form-check"><input type="checkbox" name="remove_profile_image" value="1" class="form-check-input"> Remove existing image</label></div>`
                    );
                }

                // additional file preview
                if ($form.find("#additional-preview").length === 0)
                    $form
                        .find('[name="additional_file"]')
                        .after(
                            '<div id="additional-preview" class="mt-2"></div>'
                        );
                const $ap = $form.find("#additional-preview").empty();
                if (d.additional_file) {
                    const fu = ui.formatStorageUrl(d.additional_file);
                    $ap.append(
                        `<div><a href="${fu}" target="_blank">Current file: ${fu
                            .split("/")
                            .pop()}</a></div>`
                    );
                    $ap.append(
                        `<div><label class="form-check"><input type="checkbox" name="remove_additional_file" value="1" class="form-check-input"> Remove existing file</label></div>`
                    );
                }

                // emails
                if (Array.isArray(d.emails) && d.emails.length) {
                    d.emails.forEach((e) => {
                        const val =
                            typeof e === "object" && e.email ? e.email : e;
                        const $node = cloneTemplateAsElement(
                            $modal.find("#tpl-email")
                        );
                        $node.find("input").val(val);
                        $modal.find("#emails-wrapper").append($node);
                    });
                }
                // phones
                if (Array.isArray(d.phones) && d.phones.length) {
                    d.phones.forEach((p) => {
                        const val =
                            typeof p === "object" && p.phone ? p.phone : p;
                        const $node = cloneTemplateAsElement(
                            $modal.find("#tpl-phone")
                        );
                        $node.find("input").val(val);
                        $modal.find("#phones-wrapper").append($node);
                    });
                }
                // custom fields
                if (Array.isArray(d.custom_fields) && d.custom_fields.length) {
                    d.custom_fields.forEach((cf) => {
                        const key = cf.field_key ?? cf.key ?? "";
                        const value = cf.field_value ?? cf.value ?? "";
                        const $node = cloneTemplateAsElement(
                            $modal.find("#tpl-custom")
                        );
                        $node
                            .find('input[name="custom_fields[][key]"]')
                            .val(key);
                        $node
                            .find('input[name="custom_fields[][value]"]')
                            .val(value);
                        $modal.find("#custom-fields-wrapper").append($node);
                    });
                }

                wireDynamicRows($modal);
                const bsModal = new bootstrap.Modal(modalEl);
                bsModal.show();
            })
            .fail((xhr) => {
                ui.toast("Failed to load contact for editing", "danger");
                console.error(xhr);
            });
    }

    // ---------- Delete ----------
    function deleteHandler(id) {
        if (!id) return;
        if (!confirm("Are you sure you want to delete this contact?")) return;

        // find the first matching button for UI feedback (may be multiple if duplicates exist)
        const $btn = $(`#contacts-table .btn-delete[data-id="${id}"]`).first();
        const $row = $(`#contacts-table tr[data-id="${id}"]`);

        // guard against double runs
        if ($btn.data("deleting")) return;
        $btn.data("deleting", true).attr("disabled", true);

        api.post("/contacts/" + id, { _method: "DELETE" })
            .done((res) => {
                if (res && res.success) {
                    ui.toast(res.message || "Contact deleted");

                    table.refresh();
                } else {
                    ui.toast(
                        res && res.message ? res.message : "Delete failed",
                        "danger"
                    );
                    table.refresh();
                }
            })
            .fail((xhr) => {
                // show real error
                const errMsg =
                    xhr && xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : "Server error while deleting";
                ui.toast(errMsg, "danger");
                console.error(xhr);
                table.refresh();
            })
            .always(() => {
                $btn.data("deleting", false).attr("disabled", false);
                table.refresh();
            });
    }

    // ---------- Merge flow ----------
    function openMergeModalHandler(preselectSecondaryId = null) {
        const $modal = $("#mergeModal");
        const bs = new bootstrap.Modal($modal[0]);

        api.get("/contacts", { per_page: 1000 })
            .done((res) => {
                let contacts = [];
                if (res && res.data && Array.isArray(res.data.data))
                    contacts = res.data.data;
                else if (Array.isArray(res)) contacts = res;

                const $master = $modal.find("#merge-master").empty();
                const $secondary = $modal.find("#merge-secondary").empty();

                contacts.forEach((c) => {
                    const label = `${c.name || "(no name)"} (${
                        c.email || "—"
                    })`;
                    $master.append(`<option value="${c.id}">${label}</option>`);
                    $secondary.append(
                        `<option value="${c.id}">${label}</option>`
                    );
                });

                if (preselectSecondaryId) $secondary.val(preselectSecondaryId);

                $master.off("change").on("change", function () {
                    const s = $secondary.val();
                    if (s && s === $(this).val()) {
                        $secondary.find("option").each(function () {
                            if (this.value !== $master.val()) {
                                $secondary.val(this.value);
                                return false;
                            }
                        });
                    }
                });

                $modal
                    .find("#btn-merge-preview")
                    .off("click")
                    .on("click", function () {
                        const masterId = $master.val(),
                            secondaryId = $secondary.val();
                        if (!masterId || !secondaryId) {
                            ui.toast(
                                "Select both master and secondary",
                                "danger"
                            );
                            return;
                        }
                        if (masterId === secondaryId) {
                            ui.toast(
                                "Master and secondary must be different",
                                "danger"
                            );
                            return;
                        }
                        showMergePreview(masterId, secondaryId);
                    });

                $modal
                    .find("#btn-merge-do")
                    .off("click")
                    .on("click", function () {
                        const masterId = $master.val(),
                            secondaryId = $secondary.val();
                        if (!masterId || !secondaryId) {
                            ui.toast(
                                "Select both master and secondary",
                                "danger"
                            );
                            return;
                        }
                        if (masterId === secondaryId) {
                            ui.toast(
                                "Master and secondary must be different",
                                "danger"
                            );
                            return;
                        }
                        if (
                            !confirm(
                                "Are you sure you want to merge these contacts?"
                            )
                        )
                            return;

                        api.post("/contacts/merge", {
                            master_id: masterId,
                            secondary_id: secondaryId,
                        })
                            .done((res) => {
                                if (res && res.success) {
                                    ui.toast(
                                        res.message || "Merged successfully",
                                        "success"
                                    );
                                    bs.hide();
                                    table.refresh();
                                } else {
                                    ui.toast(
                                        res && res.message
                                            ? res.message
                                            : "Merge failed",
                                        "danger"
                                    );
                                    console.log(res);
                                }
                            })
                            .fail((xhr) => {
                                ui.toast("Merge failed", "danger");
                                console.error(xhr);
                            });
                    });

                bs.show();
            })
            .fail(() =>
                ui.toast("Unable to fetch contacts for merge", "danger")
            );
    }

    // ---------- Merge preview ----------
    function showMergePreview(masterId, secondaryId) {
        const $modal = $("#mergeModal");
        const $area = $modal
            .find("#merge-preview-area")
            .empty()
            .append('<div class="text-muted">Loading preview…</div>');

        $.when(
            api.get("/contacts/" + masterId),
            api.get("/contacts/" + secondaryId)
        )
            .done((masterRes, secondaryRes) => {
                const master =
                    masterRes && masterRes.data ? masterRes.data : masterRes;
                const secondary =
                    secondaryRes && secondaryRes.data
                        ? secondaryRes.data
                        : secondaryRes;

                const html = [];
                html.push('<div class="row">');
                html.push('<div class="col-md-6"><h6>Master</h6>');
                html.push(
                    '<table class="table table-sm table-borderless"><tbody>'
                );
                html.push(rowCompare("Name", master.name, secondary.name));
                html.push(
                    rowCompare("Primary Email", master.email, secondary.email)
                );
                html.push(
                    rowCompare("Primary Phone", master.phone, secondary.phone)
                );
                html.push(
                    rowCompare("Gender", master.gender, secondary.gender)
                );
                html.push("</tbody></table></div>");
                html.push('<div class="col-md-6"><h6>Secondary</h6>');
                html.push(
                    '<table class="table table-sm table-borderless"><tbody>'
                );
                html.push(rowCompare("Name", secondary.name, master.name));
                html.push(
                    rowCompare("Primary Email", secondary.email, master.email)
                );
                html.push(
                    rowCompare("Primary Phone", secondary.phone, master.phone)
                );
                html.push(
                    rowCompare("Gender", secondary.gender, master.gender)
                );
                html.push("</tbody></table></div>");
                html.push("</div>");

                html.push(
                    '<hr><div class="row"><div class="col-md-6"><h6>Master Emails</h6>'
                );
                html.push(renderEmails(master, secondary));
                html.push(
                    '</div><div class="col-md-6"><h6>Secondary Emails</h6>'
                );
                html.push(renderEmails(secondary, master));
                html.push("</div></div>");

                html.push(
                    '<hr><div class="row"><div class="col-md-6"><h6>Master Phones</h6>'
                );
                html.push(renderPhones(master, secondary));
                html.push(
                    '</div><div class="col-md-6"><h6>Secondary Phones</h6>'
                );
                html.push(renderPhones(secondary, master));
                html.push("</div></div>");

                html.push(
                    '<hr><div class="row"><div class="col-md-12"><h6>Custom Fields (Master vs Secondary)</h6>'
                );
                html.push(renderCustomFields(master, secondary));
                html.push("</div></div>");

                $area.html(html.join(""));
            })
            .fail(() => {
                $area.html(
                    '<div class="text-danger">Failed to load contact preview.</div>'
                );
            });

        function rowCompare(label, a, b) {
            let cls = "";
            if (!a && b) cls = "text-info";
            else if (a && b && String(a) !== String(b)) cls = "text-warning";
            return `<tr><th style="width:35%">${label}</th><td class="${cls}">${
                a ?? "<em>—</em>"
            }</td></tr>`;
        }

        function renderEmails(src, other) {
            const s =
                src.emails && src.emails.length
                    ? src.emails
                          .map((e) => (typeof e === "object" ? e.email : e))
                          .filter(Boolean)
                    : [];
            const o =
                other.emails && other.emails.length
                    ? other.emails
                          .map((e) => (typeof e === "object" ? e.email : e))
                          .filter(Boolean)
                    : [];
            let out = "<ul>";
            s.forEach((item) => (out += `<li>${item}</li>`));
            if (s.length === 0)
                out += '<li class="text-muted"><em>None</em></li>';
            out += "</ul>";
            const addables = o.filter((x) => s.indexOf(x) === -1);
            if (addables.length)
                out +=
                    '<div class="mt-2"><small class="text-success">From other: ' +
                    addables
                        .map(
                            (a) => `<span class="badge bg-success">${a}</span>`
                        )
                        .join(" ") +
                    "</small></div>";
            return out;
        }

        function renderPhones(src, other) {
            const s =
                src.phones && src.phones.length
                    ? src.phones
                          .map((p) => (typeof p === "object" ? p.phone : p))
                          .filter(Boolean)
                    : [];
            const o =
                other.phones && other.phones.length
                    ? other.phones
                          .map((p) => (typeof p === "object" ? p.phone : p))
                          .filter(Boolean)
                    : [];
            let out = "<ul>";
            s.forEach((item) => (out += `<li>${item}</li>`));
            if (s.length === 0)
                out += '<li class="text-muted"><em>None</em></li>';
            out += "</ul>";
            const addables = o.filter((x) => s.indexOf(x) === -1);
            if (addables.length)
                out +=
                    '<div class="mt-2"><small class="text-success">From other: ' +
                    addables
                        .map(
                            (a) => `<span class="badge bg-success">${a}</span>`
                        )
                        .join(" ") +
                    "</small></div>";
            return out;
        }

        function renderCustomFields(master, secondary) {
            const m = Array.isArray(master.custom_fields)
                ? master.custom_fields
                : Array.isArray(master.customFields)
                ? master.customFields
                : [];
            const s = Array.isArray(secondary.custom_fields)
                ? secondary.custom_fields
                : Array.isArray(secondary.customFields)
                ? secondary.customFields
                : [];
            const mmap = {};
            m.forEach((cf) => {
                const key = cf.field_key ?? cf.key ?? "";
                mmap[key] = cf.field_value ?? cf.value ?? "";
            });
            const smap = {};
            s.forEach((cf) => {
                const key = cf.field_key ?? cf.key ?? "";
                smap[key] = cf.field_value ?? cf.value ?? "";
            });
            const keys = Array.from(
                new Set(Object.keys(mmap).concat(Object.keys(smap)))
            );
            if (keys.length === 0)
                return '<div class="text-muted"><em>No custom fields</em></div>';
            let out =
                '<table class="table table-sm"><thead><tr><th>Field</th><th>Master</th><th>Secondary</th><th>Result (policy)</th></tr></thead><tbody>';
            keys.forEach((k) => {
                const mv = mmap[k] ?? "";
                const sv = smap[k] ?? "";
                let res = "";
                if (!mv && sv)
                    res = `<span class="text-success">Will be added: ${sv}</span>`;
                else if (mv && sv && mv !== sv)
                    res = `<span class="text-warning">Conflict — will append: ${mv};;${sv}</span>`;
                else res = mv || sv || "<em>—</em>";
                out += `<tr><td>${k}</td><td>${mv || "<em>—</em>"}</td><td>${
                    sv || "<em>—</em>"
                }</td><td>${res}</td></tr>`;
            });
            out += "</tbody></table>";
            return out;
        }
    }

    // ---------- Initialization ----------
    function init() {
        // pre-wire create button to reset modal form
        $("#btn-create").on("click", function () {
            const modalEl = document.getElementById("contactModal");
            const $modal = $(modalEl);
            const $form = $modal.find("#contact-form");
            ui.clearFormErrors($form);
            $form[0].reset();
            $form
                .find("#emails-wrapper,#phones-wrapper,#custom-fields-wrapper")
                .empty();
            $form.find('[name="id"]').val("");
        });

        // filter button refresh
        $("#btn-filter").on("click", function (e) {
            e.preventDefault();
            const filters = {
                name: $("#filter-name").val(),
                email: $("#filter-email").val(),
                gender: $("#filter-gender").val(),
            };
            table.refresh(filters);
        });

        // wire modal dynamic rows and contact form
        const contactModalEl = document.getElementById("contactModal");
        if (contactModalEl) {
            const $modal = $(contactModalEl);
            contactModalEl.addEventListener("shown.bs.modal", function () {
                wireDynamicRows($modal);
            });
            wireDynamicRows($modal);
            bindContactForm();
        }

        // delegation for server-side markup delete (if any)
        $("#contacts-table").on("click", ".btn-delete", function (e) {
            e.preventDefault();
            const id = $(this).data("id") || $(this).closest("tr").data("id");
            if (id) window.deleteContact(id);
        });

        // optional: initial refresh (commented out so blade initial markup remains)
        // table.refresh();
    }

    // expose globals (backwards compatibility)
    window.openEdit = openEditHandler;
    window.deleteContact = deleteHandler;
    window.openMergeModal = openMergeModalHandler;

    // start
    $(init);
})(jQuery);
