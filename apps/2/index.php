<?php
$sum = 0;
for ($i=1; $i<=10000; $i++)
{
    if( checkNumber((string)$i) )
    {
        continue;
    }
    $sum += $i;
}
print_r($sum);
function checkNumber(string $number) : bool
{
    $arrayDigits = str_split($number);
    if (count($arrayDigits) < 3)
    {
        return false;
    }
    for ($j=0 ; $j < count($arrayDigits) - 2 ; $j++)
    {
        if ( $arrayDigits[$j] < $arrayDigits[$j+1] && $arrayDigits[$j+1] < $arrayDigits[$j+2] )
        {
            return true;
        }
    }
    return false;
}