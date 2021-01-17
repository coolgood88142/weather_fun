import "bootstrap/dist/css/bootstrap.css"
import taiwan from "./components/weather/taiwan.vue"

let app = new Vue({
	el: "#app",
	data: {
    cityCh: "縣市中文",
    cityEn: "縣市英文",
    climate: "顯示氣候",
    temperature: "顯示溫度",
    rain: "降雨機率",
  },
  components: {
    'taiwan': taiwan,
  },
	methods: {
		saveCityData(cardArray) {
      this.cityCh = cardArray[0]
      this.cityEn = cardArray[1]
      this.temperature = cardArray[2]
      this.rain = cardArray[3]
      this.climate = cardArray[4]
		},
	},
})
