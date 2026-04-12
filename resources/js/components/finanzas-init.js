import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import listPlugin from "@fullcalendar/list";
import interactionPlugin from "@fullcalendar/interaction";
import esLocale from "@fullcalendar/core/locales/es";
import ApexCharts from "apexcharts";

export function initFinanzas() {
    console.log("Inicializando Finanzas Dashboard...");

    initIngresosChart();
    initCalendarioRecordatorios();
    initCalendarioVencimientos();
}

function initIngresosChart() {
    const el = document.getElementById("finanzasIngresosChart");
    if (!el) return;

    try {
        const isDark = document.documentElement.classList.contains("dark");
        const cats = JSON.parse(el.dataset.cats || "[]");
        const ingresos = JSON.parse(el.dataset.ingresos || "[]");
        const egresos = JSON.parse(el.dataset.egresos || "[]");

        const options = {
            series: [
                { name: "Ingresos", data: ingresos },
                { name: "Egresos", data: egresos },
            ],
            chart: {
                type: "area",
                height: 240,
                fontFamily: "Inter, sans-serif",
                toolbar: { show: false },
                zoom: { enabled: false },
                background: "transparent",
            },
            colors: ["#10b981", "#ef4444"],
            fill: {
                type: "gradient",
                gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.02, stops: [0, 100] },
            },
            stroke: { curve: "smooth", width: 2 },
            dataLabels: { enabled: false },
            grid: {
                borderColor: isDark ? "#374151" : "#f3f4f6",
                strokeDashArray: 4,
                yaxis: { lines: { show: true } },
                xaxis: { lines: { show: false } },
            },
            xaxis: {
                categories: cats,
                tickAmount: 7,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: isDark ? "#9ca3af" : "#6b7280", fontSize: "10px" } },
            },
            yaxis: {
                labels: {
                    style: { colors: isDark ? "#9ca3af" : "#6b7280", fontSize: "11px" },
                    formatter: (v) => "$" + new Intl.NumberFormat("es").format(v),
                },
            },
            legend: { show: false },
            tooltip: {
                theme: isDark ? "dark" : "light",
                y: { formatter: (v) => "$" + new Intl.NumberFormat("es").format(v) },
            },
        };
        new ApexCharts(el, options).render();
    } catch (err) {
        console.error("Error inicializando grafica ingresos", err);
    }
}

function initCalendarioRecordatorios() {
    const el = document.getElementById("calendarioRecordatorios");
    if (!el) return;

    let calRecordatorios = null;
    let selectedColor = "#3b82f6";

    // Reattach all the form logic once
    if (!el.dataset.initDone) {
        el.dataset.initDone = "true";

        document.querySelectorAll(".color-option").forEach((btn) => {
            btn.addEventListener("click", function () {
                selectedColor = this.dataset.color;
                document.getElementById("recordatorioColor").value = selectedColor;
                document.querySelectorAll(".color-option").forEach((b) => {
                    b.style.outline = "none";
                });
                this.style.outline = "2px solid #1d4ed8";
                this.style.outlineOffset = "2px";
            });
        });

        const form = document.getElementById("formRecordatorio");
        if (form) {
            form.addEventListener("submit", async function (e) {
                e.preventDefault();
                const id = document.getElementById("recordatorioIdInput").value;
                const url = id ? `/finanzas/recordatorios/${id}` : "/finanzas/recordatorios";
                const method = id ? "PUT" : "POST";
                const tk = document.querySelector('[name=_token]');

                try {
                    const res = await fetch(url, {
                        method,
                        headers: {
                            "X-CSRF-TOKEN": tk ? tk.value : '',
                            Accept: "application/json",
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify({
                            fecha: document.getElementById("recordatorioFechaInput").value,
                            titulo: document.getElementById("recordatorioTitulo").value,
                            descripcion: document.getElementById("recordatorioDescripcion").value,
                            color: document.getElementById("recordatorioColor").value,
                        }),
                    });
                    if (res.ok) {
                        cerrarModalRecordatorio();
                        if (calRecordatorios) calRecordatorios.refetchEvents();
                    }
                } catch (err) {
                    console.error(err);
                }
            });
        }

        const btnBorrar = document.getElementById("btnBorrarRecordatorio");
        if (btnBorrar) {
            btnBorrar.addEventListener("click", async function () {
                const id = document.getElementById("recordatorioIdInput").value;
                const tk = document.querySelector('[name=_token]');
                if (!id || !confirm("¿Eliminar este recordatorio?")) return;
                await fetch(`/finanzas/recordatorios/${id}`, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": tk ? tk.value : '',
                        Accept: "application/json",
                    },
                });
                cerrarModalRecordatorio();
                if (calRecordatorios) calRecordatorios.refetchEvents();
            });
        }

        window.cerrarModalRecordatorio = function () {
            document.getElementById("modalRecordatorio").classList.add("hidden");
            if (form) form.reset();
        };
    }

    function abrirModalRecordatorio(date, eventId, titulo, descripcion, color) {
        const modal = document.getElementById("modalRecordatorio");
        if (!modal) return;
        const isEdit = !!eventId;
        document.getElementById("modalRecordatorioTitle").textContent = isEdit
            ? "Editar Recordatorio"
            : "Nuevo Recordatorio";
        document.getElementById("modalRecordatorioFecha").textContent = "📅 " + date;
        document.getElementById("recordatorioFechaInput").value = date;
        document.getElementById("recordatorioIdInput").value = eventId || "";
        document.getElementById("recordatorioTitulo").value = titulo || "";
        document.getElementById("recordatorioDescripcion").value = descripcion || "";
        document.getElementById("btnBorrarRecordatorio").classList.toggle("hidden", !isEdit);

        // Set color
        selectedColor = color || "#3b82f6";
        document.getElementById("recordatorioColor").value = selectedColor;
        document.querySelectorAll(".color-option").forEach((btn) => {
            btn.style.outline = btn.dataset.color === selectedColor ? "2px solid #1d4ed8" : "none";
            btn.style.outlineOffset = "2px";
        });

        modal.classList.remove("hidden");
        document.getElementById("recordatorioTitulo").focus();
    }

    try {
        calRecordatorios = new Calendar(el, {
            plugins: [dayGridPlugin, listPlugin, interactionPlugin],
            locale: esLocale,
            initialView: "dayGridMonth",
            height: 420,
            headerToolbar: {
                left: "prev,next today",
                center: "title",
                right: "dayGridMonth,listWeek",
            },
            buttonText: { today: "Hoy", month: "Mes", list: "Lista" },
            events: "/finanzas/recordatorios",
            dateClick: function (info) {
                abrirModalRecordatorio(info.dateStr, null, "", "", "#3b82f6");
            },
            eventClick: function (info) {
                const ev = info.event;
                abrirModalRecordatorio(
                    ev.startStr.split("T")[0],
                    ev.id,
                    ev.title,
                    ev.extendedProps.descripcion,
                    ev.backgroundColor
                );
            },
        });
        calRecordatorios.render();
    } catch (err) {
        console.error("Error initializando calRecordatorios", err);
    }
}

function initCalendarioVencimientos() {
    const el = document.getElementById("calendarioVencimientos");
    if (!el) return;

    try {
        const cal = new Calendar(el, {
            plugins: [dayGridPlugin, interactionPlugin],
            locale: esLocale,
            initialView: "dayGridMonth",
            height: 420,
            headerToolbar: {
                left: "prev,next today",
                center: "title",
                right: "",
            },
            buttonText: { today: "Hoy" },
            events: "/finanzas/calendar-events",
            eventClick: function (info) {
                const ev = info.event;
                const grupo = ev.extendedProps.grupo || [];
                const fecha = ev.startStr.split("T")[0];

                const titleEl = document.getElementById("modalVencimientosTitle");
                if (titleEl) titleEl.textContent = "📅 Vencimientos – " + fecha;

                let html = "";
                grupo.forEach((item) => {
                    const badge =
                        item.tipo === "pagar"
                            ? '<span class="px-2 py-0.5 rounded-full text-xs bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400">Por Pagar</span>'
                            : '<span class="px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400">Por Cobrar</span>';
                    const estadoBadge = item.estado_pago
                        ? `<span class="px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600 dark:bg-neutral-700 dark:text-gray-400">${item.estado_pago}</span>`
                        : "";
                    html += `
                        <div class="flex items-center justify-between p-3 rounded-xl border-2
                                    border-gray-100 dark:border-neutral-700 hover:border-amber-200
                                    dark:hover:border-amber-700 transition-colors">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 mb-1">${badge}${estadoBadge}</div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-white truncate">${item.referencia}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">${item.descripcion}</p>
                            </div>
                            <p class="text-sm font-bold text-gray-700 dark:text-white ml-4 flex-shrink-0">
                                $${new Intl.NumberFormat("es").format(item.monto)}
                            </p>
                        </div>`;
                });

                const bodyEl = document.getElementById("modalVencimientosBody");
                if (bodyEl) {
                    bodyEl.innerHTML = html || '<p class="text-sm text-gray-400">Sin detalles</p>';
                }
                const modal = document.getElementById("modalVencimientos");
                if (modal) modal.classList.remove("hidden");
            },
        });
        cal.render();
    } catch (err) {
        console.error("Error initializando calendarioVencimientos", err);
    }
}
