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
    showPreviewFlag: false,
    previewName: '',
    previewDescription: '',
    previewCategory: '',
    previewPrice: '',
    previewStock: 0,
    previewVariants: [],

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

    openPreview() {
        this.previewName = this.$refs.name?.value || ''
        this.previewDescription = this.$refs.description?.value || ''
        this.previewCategory = this.$refs.category?.selectedOptions?.[0]?.text || ''
        this.previewPrice = this.priceLabel()
        this.previewStock = Number.parseInt(this.$refs.stock?.value || '0', 10) || 0
        this.previewVariants = (this.$refs.variants?.value || '')
            .split('\n')
            .map((line) => line.trim())
            .filter(Boolean)
            .map((line) => {
                const [name, price] = line.split('|').map((part) => part.trim())
                const amount = Number.parseInt(price || '', 10)

                return {
                    name,
                    price: Number.isFinite(amount) && amount > 0 ? '₱' + amount.toLocaleString('en-US') : '',
                }
            })
        this.showPreviewFlag = true
    },
})



/* ------------------------------------------------------------------ charts */

const gridColor = 'rgba(21, 21, 21, 0.06)'
const tickColor = '#666666'

function baseOptions() {
    return {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 600, easing: 'easeOutQuart' },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#151515',
                borderColor: 'rgba(21, 21, 21, 0.2)',
                borderWidth: 1,
                titleColor: '#ffffff',
                bodyColor: '#c9c9cf',
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
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    hoverOffset: 8,
                    hoverBorderColor: '#ffffff',
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
                    backgroundColor: 'rgba(201, 0, 22, 0.6)',
                    borderColor: '#c90016',
                    borderWidth: 1.5,
                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 26,
                },
                {
                    label: 'Highest',
                    data: data.max,
                    backgroundColor: 'rgba(201, 0, 22, 0.14)',
                    borderColor: 'rgba(201, 0, 22, 0.45)',
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

/* ------------------------------------------------- public storefront favs */

// Session-local favorites used by the public storefront. Nothing is sent to
// the server: the list is persisted in localStorage so a visitor can mark
// products they like across page loads.
Alpine.store('storefront', {
    favorites: [],

    init() {
        try {
            const raw = localStorage.getItem('techverse:favorites')
            this.favorites = raw ? JSON.parse(raw) : []
        } catch {
            this.favorites = []
        }
    },

    get count() {
        return Array.isArray(this.favorites) ? this.favorites.length : 0
    },

    get favoritesList() {
        return Array.isArray(this.favorites) ? this.favorites : []
    },

    has(id) {
        return Array.isArray(this.favorites) && this.favorites.some((f) => Number(f.id) === Number(id))
    },

    toggle(product) {
        const index = this.favorites.findIndex((f) => Number(f.id) === Number(product.id))
        if (index < 0) {
            this.favorites.push(product)
        } else {
            this.favorites.splice(index, 1)
        }
        this.persist()
    },

    persist() {
        try {
            localStorage.setItem('techverse:favorites', JSON.stringify(this.favorites))
        } catch {
            /* storage unavailable — favorites stay in memory for the session */
        }
    },
})

window.Alpine = Alpine
Alpine.start()

document.addEventListener('DOMContentLoaded', renderCharts)
