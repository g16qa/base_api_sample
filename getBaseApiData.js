try{
  var request = new XMLHttpRequest();
  var requestUrl = "https://g16qa.vivian.jp/ksr_shukushuku/baseWrappingApi/request.php";
  request.open('POST', requestUrl, true);
  request.send();
  request.addEventListener('load', function () {
    if(this.response != '' || this.response != undefined){
      var itemsList = JSON.parse(this.response || "null" || "");
        for (var itemid in itemsList) {
          console.log("商品ID" + itemid + "/在庫数" + itemsList[itemid]);
        }
    }
  },false);
}catch(e){
  console.log(e.message);
}
