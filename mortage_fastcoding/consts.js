/* стандартизация входных данных для расчетов */
/* изменения здесь синхронизировать с consts.php */
/* передача из js в php */
const PARAM_TYPE = 'type';
const PARAM_REALTY = 'realty_value';
const PARAM_INITIAL = 'initial_value';
const PARAM_YEARS = 'years';
const PARAM_BONUS = 'bonus';

/* для стандартизации "выходных данных" ajax запроса */
/* изменения здесь синхронизировать с consts.php */
const BLOCK_DATA_DELIMITER = '<br />';
const DATA_DELIMITER = ':';
const CREDIT_SUM_ID = 'credit_sum';
const EVERYMONTH_PAY_ID = 'everymonth_pay';
const REQUIRED_INCOME_ID = 'required_income';
const INTEREST_RATE_ID = 'interest_rate';
const ERROR_ID = 'error';

/* минимальные и максимальные значения для слайдеров, корректирующих величины
стоимости недвижимости, срока кредита и т.п. */
const SLIDER_FIRST_MIN_VALUE = 300000;
const SLIDER_FIRST_MAX_VALUE = 30000000;
const SLIDER_SECOND_MIN_VALUE = 45000;
const SLIDER_SECOND_MAX_VALUE = 10000000;
const SLIDER_THIRD_MIN_VALUE = 1;
const SLIDER_THIRD_MAX_VALUE = 30;