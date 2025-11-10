<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>داشبورد SEO - متکوجا</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center">
                        <h1 class="text-2xl font-bold text-gray-900">داشبورد SEO</h1>
                    </div>
                    <div class="flex items-center space-x-4 space-x-reverse">
                        <button onclick="generateSitemap()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-sitemap mr-2"></i>
                            تولید Sitemap
                        </button>
                        <button onclick="generateRobots()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-robot mr-2"></i>
                            تولید robots.txt
                        </button>
                        <button onclick="refreshData()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-sync-alt mr-2"></i>
                            بروزرسانی
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-building text-2xl"></i>
                        </div>
                        <div class="mr-4">
                            <p class="text-blue-100 text-sm font-medium">کل کسب‌وکارها</p>
                            <p class="text-2xl font-bold" id="total-businesses">0</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-tags text-2xl"></i>
                        </div>
                        <div class="mr-4">
                            <p class="text-green-100 text-sm font-medium">کل دسته‌بندی‌ها</p>
                            <p class="text-2xl font-bold" id="total-categories">0</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-chart-line text-2xl"></i>
                        </div>
                        <div class="mr-4">
                            <p class="text-purple-100 text-sm font-medium">درصد تکمیل SEO</p>
                            <p class="text-2xl font-bold" id="seo-completion">0%</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-6 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-file-alt text-2xl"></i>
                        </div>
                        <div class="mr-4">
                            <p class="text-orange-100 text-sm font-medium">صفحات SEO</p>
                            <p class="text-2xl font-bold" id="seo-pages">0</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- SEO Completion Chart -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">وضعیت تکمیل SEO</h3>
                    <div class="chart-container">
                        <canvas id="seoCompletionChart"></canvas>
                    </div>
                </div>

                <!-- Meta Tags Distribution Chart -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">توزیع Meta Tags</h3>
                    <div class="chart-container">
                        <canvas id="metaTagsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Technical Status -->
            <div class="bg-white p-6 rounded-lg shadow mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">وضعیت فنی</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-sitemap text-2xl text-blue-500 mr-3"></i>
                            <div>
                                <p class="font-medium text-gray-900">Sitemap.xml</p>
                                <p class="text-sm text-gray-500" id="sitemap-status">در حال بررسی...</p>
                            </div>
                        </div>
                        <span id="sitemap-icon" class="text-gray-400">
                            <i class="fas fa-question-circle"></i>
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-robot text-2xl text-green-500 mr-3"></i>
                            <div>
                                <p class="font-medium text-gray-900">robots.txt</p>
                                <p class="text-sm text-gray-500" id="robots-status">در حال بررسی...</p>
                            </div>
                        </div>
                        <span id="robots-icon" class="text-gray-400">
                            <i class="fas fa-question-circle"></i>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">فعالیت‌های اخیر</h3>
                <div class="space-y-4" id="recent-activity">
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                        <p class="text-gray-700">در حال بارگذاری...</p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        let seoCompletionChart, metaTagsChart;

        // Load dashboard data
        async function loadDashboardData() {
            try {
                const response = await fetch('/api/admin/seo/dashboard');
                const data = await response.json();
                
                // Update stats cards
                document.getElementById('total-businesses').textContent = data.overview.total_businesses;
                document.getElementById('total-categories').textContent = data.overview.total_categories;
                document.getElementById('seo-completion').textContent = 
                    Math.round((data.overview.businesses_seo_percentage + data.overview.categories_seo_percentage) / 2) + '%';
                document.getElementById('seo-pages').textContent = data.overview.seo_pages_count;

                // Update technical status
                updateTechnicalStatus(data.technical);

                // Update charts
                updateSeoCompletionChart(data.charts.seo_completion);
                updateMetaTagsChart(data.charts.meta_tags_distribution);

            } catch (error) {
                console.error('Error loading dashboard data:', error);
                showNotification('خطا در بارگذاری داده‌ها', 'error');
            }
        }

        // Update technical status
        function updateTechnicalStatus(technical) {
            const sitemapStatus = document.getElementById('sitemap-status');
            const sitemapIcon = document.getElementById('sitemap-icon');
            const robotsStatus = document.getElementById('robots-status');
            const robotsIcon = document.getElementById('robots-icon');

            // Sitemap status
            if (technical.sitemap_exists) {
                sitemapStatus.textContent = 'موجود';
                sitemapIcon.innerHTML = '<i class="fas fa-check-circle text-green-500"></i>';
            } else {
                sitemapStatus.textContent = 'موجود نیست';
                sitemapIcon.innerHTML = '<i class="fas fa-times-circle text-red-500"></i>';
            }

            // Robots status
            if (technical.robots_exists) {
                robotsStatus.textContent = 'موجود';
                robotsIcon.innerHTML = '<i class="fas fa-check-circle text-green-500"></i>';
            } else {
                robotsStatus.textContent = 'موجود نیست';
                robotsIcon.innerHTML = '<i class="fas fa-times-circle text-red-500"></i>';
            }
        }

        // Update SEO completion chart
        function updateSeoCompletionChart(data) {
            const ctx = document.getElementById('seoCompletionChart').getContext('2d');
            
            if (seoCompletionChart) {
                seoCompletionChart.destroy();
            }

            seoCompletionChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['کامل', 'ناقص'],
                    datasets: [{
                        data: [data.businesses.complete + data.categories.complete, 
                               data.businesses.incomplete + data.categories.incomplete],
                        backgroundColor: ['#10B981', '#F59E0B'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Update meta tags chart
        function updateMetaTagsChart(data) {
            const ctx = document.getElementById('metaTagsChart').getContext('2d');
            
            if (metaTagsChart) {
                metaTagsChart.destroy();
            }

            metaTagsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Title', 'Description', 'Keywords'],
                    datasets: [{
                        label: 'کسب‌وکارها',
                        data: [data.title.businesses, data.description.businesses, data.keywords.businesses],
                        backgroundColor: '#3B82F6'
                    }, {
                        label: 'دسته‌بندی‌ها',
                        data: [data.title.categories, data.description.categories, data.keywords.categories],
                        backgroundColor: '#10B981'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Generate sitemap
        async function generateSitemap() {
            try {
                showNotification('در حال تولید Sitemap...', 'info');
                const response = await fetch('/api/seo/sitemap/generate', {
                    method: 'POST'
                });
                const result = await response.json();
                
                if (response.ok) {
                    showNotification('Sitemap با موفقیت تولید شد', 'success');
                    refreshData();
                } else {
                    showNotification('خطا در تولید Sitemap', 'error');
                }
            } catch (error) {
                console.error('Error generating sitemap:', error);
                showNotification('خطا در تولید Sitemap', 'error');
            }
        }

        // Generate robots.txt
        async function generateRobots() {
            try {
                showNotification('در حال تولید robots.txt...', 'info');
                const response = await fetch('/api/seo/robots/generate', {
                    method: 'POST'
                });
                const result = await response.json();
                
                if (response.ok) {
                    showNotification('robots.txt با موفقیت تولید شد', 'success');
                    refreshData();
                } else {
                    showNotification('خطا در تولید robots.txt', 'error');
                }
            } catch (error) {
                console.error('Error generating robots.txt:', error);
                showNotification('خطا در تولید robots.txt', 'error');
            }
        }

        // Refresh data
        function refreshData() {
            loadDashboardData();
        }

        // Show notification
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 
                type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
            } text-white`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardData();
        });
    </script>
</body>
</html>
