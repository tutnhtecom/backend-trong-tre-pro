<?php
namespace backend\onepay;
class Util
{

    public function generateStringToHash($array)
    {
        $stringToHash = "";
        foreach ($array as $key => $value) {
            $pref4 = substr($key, 0, 4);
            $pref5 = substr($key, 0, 5);
            if ($pref4 == "vpc_" || $pref5 == "user_") {
                if ($key != "vpc_SecureHashType" && $key != "vpc_SecureHash") {
                    if (strlen($value) > 0) {
                        if (strlen($stringToHash) > 0) {
                            $stringToHash = $stringToHash . "&";
                        }
                        $stringToHash = $stringToHash . $key . "=" . $value;
                    }
                }
            }
        }
        return $stringToHash;
    }

    public function generateSecureHash($stringToHash, $merchantHashCode)
    {
        $merchantHex = cryptoJsHexParse($merchantHashCode);
        return cryptoJsHmacSha256($stringToHash, $merchantHex);
    }

    public function sendHttpRequest($apiUrl)
    {
        print_r($apiUrl);
        // Create a stream
        $curl = curl_init($apiUrl);
        curl_setopt($curl, CURLOPT_HEADER, true);  // we want headers
        curl_exec($curl);
        curl_close($curl);
    }

    public function createRequestSignatureITA(
        $method,
        $uri,
        $httpHeaders,
        $signedHeaderNames,
        $merchantId,
        $merchantHashCode
    ) {
        $created = time();
        $lowercaseHeaders = array_change_key_case($httpHeaders, CASE_LOWER);
        $lowercaseHeaders['(request-target)'] = strtolower($method) . ' ' . $uri;
        $lowercaseHeaders['(created)'] = $created;

        $signingString = '';

        $headerNames = '';
        foreach ($signedHeaderNames as $element) {
            $headerName = $element;
            if (!array_key_exists($headerName, $lowercaseHeaders)) {
                throw new Exception("MissingRequiredHeaderException: $headerName");
            }
            if ($signingString !== '')
                $signingString .= "\n";
            $signingString .= $headerName . ': ' . $lowercaseHeaders[$headerName];

            if ($headerNames !== '')
                $headerNames .= ' ';
            $headerNames .= $headerName;
        }

        echo "signingString=" . $signingString . "\n";

        $hmacKey = hex2bin($merchantHashCode);
        $signature = base64_encode(hash_hmac('sha512', $signingString, $hmacKey, true));
        $signingAlgorithm = 'hs2019';
        return 'algorithm="' . $signingAlgorithm . '", keyId="' . $merchantId . '", headers="' . $headerNames . '", created=' . $created . ', signature="' . $signature . '"';
    }

    public function sendHttpGetWithHeader($url, $headers)
    {
        // Khởi tạo một yêu cầu cURL
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Thực thi yêu cầu cURL và lấy dữ liệu trả về
        $response = curl_exec($curl);

        // Kiểm tra nếu có lỗi xảy ra
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            echo $error_msg;
        }

        // Đóng phiên cURL
        curl_close($curl);

        // Trả về dữ liệu phản hồi
        echo $response;
    }

    public function sendHttpPost($url, $data)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);

        if ($response === false) {
            echo 'Error: ' . curl_error($curl);
        } else {
            echo 'Response: ' . $response;
        }

        curl_close($curl);
    }
}

function cryptoJsHmacSha256(string $data, string $key): string
{
    $sign = hash_hmac("sha256", $data, $key, false);
    return strtoupper($sign);
}

function cryptoJsHexParse(string $hexString): string
{
    return hex2bin($hexString);
}
