#!/usr/bin/env python
import hashlib;
import requests;
import time;
import sys;
import json;

def createSig(userId, timeStamp, deviceToken):
    deviceToken = deviceToken.rstrip();
    inToken = "reward3V3ra52Lw1." + userId + ".50.bac." + timeStamp + "." + deviceToken;
    sigText = hashlib.md5(inToken.encode()).hexdigest();
    return sigText;

def getReward(userId, deviceToken):
    t0 = int(round(time.time()));
    ###
    try:
        s = requests.Session();
        timeStamp = str(int(round(time.time())) + 120 - 20);
        url_post = "http://aios.truyencv.com/registDeviceToken?user_id=" + userId + "&status=1&device_token=Android-" + deviceToken;
        r = s.post(url_post, timeout=10);
        time.sleep(6);
        ###
        timeStamp = str(int(round(time.time())));
        sigText = createSig(userId, timeStamp, deviceToken);
        url_get = "http://aios.truyencv.com/reward?user_id=" + userId + "&time=" + timeStamp + "&sig=" + sigText;
        # user_agent = "Mozilla/5.0 (Linux; U; Android 4.4.2; en-us; SCH-I535 Build/KOT49H)";
        user_agent = "Dalvik/1.6.0 (Linux; U; Android 4.4.2; Coolpad Y70-C Build/KTU84P)";
        headers = {"User-Agent": user_agent};
        r = s.get(url_get, headers=headers, timeout=15);
        try:
            json_object = json.loads(r.text);
            print("aaa: " + r.text );
            if (r.text.find("B\\u1ea1n nh\\u1eadn") != -1):
                #print("aaa: " + r.text );
                a = 1;
            elif (r.text.find("T\\u00e0i kho\\u1ea3n b\\u1ea1n ch\\u1ec9") != -1):
                return userId;
            else:
                print("error connect: " );
            return "0";
        except ValueError as ee:
		         print("error connet: " );
    except requests.Timeout as err:
        print("error timeout: " );
    except requests.exceptions.RequestException as e:
        print("error: " );
    return "-1";


###
cfg_file = sys.argv[1];
print("cfg_file: " + cfg_file);
time.sleep(5);
f = open(cfg_file, 'r');
s = f.readline();
hashId = {};
print("Read cfg");
while(len(s) > 0):
    s = s.replace('\n', '');
    ss = s.split(",");
    userId = str(ss[0]);
    hashId[userId] = ss[1];
    s = f.readline();
f.close();
i = 0;
while(True):
    if (len(hashId.keys()) == 0):
        exit;
    t0 = int(round(time.time()));
    list_id = hashId.keys();
    list_tmp = [];
    for userId in list_id:
        print("Current date & time " + time.strftime("%c") + " : " + userId );
        uid = getReward(userId, hashId[userId]);
        time.sleep(5);
    t1 = int(round(time.time()));
    if (t1 - t0 < 5000):
        print("sleep: " + str(5000 - (t1 - t0)));
        time.sleep(5000 - (t1 - t0));
