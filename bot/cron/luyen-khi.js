import fetch from "node-fetch";
import cheerio from "cheerio";

const VU_KHI = 1;
const KHAI_GIAP = 2;

const PHAM_CAP = 0;
const HOANG_CAP = 1;
const HUYEN_CAP = 2;
const DIA_CAP = 3;
const THIEN_CAP = 4;
const LINH_CAP = 5;
const THANH_CAP = 6;
const THAN_CAP = 7;
const CHI_TON = 8;
const TIEN_KHI = 9;
const HONG_MONG_HP = 10;
const HONG_MONG_TP = 11;
const HONG_MONG_THP = 12;
const HONG_MONG_CP = 13;

const NTLNHP_ID = 50;
const NTLNTP_ID = 51;
const NTLNTHP_ID = 52;
const NTLNCP_ID = 53;
const LUYEN_KHI_SU = [
    {
        id: 246261,
        cookie: 'USER=%2FXPxkg1yaSLJ%3AFcGE1xXdWniG1S5ew7Qq821bEYAIMJll7qTmo7NM6Q59',
        cap: HONG_MONG_TP,
        phutro: NTLNCP_ID,
        type: KHAI_GIAP,
    },
    {
        id: 228826,
        cookie: 'USER=OW9pTd1E39pc%3A%2BLDu1rp5OTKigC97woOSHksglehIqmgDQtwadoBDkh80',
        cap: HONG_MONG_TP,
        phutro: NTLNCP_ID,
        type: VU_KHI,
    }
]

const KHAI_GIAPS = [
    {
        id: 0,
        name: 'Thuẫn',
    }, {
        id: 1,
        name: 'Khôi',
    }, {
        id: 2,
        name: 'Giáp',
    }, {
        id: 3,
        name: 'Thủ Sáo',
    }, {
        id: 4,
        name: 'Yêu Đái',
    }, {
        id: 5,
        name: 'Trang',
    }, {
        id: 6,
        name: 'Ngoa',
    }
];
const VU_KHIS = [
    {
        id: 1,
        name: 'Đao',
    }, {
        id: 2,
        name: 'Thương',
    }, {
        id: 3,
        name: 'Kiếm',
    }, {
        id: 4,
        name: 'Kích',
    }, {
        id: 5,
        name: 'Chùy',
    }, {
        id: 6,
        name: 'Phủ',
    }, {
        id: 7,
        name: 'Cầm',
    }, {
        id: 8,
        name: 'Châm',
    }, {
        id: 9,
        name: 'Tiên',
    }, {
        id: 10,
        name: 'Nhận',
    }
];

function random(min, max) {
    return Math.floor(Math.random() * (max - min)) + min;
}

function delay(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

const fetchLuyenKhi = async (accountId, cookie, cap, txtName, phutro, LoaiPhapKhi) => {
    const response = await fetch("https://tutien.net/account/tu_luyen/luyen_khi_that/", {
        "headers": {
            "accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
            "accept-language": "en-US,en;q=0.9,vi;q=0.8",
            "cache-control": "max-age=0",
            "sec-ch-ua": "\" Not A;Brand\";v=\"99\", \"Chromium\";v=\"96\", \"Google Chrome\";v=\"96\"",
            "sec-ch-ua-mobile": "?0",
            "sec-ch-ua-platform": "\"macOS\"",
            "sec-fetch-dest": "document",
            "sec-fetch-mode": "navigate",
            "sec-fetch-site": "none",
            "sec-fetch-user": "?1",
            "upgrade-insecure-requests": "1",
            "cookie": cookie
        },
        "referrerPolicy": "strict-origin-when-cross-origin",
        "body": null,
        "method": "GET"
    });

    const body = await response.text();
    const $ = cheerio.load(body);

    const availableDanDuoc = $('div.progress-bar-striped').first().text();
    if (!availableDanDuoc) {
        await chuyenBac('AUTO', accountId, 30000);
        await luyenKhi(accountId, cookie, cap, txtName, phutro, LoaiPhapKhi);
    } else {
        console.log(accountId + availableDanDuoc);
    }
}

setInterval(async () => {
    console.log("=================" + (new Date()).toString() + "==================");
    const txtName = random(0, 10000);
    for (let i = 0; i < LUYEN_KHI_SU.length; i++) {
        const account = LUYEN_KHI_SU[i];
        let LoaiPhapKhi;
        if (account.type == 1) {
            LoaiPhapKhi = VU_KHIS[random(0, VU_KHIS.length)].id;
        } else {
            LoaiPhapKhi = KHAI_GIAPS[random(0, KHAI_GIAPS.length)].id;
        }
        try {
            await fetchLuyenKhi(account.id, account.cookie, account.cap, txtName, account.phutro, LoaiPhapKhi);
            await delay(2000);
        } catch (error) {
            console.log(error);
        }
    }
},  1000); // every 2 minutes


const luyenKhi = async (accountId, cookie, cap, txtName, phuTro_id, LoaiPhapKhi) => {
    const body = 'btnLuyenKhi=1&radLuyenKhi=' + cap + '&txtName=Niết Bàn ' + txtName + '&radDanLo=32226&radPhuTro=' + phuTro_id + '&radLoaiPhapKhi=' + LoaiPhapKhi;
    const response = await fetch("https://tutien.net/account/tu_luyen/luyen_khi_that/", {
        "headers": {
            "accept": "*/*",
            "accept-language": "en-US,en;q=0.9,vi;q=0.8",
            "content-type": "application/x-www-form-urlencoded; charset=UTF-8",
            "sec-ch-ua": "\" Not A;Brand\";v=\"99\", \"Chromium\";v=\"96\", \"Google Chrome\";v=\"96\"",
            "sec-ch-ua-mobile": "?0",
            "sec-ch-ua-platform": "\"macOS\"",
            "sec-fetch-dest": "empty",
            "sec-fetch-mode": "cors",
            "sec-fetch-site": "same-origin",
            "x-requested-with": "XMLHttpRequest",
            "cookie": cookie,
            "Referer": "https://tutien.net/account/tu_luyen/luyen_khi_that/",
            "Referrer-Policy": "strict-origin-when-cross-origin"
        },
        "body": body,
        "method": "POST"
    });
    const res = await response.text();
    console.log(accountId, res);
}

const chuyenBac = async (fromId, toId, amount) => {
    const referrer = `https://tutien.net/member/${toId}`;
    const body = `btntangNganLuong=1&txtMoney=${amount}&member=${toId}`;
    const response = await fetch("https://tutien.net/index.php", {
        "headers": {
            "accept": "*/*",
            "accept-language": "en-US,en;q=0.9,vi;q=0.8",
            "content-type": "application/x-www-form-urlencoded; charset=UTF-8",
            "sec-ch-ua": "\"Google Chrome\";v=\"89\", \"Chromium\";v=\"89\", \";Not A Brand\";v=\"99\"",
            "sec-ch-ua-mobile": "?0",
            "sec-fetch-dest": "empty",
            "sec-fetch-mode": "cors",
            "sec-fetch-site": "same-origin",
            "x-requested-with": "XMLHttpRequest",
            "cookie": "USER=Zf2CHIgzwART%3Au542MCcAWKHgrQ6bNfZRRg9z4gmOl6noVHa3Q5WGRyUp",
        },
        referrer,
        "referrerPolicy": "strict-origin-when-cross-origin",
        body,
        "method": "POST",
        "mode": "cors"
    });

    const res = await response.text();
    if (res == '1') {
        console.log(`[success] ${amount} bạc: ${fromId} => ${toId}`)
    } else {
        console.log(`[false] ${amount} bạc: ${fromId} => ${toId}`);
    }
    return res === '1';
}