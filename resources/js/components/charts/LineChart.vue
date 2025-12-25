<template>
  <Line :data="chartData" :options="chartOptions" />
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Line } from 'vue-chartjs';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  Filler
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale, Filler);

interface Props {
  labels: string[];
  datasets: {
    label: string;
    data: number[];
    borderColor?: string;
    backgroundColor?: string;
    fill?: boolean;
    tension?: number;
  }[];
  showLegend?: boolean;
  height?: string;
}

const props = withDefaults(defineProps<Props>(), {
  showLegend: true,
  height: '300px'
});

const defaultColors = [
  { border: 'rgb(59, 130, 246)', bg: 'rgba(59, 130, 246, 0.1)' },
  { border: 'rgb(16, 185, 129)', bg: 'rgba(16, 185, 129, 0.1)' },
  { border: 'rgb(239, 68, 68)', bg: 'rgba(239, 68, 68, 0.1)' },
  { border: 'rgb(139, 92, 246)', bg: 'rgba(139, 92, 246, 0.1)' },
];

const chartData = computed(() => ({
  labels: props.labels,
  datasets: props.datasets.map((ds, i) => ({
    ...ds,
    borderColor: ds.borderColor || defaultColors[i % defaultColors.length].border,
    backgroundColor: ds.backgroundColor || defaultColors[i % defaultColors.length].bg,
    fill: ds.fill ?? true,
    tension: ds.tension ?? 0.4,
    pointRadius: 4,
    pointHoverRadius: 6,
    borderWidth: 2,
  }))
}));

const chartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  interaction: {
    intersect: false,
    mode: 'index' as const
  },
  plugins: {
    legend: {
      display: props.showLegend && props.datasets.length > 1,
      position: 'top' as const,
      labels: {
        font: { family: 'Inter, sans-serif', size: 12 },
        padding: 16,
        usePointStyle: true
      }
    },
    tooltip: {
      backgroundColor: 'rgba(17, 24, 39, 0.9)',
      titleFont: { family: 'Inter, sans-serif', size: 14 },
      bodyFont: { family: 'Inter, sans-serif', size: 13 },
      padding: 12,
      cornerRadius: 8,
      callbacks: {
        label: (ctx: any) => `${ctx.dataset.label}: ${new Intl.NumberFormat('tr-TR').format(ctx.parsed.y)}`
      }
    }
  },
  scales: {
    x: {
      grid: { display: false },
      ticks: { font: { family: 'Inter, sans-serif', size: 11 } }
    },
    y: {
      grid: { color: 'rgba(0, 0, 0, 0.05)' },
      ticks: {
        font: { family: 'Inter, sans-serif', size: 11 },
        callback: (value: string | number) => new Intl.NumberFormat('tr-TR', { notation: 'compact' }).format(Number(value))
      }
    }
  }
}));
</script>
