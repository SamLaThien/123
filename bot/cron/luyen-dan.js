//import fetch from "node-fetch";
//import cheerio from "cheerio";
//import {updateRuong} from "../modules/chuyen-do.js";
//import {chat, getItem, setExpire, setItem, getTcvNameFromTcvId} from "../helper.js";
//import axios from "axios";
//
//const TTD = 'Tẩy Tủy Đan';
//const TCD = 'Trúc Cơ Đan';
//const BND = 'Bổ Nguyên Đan';
//const BAD = 'Bổ Anh Đan';
//const HND = 'Hóa Nguyên Đan';
//const LTD = 'Luyện Thần Đan';
//
//const TTD_ID = 9;
//const TCD_ID = 13;
//const BND_ID = 14;
//const BAD_ID = 40;
//const HND_ID = 62;
//const LTD_ID = 77;
//
//const NTLNHP = 'Ngọc Tủy Linh Nhũ HP';
//const NTLNTP = 'Ngọc Tủy Linh Nhũ TP';
//const NTLNTHP = 'Ngọc Tủy Linh Nhũ THP';
//
//const NTLNHP_ID = 50;
//const NTLNTP_ID = 51;
//const NTLNTHP_ID = 52;
//
//const TDL_ID = 67;
//const HMD_ID = 12287;
//const TDD_ID = 12288;
//const TND_ID = 32226;
//
//const TDL = 'Thiên Địa Lô';
//const HMD = 'Hắc Ma Đỉnh';
//const TDD = 'Thánh Diệu Đỉnh';
//const TND = 'Thần Nông Đỉnh';
//
//const ACCOUNTS = [
//  {
//    id: 132301,
//    cookie: 'USER=TwRpTyqt3y4R%3ASy7%2FIXt4vyy6Eeo%2BYVxgKj55TNph5rPbBEb4DUuONsN9',
//    phutro: [],
//    danlo: [TND_ID],
//    dan_id: 14
//  },
//  {
//    id: 301356,
//    cookie: 'USER=rp7pbXvABThf%3AujUMdQMzrSvr5Ja4Nj8zYSB14IFmKGCkyt6h%2BZJPcjOY',
//    phutro: [],
//    danlo: [TND_ID],
//    dan_id: 14
//  },
//  {
//    id: 399989,
//    cookie: 'USER=8VaIX%2BKVm2hS%3ALhzkwbXjV38qmBd8oheGZLnzHDPy27GOGJRcprDX4AmA',
//    phutro: [],
//    danlo: [HMD_ID],
//    dan_id: 13
//  },
//  {
//    id: 599798,
//    cookie: 'USER=HKLVPiBJCAAQ%3AZKYNafBgv9JbsvIxq6EJZeMHnMv%2FN3K%2B0BXNu5eUQcVO',
//    phutro: [],
//    danlo: [TDL_ID],
//    dan_id: 9
//  },
//  {
//    id: 625923,
//    cookie: 'USER=SHzFae2J5PyR%3AjOzO3gWJG8yUYJBcn6w7V8HjnYeUznM1RpLY2yCHRMgl',
//    phutro: [],
//    danlo: [TDL_ID],
//    dan_id: 13
//  },
//  {
//    id: 289602,
//    cookie: 'USER=2vM1VFAN5z44%3A0j0JOxOBOx511LTta87x45xEaq9i04yl1KfgTKpvxBIt',
//    phutro: [NTLNHP_ID],
//    danlo: [TDD_ID],
//    dan_id: 62
//  },
//  {
//    id: 150184,
//    cookie: 'USER=pXHmQHReqIK%2B%3AbcKJ5sr1XXk%2FayJ3W%2BhkXsTlO%2F%2FG5NpBnlID1wOmcbmy',
//    phutro: [NTLNTP_ID],
//    danlo: [TDD_ID],
//    dan_id: 62
//  },
//  {
//    id: 150495,
//    cookie: 'USER=I64iEkrU8l31%3AuCxFHQ%2BFiZ6UXCj%2B2k5qRSmkI6aILMSPzL7WK9rDyxNf',
//    phutro: [NTLNTHP_ID],
//    danlo: [TND_ID],
//    dan_id: 77
//  },
//  {
//    id: 3842,
//    cookie: 'USER=Vny%2FlSatE95n%3ASzCiB7amIJacQ1oqxmQJ%2FDhmnRu%2BBlVyNYzN9fJ51VDK',
//    phutro: [NTLNHP_ID],
//    danlo: [TDD_ID],
//    dan_id: 40
//  },
//  {
//    id: 39133,
//    cookie: 'USER=HAH%2BmjPMuNTk%3AolAIuB5FeZ1gqB9b%2FhoF6nF7zRD7V1DZROgM6aN5zuRg',
//    phutro: [],
//    danlo: [TDL_ID],
//    dan_id: 14
//  },
//  {
//    id: 70349,
//    cookie: 'USER=pqD43C6sVQGX%3A60pyRjKngxah4c7r4gN92qCsvwv81rZnNIxmSozLvm2x',
//    phutro: [NTLNTP_ID],
//    danlo: [TDL_ID],
//    dan_id: 77
//  },
//  {
//    id: 573444,
//    cookie: 'USER=flTvUisnyiZn%3AqyUVk%2FtMLMASER7g2qwHJe6ZCqkSZF3i0IbfXhPg0wBV',
//    phutro: [NTLNHP_ID],
//    danlo: [TDD_ID],
//    dan_id: 40
//  },
//  {
//    id: 235579,
//    cookie: 'USER=TkxeCRBX4F5e%3ADNlF9aTbWl0MMhdsdRc2WHEbDj4%2FeQIZQ4ErG3I0VJzN',
//    phutro: [],
//    danlo: [TDL_ID],
//    dan_id: 13
//  }
//];
//
//
//export const activeDan = async (tcvName, toId) => {
//    let toName = await getTcvNameFromTcvId(toId);
//    
//    let danStatus = await getItem(`dan_status_${toId}`);
//    if (danStatus == false || danStatus == 'false' || danStatus == null || danStatus == "NaN" || danStatus == NaN) {
//        await setItem(`dan_status_${toId}`, true);
//        danStatus = true;
//    }
//    await chat(`${tcvName} - Đã bật luyện đan cho ${toName}.`);
//}
//
//export const inActiveDan = async (tcvName, toId) => {
//    let toName = await getTcvNameFromTcvId(toId);
//    
//    let danStatus = await getItem(`dan_status_${toId}`);
//    if (danStatus == true || danStatus == 'true' || danStatus == null || danStatus == "NaN" || danStatus == NaN) {
//        await setItem(`dan_status_${toId}`, false);
//        danStatus = false;
//    }
//    await chat(`${tcvName} - Đã tắt luyện đan cho ${toName}.`);
//}
//
//function delay(ms) {
//  return new Promise(resolve => setTimeout(resolve, ms));
//}
//
//const luyenDan = async (accountId, dan_id, danLo_id, phuTro_id, cookie) => { 
//  const body = 'btnLuyenDan=1&radLuyenDan='+dan_id+'&radDanLo='+danLo_id+'&radPhuTro='+phuTro_id;
//  const response = await fetch("https://tutien.net/account/tu_luyen/luyen_dan_that/", {
//    "headers": {
//      "accept": "*/*",
//      "accept-language": "en-US,en;q=0.9,vi;q=0.8",
//      "content-type": "application/x-www-form-urlencoded; charset=UTF-8",
//      "sec-ch-ua": "\" Not A;Brand\";v=\"99\", \"Chromium\";v=\"96\", \"Google Chrome\";v=\"96\"",
//      "sec-ch-ua-mobile": "?0",
//      "sec-ch-ua-platform": "\"macOS\"",
//      "sec-fetch-dest": "empty",
//      "sec-fetch-mode": "cors",
//      "sec-fetch-site": "same-origin",
//      "x-requested-with": "XMLHttpRequest",
//      "cookie": cookie,
//      "Referer": "https://tutien.net/account/tu_luyen/tien_phu/",
//      "Referrer-Policy": "strict-origin-when-cross-origin"
//    },
//    "body": body,
//    "method": "POST"
//  });
//  
//  const res = await response.text();
//  const getName = await axios.get(`https://soi-tcvtool.xyz/truyencv/member/${accountId}`);
//  const accountName = await getName.data.name;
//  const loaiDan = await getTenDan(dan_id);
//  if (res == '1') {
//      console.log(`${accountName} đã luyện ${loaiDan}`);    
//  } else if (res == 'Đạo hữu đang bế quan, không thể luyện đan') {
//      console.log(`${accountName} - ${res}`);
//  } else {
//      console.log(`${accountName} - ${res} ${loaiDan}`);
//  }
//}
//
//const luyenDan2 = async (accountId, dan_id, danLo_id, phuTro_id, cookie) => { 
//  const body = 'btnLuyenDan=1&radLuyenDan='+dan_id+'&radDanLo='+danLo_id+'&radPhuTro='+phuTro_id;
//  const response = await fetch("https://tutien.net/account/tu_luyen/luyen_dan_that/", {
//    "headers": {
//      "accept": "*/*",
//      "accept-language": "en-US,en;q=0.9,vi;q=0.8",
//      "content-type": "application/x-www-form-urlencoded; charset=UTF-8",
//      "sec-ch-ua": "\" Not A;Brand\";v=\"99\", \"Chromium\";v=\"96\", \"Google Chrome\";v=\"96\"",
//      "sec-ch-ua-mobile": "?0",
//      "sec-ch-ua-platform": "\"macOS\"",
//      "sec-fetch-dest": "empty",
//      "sec-fetch-mode": "cors",
//      "sec-fetch-site": "same-origin",
//      "x-requested-with": "XMLHttpRequest",
//      "cookie": cookie,
//      "Referer": "https://tutien.net/account/tu_luyen/tien_phu/",
//      "Referrer-Policy": "strict-origin-when-cross-origin"
//    },
//    "body": body,
//    "method": "POST"
//  });
//  
//  const res = await response.text(); 
//  return res;
//}
//
//function getTenDan(danId) {
//    let tenDan = '';
//    switch (danId) {
//        case '9':
//        case 9:
//            tenDan = 'Tẩy Tủy Đan';
//            break;
//        case '13':
//        case 13:
//            tenDan = 'Trúc Cơ Đan';
//            break;
//        case '14':
//        case 14:
//            tenDan = 'Bổ Nguyên Đan';
//            break;
//        case '40':
//        case 40:
//            tenDan = 'Bổ Anh Đan';
//            break;
//        case '62':
//        case 62:
//            tenDan = 'Hóa Nguyên Đan';
//            break;
//        case '77':
//        case 77:
//            tenDan = 'Luyện Thần Đan';
//            break; 
//        default:
//            break;
//    }
//    return tenDan;
//}
//
//export async function getListAcc() {
//    const listAcc = [`Danh sách các account đang luyện đan:`];
//    for (let i = 0; i < ACCOUNTS.length; i++) {
//        const account = ACCOUNTS[i];
//        try {
//            const accountId = account.id;
//            const response = await axios.get(`https://soi-tcvtool.xyz/truyencv/member/${accountId}`);
//            const accountName = await response.data.name;
//            const loaiDan = await getTenDan(account.dan_id);
//            listAcc.push(`${i + 1}. ${accountName} đang luyện ${loaiDan}`);
//        } catch (error) {
//            chat('Có lỗi xảy ra!');
//        }
//    }
//    chat(listAcc.join('[br]'));
//}
//
//export async function getLogDan() {
//   let log = [`Log luyện đan:`];
//   for (let i = 0; i < ACCOUNTS.length; i++) {
//    const account = ACCOUNTS[i]; 
//    try {
//      const res = await luyenDan2(account.id, account.dan_id, account.danlo, account.phutro, account.cookie);
//      await delay(1000);
//      const getName = await axios.get(`https://soi-tcvtool.xyz/truyencv/member/${account.id}`);
//      const accountName = await getName.data.name;
//      const loaiDan = await getTenDan(account.dan_id);
//      
//      if (res == '1') {
//          log.push(`✦ ${accountName} đã luyện ${loaiDan}`);    
//      } else if (res == 'Đạo hữu đang bế quan, không thể luyện đan') {
//          log.push(`✦ ${accountName} - ${res}`);
//      } else {
//          log.push(`✦ ${accountName} - ${res} ${loaiDan}`);
//      }
//      
//    } catch (error) {
//      console.log(error);
//    }
//  }
//  chat(log.join('[br]'));
//}
//
//setInterval(async () => {
//  const date_ob = new Date();
//  console.log("================="+ date_ob.toLocaleTimeString() +"==================");
//  for (let i = 0; i < ACCOUNTS.length; i++) {
//    const account = ACCOUNTS[i]; 
//    try {
//      await luyenDan(account.id, account.dan_id, account.danlo, account.phutro, account.cookie);
//      await delay(2000);
//    } catch (error) {
//      console.log(error);
//    }
//  }
//}, 2 * 60 * 1000); // every 2 minutes
//
//