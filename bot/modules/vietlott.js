import {chat, pmCbox, getItem, getKeys, getTcvNameFromTcvId, setItem, formatBac} from '../helper.js';
import {updateRuong, napRuongVietlott, napRuongVietlott2} from "./chuyen-do.js";
import axios from "axios";
import {cap, viettat} from "./viettat.js";

const getRandom = (min, max) => {
    max++;
    let bb = 0;
    for (let l = 1; l <= 10; l++) {
        bb = Math.floor(Math.random() * (max - min) ) + min;
    }
    return bb;
}

const getRuong = async tcvId => {
    const key = "ruong_do_ao_" + tcvId + "_*";
    const itemKeys = await getKeys(key);
    if (itemKeys.length === 0) {
        return null;
    }

    const items = {};
    for (let i = 0; i < itemKeys.length; i ++) {
        let item = await getItem(itemKeys[i]);
        if (item === "{}" || item === "") {
            await delKey(itemKeys[i]);
            continue;
        }

        item = JSON.parse(item);
        Object.assign(items, item);
    }

    return items;
}

function getRandomInt(max) {
    return Math.floor(Math.random() * Math.floor(max));
}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

export const checkVietlott = async () => { 
    const preKey = `vietlott_`;
    const keys = await getKeys(`*${preKey}`);
    const items = [`Danh sách thành viên mua vé Vietlott:`];
    for (let i = 0; i < keys.length; i++) {
        const key = keys[i];
        const amount = await getItem(key);
        const accountId = key.replace(preKey, '');
        const accountName = await getTcvNameFromTcvId(accountId);
    	//const bacText = `${bac} bạc`.padEnd(11);
        items.push(`✦ ${accountName} mua ${amount} vé`);
    }

    chat(items.join('[br]'));
}

export const muaVe = async (tcvId, cboxId, tcvName, amount) => {
    const key = "ruong_do_ao_" + tcvId + "_bac";
    let ruong = await getItem(key);
    if (!ruong) {
         chat(`${fromName} - Số dư không đủ để chơi.`);
        //await pmCbox(fromCboxId, "Bạc không đủ để chuyển.");
        return;
    }
    if (ruong === "") {
         chat(`${fromName} - Số dư không đủ để chơi.`);
        //await pmCbox(fromCboxId, "Bạc không đủ để chuyển.");
        return;
    }

    ruong = JSON.parse(ruong);
    //const currentAmount = parseInt(ruong["bạc"]);
    if (ruong == null) {
        await setItem(key, 0);
        ruong['bạc'] = 0;
    }
    
    const currentAmount = parseInt(ruong['bạc'] == null ? 0 : parseInt(ruong['bạc'])); 
    if (currentAmount < amount) {
        chat(`${fromName} - Số dư không đủ để chơi.`);
        //await pmCbox(fromCboxId, "Bạc không đủ để chuyển.");
        return;
    }

    await napRuongVietlott2(tcvId, -amount);

    const vietlottKey = "vietlott_" + tcvId;
    let vietlottKeys = await getItem(vietlottKey);
    vietlottKeys = JSON.parse(vietlottKeys);
    if (vietlottKeys == null) {
        await setItem(vietlottKey, 0);
        vietlottKeys = 0;
    }
    vietlottKeys += amount;
    pmCbox(cboxId, `Đã mua ${amount} vé vietlott.`);
}


