<?php

namespace backend\onepay;
use backend\onepay\Util;

class VerifyVpcSecureHash
{
    private $merchantId;
    private $merchantAccessCode;
    private $merchantHashCode;
    public function __construct($merchantId, $merchantAccessCode, $merchantHashCode)
    {
        $this->merchantId = $merchantId;
        $this->merchantAccessCode = $merchantAccessCode;
        $this->merchantHashCode = $merchantHashCode;
    }
    function onePayVerifySecureHash($url)
    {
        $parts = parse_url($url);
        $queriesString = $parts['query'];
        $queriesParamMap = [];
        parse_str($queriesString, $queriesParamMap);
        $merchantHash = $queriesParamMap['vpc_SecureHash'];
        ksort($queriesParamMap);
        $util = new Util();
        $stringToHash = $util->generateStringToHash($queriesParamMap);
        $onePayHash = $util->generateSecureHash($stringToHash, $this->merchantHashCode);
        if ($merchantHash != $onePayHash) {
            return false;
        } else {
            return true;
        }
    }
}