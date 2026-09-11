document.addEventListener("DOMContentLoaded", function () {
    // 1. Auto-save handlers for Notification Settings
    async function saveNotificationSettings(customMsg) {
        const form = document.getElementById("notification-settings-form");
        if (!form) return;

        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: "POST",
                body: formData,
                headers: {
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": formData.get("_token") || ""
                }
            });
            const result = await response.json();
            if (response.ok && result.status) {
                if (typeof toastr !== "undefined") {
                    toastr.success(customMsg || result.message || "Notification settings updated successfully.");
                }
            } else {
                const msg = result.message || "Failed to update notification settings.";
                if (typeof toastr !== "undefined") {
                    toastr.error(msg);
                }
            }
        } catch (err) {
            console.error(err);
            if (typeof toastr !== "undefined") {
                toastr.error("An error occurred while updating settings.");
            }
        }
    }

    // 2. Auto-save handlers for Report Settings
    async function saveReportSettings(customMsg) {
        const form = document.getElementById("report-settings-form");
        if (!form) return;

        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: "POST",
                body: formData,
                headers: {
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": formData.get("_token") || ""
                }
            });
            const result = await response.json();
            if (response.ok && result.status) {
                if (typeof toastr !== "undefined") {
                    toastr.success(customMsg || result.message || "Report settings updated successfully.");
                }
            } else {
                const msg = result.message || "Failed to update report settings.";
                if (typeof toastr !== "undefined") {
                    toastr.error(msg);
                }
            }
        } catch (err) {
            console.error(err);
            if (typeof toastr !== "undefined") {
                toastr.error("An error occurred while updating settings.");
            }
        }
    }

    // 3. Dropdown setup with change auto-save
    function setupDropdown(dropdownId, labelId, labelClass, onCheckedChange) {
        const checkboxes = document.querySelectorAll("#" + dropdownId + " + .dropdown-menu input[type='checkbox']");
        const labelText = document.getElementById(labelId);
        if (!checkboxes.length || !labelText) return;

        function updateLabel() {
            const selected = [];
            checkboxes.forEach(cb => {
                if (cb.checked) {
                    const label = cb.closest('label').querySelector('.' + labelClass);
                    if (label) selected.push(label.textContent.trim());
                }
            });

            if (selected.length === 0) {
                labelText.textContent = dropdownId === 'reportFrequencyDropdown' ? "Select report frequency" : "Select notify event(s)";
            } else {
                labelText.textContent = selected.join(", ");
            }
        }

        checkboxes.forEach(cb => {
            cb.addEventListener("change", function () {
                updateLabel();
                if (typeof onCheckedChange === "function") {
                    const itemLabel = cb.closest('label')?.querySelector('.' + labelClass)?.textContent.trim() || 'Event';
                    onCheckedChange(cb, itemLabel, cb.checked);
                }
            });
        });
        updateLabel();
    }

    // 4. Switch sync with dropdown & auto-save
    function setupSwitch(switchId, dropdownBtnId, onSwitchChange) {
        const switchEl = document.getElementById(switchId);
        const dropdownBtn = document.getElementById(dropdownBtnId);
        if (!switchEl) return;

        function toggleState() {
            if (dropdownBtn) {
                if (switchEl.checked) {
                    dropdownBtn.disabled = false;
                    dropdownBtn.classList.remove("opacity-50", "pe-none");
                } else {
                    dropdownBtn.disabled = true;
                    dropdownBtn.classList.add("opacity-50", "pe-none");
                }
            }
        }

        switchEl.addEventListener("change", function () {
            toggleState();
            if (typeof onSwitchChange === "function") {
                onSwitchChange(switchEl.checked);
            }
        });
        toggleState();
    }

    // Initialize E-mail notifications
    setupDropdown("notifyEventsDropdownEmail", "selected-events-text-email", "event-label-text", function (cb, itemLabel, isChecked) {
        saveNotificationSettings(`Email "${itemLabel}" ${isChecked ? 'enabled' : 'disabled'} successfully.`);
    });
    setupSwitch("email_notification", "notifyEventsDropdownEmail", function (isChecked) {
        saveNotificationSettings(isChecked ? "Email notifications enabled successfully." : "Email notifications disabled successfully.");
    });

    // Initialize SMS notifications
    setupDropdown("notifyEventsDropdownSms", "selected-events-text-sms", "event-label-text", function (cb, itemLabel, isChecked) {
        saveNotificationSettings(`SMS "${itemLabel}" ${isChecked ? 'enabled' : 'disabled'} successfully.`);
    });
    setupSwitch("sms_notification", "notifyEventsDropdownSms", function (isChecked) {
        saveNotificationSettings(isChecked ? "SMS notifications enabled successfully." : "SMS notifications disabled successfully.");
    });

    // Initialize E-mail Reports
    setupDropdown("reportFrequencyDropdown", "selected-report-text", "report-label-text", function (cb, itemLabel, isChecked) {
        saveReportSettings(`${itemLabel} ${isChecked ? 'enabled' : 'disabled'} successfully.`);
    });
    setupSwitch("email_report_notification", "reportFrequencyDropdown", function (isChecked) {
        saveReportSettings(isChecked ? "Email reports enabled successfully." : "Email reports disabled successfully.");
    });
});