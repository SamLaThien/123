import {getItem, pmTcv, setItem, snake_case, chat, getTcvNameFromTcvId, getKeys} from "../helper.js";
import {updateRuong, chuyenDoHacDiem} from "./chuyen-do.js";
import {cap, viettat} from "./viettat.js";
import axios from "axios";
const HE_THONG = 1; // Vật phẩm hệ thống, có thể chuyển về acc
const HAC_DIEM = 2; // Vật phẩm hắc điểm, chỉ có thế chuyển rương

const SELL_ITEMS = [
  {
    name: 'Thần Nông Lệnh',
    type: HAC_DIEM,
  }, 
  {
    name: 'Tạo Hóa Ngọc Diệp',
    type: HAC_DIEM,
  },
  {
    name: 'Tiên Phủ Lệnh',
    type: HAC_DIEM,
  },
  {
    name: 'Cổ Chiến Lệnh',
    type: HAC_DIEM,
  },
  {
    name: 'Tru Tiên Đan',
    type: HAC_DIEM,
  },
  {
    name: 'Uẩn Huyết Đan',
    type: HAC_DIEM,
  },
  {
    name: 'Vận Khí Đan',
    type: HAC_DIEM,
  },
  {
    name: 'Hồi Linh Đan',
    type: HAC_DIEM,
  },
];
const BUY_ITEMS = {
  'tinh_linh_cp' :'Tinh Linh CP',
  'tu_tinh_hp' :'Tử Tinh HP',
  'tu_tinh_thp' :'Tử Tinh THP',
  'tui_sung_vat' :'Túi Sủng Vật',
  'anh_tam_thao' :'Anh Tâm Thảo',
  'ban_dao_qua' :'Bàn Đào Quả',
  'bai_thiep' :'Bái Thiếp',
  'bo_de_qua' :'Bồ Đề Quả',
  'bo_nguyen_dan' :'Bổ Nguyên Đan',
  'co_than_dan' :'Cố Thần Đan',
  'hoa_long_thao' :'Hóa Long Thảo',
  'hoa_nguyen_thao' :'Hóa Nguyên Thảo',
  'hu_khong_chi thạch' :'Hư Không Chi Thạch',
  'ho_linh_tran' :'Hộ Linh Trận',
  'kim_thuong' :'Kim Thuổng',
  'linh_thach_hp' :'Linh Thạch HP',
  'linh_thach_thp' :'Linh Thạch THP',
  'linh_thach_cp' :'Linh Thạch CP',
  'luyen_than_thao' :'Luyện Thần Thảo',
  'ngoc_gian_truyen_cong' :'Ngọc Giản Truyền Công',
  'ngoc_tuy_chi' :'Ngọc Tủy Chi',
  'ngoc_tuy_linh_nhu_hp' :'Ngọc Tủy Linh Nhũ HP',
  'ngoc_tuy_linh_nhu_tp' :'Ngọc Tủy Linh Nhũ TP',
  'thanh_tam_dan' :'Thanh Tâm Đan',
  'thien_linh_qua' :'Thiên Linh Quả',
  'thien_nguyen_thao' :'Thiên Nguyên Thảo',
  'tinh_linh_thp' :'Tinh Linh THP',
  'trich_tinh_thao' :'Trích Tinh Thảo',
  'truc_co_dan' :'Trúc Cơ Đan',
  'tui_thuc_an' :'Túi Thức Ăn',
  'tay_tuy_dan' :'Tẩy Tủy Đan',
  'ti_loi_chau' :'Tị Lôi Châu',
  'tu_tinh_tp' :'Tử Tinh TP',
  'uan_kim_thao' :'Uẩn Kim Thảo',
  'de_giai_thuan' :'Đê Giai Thuẫn',
  'hoi_huyet_dan' :'Hồi Huyết Đan',
  'bang_hoa_ngoc' :'Băng Hỏa Ngọc',
  'thoi_gian_chi_thuy' :'Thời Gian Chi Thủy',
  'tui_phan_bon' : 'Túi Phân Bón',
  'thai_ngoc_chau': 'Thải Ngọc Châu',
  'hoa_ngoc_chau': 'Hỏa Ngọc Châu',
  'sa_ngoc_chau': 'Sa Ngọc Châu',
  'tay_tuy_dan': 'Tẩy Tủy Đan',
  'truc_co_dan': 'Trúc Cơ Đan',
  'bo_nguyen_dan': 'Bổ Nguyên Đan',
  'bo_anh_dan': 'Bổ Anh Đan',
  'hoa_nguyen_dan': 'Hóa Nguyên Đan',
  'luyen_than_dan': 'Luyện Thần Đan',
};

export const activeShop = async (tcvId) => {
    let fromName = await getTcvNameFromTcvId(tcvId);
    
    let shopStatus = await getItem('shop_status');
    if (shopStatus == false || shopStatus == 'false' || shopStatus == null || shopStatus == "NaN" || shopStatus == NaN) {
        await setItem('shop_status', true);
        shopStatus = true;
    }
    await chat(`${fromName} - Đã mở cửa hàng.`);
}

export const inActiveShop = async (tcvId) => {
    let fromName = await getTcvNameFromTcvId(tcvId);
    
    let shopStatus = await getItem('shop_status');
    if (shopStatus == true || shopStatus == 'true' || shopStatus == null || shopStatus == "NaN" || shopStatus == NaN) {
        await setItem('shop_status', false);
        shopStatus = false;
    }
    await chat(`${fromName} - Đã đóng cửa hàng.`);
}

export async function setPrice(itemName, price, isSell = false) {
  const name = viettat(itemName);
  let redisKey = `hac_diem_${snake_case(name)}_price`;
  if (isSell) {
    redisKey = `hac_diem_sell_${snake_case(name)}_price`;
  }
  await setItem(redisKey, price);
}

export async function setAmount(itemName, amount, isSell = false) {
  const name = viettat(itemName);
  let redisKey = `hac_diem_${snake_case(name)}_amount`;
  if (isSell) {
    redisKey = `hac_diem_sell_${snake_case(name)}_amount`;
  }
  await setItem(redisKey, amount);
}


export async function getPrice(itemName, isSell = false) {
  const name = viettat(itemName);
  let redisKey = `hac_diem_${snake_case(name)}_price`;
  if (isSell) {
    redisKey = `hac_diem_sell_${snake_case(name)}_price`;
  }
  const price = await getItem(redisKey);
  return price ? parseInt(price) : 0;
}

export async function getAmount(itemName, isSell = false) {
  const name = viettat(itemName);
  let redisKey = `hac_diem_${snake_case(name)}_amount`;
  if (isSell) {
    redisKey = `hac_diem_sell_${snake_case(name)}_amount`;
  }
  const amount = await getItem(redisKey);
  return amount ? parseInt(amount) : 0;
}

// Member mua vật phẩm
export async function buyItem(accountId, itemName, amount) {
  const accountName = await getTcvNameFromTcvId(accountId);
  const name = cap(viettat(itemName), false);
  const item = SELL_ITEMS.find((buyItem) => buyItem.name === name);
  if (!item) { // Hệ thống không thu mua ${name}
    return;
  }

  const hacDiemAmount = await getAmount(itemName);
  const price = await getPrice(itemName);

  // Chưa set giá hoặc số lượng vật phẩm
  if (!hacDiemAmount || !price) {
    // Hắc điếm không mua vật phẩm
    chat(`${accountName} - Cửa hàng không bán ${cap(name)}`);
    return;
  }

  if (hacDiemAmount < amount) {
    // Hắc điếm chỉ còn hacDiemAmount vật phẩm
    chat(`${accountName} - Cửa hàng chỉ còn lại ${hacDiemAmount} ${cap(name)}`);
    return;
  }

  const redisItem = await getItem("ruong_do_ao_" + accountId + "_bac");
  // Rương không có bạc
  if (!redisItem) {
    chat(`${accountName} - Số dư không đủ để mua.`);
    return;
  }

  const ruongItem = JSON.parse(redisItem);
  const bacAmount = parseInt(`${Object.values(ruongItem)[0]}`);
  const totalPrice = amount*price;
  if (bacAmount < totalPrice) {
    // Không đủ bạc để mua
    chat(`${accountName} - Số dư không đủ để mua.`);
    return;
  }

  if (item.type === HE_THONG) {
    // Chuyển đồ về account
    //await updateRuong(accountId, name, -1*amount);
    await chuyenDoHacDiem(name, amount, accountId);
  } else {
    // Nạp rương
    //await updateRuong(accountId, name, amount);
    if (name == 'Cổ Chiến Lệnh') {
        var today = new Date().getDate(); 
        if (today < 10) {
            today = '0' + today;
        }
        var m = new Array();
        m[0] = "01";
        m[1] = "02";
        m[2] = "03";
        m[3] = "04";
        m[4] = "05";
        m[5] = "06";
        m[6] = "07";
        m[7] = "08";
        m[8] = "09";
        m[9] = "10";
        m[10] = "11";
        m[11] = "12";
        var month = m[new Date().getMonth()];
        let isMuaChienLenh = await getItem(`${accountId}_${today}_${month}_muachienlenh`); 
        if (isMuaChienLenh == null || isMuaChienLenh == "NaN" || isMuaChienLenh == NaN) {
            await setItem(`${accountId}_${today}_${month}_muachienlenh`, 0);
            isMuaChienLenh = 0;
        } 
        
        let muaChienLenh = isMuaChienLenh ? parseInt(isMuaChienLenh) : 0;
        let luotMuaConLai = 20 - muaChienLenh;
        if (amount + muaChienLenh > 20) {
            chat(`${accountName} chỉ được mua 20 Cổ Chiến Lệnh trong ngày, hiện tại còn mua được ${luotMuaConLai}.`);
            return;
        } else {
            muaChienLenh += amount;
            await setItem(`${accountId}_${today}_${month}_muachienlenh`, muaChienLenh);
            await updateRuong(accountId, name, amount);
            await updateRuong(accountId, 'bạc', -1*amount*price);
            await setAmount(name, hacDiemAmount - amount);
            //await pmTcv(accountId, 'Xong!');
            const key1 = "ruong_do_ao_" + accountId + "_bac";
            let ruong = await getItem(key1);

            ruong = JSON.parse(ruong);
            const soDu = parseInt(ruong["bạc"]);
            chat(`${accountName} - Đã bán ${amount} ${cap(name)}`);
            axios({
                method: "POST",
                url: "https://soi-tcvtool.xyz/truyencv/member/add-log-bac",
                data: {
                    "tcvId": accountId,
                    "updown": "-",
                    "amount": amount*price,
                    "sodu": soDu,
                    "action": `Mua ${amount} ${cap(name)}`,
                }
            });
        }
    } else {
        updateRuong(accountId, name, amount);
        await updateRuong(accountId, 'bạc', -1*amount*price);
        await setAmount(name, hacDiemAmount - amount);
        //await pmTcv(accountId, 'Xong!');
        const key2 = "ruong_do_ao_" + accountId + "_bac";
        let ruong2 = await getItem(key2);

        ruong2 = JSON.parse(ruong2);
        const soDu2 = parseInt(ruong2["bạc"]);
        chat(`${accountName} - Đã bán ${amount} ${cap(name)}`);
        axios({
            method: "POST",
            url: "https://soi-tcvtool.xyz/truyencv/member/add-log-bac",
            data: {
                "tcvId": accountId,
                "updown": "-",
                "amount": amount*price,
                "sodu": soDu2,
                "action": `Mua ${amount} ${cap(name)}`,
            }
        });
    }
  } 
}

// Member bán vật phẩm
export async function sellItem(accountId, itemName, amount) {
  const accountName = await getTcvNameFromTcvId(accountId);
  const name = cap(viettat(itemName), false);
  const key = "ruong_do_ao_" + accountId + "_" + snake_case(name);
  const hacDiemAmount = await getAmount(itemName);
  const price = await getPrice(itemName);

  // Chưa set giá hoặc số lượng vật phẩm
  if (!hacDiemAmount || !price) {
    // Hắc điếm không mua vật phẩm
    chat(`${accountName} [img]https://cbox.im/i/R50RI.png[/img]`);
    return;
  }

  if (amount > hacDiemAmount) {
    // Hắc điếm chỉ thu mua hacDiemAmount cái
    chat(`${accountName} - Cửa hàng chỉ thu thêm ${hacDiemAmount} ${cap(name)}`);
    return;
  }

  const redisItem = await getItem(key);
  if (!redisItem) {
    // Rương không có vật phẩm
    chat(`${accountName} - Không tìm thấy log đã nộp ${amount} ${cap(name)}`);
    return;
  }

  const ruongItem = JSON.parse(redisItem);
  const itemAmount = parseInt(`${Object.values(ruongItem)[0]}`);
  if (itemAmount < amount) {
    // Không đủ vật phẩm để bán
    chat(`${accountName} - Số lượng ${cap(name)} không đủ để bán (còn ${itemAmount}).`);
    return;
  }

  await updateRuong(accountId, name, -1*amount);
  await updateRuong(accountId, 'bạc', amount*price);
  await setAmount(name, hacDiemAmount - amount);
  //await pmTcv(accountId, 'Xong!');
  const key1 = "ruong_do_ao_" + accountId + "_bac";
  let ruong = await getItem(key1);

  ruong = JSON.parse(ruong);
  const soDu = parseInt(ruong["bạc"]);
  chat(`${accountName} - Đã thu ${amount} ${cap(name)}`);
  axios({
      method: "POST",
      url: "https://soi-tcvtool.xyz/truyencv/member/add-log-bac",
      data: {
          "tcvId": accountId,
          "updown": "+",
          "amount": amount*price,
          "sodu": soDu,
          "action": `Bán ${amount} ${name}`,
      }
  });
}

export async function listBuy(accountId) {
  const keys = await getKeys('hac_diem_*_amount');
  const list = [];
  for (let i = 0; i < keys.length; i++) {
    const key = keys[i];
    if (key.includes('_sell_')) {
      continue;
    }
    
    const itemName = key.replace('hac_diem_','').replace('_amount', '');
    // const vietTat = snake_case(BUY_ITEMS[i]);
    const amount = await getAmount(itemName);
    const price =  await getPrice(itemName);
    if (!amount || !price) {
      continue;
    }

    list.push(`[br]✦ ${cap(BUY_ITEMS[key])} (${amount}): ${price} bạc/vp`);
  }

  const message = list.join('');
  await pmTcv(accountId, message);
}

export async function listSell(accountId) {
  const list = [];
  for (let i = 0; i < SELL_ITEMS.length; i++) {
    const item = SELL_ITEMS[i];
    const amount = await getAmount(item.name);
    const price =  await getPrice(item.name)
    if (!amount || !price) {
      continue;
    }

    list.push(`[br]✦ ${cap(item.name)} (${amount}): ${price} bạc/vp`);
  }

  const keys = await getKeys('hac_diem_*_amount');
  for (let i = 0; i < keys.length; i++) {
    const key = keys[i];
    if (!key.includes('_sell_')) {
      continue;
    }
    
    const itemName = key.replace('hac_diem_sell_','').replace('_amount', '');
    // const vietTat = snake_case(BUY_ITEMS[i]);
    const amount = await getAmount(itemName);
    const price =  await getPrice(itemName);
    if (!amount || !price) {
      continue;
    }

    list.push(`[br]✦ ${cap(BUY_ITEMS[key])} (${amount}): ${price} bạc/vp`);
  }

  const message = list.join('');
  await pmTcv(accountId, message);
}
