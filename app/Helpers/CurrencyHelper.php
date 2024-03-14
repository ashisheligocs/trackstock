<?php

function formatCurrency($value)
{
    $currencyPosition = config('config.currencyPosition');
    $currencySymbol = config('config.currencySymbol');
    if ($currencyPosition == 'left') {
        return $currencySymbol . number_format($value, 2, '.', ',');
    }else{
        return number_format($value, 2, '.', ',') .  $currencySymbol;
    }
}

//Slab wise gst percentage (SGST + CGST)
function gstSlab($range) {
    if ($range <= 7500) {
        return 12;
    }

    return 18;
}
