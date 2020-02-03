<?php

namespace App\MajorSystem;

use Illuminate\Support\Facades\Storage;

class MajorSystem
{
    const WORD_LIST_PATH = 'storage/app/words.txt';

    /**
     * Create a new MajorSystem instance.
     *
     * @param string $word_list
     * @return void
     */
    public function __construct(string $word_list = '')
    {
        if (!empty($word_list)) {
            $this->wordList = $word_list;
        }
    }

    /**
     * Generator that returns a batch of lines from the word list file.
     *
     * @param int $batchSize
     * @return string
     *
     * @throws \Exception
     */
    public function getWordBatch(int $batchSize = 1000)
    {
        $batchValue = '';
        if (file_exists(self::WORD_LIST_PATH)) {
            $handle = fopen(self::WORD_LIST_PATH, 'r');
            $count = 0;
            while (!feof($handle)) {
                $nextLine = fgets($handle);
                $count++;
                if ($nextLine === false || $count === $batchSize) {
                    $count = 0;
                    if (is_string($nextLine)) {
                        $batchValue .= $nextLine;
                    }
                    yield $batchValue;
                    $batchValue = '';
                } else {
                    $batchValue .= $nextLine;
                }
            }
            fclose($handle);
        } else {
            throw new \Exception('Word list file not found.');
        }
    }

    /**
     * Search the word list for matches and return an array of results.
     *
     * @param string $pattern
     * @return array
     */
    public function searchWordList(string $pattern)
    {
        $results = [];
        $reader = $this->getWordBatch();
        foreach ($reader as $batch) {
            preg_match_all($pattern, $batch, $matches);
            $results = array_merge($results, $matches[0]);
        }
        return $results;
    }

    public function foo()
    {
        return ['larry' => 'gary'];
    }
}