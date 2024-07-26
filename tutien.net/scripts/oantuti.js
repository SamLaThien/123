// self executing function here
let start = false;

function getRandomInt(max) {
    return Math.floor(Math.random() * Math.floor(max)) + 1;
}

(function () {
    let stop = 300;
    // let userchoice = getRandomInt(3);
    console.log("==============================");
    const autoInterval = setInterval(function() {
        if (start) {
            // https://truyencv.com/guesspro/?a=play&userchoice=1&credit=50&formhash=4b18a246&random=1544163148762
            // const userchoice = 3; // bao
            let userchoice = getRandomInt(3);
            const credit = 50;
            const formhash = guess_formhash;
            const random = (new Date()).getTime();
            const url = 'https://truyencv.com/guesspro/?a=play&userchoice=' + userchoice + '&credit=' + credit + '&formhash=' + formhash + '&random=' + random;

            $.get(url, function(data, status) {
                const result = JSON.parse(data);
                const code = result.code;
                if (code !== 0) {
                    clearInterval(autoInterval);
                    console.log("==============================");
                } else {
                    console.log((300 - stop) + " - thắng: " + result.user.wins + ", hòa: " + result.user.ties + ", thua: " + result.user.losses);
                }
            });

            stop--;
            if (stop === 0) {
                console.log("==============================");
            }
        }
    }, 4000); // 4000ms = 4s
})();
