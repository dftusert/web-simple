<?php
require "consts.php";

function beautify_money ($money_value) {
    $money_value = (string) $money_value;
    $beautify_value = '';
    
    $len = strpos($money_value, '.');
    if($len === false)
        $len = strlen($money_value);

    for($i = 0; $i < $len; $i++) {
        if (($len - $i - 1) % 3 == 0 && $i !== $len - 1)
            $beautify_value .= $money_value{$i} . ' ';
        else
            $beautify_value .= $money_value{$i};
    }

    $beautify_value .= substr($money_value, $len);
    return ($beautify_value . ' ₽');
}

if(!isset($_GET[PARAM_TYPE]) || $_GET[PARAM_TYPE] == NULL ||
   !isset($_GET[PARAM_REALTY]) || $_GET[PARAM_REALTY] == NULL ||
   !isset($_GET[PARAM_INITIAL]) || $_GET[PARAM_INITIAL] == NULL ||
   !isset($_GET[PARAM_YEARS]) || $_GET[PARAM_YEARS] == NULL ||
   !isset($_GET[PARAM_BONUS]) || $_GET[PARAM_BONUS] == NULL)
        die(ERROR_ID . DATA_DELIMITER . 'Не задан один или несколько параметров' . BLOCK_DATA_DELIMITER);

$type = htmlspecialchars($_GET[PARAM_TYPE]);
$realty_value = (float) htmlspecialchars($_GET[PARAM_REALTY]);
$initial_value = (float) htmlspecialchars($_GET[PARAM_INITIAL]);
$years = (float) htmlspecialchars($_GET[PARAM_YEARS]);
$bonus = (float) htmlspecialchars($_GET[PARAM_BONUS]);

if(!array_key_exists($type, TYPES_INTEREST_RATES))
    die (ERROR_ID . DATA_DELIMITER . 'Неверно задан тип' . BLOCK_DATA_DELIMITER);

$coeff = -1.0;
for($i = 0; $i < count(TYPES_YEARS_COEFF); $i++) {
    if(TYPES_YEARS_COEFF[$i][0] == $type &&
       $years >= TYPES_YEARS_COEFF[$i][1] &&
       ($years <= TYPES_YEARS_COEFF[$i][2] || TYPES_YEARS_COEFF[$i][2] == -1)) {
           $coeff = TYPES_YEARS_COEFF[$i][3];
           break;
       }
}

if($coeff == -1.0)
    die (ERROR_ID . DATA_DELIMITER . 'Коэффициент платеж / доход не найден' . BLOCK_DATA_DELIMITER);

$interest_rate = TYPES_INTEREST_RATES[$type] - $bonus;
$proc = $interest_rate / (12*100);
$month = $years*12;

$credit_sum = $realty_value - $initial_value;

if($type == 'REF_MOR_ANOTHER_BANK' || $type == 'UNG_MOR_CREDIT')
    $credit_sum = $initial_value;

$everymonth_pay = $credit_sum*$proc*pow(1+$proc, $month)/(pow(1+$proc, $month) - 1);

if ($credit_sum <= (float) MINIMUM_CREDIT_SUM)
    die (ERROR_ID . DATA_DELIMITER . 'Сбербанк не выдаёт ипотечные кредиты меньше '. beautify_money(MINIMUM_CREDIT_SUM));


echo CREDIT_SUM_ID . DATA_DELIMITER . $credit_sum . BLOCK_DATA_DELIMITER;
echo EVERYMONTH_PAY_ID . DATA_DELIMITER . round($everymonth_pay + 0.5) . BLOCK_DATA_DELIMITER;
echo REQUIRED_INCOME_ID . DATA_DELIMITER . round($everymonth_pay / $coeff + 0.5) . BLOCK_DATA_DELIMITER;
echo INTEREST_RATE_ID . DATA_DELIMITER . $interest_rate . BLOCK_DATA_DELIMITER;
?>