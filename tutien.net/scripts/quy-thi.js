const autoNames = [
    'Trích Tinh Thảo', 
    'Anh Tâm Thảo',
    'Hóa Nguyên Thảo',
    'Trúc Cơ Đan', 
    'Hòa Thị Bích', 
    'Cố Thần Đan',
    'Dung Thần Đan',
    'Hắc Diệu Thạch',
    'Hộ Linh Trận',
    'La Bàn',
    'Ngọc Giản Truyền Công',
    'Nội Đan C1',
    'Nội Đan C2',
    'Nội Đan C3',
    'Nội Đan C4',
    'Nội Đan C5',
    'Nội Đan C6',
    'Nội Đan C7',
    'Nội Đan C8',
    'Nội Đan C9',
    'Quy Giáp',
    'Tinh Linh CP',
    'Tinh Linh THP',
    'Tinh Linh TP',
    'Tinh Linh HP',
    'Tán Lôi Trận',
    'Túi Trữ Vật'
];

const autoPrices = [
    100,   // Trích Tinh Thảo
    200,   // Anh Tâm Thảo,
    200,   // Hóa Nguyên Thảo,
    30,    // Trúc Cơ Đan
    30000, // Hòa Thị Bích
    7000,  // Cố Thần Đan
    8000,  // Dung Thần Đan
    30000, // Hắc Diệu Thạch
    10000, // Hộ Linh Trận
    8000,  // La Bàn
    10000, // Ngọc Giản Truyền Công
    10000, // Nội Đan C1
    10000, // Nội Đan C2
    10000, // Nội Đan C3
    10000, // Nội Đan C4
    10000, // Nội Đan C5
    10000, // Nội Đan C6
    11000, // Nội Đan C7
    12000, // Nội Đan C8
    15000, // Nội Đan C9
    8000 , // Quy Giáp,
    30000, // Tinh Linh CP
    20000, // Tinh Linh THP
    20000, // Tinh Linh TP
    10000, // Tinh Linh HP
    40000, // Tán Lôi Trận
    2000   // Túi Trữ Vật
];

let auto = false;

// Override default function
function hacThiLoad2(media, number, page, abc) {
    console.log('Overrided');
}
function hacThi(hacthi_id) {
    console.log('Overrided');
}

// New function
function hacThi2(hacthi_id) {
    var data = 'btnTuLuyenHacThi=1&hacthi_id=' + hacthi_id;
    $.ajax({
        url: site + '/account/tu_luyen/quy_thi/',
        type: "POST",
        data: data,
        cache: false,
        success: function (html) {
            if (html == 1) {
                console.log("Bạn đã mua thành công!");
                $("#hacthi_" + hacthi_id).remove();
                $("#ban_" + hacthi_id).remove();
            } else {
            console.log('Có lỗi hoặc có người đã mua vật phẩm này');
            }
        }
    });
}

function hacThiLoad3(media, number, page, abc) {
    var selItems = $('#selItemsmoirao').val();
    var selPrice = $('#selPricemoirao').val();
    if (!page)
        page = $('#page').val();
    var data = 'btnHacThiLoad2=1&selItems=' + selItems + '&selPrice=' + selPrice + '&page=' + page;
    $.ajax({
        url: site + '/account/tu_luyen/quy_thi/',
        type: "POST",
        data: data,
        cache: false,
        success: function (html) {
            auto = true;
            autoBuy(html);
            // if (html) {
            //     $('#hacthiload2').html(html);
            // }
        },
        complete: function() {
            // setTimeout(function() {
            //     autoBuy();
            // }, 300);
        }
    });
    return false;
}

function autoBuy(html) {
    // console.log('======== AUTO BUY ========');
    const wrapper = $('<div>' + html + '</div>');
    const items = $('[id^=hacthi_]', wrapper);
    // console.log(items);
    for (let i = 0; i < items.length; i++) {
        const item = items[i];
        const name = $('div:nth-child(1) > span', item).text();
        const index = autoNames.indexOf(name);
        if (index !== -1) {
            const autoPrice = autoPrices[index];
            const price = $('div:nth-child(4) > p', item).text().replace(',', '').replace('(', '').replace(' bạc/1 cái)', '');
            if (parseInt(price) <= autoPrice) {
                const id = items.getAttribute('id').replace('hacthi_', '');
                hacThi2(id);
            }
        }
    }
}

(function () {
    // Clear all interval
    for(let i = 0; i < 10000; i++) {
        window.clearInterval(i);
    }
    
    setTimeout(function() {
        window.location.reload(true);
    }, 1800000);
    
    setInterval(hacThiLoad3, 8000);
})();
