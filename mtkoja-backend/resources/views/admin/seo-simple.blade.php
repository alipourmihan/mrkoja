<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEO ساده - متکوجا</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center">
                        <h1 class="text-2xl font-bold text-gray-900">SEO ساده</h1>
                    </div>
                    <div class="flex items-center space-x-4 space-x-reverse">
                        <button onclick="goBack()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-arrow-right mr-2"></i>
                            بازگشت
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">مدیریت SEO</h2>
                
                <!-- Tabs -->
                <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-8 space-x-reverse">
                        <button onclick="switchTab('overview')" id="tab-overview" class="tab-button py-2 px-1 border-b-2 font-medium text-sm border-blue-500 text-blue-600">
                            <i class="fas fa-chart-pie mr-2"></i>
                            نمای کلی
                        </button>
                        <button onclick="switchTab('pages')" id="tab-pages" class="tab-button py-2 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <i class="fas fa-file-alt mr-2"></i>
                            صفحات
                        </button>
                        <button onclick="switchTab('reports')" id="tab-reports" class="tab-button py-2 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <i class="fas fa-chart-bar mr-2"></i>
                            گزارش‌ها
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div id="tab-content">
                    <!-- Overview Tab -->
                    <div id="content-overview" class="tab-content">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div class="bg-blue-50 p-6 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-building text-3xl text-blue-500 mr-4"></i>
                                    <div>
                                        <p class="text-sm font-medium text-blue-600">کل کسب‌وکارها</p>
                                        <p class="text-2xl font-bold text-blue-900" id="total-businesses">0</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-green-50 p-6 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-tags text-3xl text-green-500 mr-4"></i>
                                    <div>
                                        <p class="text-sm font-medium text-green-600">کل دسته‌بندی‌ها</p>
                                        <p class="text-2xl font-bold text-green-900" id="total-categories">0</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-purple-50 p-6 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-chart-line text-3xl text-purple-500 mr-4"></i>
                                    <div>
                                        <p class="text-sm font-medium text-purple-600">درصد تکمیل SEO</p>
                                        <p class="text-2xl font-bold text-purple-900" id="seo-completion">0%</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">وضعیت فنی</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex items-center justify-between p-4 bg-white rounded-lg">
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

                                <div class="flex items-center justify-between p-4 bg-white rounded-lg">
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
                    </div>

                    <!-- Pages Tab -->
                    <div id="content-pages" class="tab-content hidden">
                        <div class="mb-6">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-semibold text-gray-900">صفحات SEO</h3>
                                <div class="flex space-x-2 space-x-reverse">
                                    <button onclick="generateBulkSeo()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                                        <i class="fas fa-magic mr-2"></i>
                                        تولید انبوه SEO
                                    </button>
                                    <button onclick="refreshPages()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                                        <i class="fas fa-sync-alt mr-2"></i>
                                        بروزرسانی
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border rounded-lg overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                <input type="checkbox" id="select-all" class="rounded">
                                            </th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نام</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نوع</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">وضعیت SEO</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عملیات</th>
                                        </tr>
                                    </thead>
                                    <tbody id="pages-table-body" class="bg-white divide-y divide-gray-200">
                                        <tr>
                                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                                در حال بارگذاری...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Reports Tab -->
                    <div id="content-reports" class="tab-content hidden">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">گزارش‌های SEO</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-white p-6 rounded-lg shadow">
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">گزارش کلی</h4>
                                <div id="overview-report" class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">کل صفحات:</span>
                                        <span class="font-medium" id="total-pages">0</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">صفحات با SEO کامل:</span>
                                        <span class="font-medium" id="complete-pages">0</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">درصد تکمیل:</span>
                                        <span class="font-medium" id="completion-percentage">0%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white p-6 rounded-lg shadow">
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">آخرین فعالیت‌ها</h4>
                                <div id="recent-activities" class="space-y-2">
                                    <div class="text-gray-500 text-sm">در حال بارگذاری...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        let currentTab = 'overview';

        // Switch tab
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active class from all tab buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('border-blue-500', 'text-blue-600');
                button.classList.add('border-transparent', 'text-gray-500');
            });

            // Show selected tab content
            document.getElementById('content-' + tabName).classList.remove('hidden');

            // Add active class to selected tab button
            const activeButton = document.getElementById('tab-' + tabName);
            activeButton.classList.remove('border-transparent', 'text-gray-500');
            activeButton.classList.add('border-blue-500', 'text-blue-600');

            currentTab = tabName;

            // Load data for the tab
            if (tabName === 'overview') {
                loadOverviewData();
            } else if (tabName === 'pages') {
                loadPagesData();
            } else if (tabName === 'reports') {
                loadReportsData();
            }
        }

        // Load overview data
        async function loadOverviewData() {
            try {
                const response = await fetch('/api/admin/seo/dashboard');
                const data = await response.json();
                
                document.getElementById('total-businesses').textContent = data.overview.total_businesses;
                document.getElementById('total-categories').textContent = data.overview.total_categories;
                document.getElementById('seo-completion').textContent = 
                    Math.round((data.overview.businesses_seo_percentage + data.overview.categories_seo_percentage) / 2) + '%';

                // Update technical status
                updateTechnicalStatus(data.technical);

            } catch (error) {
                console.error('Error loading overview data:', error);
                showNotification('خطا در بارگذاری داده‌ها', 'error');
            }
        }

        // Load pages data
        async function loadPagesData() {
            try {
                const response = await fetch('/api/admin/seo/pages');
                const data = await response.json();
                
                const tbody = document.getElementById('pages-table-body');
                tbody.innerHTML = '';

                if (data.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">هیچ صفحه‌ای یافت نشد</td></tr>';
                    return;
                }

                data.data.forEach(page => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" class="page-checkbox rounded" value="${page.id}">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            ${page.name}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${page.type === 'business' ? 'کسب‌وکار' : page.type === 'category' ? 'دسته‌بندی' : 'صفحه SEO'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                                page.seo_status === 'complete' ? 'bg-green-100 text-green-800' :
                                page.seo_status === 'incomplete' ? 'bg-yellow-100 text-yellow-800' :
                                'bg-red-100 text-red-800'
                            }">
                                ${page.seo_status === 'complete' ? 'کامل' : 
                                  page.seo_status === 'incomplete' ? 'ناقص' : 'مفقود'}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="editSeoPage('${page.type}', ${page.id})" class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="validateSeoPage('${page.type}', ${page.id})" class="text-green-600 hover:text-green-900">
                                <i class="fas fa-check-circle"></i>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });

            } catch (error) {
                console.error('Error loading pages data:', error);
                showNotification('خطا در بارگذاری صفحات', 'error');
            }
        }

        // Load reports data
        async function loadReportsData() {
            try {
                const response = await fetch('/api/admin/seo/reports?type=overview');
                const data = await response.json();
                
                document.getElementById('total-pages').textContent = data.total_pages;
                document.getElementById('complete-pages').textContent = data.pages_with_complete_seo;
                document.getElementById('completion-percentage').textContent = data.seo_completion_percentage + '%';

            } catch (error) {
                console.error('Error loading reports data:', error);
                showNotification('خطا در بارگذاری گزارش‌ها', 'error');
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

        // Generate bulk SEO
        async function generateBulkSeo() {
            const checkboxes = document.querySelectorAll('.page-checkbox:checked');
            if (checkboxes.length === 0) {
                showNotification('لطفاً صفحاتی را انتخاب کنید', 'warning');
                return;
            }

            const ids = Array.from(checkboxes).map(cb => cb.value);
            const type = 'businesses'; // You can make this dynamic based on selected pages

            try {
                showNotification('در حال تولید SEO...', 'info');
                const response = await fetch('/api/admin/seo/generate-bulk', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ type, ids })
                });

                if (response.ok) {
                    showNotification('SEO با موفقیت تولید شد', 'success');
                    loadPagesData();
                } else {
                    showNotification('خطا در تولید SEO', 'error');
                }
            } catch (error) {
                console.error('Error generating bulk SEO:', error);
                showNotification('خطا در تولید SEO', 'error');
            }
        }

        // Edit SEO page
        function editSeoPage(type, id) {
            showNotification('قابلیت ویرایش در حال توسعه است', 'info');
        }

        // Validate SEO page
        function validateSeoPage(type, id) {
            showNotification('قابلیت اعتبارسنجی در حال توسعه است', 'info');
        }

        // Refresh pages
        function refreshPages() {
            loadPagesData();
        }

        // Go back
        function goBack() {
            window.history.back();
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

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadOverviewData();
        });
    </script>
</body>
</html>
