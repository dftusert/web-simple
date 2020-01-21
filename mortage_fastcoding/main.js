/* Инициализация */
window.onload=function () {
    var credit_dest = document.getElementById('credit_dest');
    close_all_blocks();
    open_block(credit_dest);

    credit_dest.onchange = function () { 
        clear_page();

        close_all_blocks();
        open_block(credit_dest);

        var credit_dest = document.getElementById('credit_dest');
        var type = credit_dest.options[credit_dest.selectedIndex].value;
        if(type == 'REF_MOR_ANOTHER_BANK')
            document.getElementById('first_pmt').innerText = 'Остаток долга';
        else if (type == 'UNG_MOR_CREDIT')
            document.getElementById('first_pmt').innerText = 'Сумма кредита';
        else
            document.getElementById('first_pmt').innerText = 'Первоначальный взнос';

        sync_chanel();
    }

    var slider1 = document.getElementById('slider1');
    var slider2 = document.getElementById('slider2');
    var slider3 = document.getElementById('slider3');
    slider1.min = SLIDER_FIRST_MIN_VALUE; slider1.max = SLIDER_FIRST_MAX_VALUE;
    slider2.min = SLIDER_SECOND_MIN_VALUE; slider2.max = SLIDER_SECOND_MAX_VALUE;
    slider3.min = SLIDER_THIRD_MIN_VALUE; slider3.max = SLIDER_THIRD_MAX_VALUE;

    document.getElementById('slider1_min_description').innerText = modify_money(SLIDER_FIRST_MIN_VALUE);
    document.getElementById('slider1_max_description').innerText = modify_money(SLIDER_FIRST_MAX_VALUE);
    document.getElementById('slider2_min_description').innerText = modify_money(SLIDER_SECOND_MIN_VALUE);
    document.getElementById('slider2_max_description').innerText = modify_money(SLIDER_SECOND_MAX_VALUE);
    document.getElementById('slider3_min_description').innerText = beautify_age(SLIDER_THIRD_MIN_VALUE.toString());
    document.getElementById('slider3_max_description').innerText = beautify_age(SLIDER_THIRD_MAX_VALUE.toString());

    slider1.oninput = function() {
        clear_page();
        var out = document.getElementById('input1');
        out.value = beautify_money(this.value);
        sync_chanel();
    }

    slider2.oninput = function() {
        clear_page();
        var out = document.getElementById('input2');
        out.value = beautify_money(this.value);
        sync_chanel();
    }

    slider3.oninput = function() {
        clear_page();
        var out = document.getElementById('input3');
        out.value = beautify_age(this.value);
        sync_chanel();
    }

    var inp1 = document.getElementById('input1');
    var inp2 = document.getElementById('input2');
    var inp3 = document.getElementById('input3');

    slider1.value = 6500000;
    slider2.value = 2500000;
    slider3.value = 20;
    inp1.value = beautify_money(slider1.value);
    inp2.value = beautify_money(slider2.value);
    inp3.value = beautify_age(slider3.value);

    inp1.oninput = function() { clear_page(); oninpFloat(this, document.getElementById('slider1')); sync_chanel(); }
    inp2.oninput = function() { clear_page(); oninpFloat(this, document.getElementById('slider2')); sync_chanel(); }
    inp3.oninput = function() { clear_page(); oninpInt(this, document.getElementById('slider3')); sync_chanel(); }
   
    var tbl_shower = document.getElementById('tbl_shower');
    tbl_shower.onclick = function () { create_table (); }
    sync_chanel();
}

/* ajax-запрос на получение расчетов: сумма кредита, ежемесячный платеж, необходимый платеж, процентная ставка */
function sync_chanel() {
    // GET-запрос, src - к php-скрипту + параметр=значение
    var src = "index.php?";
    var credit_dest = document.getElementById('credit_dest');
    src += PARAM_TYPE + '=' + credit_dest.options[credit_dest.selectedIndex].value;
    src += '&' + PARAM_REALTY + '=' + to_float_strictly(document.getElementById('input1').value);
    src += '&' + PARAM_INITIAL + '=' + to_float_strictly(document.getElementById('input2').value);
    src += '&' + PARAM_YEARS + '=' + to_int_strictly(document.getElementById('input3').value);
    src += '&' + PARAM_BONUS + '=' + get_all_credit_bonus();

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var err_block = document.getElementById('error');
            var data_block = document.getElementById('data');

            if(data_block.className == 'hidden')
                data_block.className = 'visible';

            if(err_block.className == 'visible')
                err_block.className = 'hidden';

            var received = this.responseText.split(BLOCK_DATA_DELIMITER);
            var data;

            var credit_dest = document.getElementById('credit_dest');
            var type = credit_dest.options[credit_dest.selectedIndex].value;

            var cred_sum = document.getElementById('cred_sum');
            var credit_sum = document.getElementById('credit_sum');
            var month_pay = document.getElementById('month_pay');
            var everymonth_pay = document.getElementById('everymonth_pay');
            var req_inc = document.getElementById('req_inc');
            var required_income = document.getElementById('required_income');

            credit_sum.className = 'calc_data_left';
            everymonth_pay.className = 'calc_data_right';
            required_income.className = 'calc_data_left';
            cred_sum.className = '';
            month_pay.className = '';
            req_inc.className = '';

            if(type == 'REF_MOR_ANOTHER_BANK') {
                credit_sum.className = 'hidden';
                cred_sum.className = 'hidden';
            }

            if(type == 'MILITARY_MORTGAGE') {
                required_income.className = 'hidden';
                req_inc.className = 'hidden';
                everymonth_pay.className = 'hidden';
                month_pay.className = 'hidden';
            }

            // анализ полученных значений
            for (var i = 0; i < received.length; ++i) {
                data = received[i].split(DATA_DELIMITER);
                switch (data[0]) {
                    case CREDIT_SUM_ID: document.getElementById(CREDIT_SUM_ID).innerText = beautify_money(data[1]); break;
                    case EVERYMONTH_PAY_ID: document.getElementById(EVERYMONTH_PAY_ID).innerText = beautify_money(data[1]); break;
                    case REQUIRED_INCOME_ID: document.getElementById(REQUIRED_INCOME_ID).innerText = beautify_money(data[1]); break;
                    case INTEREST_RATE_ID: document.getElementById(INTEREST_RATE_ID).innerText = data[1] + ' %'; break;
                    case ERROR_ID:
                        data_block.className = 'hidden';
                        err_block.className = 'visible';
                        err_block.innerText = data[1]; break;
                }
            }
        }
    };

    // отправка и т.п.
    xmlhttp.open("GET", src, true);
    xmlhttp.send();
}

// вызывается при изменении поля ввода для денег (чисел с плавающей точкой)
function oninpFloat (input, slider) {
    var pos = input.selectionStart;
    input.value = to_float_not_strictly(input.value);

    var inpval = input.value;
    // убираем точку в конце если есть
    if(inpval[inpval.length - 1] == '.')
        inpval = inpval.substr(0, inpval.length - 1);
    
    inpval = parseFloat(inpval);
    var minval = parseFloat(slider.min);
    var maxval = parseFloat(slider.max);
        
    if(minval > inpval)
        slider.value = minval;
    else if(maxval < inpval)
        slider.value = maxval;
    else
        slider.value = inpval;

    // преобразование к нужному виду, установка курсора в pos
    input.value = beautify_money(input.value);
    input.setSelectionRange(pos, pos);
}

// вызывается при изменении поля ввода для лет
function oninpInt (input, slider) {
    var pos = input.selectionStart;

    input.value = to_int_strictly(input.value);
    var inpval = input.value;
    inpval = parseInt(inpval);
    var minval = parseInt(slider.min);
    var maxval = parseInt(slider.max);
        
    if(minval > inpval)
        slider.value = minval;
    else if(maxval < inpval)
        slider.value = maxval;
    else
        slider.value = inpval;

    input.value = beautify_age(input.value);
    input.setSelectionRange(pos, pos);
}

// оставляем точку в конце числа если есть
function to_float_not_strictly (val) {
    var value = val.replace(/[^\d.,]/g, '');
    var index = value.search(/\./);
    if (index > 0)
        return value.substr(0, index+1).replace(/[^\d.,]/g, '') + value.substr(index, value.length).replace(/[^\d,]/g, '');
    else
        return value.replace(/[^\d]/g, '');
}

// строгое преобразование
function to_float_strictly (val) {
    var value = val.replace(/[^\d.,]/g, '');
    var index = value.search(/\./);

    if(index > 0 && index != value.length - 1)
        return value.substr(0, index).replace(/[^\d.,]/g, '') + '.' + value.substr(index, value.length).replace(/[^\d,]/g, '');
    else
        return value.replace(/[^\d,]/g, '');
}

// строгое преобразование
function to_int_strictly (val) {
    return val.replace(/[^\d]/g, '');
}

// добавление пробелов и символа валюты
function beautify_money (money_value) {
    var beautify_value = '';
    var len;
    if(money_value.search(/\./) != -1)
        len = money_value.search(/\./);
    else
        len = money_value.length;

    for(var i = 0; i < len; ++i) {
        if ((len - i - 1) % 3 == 0 && i != len - 1)
            beautify_value += money_value[i] + ' ';
        else
            beautify_value += money_value[i];
    }

    beautify_value += money_value.substr(len, money_value.length);
    return beautify_value +  ' ₽';
}

// добавление к году пояснения
function beautify_age (age_value) {
    if(age_value.length == 1 && age_value[0] == '1')
        return age_value + ' год';
    else if (age_value.length == 1 && age_value[0] < '5')
        return age_value + ' года';
    else if (age_value.length > 1 && age_value[0] != '1' && age_value[age_value.length - 1] == '1')
        return age_value + ' год';
    else if (age_value.length > 1 && age_value[0] != '1' && age_value[age_value.length - 1] < '5' && age_value[age_value.length - 1] > '1')
        return age_value + ' года';
    return age_value + ' лет';
}

// создание или удаление таблицы платежей
function create_table () {
    if(document.getElementById('credit_data') != null) {
        clear_credit_data();
        return;
    }

    const MONTHS = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май',
                    'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь',
                    'Ноябрь', 'Декабрь'];

    var container = document.getElementById('tbl_container');
    var table = document.createElement('table');
    table.setAttribute('id', 'credit_data');
    var d = new Date();
    
    var year = parseInt(d.getFullYear());
    var month_id = parseInt(d.getMonth());

    var credit_sum = parseFloat(to_float_strictly(document.getElementById(CREDIT_SUM_ID).innerText));
    var everymonth_pay = parseFloat(to_float_strictly(document.getElementById(EVERYMONTH_PAY_ID).innerText));
    var interest_rate = parseFloat(to_float_strictly(document.getElementById(INTEREST_RATE_ID).innerText));

    interest_rate /= 100*12;

    var row = table.insertRow(0);
    row.insertCell(0).innerText = 'Месяц';
    row.insertCell(1).innerText = 'Год';
    row.insertCell(2).innerText = 'Платеж';
    row.insertCell(3).innerText = 'Проценты';
    row.insertCell(4).innerText = 'Основной долг';
    row.insertCell(5).innerText = 'Остаток долга';

    var i = 1, prcnt, base_crd, prcnt_sum = 0;
    while (credit_sum > 0) {
        row = table.insertRow(i);
        prcnt = Math.round(credit_sum*interest_rate + 0.5);
        base_crd = everymonth_pay - prcnt;
        if(credit_sum - base_crd < 0) {
            everymonth_pay = credit_sum + prcnt;
            base_crd = credit_sum;
            credit_sum = 0;
        }
        else
            credit_sum -= base_crd;

            ++month_id;
            if(month_id == 12) {
                month_id = 0;
                ++year;
            }

            prcnt_sum += prcnt;
            row.insertCell(0).innerText = MONTHS[month_id];
            row.insertCell(1).innerText = year;
            row.insertCell(2).innerText = beautify_money(everymonth_pay.toString());
            row.insertCell(3).innerText = beautify_money(prcnt.toString());
            row.insertCell(4).innerText = beautify_money(base_crd.toString());
            row.insertCell(5).innerText = beautify_money(credit_sum.toString());
        ++i;
    }

    row = table.insertRow(0);
    var cell = row.insertCell(0);
    cell.setAttribute('colspan', 6);
    cell.innerText = 'Переплата: ' + beautify_money(prcnt_sum.toString());
    container.appendChild(table);
    container.className = 'tbl';
}

// очистка таблицы платежей
function clear_credit_data () {
    var table = document.getElementById('credit_data');
    var parent = document.getElementById('tbl_container');
    if(table != null) {
        parent.removeChild(table);
        parent.className = 'hidden';
    }
}

// очистка страницы от созданных элементов
function clear_page () {
    clear_credit_data();
}

// 30000 -> 30 тыс., 1000000000 -> 1000 млн.
function modify_money (val) {
    val = parseFloat(val);
    if(val < 1000)
        return val.toString() + ' ₽'
    if(val / 1000 < 1000)
        return (val / 1000).toString() + ' тыс. ₽';
    return (val / 1000000).toString() + ' млн. ₽';
}

// для скрытия/показа блоков дополнительных снижений процентной ставки
function hide_show_if (checkbox, id) {
    if(checkbox.checked) {
        document.getElementById(id).className = 'hidden';
        document.getElementById(id).getElementsByClassName('check_box')[0].checked = true;
    }
    else {
        document.getElementById(id).className = 'bonus_data';
        document.getElementById(id).getElementsByClassName('check_box')[0].checked = false;
    }

    sync_chanel();
}

// закрывает все блоки 
function close_all_blocks() {
    var blocks_holder = document.getElementById('blocks_holder');
    var childrens = blocks_holder.children;
    
    for(var i = 0; i < childrens.length; ++i)
        childrens[i].className = 'hidden';
}

function open_block (credit_dest) {

    close_all_blocks();
    var credit_dest = document.getElementById('credit_dest');
    var type = credit_dest.options[credit_dest.selectedIndex].value;

    switch(type) {
        case 'SECONDARY_APARTMENT':  document.getElementById('sec_apartment_id').className = ''; break;
        case 'APARTMENT_NEW_BUILDING': document.getElementById('new_building_apartment_id').className = ''; break;
        case 'REF_MOR_ANOTHER_BANK': document.getElementById('refin_another_bank_id').className = ''; break;
        case 'HOUSE_BUILDING': document.getElementById('house_building_id').className = ''; break;
        case 'FOREIGN_HOUSE_BUYING': document.getElementById('foreign_house_id').className = ''; break;
        case 'UNG_MOR_CREDIT': document.getElementById('um_credit_id').className = ''; break;
        case 'GARAGE_BUYING': document.getElementById('garage_buying_id').className = ''; break;
    }
}

function get_all_credit_bonus () {
    var bonus = 0.0;
    var blocks_holder = document.getElementById('blocks_holder');
    var childrens = blocks_holder.children;
    
    var trs, tds;
    for(var i = 0; i < childrens.length; ++i) {
        if(childrens[i].className != 'hidden') {
            trs = childrens[i].children[0].children[0].children;
            for(var j = 0; j < trs.length; ++j) {
                if(trs[j].getElementsByClassName('checkbox_holder')[0].children[0].checked)
                bonus += parseFloat(trs[j].getElementsByClassName('bonus_value')[0].children[0].value);
            }
        }
    }

    return bonus;
}

function check_years_le_snc(checkbox, years) {
    if(checkbox.checked && parseInt(document.getElementById('input3').value) > years) {
        checkbox.checked = false;
    }
    sync_chanel();
}