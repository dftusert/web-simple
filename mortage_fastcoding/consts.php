<?php
/* стандартизация входных данных для расчетов */
/* изменения здесь синхронизировать с consts.js */
/* передача из js в php */
const PARAM_TYPE = 'type';
const PARAM_REALTY = 'realty_value';
const PARAM_INITIAL = 'initial_value';
const PARAM_YEARS = 'years';
const PARAM_BONUS = 'bonus';

/* для стандартизации "выходных данных" ajax запроса */
/* изменения здесь синхронизировать с consts.js */
/* передача из php в js */
const BLOCK_DATA_DELIMITER = '<br />';
const DATA_DELIMITER = ':';
const CREDIT_SUM_ID = 'credit_sum';
const EVERYMONTH_PAY_ID = 'everymonth_pay';
const REQUIRED_INCOME_ID = 'required_income';
const INTEREST_RATE_ID = 'interest_rate';
const ERROR_ID = 'error';

/* минимальный размер кредита, выдаваемый Сбербанком */
const MINIMUM_CREDIT_SUM = 300000;

/* без учета скидок и пр., процентная ставка в процентах */
const TYPES_INTEREST_RATES = array (
    "APARTMENT_NEW_BUILDING" => 11.2,        /* покупка квартиры в новостройке */
    "SECONDARY_APARTMENT" => 11.7,           /* покупка вторичной квартиры */
    "REF_MOR_ANOTHER_BANK" => 10.9,          /* рефинансирование ипотеки другого банка */
    "MILITARY_MORTGAGE" => 9.5,              /* военная ипотека */
    "MORT_STATE_SUPP_WITH_CHILD" => 6.0,     /* ипотека с господдержкой для семей с детьми */
    "HOUSE_BUILDING" => 12.2,                /* строительство дома */
    'FOREIGN_HOUSE_BUYING' => 11.7,          /* покупка загородного дома */
    'UNG_MOR_CREDIT' => 13.7,                /* нецелевой ипотечный кредит */
    'GARAGE_BUYING' => 12.2                  /* приобретение машиноместа или гаража */
);

/* тип, с, по, коэффициент ежемесячный платеж / доход */
/* Пример:
 * Покупка вторичной квартиры, коэффициент ежемесячный платеж / необходимый доход равен:
 * 0,8, если 1 <= срок кредита <= 5
 * тогда в TYPES_YEARS_COEFF будет массив
 * array('SECONDARY_APARTMENT', 1, 5, 0.8),
 * 
 * Если условие для срока кредита похоже на от 13 лет и более, то массив из предыдущего примера будет
 * array('SECONDARY_APARTMENT', 13, -1, 0.8),
 * 
 * Для формирования полностью этих значений
 * 1 - указать цель кредита (тип), указать срок кредита, поделить: ежемесячный платеж / необходимый доход (на сайте ipoteka.*)
 * 2 - узнать в каком диапазоне срока кредита коэффициент постоянен
 * 3 - записать array('A', B, C, D),
 * где A - цель кредита, взятая из массива TYPES_INTEREST_RATES
 *     B - начало отрезка срока кредита
 *     С - конец отрезка срока кредита или -1 если срок кредита может быть до бесконечности
 *     D - отношение: ежемесячный платеж / необходимый доход
*/
const TYPES_YEARS_COEFF = array (
    array('APARTMENT_NEW_BUILDING', 1, 4, 0.8),
    array('APARTMENT_NEW_BUILDING', 5, 12, 0.7),
    array('APARTMENT_NEW_BUILDING', 13, -1, 0.6),

    array('SECONDARY_APARTMENT', 1, 5, 0.8),
    array('SECONDARY_APARTMENT', 6, 12, 0.7),
    array('SECONDARY_APARTMENT', 13, -1, 0.699990933), /* пример правильного коэффициента, расчитанного с сайта */
    
    array('HOUSE_BUILDING', 1, 5, 0.8),
    array('HOUSE_BUILDING', 6, 13, 0.7),
    array('HOUSE_BUILDING', 14, -1, 0.6),
    
    array('FOREIGN_HOUSE_BUYING', 1, 5, 0.8),
    array('FOREIGN_HOUSE_BUYING', 6, 12, 0.7),
    array('FOREIGN_HOUSE_BUYING', 13, -1, 0.6),
    
    array('UNG_MOR_CREDIT', 1, 4, 0.8),
    array('UNG_MOR_CREDIT', 5, 11, 0.7),
    array('UNG_MOR_CREDIT', 12, -1, 0.6),
    
    array('REF_MOR_ANOTHER_BANK', 1, 4, 0.8),
    array('REF_MOR_ANOTHER_BANK', 5, 9, 0.7),
    array('REF_MOR_ANOTHER_BANK', 10, -1, 0.6),
    
    array('MORT_STATE_SUPP_WITH_CHILD', 1, 3, 0.8),
    array('MORT_STATE_SUPP_WITH_CHILD', 4, 7, 0.7),
    array('MORT_STATE_SUPP_WITH_CHILD', 8, -1, 0.6),
    
    array('GARAGE_BUYING', 1, 4, 0.8),
    array('GARAGE_BUYING', 5, 9, 0.7),
    array('GARAGE_BUYING', 10, -1, 0.6),
    
    array('MILITARY_MORTGAGE', 1, 4, 0.8),
    array('MILITARY_MORTGAGE', 5, 9, 0.7),
    array('MILITARY_MORTGAGE', 10, -1, 0.6)
);
?>