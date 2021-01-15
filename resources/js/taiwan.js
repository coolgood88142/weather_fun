
  
  let app = new Vue({
    el: "#app",
    data: {
      filter: "",
      place_data: {
        taipei_city:{
          place: "臺北市",
          weather: "Rainy"
        },
        
        new_taipei_city:{
          place: "新北市",
          weather: "Rainy"
        },
        
        taichung_city:{
           place: "台中市",
           weather: "Rainy"
        },
        
        tainan_city:{
          place: "臺南市",
          weather: "Rainy"
        },
        
        kaohsiung_city:{
          place: "高雄市",
          weather: "Rainy"
        },
        
        keelung_city:{
          place: "基隆市",
          weather: "Rainy"
        },
        
        taoyuan_country:{
          place: "桃園市",
          weather: "Rainy"
        },
        
        hsinchu_city:{
          place: "新竹市",
          weather: "Rainy"
        },
        
        hsinchu_country:{
          place: "新竹縣",
          weather: "Rainy"
        },
        
        miaoli_country:{
          place: "苗栗縣",
          weather: "Rainy"
        },
        
        changhua_country:{
          place: "彰化縣",
          weather: "Rainy"
        },
        
        nantou_country:{
          place: "南投縣",
          weather: "Rainy"
        },
        
        yunlin_country:{
          place: "雲林縣",
          weather: "Cloudy"
        },
        
        chiayi_city:{
           place: "嘉義市",
           weather: "Rainy"
        },
        
        chiayi_country:{
          place: "嘉義縣",
          weather: "Cloudy"
        },
        
        pingtung_country:{
          place: "屏東縣",
          weather: "Cloudy"
        },
        
        yilan_country:{
          place: "宜蘭縣",
          weather: "Cloudy"
        },
        
        hualien_country:{
          place: "花蓮縣",
          weather: "Sunny"
        },
        
        taitung_country:{
          place: "台東縣",
          weather: "Sunny"
        },
        
        penghu_country:{
          place: "澎湖縣",
          weather: "Cloudy"
        },
        
        kinmen_country:{
          place: "金門縣",
          weather: "Sunny"
        },
        
        lienchiang_country:{
           place: "連江縣",
           weather: "Rainy"
          },
        },
      h1: '縣市中文',
      h2: '縣市英文'
    }
  });
  
  $("path").click(function(e){
    var tagname = $(this).attr("data-name");
    app.h1 = app.place_data[tagname].place;
    app.h2 = tagname;
    // console.log(app.place_data[tagname].place);
  
  });
  