<template>
    <div class="dashboard-container">
        <h1>Settings Dashboard</h1>

        <!-- Row for statistics -->
        <div class="dashboard-row">
            <div class="dashboard-card">
                <h3>Product Count</h3>
                <p>{{ productCount }}</p>
            </div>
            <div class="dashboard-card">
                <h3>Customer Count</h3>
                <p>{{ customerCount }}</p>
            </div>
        </div>

        <!-- Row for charts -->
        <div class="dashboard-row">
            <!-- Pie Chart for Product and Customer Count -->
            <div class="dashboard-chart">
                <h3>Product vs Customer</h3>
                <PieChart :data="chartData" />
            </div>

            <!-- Bar Chart Example -->
            <div class="dashboard-chart">
                <h3>Example Bar Chart</h3>
                <BarChart :data="barChartData" />
            </div>
        </div>
    </div>
</template>

<script>
// Import necessary Chart.js and vue-chartjs modules
import { Pie, Bar } from "vue-chartjs";
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    ArcElement,
    CategoryScale,
    BarElement,
    LinearScale,
} from "chart.js";

// Register necessary Chart.js components
ChartJS.register(
    Title,
    Tooltip,
    Legend,
    ArcElement,
    CategoryScale,
    BarElement,
    LinearScale
);

export default {
    components: {
        PieChart: Pie, // Register Pie chart component
        BarChart: Bar, // Register Bar chart component (you can add more charts if needed)
    },
    data() {
        return {
            // Statistics Data
            productCount: 0,
            customerCount: 0,

            // Pie Chart Data
            chartData: {
                labels: ["Products", "Customers"],
                datasets: [
                    {
                        data: [0, 0],
                        backgroundColor: ["#FF6384", "#36A2EB"],
                        hoverBackgroundColor: ["#FF6384", "#36A2EB"],
                    },
                ],
            },

            // Bar Chart Data Example (replace with real data)
            barChartData: {
                labels: ["Q1", "Q2", "Q3", "Q4"],
                datasets: [
                    {
                        label: "Sales",
                        data: [10, 20, 30, 40], // Replace with your data
                        backgroundColor: "#42A5F5",
                        borderColor: "#1E88E5",
                        borderWidth: 1,
                    },
                ],
            },
        };
    },
    mounted() {
        // Fetch data from the backend
        axios
            .get("/settings")
            .then((response) => {
                this.productCount = response.data.productCount;
                this.customerCount = response.data.customerCount;

                // Update Pie chart data
                this.chartData.datasets[0].data = [
                    this.productCount,
                    this.customerCount,
                ];
            })
            .catch((error) => {
                console.error(
                    "There was an error fetching the settings data:",
                    error
                );
            });
    },
};
</script>

<style scoped>
.dashboard-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.dashboard-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 30px;
}

.dashboard-card {
    width: 30%;
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.dashboard-card h3 {
    margin-bottom: 10px;
}

.dashboard-chart {
    width: 48%;
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.dashboard-chart h3 {
    margin-bottom: 20px;
}
</style>
