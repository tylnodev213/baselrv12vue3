<template>
  <div class="home-page">
    <div class="page-header mb-4">
      <h6 class="font-weight-bolder mb-0">Dashboard Overview</h6>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
      <div class="col-md-4">
        <div class="soft-card stat-card">
          <div class="stat-content">
            <div class="stat-info">
              <p class="stat-label">Tổng Users</p>
              <h5 class="stat-value">
                {{ stats.totalUsers || 0 }}
              </h5>
            </div>
            <div class="stat-icon bg-gradient-primary">👔</div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="soft-card stat-card">
          <div class="stat-content">
            <div class="stat-info">
              <p class="stat-label">Tổng Teams</p>
              <h5 class="stat-value">
                {{ stats.totalTeams || 0 }}
              </h5>
            </div>
            <div class="stat-icon bg-gradient-dark">🏢</div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="soft-card stat-card">
          <div class="stat-content">
            <div class="stat-info">
              <p class="stat-label">Tổng Sản Phẩm</p>
              <h5 class="stat-value">
                {{ stats.totalProducts || 0 }}
              </h5>
            </div>
            <div class="stat-icon bg-gradient-info">📦</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row 1: Line Chart -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="soft-card">
          <h6 class="mb-3">Lượng User thêm mới trong tháng</h6>
          <div class="chart-container">
            <Line v-if="loaded" :data="lineChartData" :options="lineChartOptions" />
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row 2: Comparison Bar Charts -->
    <div class="row">
      <div class="col-md-6">
        <div class="soft-card">
          <h6 class="mb-3">So sánh User với năm trước</h6>
          <div class="chart-container-sm">
            <Bar v-if="loaded" :data="userComparisonData" :options="barOptions" />
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="soft-card">
          <h6 class="mb-3">So sánh Sản phẩm với năm trước</h6>
          <div class="chart-container-sm">
            <Bar v-if="loaded" :data="productComparisonData" :options="barOptions" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import { dashboardService } from '@/services/dashboardService';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  LinearScale,
  PointElement,
  CategoryScale,
  BarElement,
  Filler,
} from 'chart.js';
import { Line, Bar } from 'vue-chartjs';

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  LineElement,
  LinearScale,
  PointElement,
  CategoryScale,
  BarElement,
  Filler
);

const stats = ref({});
const loaded = ref(false);

// Chart Data Structures
const lineChartData = reactive({
  labels: [],
  datasets: [
    {
      label: 'Users',
      backgroundColor: '#cb0c9f',
      borderColor: '#cb0c9f',
      data: [],
      tension: 0.4,
      fill: true,
    },
  ],
});

const userComparisonData = reactive({
  labels: ['Năm trước', 'Năm nay'],
  datasets: [
    {
      label: 'Người dùng',
      backgroundColor: ['#67748e', '#cb0c9f'],
      data: [],
    },
  ],
});

const productComparisonData = reactive({
  labels: ['Năm trước', 'Năm nay'],
  datasets: [
    {
      label: 'Sản phẩm',
      backgroundColor: ['#67748e', '#21d4fd'],
      data: [],
    },
  ],
});

const lineChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
  },
  scales: {
    y: { grid: { borderDash: [5, 5] } },
    x: { grid: { display: false } },
  },
};

const barOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
  },
  scales: {
    y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
    x: { grid: { display: false } },
  },
};

const fetchStats = async () => {
  try {
    const response = await dashboardService.getStats();
    if (response.data.success) {
      const data = response.data.data;
      stats.value = data;

      // Update charts
      lineChartData.labels = data.userTrends.map((_, i) => `${i + 1}`);
      lineChartData.datasets[0].data = data.userTrends;
      userComparisonData.datasets[0].data = [
        data.yearlyComparison.users.lastYear,
        data.yearlyComparison.users.currentYear,
      ];
      productComparisonData.datasets[0].data = [
        data.yearlyComparison.products.lastYear,
        data.yearlyComparison.products.currentYear,
      ];

      loaded.value = true;
    }
  } catch (error) {
    console.error('Error fetching dashboard stats:', error);
  }
};

onMounted(fetchStats);
</script>

<style scoped>
.page-header {
  display: flex;
  align-items: center;
}

.font-weight-bolder {
  font-weight: 700;
}

.stat-card {
  padding: 1rem;
}

.stat-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.stat-label {
  font-size: 0.75rem;
  font-weight: 700;
  color: var(--text-secondary);
  text-transform: uppercase;
  margin-bottom: 2px;
}

.stat-value {
  margin-bottom: 0;
  font-weight: 700;
  color: var(--text-main);
}

.stat-icon {
  width: 45px;
  height: 45px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 1.2rem;
  box-shadow:
    0 4px 6px -1px rgba(0, 0, 0, 0.1),
    0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.chart-container {
  height: 300px;
  position: relative;
}

.chart-container-sm {
  height: 200px;
  position: relative;
}

.row {
  display: flex;
  flex-wrap: wrap;
  margin-right: -15px;
  margin-left: -15px;
}

.col-md-4,
.col-md-6,
.col-12 {
  position: relative;
  width: 100%;
  padding-right: 15px;
  padding-left: 15px;
}

@media (min-width: 768px) {
  .col-md-4 {
    flex: 0 0 33.333333%;
    max-width: 33.333333%;
  }
  .col-md-6 {
    flex: 0 0 50%;
    max-width: 50%;
  }
}

.col-12 {
  flex: 0 0 100%;
  max-width: 100%;
}

.mb-3 {
  margin-bottom: 1rem;
}
.mb-4 {
  margin-bottom: 1.5rem;
}
</style>
