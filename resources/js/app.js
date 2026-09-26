import Alpine from 'alpinejs'
import {
    Chart,
    ArcElement,
    BarElement,
    DoughnutController,
    BarController,
    CategoryScale,
    LinearScale,
    Tooltip,
    Legend,
} from 'chart.js'

// Chart.js 4 ships without the controllers registered, so both chart types
// have to be wired up explicitly.
Chart.register(
    ArcElement,
    BarElement,
    DoughnutController,
    BarController,
    CategoryScale,
    LinearScale,
    Tooltip,
    Legend,
)

/* ------------------------------------------------------------------ shell */

window.appShell = () => ({
    sidebarOpen: window.innerWidth >= 1024,

    init() {
        this.$watch('sidebarOpen', (open) => {
            document.body.classList.toggle('overflow-hidden', open && window.innerWidth < 1024)
        })

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                document.body.classList.remove('overflow-hidden')
            } else {
                this.sidebarOpen = false
            }
        })

        // "/" focuses the header search box, Escape closes the mobile drawer
        document.addEventListener('keydown', (event) => {
            if (event.key === '/' && !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement?.tagName)) {
                event.preventDefault()
                document.querySelector('header input[type="search"]')?.focus()
            }

            if (event.key === 'Escape') {
                this.sidebarOpen = false
            }
        })
    },
})

/* ------------------------------------------------------------ product form */

window.productForm = (initialPreview) => ({
    preview: initialPreview || '',

    pickImage(event) {
        const [file] = event.target.files || []
        this.preview = file ? URL.createObjectURL(file) : this.preview
    },

    descriptionLength() {
        return this.$refs.description?.value?.length ?? 0
    },

    priceLabel() {
        const min = Number.parseInt(this.$refs.price_min?.value || '0', 10)
        const max = Number.parseInt(this.$refs.price_max?.value || '0', 10)
        const peso = '₱'

        if (!min && !max) return `${peso}0`
        if (min === max || !max) return peso + min.toLocaleString('en-US')

        return peso + min.toLocaleString('en-US') + '–' + max.toLocaleString('en-US')
    },

    syncPrice() {
        const max = this.$refs.price_max
        const min = this.$refs.price_min

        if (max && !max.value && min) max.value = min.value

        this.priceLabel()
    },
})

/* --------------------------------------------------- confirm-before-submit */

document.addEventListener('submit', (event) => {
    const message = event.target.getAttribute('data-confirm')

    if (message && !window.confirm(message)) {
        event.preventDefault()
    }
})

/* ------------------------------------------------------------------ charts */

const gridColor = 'rgba(255, 255, 255, 0.06)'
const tickColor = '#8b93a7'

function baseOptions() {
    return {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 600, easing: 'easeOutQuart' },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#191d24',
                borderColor: '#2f3540',
                borderWidth: 1,
                titleColor: '#e8eaed',
                bodyColor: '#9aa3b2',
                padding: 10,
                cornerRadius: 10,
                displayColors: false,
            },
        },
    }
}

function renderDistribution(data) {
    const canvas = document.getElementById('chartDistribution')
    if (!canvas || !data) return

    new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: data.labels,
            datasets: [
                {
                    data: data.counts,
                    backgroundColor: data.colors,
                    borderColor: '#14171d',
                    borderWidth: 3,
                    hoverOffset: 8,
                    hoverBorderColor: '#14171d',
                },
            ],
        },
        options: {
            ...baseOptions(),
            cutout: '72%',
            plugins: {
                ...baseOptions().plugins,
                tooltip: {
                    ...baseOptions().plugins.tooltip,
                    callbacks: {
                        label: (ctx) => ` ${ctx.label}: ${ctx.parsed} products`,
                    },
                },
            },
        },
    })
}

function renderPrices(data) {
    const canvas = document.getElementById('chartPrices')
    if (!canvas || !data) return

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: 'Lowest',
                    data: data.min,
                    backgroundColor: 'rgba(249, 115, 22, 0.55)',
                    borderColor: '#f97316',
                    borderWidth: 1.5,
                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 26,
                },
                {
                    label: 'Highest',
                    data: data.max,
                    backgroundColor: 'rgba(251, 146, 60, 0.22)',
                    borderColor: 'rgba(251, 146, 60, 0.7)',
                    borderWidth: 1.5,
                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 26,
                },
            ],
        },
        options: {
            ...baseOptions(),
            scales: {
                x: {
                    grid: { display: false },
                    border: { color: gridColor },
                    ticks: {
                        color: tickColor,
                        font: { size: 10, weight: '600' },
                        maxRotation: 0,
                        autoSkip: false,
                        callback(value) {
                            const label = this.getLabelForValue(value)
                            return label.length > 16 ? label.split(' ')[0] : label
                        },
                    },
                },
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor },
                    border: { display: false },
                    ticks: {
                        color: tickColor,
                        font: { size: 10 },
                        padding: 6,
                        callback: (value) => '₱' + Number(value).toLocaleString('en-US'),
                    },
                },
            },
            plugins: {
                ...baseOptions().plugins,
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: {
                        color: tickColor,
                        boxWidth: 9,
                        boxHeight: 9,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: { size: 11, weight: '600' },
                    },
                },
                tooltip: {
                    ...baseOptions().plugins.tooltip,
                    callbacks: {
                        label: (ctx) => ` ${ctx.dataset.label}: ₱${Number(ctx.parsed.y).toLocaleString('en-US')}`,
                    },
                },
            },
        },
    })
}

function renderCharts() {
    const data = window.__techCharts
    if (!data) return

    renderDistribution(data.distribution)
    renderPrices(data.prices)
}

/* ------------------------------------------------------------------- boot */

window.Alpine = Alpine
Alpine.start()

document.addEventListener('DOMContentLoaded', renderCharts)
