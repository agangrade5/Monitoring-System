<div class="card settings-card">
    <div class="card-header d-flex align-items-center">
        <i class="bi bi-bell fs-4 me-2 text-primary"></i>
        <div>
            <h5 class="mb-0 fw-bold">Alert notification channels.</h5>
            <small class="text-muted">Choose when and how you want to be notified</small>
        </div>
    </div>
    <div class="card-body">
        <!-- System Alerts Section -->
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                <div>
                        <p class="mb-0 fw-semibold">
                        <i class="bi bi-envelope-fill text-primary me-2"></i>
                        E-mail
                    </p>
                    <input type="hidden"
                        name="email_notification"
                        value="0">
                    <small class="text-muted">
                        Receive important updates, notifications, and account-related information via email.
                    </small>
                </div>
                <div class="form-check form-switch fs-5">
                    <input type="hidden"
                    name="email_notification"
                    value="0">

                    <input class="form-check-input notification-switch"
                    type="checkbox"
                    role="switch"
                    id="email_notification"
                    data-type="email_notification"
                    {{ $settings->email_notification ? 'checked' : '' }}>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                <div>
                    <p class="mb-0 fw-semibold">
                        <i class="bi bi-phone-fill text-warning me-2"></i>
                        SMS
                    </p>
                    <small class="text-muted">
                        Receive important alerts and notifications directly on your mobile phone.
                    </small>
                </div>
                <div class="form-check form-switch fs-5">
                    <input type="hidden"
                        name="sms_notification"
                        value="0">
                    <input class="form-check-input notification-switch"
                        type="checkbox"
                        role="switch"
                        id="sms_notification"
                        data-type="sms_notification"
                        {{ $settings->sms_notification ? 'checked' : '' }}>
                </div>
            </div>
        </div>
    </div>
</div>
