import {
  Chart,
  ArcElement,
  LineElement,
  BarElement,
  PointElement,
  BarController,
  LineController,
  PieController,
  DoughnutController,
  CategoryScale,
  LinearScale,
  LogarithmicScale,
  RadialLinearScale,
  TimeScale,
  TimeSeriesScale,
  Decimation,
  Filler,
  Legend,
  Title,
  Tooltip,
  SubTitle
} from 'chart.js';

Chart.register(
  ArcElement,
  LineElement,
  BarElement,
  PointElement,
  BarController,
  LineController,
  PieController,
  DoughnutController,
  CategoryScale,
  LinearScale,
  LogarithmicScale,
  RadialLinearScale,
  TimeScale,
  TimeSeriesScale,
  Decimation,
  Filler,
  Legend,
  Title,
  Tooltip,
  SubTitle
);

Chart.defaults.responsive = true;
Chart.defaults.maintainAspectRatio = true;
Chart.defaults.plugins.legend.position = 'top';
Chart.defaults.plugins.title.display = false;

export function createChart(canvasId, config) {
  const canvas = document.getElementById(canvasId);
  if (!canvas) {
    console.error(`Canvas element with id '${canvasId}' not found`);
    return null;
  }

  return new Chart(canvas, config);
}

export function destroyChart(chart) {
  if (chart && typeof chart.destroy === 'function') {
    chart.destroy();
  }
}

export { Chart };

export default {
  Chart,
  createChart,
  destroyChart
};
