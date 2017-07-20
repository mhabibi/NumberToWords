<?php

namespace NumberToWords;

class NumberToWordsTest extends \PHPUnit_Framework_TestCase
{
    private $numberToWords;

    public function setUp()
    {
        $this->numberToWords = new NumberToWords();
    }

    /**
     * @dataProvider convertProvider
     */
    public function testConvert($number, $words)
    {
        $result = $this->numberToWords->convert($number);
        $this->assertEquals($words, $result);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Language file not found!
     */
    public function testInvalidLanguage()
    {
        new NumberToWords('00');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Wrong Number
     */
    public function testInvalidNumber()
    {
        $this->numberToWords->convert('1.2.3');
    }

    public function convertProvider()
    {
        return [
            [
                'number' => '0.123',
                'words'  => 'صد و بیست و سه یک هزارم',
            ],
            [
                'number' => '-123.45',
                'words'  => 'منفی صد و بیست و سه ممیز چهل و پنج صدم',
            ],
            [
                'number' => '+123123.400005',
                'words'  => 'مثبت صد و بیست و سه هزار و صد و بیست و سه ممیز چهارصد هزار و پنج یک میلیونیوم',
            ],
        ];
    }
}
