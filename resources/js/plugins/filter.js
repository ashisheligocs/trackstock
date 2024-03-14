import Vue from 'vue'
import store from '../store'

// return formatted number
Vue.filter('numberFormat', function (number) {
    if (number) {
        return Number(number).toFixed(2);
    }
});

// return short text
Vue.filter('shortText', function (str) {
    if (str) {
        return (str.length > 30) ? str.substr(0, 30 - 1) + ' ...' : str;
    }
});


Vue.filter('indformate', function (str) {
    if (str) {
        return parseFloat(str).toLocaleString('en-IN')
    }
});

Vue.filter('twoPoints', function (str) {
    if (str) {
        return parseFloat(Number(str)?.toFixed(2))?.toLocaleString('en-IN');
    }
})

Vue.filter('twoPointTextBox', function (str) {
    if (str) {
        return parseFloat(Number(str)?.toFixed(2));
    }
})

// return formatted currency
Vue.filter('withCurrency', function (number) {
    let currency = store.state.operations.appInfo.currency
    let newNumber = parseFloat(Number(number).toFixed(2))
    if (newNumber >= 0) {
        newNumber = newNumber.toLocaleString('en-IN');
        return currency.position == 'left' ? currency.symbol + newNumber : newNumber + currency.symbol
    } else {
        return currency.position == 'left' ? `(${currency.symbol}${Math.abs(newNumber)})` : `(${Math.abs(newNumber)}${currency.symbol})`
    }
})
Vue.filter('defaultwithCurrency', function (number) {
    let currency = store.state.operations.appInfo.currency;
    return currency.position == 'left' ? `(${currency.symbol})` : `(${currency.symbol})`
})


// return formatted currency
Vue.filter('withAbsoluteCurrency', function (number) {
    let currency = store.state.operations.appInfo.currency
    let newNumber = parseFloat((Number(number)).toFixed(2));
    if (newNumber >= 0) {
        newNumber = newNumber.toLocaleString('en-IN')
        return currency.position == 'left' ? currency.symbol + newNumber : newNumber + currency.symbol
    } else {
        return currency.position == 'left' ? '-' + currency.symbol + Math.abs(newNumber) : Math.abs(newNumber) + currency.symbol
    }
    return
})

Vue.filter('forBalanceSheetCurrency', function (number) {
    let currency = store.state.operations.appInfo.currency
    let newNumber = parseFloat((Number(number)).toFixed(2))
    if (newNumber >= 0) {
        newNumber = newNumber.toLocaleString('en-IN')
        return currency.position == 'left' ? currency.symbol + newNumber : newNumber + currency.symbol
    } else {
        return currency.position == 'left' ? '(' + currency.symbol + Math.abs(newNumber) + ')': '(' + Math.abs(newNumber) + currency.symbol + ')'
    }
    return
})

Vue.filter('forBalanceSheetCurrencyDecimalOnly', function (number) {
    let currency = store.state.operations.appInfo.currency
    let newNumber = parseFloat((Number(number)).toFixed(2))
    if (newNumber >= 0) {
        newNumber = newNumber.toLocaleString('en-IN')
        return newNumber;
        // return currency.position == 'left' ? currency.symbol + newNumber : newNumber + currency.symbol
    } else {
        let numberGet = Math.abs(newNumber);
        return '(' + numberGet.toLocaleString('en-IN') + ')'
        // return currency.position == 'left' ? '(' + currency.symbol + Math.abs(newNumber) + ')': '(' + Math.abs(newNumber) + currency.symbol + ')'
    }
    return
})


// return code with prefix
Vue.filter('withPrefix', function (code, prefix) {
    return prefix + '-' + code;
})


Vue.filter('capitalize', function (value) {
    if (!value) return '';
    return value.charAt(0).toUpperCase() + value.slice(1);
});
