<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Search\Model;

use Magento\Search\Api\SynonymAnalyzerInterface;
use Magento\Search\Model\SynonymsReader;

class SynonymAnalyzer implements SynonymAnalyzerInterface
{
    /**
     * @var SynonymReader $synReaderModel
     */
    protected $synReaderModel;

    /**
     * Constructor
     *
     * @param SynonymReader $synReader
     */
    public function __construct(SynonymReader $synReader)
    {
        $this->synReaderModel = $synReader;
    }

    /**
     * Returns an array of arrays consisting of the synonyms found for each word in the input phrase
     *
     * @param string $phrase
     * @return array
     */
    public function getSynonymsForPhrase($phrase)
    {
        $synGroups = [];

        if (empty($phrase)) {
            return $synGroups;
        }

        // strip off all the white spaces, comma, semicolons, and other such
        // "non-word" characters. Then implode it into a single string using white space as delimiter
        //$words = preg_split('/\W+/', strtolower($phrase), -1, PREG_SPLIT_NO_EMPTY);
        $words = preg_split(
            '/[~`!@#$%^&*()_+={}\[\]:"\',\s\.<>?\/\;\\\]+/',
            strtolower($phrase),
            -1,
            PREG_SPLIT_NO_EMPTY
        );
        $phrase = implode(' ', $words);

        $rows = $this->synReaderModel->loadByPhrase($phrase)->getData();
        $synonyms = [];
        foreach ($rows as $row) {
            $synonyms [] = $row['synonyms'];
        }

        // Go through every returned record looking for presence of the actual phrase. If there were no matching
        // records found in DB then create a new entry for it in the returned array
        foreach ($words as $w) {
            $position = $this->findInArray($w, $synonyms);
            if ($position !== false) {
                $synGroups[] = explode(',', $synonyms[$position]);
            } else {
                // No synonyms were found. Return the original word in this position
                $synGroups[] = [$w];
            }
        }
        return $synGroups;
    }

    /**
     * Helper method to find the presence of $word in $wordsArray. If found, the particular array index is returned.
     * Otherwise false will be returned.
     *
     * @param string $word
     * @param $array $wordsArray
     * @return boolean | int
     */
    private function findInArray($word, $wordsArray)
    {
        if (empty($wordsArray)) {
            return false;
        }
        $position = 0;
        foreach ($wordsArray as $wordsLine) {
            $pattern = '/^' . $word . ',|,' . $word . ',|,' . $word . '$/';
            $rv = preg_match($pattern, $wordsLine);
            if ($rv != 0) {
                return $position;
            }
            $position++;
        }
        return false;
    }
}
