## Auto accept 
```js
const targetId = '291136';
(function () {
    console.log("waittt");
    var thachdau;
    let interval = setInterval(function() {
        thachdau = $("ul#blockthachdau li");
        if (thachdau.length) {
            for (var i = 0; i < thachdau.length; i++) {
                const url = $('a', thachdau[0]).attr("href");
                if (url.indexOf(targetId) != -1) {
                    $('button', thachdau[0]).click();
                    i = thachdau.length;
                    clearInterval(interval);
                }
            } 
        }
    }, 1000);
})();
```

## Auto win
```js
// $("#thongbao").text();

(function () {
    console.log("waittt");
    setTimeout(function() {
        const skills = $(".radio input");
        let i = 1;
        skills[2].click();
        gameTanCong();
        // const interval = setInterval(function() {
        //     skills[i].click();
        //     gameTanCong();
        //     i++;
        //     if (i == skills.length) {
        //         // gameThoat();
        //         clearInterval(interval);
        //     }
        // }, 1200);
        
        setInterval(function() {
            if ($("#thongbao").text() != "") {
                window.location.reload(true);
            }
        }, 1000);
    }, 5000);
})();
```

## Auto thua
```js
// const skills = $(".radio input");
// for (var i = 1; i < skills.length; i++) {
//     skills[i].click();
//     gameTanCong();
//     setTimeout(function() {
//         console.log("waittt");
//     }, 1000);
// }

// gameThoat();


(function () {
    console.log("waittt");
    setTimeout(function() {
        const skills = $(".radio input");
        let i = 1;
        const interval = setInterval(function() {
            skills[i].click();
            gameTanCong();
            i++;
            if (i == skills.length) {
                setTimeout(function() {
                    clearInterval(interval);
                    gameThoat();
                }, 3500);
            }
        }, 1200);
    }, 4000);
    
    // for (var i = 1; i < skills.length; i++) {
    //     skills[i].click();
    //     gameTanCong();
    //     setTimeout(function() {
    //         console.log("waittt");
    //     }, 1000);
    // }
    
    
})();
```

## Auto thach dau
```js
// gameThachDau(280206);

let isThachdau = false;
(function () {
    const interval = setInterval(function() {
        if ($("#active-280206").length && !isThachdau) {
            isThachdau = true;
            gameThachDau(280206);
        }
    }, 1000);
})();
```