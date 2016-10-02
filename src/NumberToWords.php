<?php
namespace NumberToWords;

/**
 * Convert a number to words
 *
 * @author Mohammad Mehdi Habibi (mhabibi.org)
 * @link https://github.com/mhabibi/NumberToWords/
 */
class NumberToWords
{

    /**
     * Language code
     *
     * @var string
     */
    protected $language;

    /**
     * Language string variables
     *
     * @var array
     */
    protected $strings;

    /**
     * Get language and load strings
     *
     * @param string $language            
     *
     * @throws Exception
     */
    public function __construct($language = 'fa')
    {
        $languageFile = __DIR__ . '/languages/' . $language . '.php';
        if (! file_exists($languageFile)) {
            throw new \Exception('Language file not found!');
        }
        
        $this->strings = include $languageFile;
        $this->language = $language;
    }

    /**
     * Convert a number to words
     *
     * @param number $number            
     *
     * @return string
     */
    public function convert($number)
    {
        $prefix = '';
        if ($number[0] == '-') {
            $prefix = $this->getString('minus');
            $number = substr($number, 1);
        } elseif ($number[0] == '+') {
            $prefix = $this->getString('plus');
            $number = substr($number, 1);
        }
        list ($number, $result) = $this->converter($number);
        
        return $prefix . $result;
    }

    /**
     *
     * @param number $number            
     *
     * @return array
     */
    protected function converter($number)
    {
        $number = str_replace('/', '.', $number);
        if (strstr($number, '.')) {
            return $this->decimalConverter($number);
        }
        $suffix = $this->getString('suffix');
        
        // Convert to three-digit array
        $dividedArray = str_split($number, 3);
        $dividedCount = count($dividedArray);
        $tmpStringNumber = $number;
        for ($i = 0; $i < $dividedCount; $i ++) {
            $splitString[$i] = substr($tmpStringNumber, - 3);
            $tmpStringNumber = substr($tmpStringNumber, 0, strlen($tmpStringNumber) - 3);
        }
        
        // Convert each three-digit to words and add proper suffix
        $resultArray = array();
        for ($i = $dividedCount - 1; $i >= 0; $i --) {
            $threeDigitsString = '';
            if ($splitString[$i] != 000) {
                $threeDigitsString = $this->threeDigits($splitString[$i]);
                if ($suffix[$i * 3] != '') {
                    $threeDigitsString .= $suffix[$i * 3];
                }
            }
            if ($threeDigitsString) {
                $resultArray[] = $threeDigitsString;
            }
        }
        
        // Iterate over array and add separator
        $result = '';
        for ($i = 0; $i < count($resultArray); $i ++) {
            if ($result) {
                $result .= ltrim($this->getString('separator'));
            }
            $result .= $resultArray[$i];
        }
        
        return array(
            $number,
            $result
        );
    }

    /**
     *
     * @param number $number            
     *
     * @throws Exception
     * @return array
     */
    protected function decimalConverter($number)
    {
        $parts = explode('.', $number);
        if (count($parts) > 2) {
            throw new \Exception('Wrong Number');
        }
        
        // convert decimal part
        $decimal = $parts[1];
        $decimalSuffix = trim($this->converter(('1' . str_repeat('0', strlen($decimal))))[1]);
        $decimalLen = strlen($decimal);
        if (($decimalLen > 0 && $decimalLen <= 5) || (($decimalLen >= 9 && $decimalLen <= 11))) {
            $decimalSuffix .= $this->getString('decimal_suffix1');
        } else {
            $decimalSuffix .= $this->getString('decimal_suffix2');
        }
        list ($part1, $part1Converted) = $this->converter($parts[1]);
        $part1Converted .= ' ' . $decimalSuffix;
        
        list ($part0, $part0Converted) = $this->converter($parts[0]);
        if ($part0 > 0) {
            return array(
                $number,
                $part0Converted . $this->getString('decimal_separator') . $part1Converted
            );
        }
        return array(
            $number,
            $part1Converted
        );
    }

    /**
     *
     * @param number $number            
     *
     * @return string
     */
    protected function threeDigits($number)
    {
        $result = '';
        $number = (int) $number;
        $words = $this->getString('words');
        if ($number < 20) {
            $result .= $words[$number * 10];
        } else {
            $charArrayOfNumber = str_split($number);
            if (array_key_exists(($charArrayOfNumber[0] . strlen($number) - 1), $words)) {
                $result .= $words[($charArrayOfNumber[0] . strlen($number) - 1)];
            }
            $remaining = $number - ($charArrayOfNumber[0] * ($this->power((strlen($number) - 1))));
            if ($remaining > 0) {
                $result .= $this->getString('separator');
                $result .= $this->threeDigits($remaining);
            }
        }
        return $result;
    }

    /**
     *
     * @param string $name            
     *
     * @return string
     */
    protected function getString($name = null)
    {
        return $this->strings[$name];
    }

    /**
     *
     * @param number $number            
     *
     * @return number
     */
    protected function power($number)
    {
        $result = 1;
        for ($i = 0; $i < $number; $i ++) {
            $result = $result * 10;
        }
        return $result;
    }
}
