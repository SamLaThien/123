import fetch from "node-fetch";
import proxyAgentP from 'https-proxy-agent';
import redis from "redis";
const { HttpsProxyAgent } = proxyAgentP;

const COOKIE_INDEX = process.argv[2];
// const COOKIE_INDEX = 0;
const PROXY_URLS = [
    //'http://doanhpv:Z2RHu8f8VUPy@103.159.51.250:3128',
    //'http://doanhpv:cHuZZ87wg5dp@103.159.51.50:3128',
    //'http://doanhpv:XW8dkKy5Ukqw@45.117.83.77:3128',
    //'http://doanhpv:A9gTrpz8WcfE@45.117.80.203:3128',
    //'http://doanhpv:QLMFWPPPd3ad@103.101.161.169:3128',
    //'http://doanhpv:5PzsMm2e4e7u@103.124.94.253:3128',
    //'http://doanhpv:RvGw2LU9gmxa@103.170.123.192:3128',
    //'http://doanhpv:r7h9rZG7akmx@103.170.123.167:3128',
    //'http://doanhpv:nyYzGAKdRfCf@103.170.123.186:3128',
    //'http://doanhpv:7Cfn7XAyXCNn@103.101.163.32:3128',
"http://user49110:tTLEVnkcsZ@103.79.141.68:49110",
"http://user49077:oUaLVU74RF@103.79.141.46:49077",
"http://user49184:Oal3sgcW3A@103.121.89.248:49184",
"http://user49056:K8ttBllkY2@103.121.91.115:49056",
"http://user49292:Q07e8ldqgW@103.3.246.132:49292",
"http://user49128:9Y3rImCr4v@103.162.30.88:49128",
"http://user49119:8g1CyDWRjq@103.3.246.14:49119",
"http://user49156:5LgJ3QCraJ@103.3.246.232:49156",
"http://user49187:u0qlyyJvEM@103.161.16.40:49187",
"http://user49053:csotNFAUkc@103.3.246.31:49053",
];
const PROXY_COUNT = PROXY_URLS.length;
const PROXY_URL = PROXY_URLS[COOKIE_INDEX];
const proxyAgent = new HttpsProxyAgent(PROXY_URL);
// =========================== START REDIS CONFIG ===========================


// =========================== START REDIS CONFIG ===========================
const redisClient = redis.createClient();
const COOKIE_KEY_PREFIX = "cookie_dong";
const getCookies = async () => {
    const data = await redisClient.get(COOKIE_KEY_PREFIX);
    if (!data) return [];
    const parsedData = JSON.parse(data);
    const res = [];
    for (let i = 0; i < parsedData.length; i++) {
        if (i % PROXY_COUNT == COOKIE_INDEX) {
            res.push(parsedData[i]);
        }
    }

    return res;
}


// const getCookies = () => {
//     const res = [];
//     for (let i = 0; i < ALL_COOKIE.length; i++) {
//         if (i % PROXY_COUNT == COOKIE_INDEX) {
//             res.push(ALL_COOKIE[i]);
//         }
//     }

//     return res;
// }

let isJsonError = false;
const call_dong_thien = async (cookieIndex) => {
    let userCookie = cookies[cookieIndex];
    if (!userCookie.includes('USER')) {
        userCookie = `USER=${userCookie}`;
    }

    try {
        const res = await fetch("https://tutien.net/account/bang_phai/dong_thien/", {
            agent: proxyAgent,
            "headers": {
                "authority": "tutien.net",
                "origin": "https://tutien.net",
                "content-type": "application/x-www-form-urlencoded; charset=UTF-8",
                "referer": "https://tutien.net/account/bang_phai/dong_thien/",
                "user-agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.159 Safari/537.36",
                "x-requested-with": "XMLHttpRequest",
                "cookie": userCookie
            },
            "referrer": "https://tutien.net/account/bang_phai/dong_thien/",
            "referrerPolicy": "strict-origin-when-cross-origin",
            "body": "btnActive=1",
            "method": "POST",
            "mode": "cors"
        });

        const headers = res.headers;
        const setCookie = headers.get('set-cookie').split(',');
        const newCookieArr = userCookie.split("; ");
        for (let i = 0; i < setCookie.length; i++) {
            const newCookieData = setCookie[i];
            if (!userCookie.includes('PHPSESSID') && newCookieData.includes('PHPSESSID')) {
                const phpsessid = newCookieData.split(';')[0].trim();
                newCookieArr.push(phpsessid);
                continue;
            }

            if (!userCookie.includes('TAM') && newCookieData.includes('TAM')) {
                const tam = newCookieData.split(';')[0].trim();
                newCookieArr.push(tam);
                continue;
            }

            if (newCookieData.includes('reada')) {
                const reada = newCookieData.split(';')[0].trim();
                const readaIndex = newCookieArr.findIndex(data => data.includes('reada'));
                if (readaIndex >= 0) {
                    newCookieArr[3] = reada;
                } else {
                    newCookieArr.push(reada);
                }
                continue;
            }
        }

        cookies[cookieIndex] = newCookieArr.join("; ");
        const body = await res.json();
        const {process_percent, process_text} = body;
        console.log(process_text);
        if (process_percent == 100) {
            await dot_pha_account(userCookie);
        }
    } catch (error) {
        //console.log("Cookie error: " + userCookie);
    }
}

const dot_pha_account = async (cookie) => {
    let userCookie = cookie;
    if (!userCookie.includes('USER')) {
        userCookie = `USER=${userCookie}`;
    }
    try {
        const res = await fetch("https://tutien.net/account/bang_phai/dong_thien/", {
        agent: proxyAgent,
            "headers": {
                "authority": "tutien.net",
                "origin": "https://tutien.net",
                "content-type": "application/x-www-form-urlencoded; charset=UTF-8",
                "user-agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.159 Safari/537.36",
                "referer": "https://tutien.net/account/bang_phai/dong_thien/",
                "x-requested-with": "XMLHttpRequest",
                "cookie": userCookie
            },
            "referrer": "https://tutien.net/account/bang_phai/dong_thien/",
            "body": "btnDotPha=1&tiledotpha=0",
            "method": "POST",
        });
    } catch (error) {
        const now = new Date();
        console.log("=============== ERROR: " + now.toUTCString() + " ===============");
        console.log(error);
    }
}

function delay(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

let cookies = [];
const now = new Date();
console.log("============ START: " + now.toUTCString() + "============");

const call_nsd_for_all = async () => {
    const actions = [];
    let i = 0;
    for (let i = 0; i < cookies.length; i++) {
      actions.push(call_dong_thien(i));
    }
    await Promise.all(actions);
    await delay(5000);
    setImmediate(call_nsd_for_all);
}

(async () => {
    await redisClient.connect();
    cookies = await getCookies();
    console.log(cookies);
    setImmediate(call_nsd_for_all);
})();
