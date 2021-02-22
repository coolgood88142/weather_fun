$(document).ready(function() {
    axios.post(url, {
        symbolId: this.symbolId,
        begintime: this.begintime,
        endtime: this.endtime
    }).then((response) => {
        if (response.data.message != '') {
            let chart = response.data.chart
            let dealts = response.data.dealts
            this.updateChart(chart, dealts)
        }else{
            this.showMessage(true, response.data.symbolIdMessage)
        }

        
    }).catch((error) => {
        if (error.response) {
            console.log(error.response.data)
            console.log(error.response.status)
            console.log(error.response.headers)
        } else {
            console.log("Error", error.message)
        }
    })
})