<template>
  <div>
    <canvas ref="chart"></canvas>
  </div>
</template>

<script>
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  LinearScale,
  PointElement,
  CategoryScale,
  Filler
} from 'chart.js'

// Only import plugins if you need them
// Remove these if you don't need zoom/annotation functionality
import zoomPlugin from 'chartjs-plugin-zoom'
import annotationPlugin from 'chartjs-plugin-annotation'

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  LineElement,
  LinearScale,
  PointElement,
  CategoryScale,
  Filler,
  zoomPlugin,
  annotationPlugin
)

export default {
  name: 'LineChart',
  extends: Line,
  props: {
    chartData: {
      type: Object,
      required: true
    },
    chartOptions: {
      type: Object,
      default: () => ({})
    }
  },
  mounted() {
    this.renderChart(this.chartData, this.chartOptions)
  },
  watch: {
    chartData: {
      handler(newData) {
        if (this.$data._chart) {
          this.$data._chart.data = newData
          this.$data._chart.update()
        }
      },
      deep: true
    },
    chartOptions: {
      handler(newOptions) {
        if (this.$data._chart) {
          this.$data._chart.options = newOptions
          this.$data._chart.update()
        }
      },
      deep: true
    }
  }
}
</script>