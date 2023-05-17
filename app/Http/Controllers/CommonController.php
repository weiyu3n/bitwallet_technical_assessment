<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class CommonController extends Controller
{
    /* -------------------------------------------------------------------------- */
    /*                                 Question 1                                 */
    /* -------------------------------------------------------------------------- */
    public function Q1() {

        $array = [1, 2, 3, 4, 5];
        $target = 6;
        $combinations = $this->findCombinations($array, $target);

        return $combinations;
    }

    private function findCombinations($array, $target) {
        $combinations = [];
        $subset = [];
        $startIndex = 0;
    
        $this->generateSubsets($array, $target, $startIndex, $subset, $combinations);
    
        return $combinations;
    }
    
    private function generateSubsets($array, $target, $startIndex, $subset, &$combinations) {
        if (array_sum($subset) === $target) {
            $combinations[] = $subset;
        }
    
        for ($i = $startIndex; $i < count($array); $i++) {
            $newSubset = $subset;
            $newSubset[] = $array[$i];
            $this->generateSubsets($array, $target, $i + 1, $newSubset, $combinations);
        }
    }

    /* -------------------------------------------------------------------------- */
    /*                                 Question 2                                 */
    /* -------------------------------------------------------------------------- */

    // i have tried to crawl data from website. But it seem like got some security checking cauesed 403 forbidden when cralling.
    // Therefore i tried search online, and found out they do have an API docs. Thus, i used it to finish the task below.
    // And i found out that Kraken do have their own code for the pairs.
    // So i have do a Q2 V2, to compare all the pair by the giving the pair.

    public function Q2() {
        $krakenData = $this->crawlKraken();
        $quoineData = $this->crawlQuoine();
        $compareData = [];     
        foreach($krakenData as $pair => $price) {
            if(!empty($quoineData[$pair])) {
                $compareData[$pair] = [
                    'Kraken' => $price,
                    'Quoine' => $quoineData[$pair]
                ];
            }
        }

        return $compareData;
    }

    private function crawlKraken() {
        $krakenUrl = 'https://api.kraken.com/0/public/Ticker';
        $krakenResponse = file_get_contents($krakenUrl);
        $krakenData = json_decode($krakenResponse, true);
        $result = [];
        foreach($krakenData['result'] as $code => $data) {
            $result[strtoupper($code)] = $data['c'][0];
        }
        return $result;
    }

    private function crawlQuoine() {
        $quoineUrl = 'https://api.liquid.com/products';
        $quoineResponse = file_get_contents($quoineUrl);
        $quoineData = json_decode($quoineResponse, true);
        $result = [];
        foreach($quoineData as $code => $data) {
            $result[strtoupper($data['currency_pair_code'])] = $data['last_traded_price'];
        }
        return $result;
    }

    /* -------------------------------------------------------------------------- */
    /*                                Question 2 v2                               */
    /* -------------------------------------------------------------------------- */
    function Q2pair($pair) {
        $pair = strtoupper($pair);
        $krakenUrl = 'https://api.kraken.com/0/public/Ticker?pair=' . $pair;
        $krakenResponse = file_get_contents($krakenUrl);
        $krakenData = json_decode($krakenResponse, true);
    
        $quoineUrl = 'https://api.liquid.com/products';
        $quoineResponse = file_get_contents($quoineUrl);
        $quoineData = json_decode($quoineResponse, true);
        foreach($quoineData as $code => $data) {
            if(strtoupper($data['currency_pair_code']) == $pair) {
                $quoineExchangeRate = $data['last_traded_price'];
                break;
            }
        }
        $krakenPairKey = array_keys($krakenData['result']);
        $krakenExchangeRate = $krakenData['result'][$krakenPairKey[0]]['c'][0];

        return ['Kraken' => $krakenExchangeRate, 'Quoine' => $quoineExchangeRate ?? 0];
    
    }

    /* -------------------------------------------------------------------------- */
    /*                                 Question 3                                 */
    /* -------------------------------------------------------------------------- */
    public function Q3() {

        $array = [10, 20, 30, 40, 50, 60, 70, 80, 90, 100];

        $array = [
            [30, 60, $this->calculateSum($array, 30, 60)],
            [1, 110, $this->calculateSum($array, 1, 110)],
            [41, 110, $this->calculateSum($array, 41, 110)],
        ];

        return $array;

    }

    private function calculateSum($array, $firstInt, $secondInt) {
        if ($firstInt <= 0 || $secondInt <= 0) {
            return -1;
        }
    
        if ($firstInt >= $secondInt) {
            return 0;
        }
    
        $sum = 0;
        $foundFirstInt = false;
    
        for ($i = 0; $i < count($array); $i++) {
            if ($array[$i] >= $firstInt) {
                $foundFirstInt = true;
            }
    
            if ($foundFirstInt) {
                $sum += $array[$i];
    
                if ($array[$i] == $secondInt) {
                    break;
                }
            }
        }
    
        if (!$foundFirstInt || $i == count($array) - 1) {
            $sum = 0;
        }
    
        return $sum;
    }
}