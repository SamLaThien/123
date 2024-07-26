function formatDate(date) {
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var ampm = hours >= 12 ? 'pm' : 'am';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? '0' + minutes : minutes;
    var strTime = hours + ':' + minutes + ' ' + ampm;
    return date.getDate() + "/" + (date.getMonth() + 1) + "/" + date.getFullYear() + "  " + strTime;
}

function tuoiNuoc(duocvien_id) {
    if (!islogin) {
        $('#modal-login').modal('show');
        return false;
    }
    var data = 'btnTuoiNuoc=1&duocvien_id=' + duocvien_id;
    $("button").attr("disabled", true);
    $.ajax({
        url: site + '/account/tu_luyen/duoc_vien/',
        type: "POST",
        data: data,
        cache: false,
        success: function (html) {
            // if (html == 1) {
            //     window.location.reload(true);
            // } else if (html) {
            //     alert(html);
            // } else {
            //     alert('Sorry, unexpected error. Please try again later.');
            // }
            console.log('Tưới nước thành công ==> Reload trang');
            $("button").attr("disabled", false);
        },
        complete: function () {
            setTimeout(function () {
                window.location.reload(true);
            }, 2000);
        }
    });
    return false;
}

function thuHoach(duocvien_id) {
    if (!islogin) {
        $('#modal-login').modal('show');
        return false;
    }
    var data = 'btnThuHoach=1&duocvien_id=' + duocvien_id;
    $("button").attr("disabled", true);
    $.ajax({
        url: site + '/account/tu_luyen/duoc_vien/',
        type: "POST",
        data: data,
        cache: false,
        success: function (html) {
            // if (html == 1) {
            //     //$('#div_linhdien_'+duocvien_id).remove();
            //     window.location.reload(true);
            // } else if (html == 2) {
            //     alert('Đạo hữu không chăm sóc linh điền nên không thu hoạch được gì.');
            //     //$('#div_linhdien_'+duocvien_id).remove();
            //     window.location.reload(true);
            // } else if (html) {
            //     alert(html);
            // } else {
            //     alert('Sorry, unexpected error. Please try again later.');
            // }
            $("button").attr("disabled", false);
            console.log('Thu hoạch thành công ==> Reload trang');
        },
        complete: function () {
            setTimeout(function () {
                window.location.reload(true);
            }, 2000);
        }
    });
    return false;
}

// self executing function here
(function () {
    const buttons = $('#content> div > div.col-md-10 > div.text-muted > div > div > button');
    console.log("=============== " + formatDate(new Date()) + " ===============");
    setTimeout(function () {
        if (buttons.length > 0) {
            for (var i = 0; i < buttons.length; i++) {
                const button = buttons[i];
                if (!button.disabled) {
                    button.click();
                    console.log("Dược viên: " + i + " chưa tưới => Tự động tưới nước");
                } else {
                    console.log("Dược viên: " + i + " đã tưới");
                }
            }
        }
    }, 2000);
    setTimeout(function () {
        window.location.reload(true);
    }, 120000); // 120000ms=120s=2min, tự động reloasau 3 min
})();
