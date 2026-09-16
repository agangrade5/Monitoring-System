document.addEventListener("DOMContentLoaded", function () {
    // 1. Auto-save handlers for Notification Settings
    async function saveNotificationSettings(customMsg, showToast = true) {
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
                if (showToast && typeof toastr !== "undefined") {
                    toastr.success(customMsg || result.message || "Notification settings updated successfully.");
                }
            } else {
                const msg = result.message || "Failed to update notification settings.";
                if (showToast && typeof toastr !== "undefined") {
                    toastr.error(msg);
                }
            }
        } catch (err) {
            console.error(err);
            if (showToast && typeof toastr !== "undefined") {
                toastr.error("An error occurred while updating settings.");
            }
        }
    }

    // 2. Auto-save handlers for Report Settings
    async function saveReportSettings(customMsg, showToast = true) {
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
                if (showToast && typeof toastr !== "undefined") {
                    toastr.success(customMsg || result.message || "Report settings updated successfully.");
                }
            } else {
                const msg = result.message || "Failed to update report settings.";
                if (showToast && typeof toastr !== "undefined") {
                    toastr.error(msg);
                }
            }
        } catch (err) {
            console.error(err);
            if (showToast && typeof toastr !== "undefined") {
                toastr.error("An error occurred while updating settings.");
            }
        }
    }

    // 3. Helper to update dropdown summary label
    function updateDropdownSummary(dropdownBtnId, labelSpanId, labelClass, placeholder) {
        const dropdownBtn = document.getElementById(dropdownBtnId);
        const labelSpan = document.getElementById(labelSpanId);
        if (!dropdownBtn || !labelSpan) return;

        const container = dropdownBtn.closest(".dropdown");
        if (!container) return;

        const checkboxes = container.querySelectorAll(".dropdown-menu input[type='checkbox']");
        const selected = [];

        checkboxes.forEach(cb => {
            if (cb.checked) {
                const label = cb.closest("label")?.querySelector("." + labelClass);
                if (label) {
                    selected.push(label.textContent.trim());
                }
            }
        });

        if (selected.length === 0) {
            labelSpan.textContent = placeholder;
        } else {
            labelSpan.textContent = selected.join(", ");
        }
    }

    // 4. Synchronize Channel (Switch + Dropdown Checkboxes)
    function setupNotificationChannel({
        switchId,
        dropdownBtnId,
        labelSpanId,
        labelClass,
        placeholder,
        saveCallback,
        channelName
    }) {
        const switchEl = document.getElementById(switchId);
        const dropdownBtn = document.getElementById(dropdownBtnId);
        const labelSpan = document.getElementById(labelSpanId);

        if (!switchEl || !dropdownBtn || !labelSpan) return;

        const container = dropdownBtn.closest(".dropdown");
        const checkboxes = container ? container.querySelectorAll(".dropdown-menu input[type='checkbox']") : [];

        // Apply UI state (enabled / disabled appearance)
        function applySwitchState() {
            if (switchEl.checked) {
                dropdownBtn.disabled = false;
                dropdownBtn.classList.remove("opacity-50", "pe-none");
            } else {
                dropdownBtn.disabled = true;
                dropdownBtn.classList.add("opacity-50", "pe-none");
            }
        }

        // On Master Switch Change
        switchEl.addEventListener("change", function () {
            applySwitchState();

            if (!switchEl.checked) {
                // When master switch is disabled, automatically uncheck all events in dropdown
                checkboxes.forEach(cb => {
                    cb.checked = false;
                });
                labelSpan.textContent = placeholder;
                saveCallback(`${channelName} notifications disabled successfully.`, true);
            } else {
                // When master switch is enabled, DO NOT auto-check checkboxes (keep user selection)
                updateDropdownSummary(dropdownBtnId, labelSpanId, labelClass, placeholder);
                saveCallback(`${channelName} notifications enabled successfully.`, true);
            }
        });

        // On Individual Dropdown Checkbox Change (Silent Save - NO Toastr popup)
        checkboxes.forEach(cb => {
            cb.addEventListener("change", function () {
                let checkedCount = 0;
                checkboxes.forEach(c => {
                    if (c.checked) checkedCount++;
                });

                if (checkedCount > 0 && !switchEl.checked) {
                    // Turn switch ON if user selects an event
                    switchEl.checked = true;
                    applySwitchState();
                }

                updateDropdownSummary(dropdownBtnId, labelSpanId, labelClass, placeholder);

                // Silent auto-save (showToast = false)
                saveCallback(null, false);
            });
        });

        // Initial setup on page load
        applySwitchState();
        updateDropdownSummary(dropdownBtnId, labelSpanId, labelClass, placeholder);
    }

    // ----------------------------------------------------
    // Initialize Admin Settings Channels
    // ----------------------------------------------------

    // 1. E-mail Notifications
    setupNotificationChannel({
        switchId: "email_notification",
        dropdownBtnId: "notifyEventsDropdownEmail",
        labelSpanId: "selected-events-text-email",
        labelClass: "event-label-text",
        placeholder: "Select notify event(s)",
        saveCallback: saveNotificationSettings,
        channelName: "Email"
    });

    // 2. SMS Notifications
    setupNotificationChannel({
        switchId: "sms_notification",
        dropdownBtnId: "notifyEventsDropdownSms",
        labelSpanId: "selected-events-text-sms",
        labelClass: "event-label-text",
        placeholder: "Select notify event(s)",
        saveCallback: saveNotificationSettings,
        channelName: "SMS"
    });

    // 3. E-mail Reports (Weekly / Monthly)
    setupNotificationChannel({
        switchId: "email_report_notification",
        dropdownBtnId: "reportFrequencyDropdown",
        labelSpanId: "selected-report-text",
        labelClass: "report-label-text",
        placeholder: "Select report frequency",
        saveCallback: saveReportSettings,
        channelName: "Email Report"
    });

    // ----------------------------------------------------
    // Initialize User Settings Channels (if present)
    // ----------------------------------------------------
    setupNotificationChannel({
        switchId: "notif-email",
        dropdownBtnId: "notifyEventsDropdownUserEmail",
        labelSpanId: "selected-events-text-user-email",
        labelClass: "event-label-text",
        placeholder: "Select notify event(s)",
        saveCallback: saveNotificationSettings,
        channelName: "Email"
    });

    setupNotificationChannel({
        switchId: "notif-sms",
        dropdownBtnId: "notifyEventsDropdownUserSms",
        labelSpanId: "selected-events-text-user-sms",
        labelClass: "event-label-text",
        placeholder: "Select notify event(s)",
        saveCallback: saveNotificationSettings,
        channelName: "SMS"
    });
});