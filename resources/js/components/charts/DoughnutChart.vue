<template>
  <Doughnut :data="chartData" :options="chartOptions" />
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Doughnut } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, ArcElement);

interface Props {
  labels: string[];
  data: number[];
  colors?: string[];
  showLegend?: boolean;
  cutout?: string;
}

const props = withDefaults(defineProps<Props>(), {
  showLegend: true,
  cutout: '60%'
});

const defaultColors = [
  'rgba(59, 130, 246, 0.9)',   // blue
  'rgba(16, 185, 129, 0.9)',   // green
  'rgba(239, 68, 68, 0.9)',    // red
  'rgba(139, 92, 246, 0.9)',   // purple
  'rgba(245, 158, 11, 0.9)',   // amber
  'rgba(14, 165, 233, 0.9)',   // sky
  'rgba(236, 72, 153, 0.9)',   // pink
  'rgba(34, 197, 94, 0.9)',    // emerald
];

const chartData = computed(() => ({
  labels: props.labels,
  datasets: [{
    data: props.data,
    backgroundColor: props.colors || defaultColors.slice(0, props.data.length),
    borderWidth: 0,
    hoverOffset: 8
  }]
}));

const chartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  cutout: props.cutout,
  plugins: {
    legend: {
      display: props.showLegend,
      position: 'right' as const,
      labels: {
        font: { family: 'Inter, sans-serif', size: 12 },
        padding: 12,
        usePointStyle: true,
        pointStyle: 'circle'
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
          const total = ctx.dataset.data.reduce((a: number, b: number) => a + b, 0);
          const percentage = ((ctx.parsed / total) * 100).toFixed(1);
          return `${ctx.label}: ${new Intl.NumberFormat('tr-TR').format(ctx.parsed)} (%${percentage})`;
        }
      }
    }
  }
}));
</script>
