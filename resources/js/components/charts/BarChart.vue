<template>
  <Bar :data="chartData" :options="chartOptions" />
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Bar } from 'vue-chartjs';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

interface Props {
  labels: string[];
  datasets: {
    label: string;
    data: number[];
    backgroundColor?: string | string[];
    borderColor?: string | string[];
    borderWidth?: number;
  }[];
  horizontal?: boolean;
  stacked?: boolean;
  showLegend?: boolean;
  height?: string;
}

const props = withDefaults(defineProps<Props>(), {
  horizontal: false,
  stacked: false,
  showLegend: true,
  height: '300px'
});

const defaultColors = [
  'rgba(59, 130, 246, 0.8)',   // blue
  'rgba(16, 185, 129, 0.8)',   // green
  'rgba(239, 68, 68, 0.8)',    // red
  'rgba(139, 92, 246, 0.8)',   // purple
  'rgba(245, 158, 11, 0.8)',   // amber
  'rgba(14, 165, 233, 0.8)',   // sky
];

const chartData = computed(() => ({
  labels: props.labels,
  datasets: props.datasets.map((ds, i) => ({
    ...ds,
    backgroundColor: ds.backgroundColor || defaultColors[i % defaultColors.length],
    borderColor: ds.borderColor || defaultColors[i % defaultColors.length].replace('0.8', '1'),
    borderWidth: ds.borderWidth ?? 1,
    borderRadius: 6,
  }))
}));

const chartOptions = computed(() => ({
  indexAxis: props.horizontal ? 'y' as const : 'x' as const,
  responsive: true,
  maintainAspectRatio: false,
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
        label: (ctx: any) => {
          const value = ctx.parsed.y ?? ctx.parsed.x;
          return `${ctx.dataset.label}: ${new Intl.NumberFormat('tr-TR').format(value)}`;
        }
      }
    }
  },
  scales: {
    x: {
      stacked: props.stacked,
      grid: { display: false },
      ticks: { font: { family: 'Inter, sans-serif', size: 11 } }
    },
    y: {
      stacked: props.stacked,
      grid: { color: 'rgba(0, 0, 0, 0.05)' },
      ticks: {
        font: { family: 'Inter, sans-serif', size: 11 },
        callback: (value: string | number) => new Intl.NumberFormat('tr-TR', { notation: 'compact' }).format(Number(value))
      }
    }
  }
}));
</script>
