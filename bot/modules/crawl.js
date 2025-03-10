const rp = require("request-promise");
const cheerio = require("cheerio");
const fs = require("fs");
 
const URL = `https://fanqienovel.com/page/7070729199706704927`;
 
const options = {
  uri: URL,
  transform: function (body) {
    //Khi lấy dữ liệu từ trang thành công nó sẽ tự động parse DOM
    return cheerio.load(body);
  },
};
 
(async function crawler() {
  try {
    // Lấy dữ liệu từ trang crawl đã được parseDOM
    var $ = await rp(options);
  } catch (error) {
    return error;
  }
 
  /* Lấy tên và miêu tả của tutorial*/
  const title = $("h3").text().trim();
 
  /* Phân tích các table và sau đó lấy các posts.
     Mỗi table là một chương 
  */
  const tableContent = $(".chapter");
  let data = [];
  for (let i = 0; i < tableContent.length; i++) {
    let chaper = $(tableContent[i]);
    // Tên của chương đó.
    let chaperTitle = chaper.find(".chapter-item").text().trim();
   
     
    //Tìm bài viết ở mỗi chương
    let chaperData = []
    const chaperLink = chaper.find(".chapter-item").find("a");
    for (let j = 0; j < chaperLink.length; j++) {
      const post = $(chaperLink[j]);
      const postLink = post.attr("href");
      const postTitle = post.text().trim();
      chaperData.push({
        postTitle,
        postLink,
      });
    }
    data.push({
    //   chaperTitle,
      chaperData,
    });
    
  }
  // Lưu dữ liệu về máy
  fs.writeFileSync('data.json', JSON.stringify(data))
})();
// dán postLink vào url 

