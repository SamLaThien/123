function tangNganLuong(member) {
    (function($) {
        var txtMoney = 300;
        var data = 'btntangNganLuong=1&txtMoney=' + txtMoney + '&member=' + member;
        $("#btntangNganLuong").attr("disabled", true);
        $("#swapmoney_loading").css("visibility", "inherit");
        $.ajax({
            url: site + '/index.php',
            type: "POST",
            data: data,
            cache: false,
            success: function(html) {
                if (html == 1) {
                    console.log("Chúc mừng bạn đã tặng ngân lượng thành công!");
                } else if (html) {
                    console.log(html);
                } else {
                    console.log('Sorry, unexpected error. Please try again later.');
                }
            }
        });
        return false;
    }
    )(jQuery);
}
