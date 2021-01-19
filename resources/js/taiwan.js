import "bootstrap/dist/css/bootstrap.css"
import taiwan from "./components/weather/taiwan.vue"

let app = new Vue({
	el: "#app",
	data: {
    cityCh: "縣市中文",
    climate: "顯示氣候",
    temperature: "顯示溫度",
    rain: "0",
    rainPH: '酸雨PH值',
    uv: '0',
    earthquake: '地震',
    ozone:'臭氧',
    wdir: '風向',
    wdsd: '風速',
    humd: '相對溼度',
    pres: '氣壓'
  },
  components: {
    'taiwan': taiwan,
  },
	methods: {
		saveCityData(weatherArray) {
      this.cityCh = weatherArray[0]
      this.temperature = weatherArray[1]
      this.rain = weatherArray[2]
      this.climate = weatherArray[3]
      this.rainPH = weatherArray[4]
      this.uv = weatherArray[5]
      this.earthquake = weatherArray[6]
      this.ozone = weatherArray[7]
      this.wdir = weatherArray[8]
      this.wdsd = weatherArray[9]
      this.humd = weatherArray[10]
      this.pres = weatherArray[11]
		},
	},
})
