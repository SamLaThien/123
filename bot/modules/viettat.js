export function viettat(vp) {
    vp = vp
        .replace('hạ phẩm', 'hp')
        .replace('trung phẩm', 'tp')
        .replace('thượng phẩm', 'thp')
        .replace('cực phẩm', 'cp');
 
    switch (vp) {
        case 's1':
        case 'mtds1':
        case 'mtđs1':
            return 'Mảnh Tàn Đồ S1';
        case 's2':
        case 'mtds2':
        case 'mtđs2':
            return 'Mảnh Tàn Đồ S2';
        case 's3':
        case 'mtds3':
        case 'mtđs3':
            return 'Mảnh Tàn Đồ S3';
        case 's4':
        case 'mtds4':
        case 'mtđs4':
            return 'Mảnh Tàn Đồ S4';
        case 's5':
        case 'mtds5':
        case 'mtđs5':
            return 'Mảnh Tàn Đồ S5';
        case 'nhũ tp':
        case 'ntlntp':
        case 'ntln tp':
            return 'Ngọc Tủy Linh Nhũ TP';
        case 'nhũ thp':
        case 'ntlnthp':
        case 'ntln thp':
            return 'Ngọc Tủy Linh Nhũ THP';
        case 'nhũ hp':
        case 'ntlnhp':
        case 'ntln hp':
            return 'Ngọc Tủy Linh Nhũ HP';
        case 'nhũ cp':
        case 'ntlncp':
        case 'ntln cp':
            return 'Ngọc Tủy Linh Nhũ CP';
        case 'ttd':
        case 'tẩy':
        case 'tẩy tủy':
            return 'tẩy tủy đan';
        case 'ktt':
            return 'khúc tương tư';
        case 'tcd':
        case 'trúc':
        case 'trúc cơ':
            return 'trúc cơ đan';
        case 'lthp':
        case 'lt hp':
            return 'Linh Thạch HP';
        case 'bnd':
        case 'bổ nguyên':
        case 'bổ':
            return 'bổ nguyên đan';
        case 'lttp':
        case 'lt tp':
            return 'Linh Thạch TP';
        case 'ltthp':
        case 'lt thp':
            return 'Linh Thạch THP';
        case 'bad':
        case 'bổ':
        case 'bổ anh':
            return 'bổ anh đan';
        case 'hnd':
        case 'hóa':
        case 'hóa nguyên':
            return 'hóa nguyên đan';
        case 'lt cp':
        case 'ltcp':
            return 'Linh Thạch CP';
        case 'ltd':
        case 'luyện':
        case 'luyện thần':
            return 'luyện thần đan';
        case 'tlhp':
        case 'tl hp':
            return 'Tinh Linh HP';
        case 'tltp':
        case 'tl tp':
            return 'Tinh Linh TP';
        case 'tlthp':
        case 'tl thp':
            return 'Tinh Linh THP';
        case 'tlcp':
        case 'tl cp':
            return 'Tinh Linh CP';
        case 'lbbh':
            return 'lệnh bài bang hội';
        case 'btt':
            return 'bánh trung thu';
        case 'hkd':
            return 'huyết khí đan';
        case 'htd':
            return 'huyết tinh đan';
        case 'bhd':
            return 'bổ huyết đan';
        case 'tlc':
        case 'tị':
        case 'tị lôi':
            return 'tị lôi châu';
        case 'hltr':
        case 'hộ':
        case 'hộ linh':
            return 'hộ linh trận';
        case 'hkl':
        case 'hoàng':
        case 'hoàng kim':
            return 'hoàng kim lệnh';
        case 'hnc':
        case 'hỏa':
        case 'hỏa ngọc':
            return 'hỏa ngọc châu';
        case 'tnc':
        case 'thải':
        case 'thải ngọc':
            return 'thải ngọc châu';
        case 'snc':
        case 'sa':
        case 'sa ngọc':
            return 'sa ngọc châu';
        case 'tlt':
        case 'tán':
        case 'tán lôi':
            return 'tán lôi trận';
        case 'dgt':
        case 'đê':
        case 'đê giai':
            return 'đê giai thuẫn';
        case 'ttd2':
        case 'thanh':
        case 'thanh tâm':
            return 'thanh tâm đan';
        case 'đgt':
            return 'đê giai thuẫn';
        case 'ntc':
            return 'ngọc tủy chi';
        case 'ttt':
        case 'trích':
            return 'trích tinh thảo';
        case 'hlt':
            return 'hóa long thảo';
        case 'tlq':
            return 'thiên linh quả';
        case 'tnt':
        case 'thiên':
        case 'thiên nguyên':
            return 'thiên nguyên thảo';
        case 'ukt':
        case 'uẩn':
        case 'uẩn kim':
            return 'uẩn kim thảo';
        case 'att':
        case 'anh':
        case 'anh tâm':
            return 'anh tâm thảo';
        case 'hnt':
            return 'hóa nguyên thảo';
        case 'ltt':
            return 'luyện thần thảo';
        case 'kt':
        case 'kim':
            return 'kim thuổng';
        case 'tthp':
        case 'tt hp':
            return 'Tử Tinh HP';
        case 'gn':
            return 'gạo nếp';
        case 'dx':
            return 'đậu xanh';
        case 'đx':
            return 'đậu xanh';
        case 'ld':
            return 'lá dong';
        case 'gv':
            return 'gia vị';
        case 'tm':
            return 'thịt mỡ';
        case 'ls':
            return 'lông sói';
        case 'tcdp':
            return 'trúc cơ đan phương';
        case 'hkdp':
            return 'huyết khí đan phương';
        case 'ttdp':
            return 'tẩy tủy đan phương';
        case 'tld':
            return 'thiên linh đỉnh';
        case 'tttp':
        case 'tt tp':
            return 'Tử Tinh TP';
        case 'ndc1':
        case 'nd c1':
        case 'nd1':
            return 'Nội Đan C1';
        case 'ndc2':
        case 'nd c2':
        case 'nd2':
            return 'Nội Đan C2';
        case 'ndc3':
        case 'nd c3':
        case 'nd3':
            return 'Nội Đan C3';
        case 'ndc4':
        case 'nd c4':
        case 'nd4':
            return 'Nội Đan C4';
        case 'ndc5':
        case 'nd c5':
        case 'nd5':
            return 'Nội Đan C5';
        case 'ndc6':
        case 'nd c6':
        case 'nd6':
            return 'Nội Đan C6';
        case 'ndc7':
        case 'nd c7':
        case 'nd7':
            return 'Nội Đan C7';
        case 'ndc8':
        case 'nd c8':
        case 'nd8':
            return 'Nội Đan C8';
        case 'ndc9':
        case 'nd c9':
        case 'nd9':
            return 'Nội Đan C9';
        case 'ndc10':
        case 'nd c10':
        case 'nd10':
            return 'Nội Đan C10';
        case 'ndc11':
        case 'nd c11':
        case 'nd11':
            return 'Nội Đan C11';
        case 'ndc12':
        case 'nd c12':
        case 'nd12':
            return 'Nội Đan C12';
        case 'cs':
            return 'chu sa';
        case 'pmtc1':
        case 'pmt c1':
        case 'pmt1':
            return 'Phụ Ma Thạch C1';
        case 'pmtc2':
        case 'pmt c2':
        case 'pmt2':
            return 'Phụ Ma Thạch C2';
        case 'pmtc3':
        case 'pmt c3':
        case 'pmt3':
            return 'Phụ Ma Thạch C3';
        case 'pmtc4':
        case 'pmt c4':
        case 'pmt4':
            return 'Phụ Ma Thạch C4';
        case 'pmtc5':
        case 'pmt c5':
        case 'pmt5':
            return 'Phụ Ma Thạch C5';
        case 'pmtc6':
        case 'pmt c6':
        case 'pmt6':
            return 'Phụ Ma Thạch C6';
        case 'pmtc7':
        case 'pmt c7':
        case 'pmt7':
            return 'Phụ Ma Thạch C7';
        case 'pmtc8':
        case 'pmt c8':
        case 'pmt8':
            return 'Phụ Ma Thạch C8';
        case 'pmtc9':
        case 'pmt c9':
        case 'pmt9':
            return 'Phụ Ma Thạch C9';
        case 'bhn':
        case 'băng':
        case 'băng hỏa':
            return 'băng hỏa ngọc';
        case 'utd':
            return 'uẩn thiên đan';
        case 'utdp':
            return 'uẩn thiên đan phương';
        case 'bndp':
            return 'bổ nguyên đan phương';
        case 'htdp':
            return 'huyết tinh đan phương';
        case 'ttthp':
        case 'tt thp':
            return 'Tử Tinh THP';
        case 'qb':
            return 'quyên bạch';
        case 'vtd':
            return 'vạn thú đỉnh';
        case 'ttv':
            return 'túi trữ vật';
        case 'cptq':
            return 'công pháp tàn quyển';
        case 'ptd':
            return 'phá thiên đan';
        case 'thl':
            return 'tạo hóa lô';
        case 'badp':
            return 'bổ anh đan phương';
        case 'ttcp':
        case 'tt cp':
            return 'Tử Tinh CP';
        case 'lb':
            return 'la bàn';
        case 'qg':
            return 'quy giáp';
        case 'ngtc':
        case 'ngọc giản':
            return 'ngọc giản truyền công';
        case 'ctd':
        case 'cố':
        case 'cố thần':
            return 'cố thần đan';
        case 'hndp':
            return 'hóa nguyên đan phương';
        case 'tdl':
            return 'thiên địa lô';
        case 'vthp':
            return 'Vẫn Thiết HP';
        case 'bhdp':
            return 'bổ huyết đan phương';
        case 'ntd':
            return 'ngưng thần đan';
        case 'vttp':
            return 'Vẫn Thiết TP';
        case 'tkl':
        case 'thiên':
        case 'thiên kiếm':
            return 'thiên kiếm lệnh';
        case 'hmd':
        case 'hắc ma':
            return 'hắc ma đỉnh';
        case 'nsvn':
        case 'nhân':
        case 'nhân sâm':
            return 'nhân sâm vạn năm';
        case 'dtd':
            return 'dung thần đan';
        case 'vtthp':
            return 'Vẫn Thiết THP';
        case 'hdc':
        case 'hoán':
        case 'hoán diện':
            return 'hoán diện châu';
        case 'vtcp':
            return 'Vẫn Thiết CP';
        case 'ltdp':
            return 'luyện thần đan phương';
        case 'kttt':
            return 'khai thiên thần thạch';
        case 'vht':
            return 'vĩnh hằng thạch';
        case 'htb':
        case 'hòa':
        case 'hòa thị':
            return 'hòa thị bích';
        case 'tbb':
        case 'tụ':
        case 'tụ bảo':
            return 'tụ bảo bài';
        case 'ktc':
        case 'kim':
        case 'kim thủ':
            return 'kim thủ chỉ';
        case 'htt':
        case 'huyết':
        case 'huyết trích':
            return 'huyết trích thạch';
        case 'nbt':
        case 'nguyệt':
        case 'nguyệt bạch':
            return 'nguyệt bạch thạch';
        case 'hpt':
        case 'hổ':
        case 'hổ phách':
            return 'hổ phách thạch';
        case 'hdt':
        case 'hắc':
        case 'hắc diệu':
            return 'hắc diệu thạch';
        case 'hht':
        case 'hấp':
        case 'hấp huyết':
            return 'hấp huyết thạch';
        case 'bdtc':
            return 'bất diết thiên công';
        case 'ntls':
        case 'ngọc':
        case 'ngọc tuyết':
            return 'Ngọc Tuyết Linh Sâm';
        case 'thll':
            return 'Tuyết Hải Lam Lăng';
        case 'vtlb':
            return 'Vô Thiên Lăng Ba';
        case 'dmc':
        case 'dạ':
        case 'dạ minh':
            return 'Dạ Minh Châu';
        case 'đào':
        case 'bdq':
            return 'Bàn Đào Quả';
        case 'bồ đề':
        case 'bdq2':
            return 'Bồ Đề Quả';
        case 'tpb':
        case 'túi phân':
            return 'Túi Phân Bón';
        case 'tsv':
        case 'túi':
        case 'túi sủng':
            return 'Túi Sủng Vật';
        case 'tta':
            return 'túi thức ăn';
        case 'bt':
        case 'bái':
            return 'bái thiếp';
	    case 'tgct':
        case 'thời':
        case 'thủy':
	        return 'thời gian chi thủy';
	    case 'hkct':
        case 'hư không':
        case 'hư':
	        return 'hư không chi thạch';
        case 'lkq':
            return 'Luyện Khí Quyết';
        case 'vcmk':
            return 'Vô Cực Ma Kinh';
        case 'hhthp':
            return 'Hồng Hoang Thạch HP';
        case 'hhttp':
            return 'Hồng Hoang Thạch TP';
        case 'hhtthp':
            return 'Hồng Hoang Thạch THP';
        case 'tpl':
            return 'Tiên Phủ Lệnh';
        case 'tnl':
            return 'Thần Nông Lệnh';
        case 'ccl':
            return 'Cổ Chiến Lệnh';
        case 'uhd':
            return 'Uẩn Huyết Đan';
        case 'vkd':
            return 'Vận Khí Đan';
        case 'hhd':
        case 'hồi':
            return 'hồi huyết đan';
        case 'tbds':
            return 'tàng bảo đồ siêu';
        case 'ndq':
        case 'ngô':
        case 'ngô đồng':
            return 'ngô đồng quả';
        case 'dhd':
        case 'đhđ':
        case 'độ':
            return 'độ hư đan';
        case 'dld':
        case 'đlđ':
        case 'đại':
            return 'đại linh đan';
        case 'tdd':
        case 'tdđ':
        case 'thánh':
        case 'thánh diệu':
            return 'thánh diệu đỉnh';
        case 'tnd':
        case 'tnđ':
        case 'thần':
        case 'thần nông':
            return 'thần nông đỉnh';
        default:
            return vp;
    }

    return vp;
}

export function cap(x, sup = true) {
    while (x.includes('_')) {
        x = x.replace('_', ' ');
    }

    let y = '';

    for (let loop = 0; loop < x.split(' ').length; loop++) {
        let string = x.split(' ')[loop].toLowerCase();
        if (string == 'hp' || 
            string == 'tp' || 
            string == 'thp' || 
            string == 'cp' || 
            string == 'c1' || 
            string == 'c2' || 
            string == 'c3' || 
            string == 'c4' || 
            string == 'c5' || 
            string == 'c6' || 
            string == 'c7' || 
            string == 'c8' || 
            string == 'c9' || 
            string == 'c10' || 
            string == 'c11' || 
            string == 'c12' || 
            string == '+1' || 
            string == '+2' || 
            string == '+3' || 
            string == '+4' || 
            string == '+5' || 
            string == '+6' || 
            string == '+7' || 
            string == 's1' || 
            string == 's2' || 
            string == 's3' || 
            string == 's4' || 
            string == 's5') {
            //y = y + ' [sup]' + string.toUpperCase() + '[/sup]';
            if (sup) {
                y = y + ' [sup]' + string.toUpperCase() + '[/sup]';
            } else {
                y = y + ' ' + string.toUpperCase();
            }
        } else {
            if (string == 'hp,' || 
                string == 'tp,' || 
                string == 'thp,' || 
                string == 'cp,' ||
                string == 'c1,' || 
                string == 'c2,' || 
                string == 'c3,' || 
                string == 'c4,' || 
                string == 'c5,' || 
                string == 'c6,' || 
                string == 'c7,' || 
                string == 'c8,' || 
                string == 'c9,' || 
                string == 'c10,' || 
                string == 'c11,' || 
                string == 'c12,' || 
                string == '+1,' || 
                string == '+2,' || 
                string == '+3,' || 
                string == '+4,' || 
                string == '+5,' || 
                string == '+6,' || 
                string == '+7,' || 
                string == 's1,' || 
                string == 's2,' || 
                string == 's3,' || 
                string == 's4,' || 
                string == 's5,') {
                //y = y + ' [sup]' + string.toUpperCase().split(0, string.length - 1) + '[/sup],';
                if (sup) {
                    y = y + ' [sup]' + string.toUpperCase().split(0, string.length - 1) + '[/sup],';
                } else {
                    y = y + ' ' + string.toUpperCase().split(0, string.length - 1) + ',';
                }
            } else {
                y = y + ' ' + string.charAt(0).toUpperCase() + string.slice(1);
            }
        }

    }

    // const r = y.slice(1);
    // if (r == "Lượt Quay") {
    //     return "[color=#00cc99]Lượt Quay[/color]" ;
    // }
    //
    // return r;
    return y.slice(1);
}

/*export function cap(x) {
    while (x.includes('_')) {
        x = x.replace('_', ' ');
    }

    let y = '';

    for (let loop = 0; loop < x.split(' ').length; loop++) {
        let string = x.split(' ')[loop].toLowerCase();

        if (string == 'hp' ||
            string == 'tp' ||
            string == 'thp' ||
            string == 'cp' ||
            string == 'qp' ||
            string == 'mp' ||
            string == 'c1' ||
            string == 'c2' ||
            string == 'c3' ||
            string == 'c4' ||
            string == 'c5' ||
            string == 'c6' ||
            string == 'c7' ||
            string == 'c8' ||
            string == 'c9' ||
            string == 'c10' ||
            string == 'c11' ||
            string == 'c12' ||
            string == '+1' ||
            string == '+2' ||
            string == '+3' ||
            string == '+4' ||
            string == '+5' ||
            string == '+6' ||
            string == '+7' ||
            string == 's1' ||
            string == 's2' ||
            string == 's3' ||
            string == 's4' ||
            string == 's5'
           ) {
            y = y + ' [sup]' + string.toUpperCase() + '[/sup]';
        } else {
            if (string == 'hp,' ||
                string == 'tp,' ||
                string == 'thp,' ||
                string == 'cp,' ||
                string == 'c1,' ||
                string == 'c2,' ||
                string == 'c3,' ||
                string == 'c4,' ||
                string == 'c5,' ||
                string == 'c6,' ||
                string == 'c7,' ||
                string == 'c8,' ||
                string == 'c9,' ||
                string == 'c10,' ||
                string == 'c11,' ||
                string == 'c12,' ||
                string == '+1,' ||
                string == '+2,' ||
                string == '+3,' ||
                string == '+4,' ||
                string == '+5,' ||
                string == '+6,' ||
                string == '+7,' ||
                string == 's1,' ||
                string == 's2,' ||
                string == 's3,' ||
                string == 's4,' ||
                string == 's5,'
               ) {
                y = y + ' [sup]' + string.toUpperCase().split(0, string.length - 1) + '[/sup],';
            } else {
                if (x == 'tàn phách kim' && string == 'kim' ||
                    x == 'tàn phách mộc' && string == 'mộc' ||
                    x == 'tàn phách thủy' && string == 'thủy' ||
                    x == 'tàn phách hỏa' && string == 'hỏa' ||
                    x == 'tàn phách thổ' && string == 'thổ'
                   ) {
                    //y = y + ' [sup]' + string.toUpperCase() + '[/sup]';
                    y = y + ' [sup]' + string.charAt(0).toUpperCase() + string.slice(1) + '[/sup]';
                } else if (x == 'tàn phách kim,' && string == 'kim,' ||
                           x == 'tàn phách mộc,' && string == 'mộc,' ||
                           x == 'tàn phách thủy,' && string == 'thủy,' ||
                           x == 'tàn phách hỏa,' && string == 'hỏa,' ||
                           x == 'tàn phách thổ,' && string == 'thổ,'
                          ) {
                    y = y + ' [sup]' + string.charAt(0).toUpperCase() + string.slice(1) + '[/sup],';
                } else {
                    y = y + ' ' + string.charAt(0).toUpperCase() + string.slice(1);
                }
            }
        }

    }

    return y.slice(1);
}*/
