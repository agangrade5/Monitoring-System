<div class="card settings-card">
    <div class="card-header d-flex align-items-center">
        <i class="bi bi-bell fs-4 me-2 text-primary"></i>
        <div>
            <h5 class="mb-0 fw-bold">Alert notification channels.</h5>
            <small class="text-muted">Choose when and how you want to be notified</small>
        </div>
    </div>
    <form id="notification-settings-form" action="{{ route('settings.notifications') }}" method="POST">
        @csrf
        <div class="card-body">
            <!-- System Alerts Section -->
            <div class="mb-2">
                <!-- E-mail Row -->
                <div class="d-flex justify-content-between align-items-center py-3 border-bottom flex-wrap gap-3">
                    <div class="flex-grow-1" style="min-width: 250px;">
                        <p class="mb-0 fw-semibold">
                            <i class="bi bi-envelope-fill text-primary me-2"></i>
                            E-mail
                        </p>
                        <small class="text-muted">
                            Receive important updates, notifications, and account-related information via email.
                        </small>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <!-- Dropdown for E-mail -->
                        <div class="dropdown custom-notify-events-dropdown position-relative" style="min-width: 260px;">
                            <button class="btn btn-outline-secondary btn-sm w-100 d-flex justify-content-between align-items-center text-start py-2 px-3 shadow-none dropdown-toggle rounded-3 border-secondary-subtle" 
                                    type="button" 
                                    id="notifyEventsDropdownEmail" 
                                    data-bs-toggle="dropdown" 
                                    data-bs-auto-close="outside" 
                                    aria-expanded="false"
                                    style="font-size: 0.85rem; background-color: #ffffff; color: #2d3748;">
                                <span id="selected-events-text-email" class="text-truncate me-2 fw-medium">Select notify event(s)</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 p-2 mt-1 w-100" 
                                 aria-labelledby="notifyEventsDropdownEmail" 
                                 style="background-color: #1e2430; min-width: 250px; z-index: 1050;">
                                <label class="dropdown-event-item d-flex align-items-center gap-2 px-3 py-2 rounded text-white cursor-pointer mb-1" for="email_event_down">
                                    <input class="form-check-input notify-event-checkbox flex-shrink-0 m-0 cursor-pointer" type="checkbox" name="email_down_event" value="1" id="email_event_down" {{ ($notificationData['email']['down_event'] ?? false) ? 'checked' : '' }}>
                                    <span class="small fw-medium event-label-text">Down events</span>
                                </label>
                                <label class="dropdown-event-item d-flex align-items-center gap-2 px-3 py-2 rounded text-white cursor-pointer mb-1" for="email_event_up">
                                    <input class="form-check-input notify-event-checkbox flex-shrink-0 m-0 cursor-pointer" type="checkbox" name="email_up_event" value="1" id="email_event_up" {{ ($notificationData['email']['up_event'] ?? false) ? 'checked' : '' }}>
                                    <span class="small fw-medium event-label-text">Up events</span>
                                </label>
                                <label class="dropdown-event-item d-flex align-items-center gap-2 px-3 py-2 rounded text-white cursor-pointer" for="email_event_ssl_domain">
                                    <input class="form-check-input notify-event-checkbox flex-shrink-0 m-0 cursor-pointer" type="checkbox" name="email_ssl_domain_expiry" value="1" id="email_event_ssl_domain" {{ ($notificationData['email']['ssl_domain_expiry'] ?? false) ? 'checked' : '' }}>
                                    <span class="small fw-medium event-label-text">SSL & Domain expiry</span>
                                </label>
                            </div>
                        </div>

                        <!-- Switch for E-mail -->
                        <div class="form-check form-switch fs-5 mb-0">
                            <input class="form-check-input notification-switch"
                                type="checkbox"
                                role="switch"
                                id="email_notification"
                                name="email_enabled"
                                value="1"
                                {{ ($notificationData['email']['enabled'] ?? false) ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>

                <!-- SMS Row -->
                <div class="d-flex justify-content-between align-items-center py-3 flex-wrap gap-3">
                    <div class="flex-grow-1" style="min-width: 250px;">
                        <p class="mb-0 fw-semibold">
                            <i class="bi bi-phone-fill text-warning me-2"></i>
                            SMS
                        </p>
                        <small class="text-muted">
                            Receive important alerts and notifications directly on your mobile phone.
                        </small>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <!-- Dropdown for SMS -->
                        <div class="dropdown custom-notify-events-dropdown position-relative" style="min-width: 260px;">
                            <button class="btn btn-outline-secondary btn-sm w-100 d-flex justify-content-between align-items-center text-start py-2 px-3 shadow-none dropdown-toggle rounded-3 border-secondary-subtle" 
                                    type="button" 
                                    id="notifyEventsDropdownSms" 
                                    data-bs-toggle="dropdown" 
                                    data-bs-auto-close="outside" 
                                    aria-expanded="false"
                                    style="font-size: 0.85rem; background-color: #ffffff; color: #2d3748;">
                                <span id="selected-events-text-sms" class="text-truncate me-2 fw-medium">Select notify event(s)</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 p-2 mt-1 w-100" 
                                 aria-labelledby="notifyEventsDropdownSms" 
                                 style="background-color: #1e2430; min-width: 250px; z-index: 1050;">
                                <label class="dropdown-event-item d-flex align-items-center gap-2 px-3 py-2 rounded text-white cursor-pointer mb-1" for="sms_event_down">
                                    <input class="form-check-input notify-event-checkbox flex-shrink-0 m-0 cursor-pointer" type="checkbox" name="sms_down_event" value="1" id="sms_event_down" {{ ($notificationData['sms']['down_event'] ?? false) ? 'checked' : '' }}>
                                    <span class="small fw-medium event-label-text">Down events</span>
                                </label>
                                <label class="dropdown-event-item d-flex align-items-center gap-2 px-3 py-2 rounded text-white cursor-pointer mb-1" for="sms_event_up">
                                    <input class="form-check-input notify-event-checkbox flex-shrink-0 m-0 cursor-pointer" type="checkbox" name="sms_up_event" value="1" id="sms_event_up" {{ ($notificationData['sms']['up_event'] ?? false) ? 'checked' : '' }}>
                                    <span class="small fw-medium event-label-text">Up events</span>
                                </label>
                                <label class="dropdown-event-item d-flex align-items-center gap-2 px-3 py-2 rounded text-white cursor-pointer" for="sms_event_ssl_domain">
                                    <input class="form-check-input notify-event-checkbox flex-shrink-0 m-0 cursor-pointer" type="checkbox" name="sms_ssl_domain_expiry" value="1" id="sms_event_ssl_domain" {{ ($notificationData['sms']['ssl_domain_expiry'] ?? false) ? 'checked' : '' }}>
                                    <span class="small fw-medium event-label-text">SSL & Domain expiry</span>
                                </label>
                            </div>
                        </div>

                        <!-- Switch for SMS -->
                        <div class="form-check form-switch fs-5 mb-0">
                            <input class="form-check-input notification-switch"
                                type="checkbox"
                                role="switch"
                                id="sms_notification"
                                name="sms_enabled"
                                value="1"
                                {{ ($notificationData['sms']['enabled'] ?? false) ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- E-mail Report Settings Card -->
<div class="card settings-card mt-4">
    <div class="card-header d-flex align-items-center">
        <i class="bi bi-file-earmark-bar-graph fs-4 me-2 text-primary"></i>
        <div>
            <h5 class="mb-0 fw-bold">E-mail report settings</h5>
            <small class="text-muted">Configure automated summary reports sent to your email</small>
        </div>
    </div>
    <form id="report-settings-form" action="{{ route('settings.report') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="mb-2">
                <div class="d-flex justify-content-between align-items-center py-3 flex-wrap gap-3">
                    <div class="flex-grow-1" style="min-width: 250px;">
                        <p class="mb-0 fw-semibold">
                            <i class="bi bi-envelope-check text-primary me-2"></i>
                            Enable e-mail reports
                        </p>
                        <small class="text-muted">
                            You will receive email reports on your account e-mail {{auth()->user()->email}}.
                        </small>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <!-- Dropdown for Report Frequency (Weekly / Monthly) -->
                        <div class="dropdown custom-notify-events-dropdown position-relative" style="min-width: 240px;">
                            <button class="btn btn-outline-secondary btn-sm w-100 d-flex justify-content-between align-items-center text-start py-2 px-3 shadow-none dropdown-toggle rounded-3 border-secondary-subtle" 
                                    type="button" 
                                    id="reportFrequencyDropdown" 
                                    data-bs-toggle="dropdown" 
                                    data-bs-auto-close="outside" 
                                    aria-expanded="false"
                                    style="font-size: 0.85rem; background-color: #ffffff; color: #2d3748;">
                                <span id="selected-report-text" class="text-truncate me-2 fw-medium">Select report frequency</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 p-2 mt-1 w-100" 
                                 aria-labelledby="reportFrequencyDropdown" 
                                 style="background-color: #1e2430; min-width: 230px; z-index: 1050;">
                                <label class="dropdown-event-item d-flex align-items-center gap-2 px-3 py-2 rounded text-white cursor-pointer mb-1" for="report_weekly">
                                    <input class="form-check-input report-frequency-checkbox flex-shrink-0 m-0 cursor-pointer" type="checkbox" name="report_weekly" value="1" id="report_weekly" {{ ($reportData['report_email']['Weekly'] ?? false) ? 'checked' : '' }}>
                                    <span class="small fw-medium report-label-text">Weekly report</span>
                                </label>
                                <label class="dropdown-event-item d-flex align-items-center gap-2 px-3 py-2 rounded text-white cursor-pointer" for="report_monthly">
                                    <input class="form-check-input report-frequency-checkbox flex-shrink-0 m-0 cursor-pointer" type="checkbox" name="report_monthly" value="1" id="report_monthly" {{ ($reportData['report_email']['Monthly'] ?? false) ? 'checked' : '' }}>
                                    <span class="small fw-medium report-label-text">Monthly report</span>
                                </label>
                            </div>
                        </div>

                        <!-- Switch for E-mail Reports -->
                        <div class="form-check form-switch fs-5 mb-0">
                            <input class="form-check-input notification-switch"
                                type="checkbox"
                                role="switch"
                                id="email_report_notification"
                                name="report_email_enabled"
                                value="1"
                                {{ ($reportData['report_email']['enabled'] ?? false) ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

