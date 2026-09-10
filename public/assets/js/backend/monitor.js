document.addEventListener("DOMContentLoaded", () => {
    const t = document.getElementById("monitor-search");
    if (t) {
        if (t.value) {
            t.focus();
            const n = t.value;
            t.value = "";
            t.value = n;
        }
        t.addEventListener("input", n => {
            const r = n.target.value.toLowerCase().trim();
            document.querySelectorAll("tbody tr").forEach(e => {
                if (e.querySelector(".py-5")) return;
                e.textContent.toLowerCase().includes(r) ? e.style.display = "" : e.style.display = "none";
            });
        });
    }

    document.querySelectorAll(".trigger-check-form").forEach(n => {
        n.addEventListener("submit", function(r) {
            r.preventDefault();
            const s = this.querySelector(".trigger-btn"),
                  e = this.querySelector(".icon-idle"),
                  o = this.querySelector(".icon-spin"),
                  tr = this.closest("tr");

            e && e.classList.add("d-none");
            o && o.classList.remove("d-none");
            s && (s.disabled = !0);

            if (tr) {
                const prepBadge = tr.querySelector(".monitor-preparing-badge");
                if (prepBadge) {
                    prepBadge.classList.remove("d-none");
                }

                const checks = [
                    { selector: ".col-uptime-status", label: "Checking Uptime...", bg: "bg-warning-subtle text-warning-emphasis border-warning-subtle", spin: "text-warning" },
                    { selector: ".col-ssl-status", label: "Validating SSL...", bg: "bg-primary-subtle text-primary border-primary-subtle", spin: "text-primary" },
                    { selector: ".col-php-status", label: "Checking PHP...", bg: "bg-info-subtle text-info-emphasis border-info-subtle", spin: "text-info" },
                    { selector: ".col-domain-status", label: "Checking Domain...", bg: "bg-secondary-subtle text-secondary border-secondary-subtle", spin: "text-secondary" },
                    { selector: ".col-security-status", label: "Scanning Headers...", bg: "bg-dark-subtle text-dark border-dark-subtle", spin: "text-dark" }
                ];

                checks.forEach(chk => {
                    const cell = tr.querySelector(chk.selector);
                    if (cell && !cell.textContent.includes("Disabled")) {
                        cell.innerHTML = `
                            <div class="my-1">
                                <span class="badge ${chk.bg} border rounded-pill d-inline-flex align-items-center gap-1 px-2 py-1" style="font-size:0.75rem;">
                                    <span class="spinner-border spinner-border-sm ${chk.spin}" role="status" style="width: 0.72rem; height: 0.72rem; border-width: 0.14em;"></span>
                                    <span class="fw-semibold">${chk.label}</span>
                                </span>
                            </div>
                        `;
                    }
                });
            }

            fetch(this.action, {
                method: "POST",
                body: new FormData(this),
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json"
                }
            })
            .then(async c => {
                const i = await c.json().catch(() => ({}));
                return { ok: c.ok, data: i };
            })
            .then(({ ok: c, data: i }) => {
                if (c && i.success) {
                    typeof toastr < "u" && toastr.success(i.message || "Check completed successfully.");
                    setTimeout(() => { window.location.reload(); }, 500);
                } else {
                    typeof toastr < "u" && toastr.error(i.message || "Failed to complete check.");
                    window.location.reload();
                }
            })
            .catch(() => {
                typeof toastr < "u" && toastr.error("An unexpected error occurred while running check.");
                window.location.reload();
            });
        });
    });
});
