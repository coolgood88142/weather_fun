import "bootstrap/dist/css/bootstrap.css"
import taiwan from "./components/weather/taiwan.vue"

let app = new Vue({
	el: "#app",
	data: {
    cityCh: "縣市中文",
    cityEn: "縣市英文",
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
      this.cityEn = weatherArray[1]
      this.temperature = weatherArray[2]
      this.rain = weatherArray[3]
      this.climate = weatherArray[4]
      this.rainPH = weatherArray[5]
      this.uv = weatherArray[6]
      this.earthquake = weatherArray[7]
      this.ozone = weatherArray[8]
      this.wdir = weatherArray[9]
      this.wdsd = weatherArray[10]
      this.humd = weatherArray[11]
      this.pres = weatherArray[12]
		},
	},
})
