"use strict";

async function getJson(url) {
    const response = await fetch(url, { headers: { "Accept": "application/json" } });
    if (!response.ok) {
        throw new Error("Request failed");
    }
    return response.json();
}

function formatNumber(value) {
    return new Intl.NumberFormat().format(Number(value) || 0);
}

async function renderAdminDashboard() {
    const root = document.getElementById("adminDashboardRoot");
    if (!root) return;

    const statsUrl = root.dataset.statsUrl;
    const chartUrl = root.dataset.chartUrl;

    try {
        const statsPayload = await getJson(statsUrl);
        if (statsPayload.ok) {
            const stats = statsPayload.data;
            document.getElementById("cardVoters").textContent = formatNumber(stats.total_voters);
            document.getElementById("cardCandidates").textContent = formatNumber(stats.total_candidates);
            document.getElementById("cardVotes").textContent = formatNumber(stats.total_votes_cast);
            document.getElementById("cardStatus").textContent = String(stats.active_election_status || "inactive");
        }

        const trendPayload = await getJson(chartUrl + "?days=7");
        if (trendPayload.ok && window.Chart) {
            const labels = trendPayload.data.map((item) => item.date);
            const values = trendPayload.data.map((item) => item.total);
            const context = document.getElementById("votesTrendChart");
            if (context) {
                new Chart(context, {
                    type: "line",
                    data: {
                        labels: labels,
                        datasets: [{
                            label: "Votes Cast",
                            data: values,
                            borderColor: "#198754",
                            backgroundColor: "rgba(25, 135, 84, 0.15)",
                            fill: true,
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0 }
                            }
                        }
                    }
                });
            }
        }
    } catch (error) {
        console.error("Failed to load dashboard data:", error);
    }
}

function initializeImagePreviews() {
    document.querySelectorAll('.image-preview-input').forEach((input) => {
        const targetSelector = input.dataset.previewTarget;
        const preview = targetSelector ? document.querySelector(targetSelector) : null;

        input.addEventListener('change', () => {
            if (!preview) {
                return;
            }

            const file = input.files && input.files[0];
            if (!file) {
                preview.src = '';
                preview.style.display = 'none';
                return;
            }

            const reader = new FileReader();
            reader.onload = () => {
                preview.src = reader.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });
    });
}

function renderReportChart() {
    const canvas = document.getElementById('reportChart');
    if (!canvas || !window.Chart) return;

    const labels = JSON.parse(canvas.dataset.labels || '[]');
    const values = JSON.parse(canvas.dataset.values || '[]');
    const sourceUrl = canvas.dataset.source;

    const context = canvas.getContext('2d');
    const reportChart = new Chart(context, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Votes',
                data: values,
                backgroundColor: '#19875488',
                borderColor: '#198754',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });

    if (!sourceUrl) return;

    async function refreshReportData() {
        try {
            const payload = await getJson(sourceUrl);
            if (!payload.ok) {
                return;
            }

            document.getElementById('reportTotalVotes').textContent = formatNumber(payload.summary.total_votes);
            document.getElementById('reportCandidateCount').textContent = formatNumber(payload.summary.total_candidates);
            document.getElementById('reportPositionCount').textContent = formatNumber(payload.summary.total_positions);

            const updatedLabels = payload.topCandidates.map((item) => item.label);
            const updatedValues = payload.topCandidates.map((item) => item.votes);

            reportChart.data.labels = updatedLabels;
            reportChart.data.datasets[0].data = updatedValues;
            reportChart.update();
        } catch (error) {
            console.error('Failed to refresh report data:', error);
        }
    }

    refreshReportData();
    setInterval(refreshReportData, 20000);
}

function initializePasswordToggle() {
    const toggleButton = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    if (toggleButton && passwordInput) {
        toggleButton.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            const icon = toggleButton.querySelector('i');
            if (icon) {
                icon.className = type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
            }
        });
    }
}

document.addEventListener("DOMContentLoaded", () => {
    renderAdminDashboard();
    initializeImagePreviews();
    initializePasswordToggle();
    renderReportChart();

    const printButton = document.getElementById('printReportButton');
    if (printButton) {
        printButton.addEventListener('click', () => window.print());
    }
});
