document.addEventListener("DOMContentLoaded",function(){const e=document.getElementById("urls-container"),o=document.getElementById("btn-add-url");function r(){const t=document.createElement("div");t.className="url-row input-group",t.innerHTML=`
            <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
            <input type="text" name="urls[]" class="form-control url-input" placeholder="e.g.  https://example.com" required>
            <button type="button" class="btn btn-outline-danger px-3 remove-url-btn" title="Remove Domain">
                <i class="bi bi-dash-lg"></i>
            </button>
        `,e.appendChild(t);const n=t.querySelector(".url-input");n&&n.focus()}o&&o.addEventListener("click",function(t){t.preventDefault(),r()}),e&&e.addEventListener("click",function(t){t.target.closest(".add-url-btn")&&(t.preventDefault(),r());const i=t.target.closest(".remove-url-btn");if(i){t.preventDefault();const l=i.closest(".url-row");l&&l.remove()}})});
