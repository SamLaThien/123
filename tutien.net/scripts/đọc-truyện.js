// Override function lumDo
function lumDo(story_id) {
    (function ($) {
        if (!islogin) {
            $('#modal-login').modal('show');
            return false;
        }

        var data = 'btnLumDo=1&story_id=' + story_id;
        $("button").attr("disabled", true);
        $.ajax({
            url: site + '/index.php',
            type: "POST",
            data: data,
            cache: false,
            success: function (html) {
                $("button").attr("disabled", false);
                if (html) {
                    $('#lumdo').hide();
                    // alert(html);
                    setTimeout(function () { window.location.reload(true); }, 2000); // Reload trang sau khi lum do
                }
            }
        });
        return false;
    })(jQuery);
}

function formatDate(date) {
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var ampm = hours >= 12 ? 'pm' : 'am';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? '0'+minutes : minutes;
    var strTime = hours + ':' + minutes + ' ' + ampm;
    return date.getDate() + "/" + (date.getMonth() + 1) + "/" + date.getFullYear() + "  " + strTime;
}

var daLum = false;

// Go to next page
function goNextChapter() {
    const aTags = $('.truyencv-read-block .truyencv-read-navigation:first-child > a');

    if (aTags.length == 3) { // Có chương tiếp theo => next
        aTags[aTags.length - 1].click();
    } else { // Chương đầu hoặc chương cuối
        if (aTags[0].getAttribute('role') === 'button') {  // Nếu là chương đầu => next
            aTags[1].click();
        } else { // Nếu là chương cuối => về chương đầu
            const currentUrl = window.location.href;
            const firstUrl = currentUrl.substring(0, currentUrl.lastIndexOf('chuong-')) + 'chuong-1';
            window.location.href = firstUrl;
        }
    }
}

// Auto lum do
function collectReward() {
    if ($('#lumdo').length > 0 || $('#lumDo').length > 0) {
        $('#lumdo').click();
        $('#lumDo').click();
        daLum = true;
        console.log("========================= " + formatDate(new Date()) + " =========================");
        console.log('Da lum duoc do');
    }
}

// Auto scroll and to to next page
function autoScroll() {
    window.scrollBy(0, 60);

    if ($(window).scrollTop() + $(window).height() > $('#js-truyencv-read-content').height() - 800) {
        setTimeout(function () {
            this.goNextChapter();
        }, 8000);
    }

    this.collectReward();
}

(function() {
    let scrollInterval;
    scrollInterval = setInterval(function() {
        if (!!document.getElementById('js-truyencv-read-content')) {
            this.autoScroll();
        } else {
            clearInterval(scrollInterval); // Xóa lặp
            // setTimeout(function(){ location.reload(); }, 900000); // 300000ms = 300s = 5min
        }
    }, 3000);
})();
