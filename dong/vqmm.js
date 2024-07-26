import fetch from "node-fetch";
import proxyAgentP from 'https-proxy-agent';
import redis from "redis";
const { HttpsProxyAgent } = proxyAgentP;

const PROXY_URLS = [
    "http://user49198:ROxH34Vuxu@103.187.168.83:49198",
    "http://user49278:pi7JAE1sYj@103.162.31.11:49278",
    "http://user49068:8jHHNJ8JJt@103.162.30.75:49068",
    "http://user49002:TdFt0BdARH@103.187.168.34:49002",
    "http://user49117:DifOrYj0vJ@103.79.141.70:49117",
    "http://user49020:uNd5szT36c@103.187.168.67:49020",
    "http://user49200:f4ICpF1PDB@103.121.90.53:49200",
    "http://user49034:rz1FDYhxpM@103.162.31.66:49034",
    "http://user49263:ks334SM0iH@103.162.31.68:49263",
    "http://user49017:bUUUmIxyZv@103.121.91.212:49017",
];

// =========================== START REDIS CONFIG ===========================
const redisClient = redis.createClient();
const COOKIE_KEY_PREFIX = "cookie_vqmm";
const getCookies = async () => {
    const data = await redisClient.get(COOKIE_KEY_PREFIX);
    if (!data) return [];
    return JSON.parse(data);
}

function getRandomInt(max) {
  return Math.floor(Math.random() * max);
}

const call_vqmm = async (cookieIndex) => {
    let userCookie = cookies[cookieIndex];
    if (!userCookie.includes('USER')) {
        userCookie = `USER=${userCookie}`;
    }

    const proxyIndex = getRandomInt(PROXY_URLS.length);
    const proxyUrl = PROXY_URLS[proxyIndex];
    const proxyAgent = new HttpsProxyAgent(proxyUrl);
    
    try {
        const res = await fetch("https://tutien.net/vong-quay-may-man/?a=play", {
            agent: proxyAgent,
            "headers": {
                "accept": "*/*",
                "accept-language": "en-US,en;q=0.9,vi;q=0.8",
                "sec-ch-ua": "\" Not A;Brand\";v=\"99\", \"Chromium\";v=\"102\", \"Google Chrome\";v=\"102\"",
                "sec-ch-ua-mobile": "?0",
                "sec-ch-ua-platform": "\"Linux\"",
                "sec-fetch-dest": "empty",
                "sec-fetch-mode": "cors",
                "sec-fetch-site": "same-origin",
                "x-requested-with": "XMLHttpRequest",
                "cookie": userCookie,
                "Referer": "https://tutien.net/vong-quay-may-man/",
                "Referrer-Policy": "strict-origin-when-cross-origin"
            },
            "body": null,
            "method": "GET"
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
        const aaa = await res.text();
        console.log(aaa);
    } catch (error) {
        console.log(error);
        console.log("Cookie error: " + userCookie);
    }
}

function delay(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

let cookies = [];
const now = new Date();
console.log("============ START: " + now.toUTCString() + "============");

// Get new cookie every 5 minutes
setInterval(async () => {
    cookies = await getCookies();
}, 5*60*1000);

const call_vqmm_for_all = async () => {
    const actions = [];

    const cookieCount = cookies.length;
    const proxyCount = PROXY_URLS.length;
    const minVqmm = Math.min(cookieCount, proxyCount);
    for (let i = 0; i < cookieCount; i++) {
      actions.push(call_vqmm(i));
    }
    await Promise.all(actions);
    await delay(5000);
    setImmediate(call_vqmm_for_all);
}

(async () => {
    await redisClient.connect();
    cookies = await getCookies();
    setImmediate(call_vqmm_for_all);
})();
