<?php

namespace Displore\Core\Logging;

class Logbook
{
    /**
     * Monolog levels.
     * 
     * @var array
     */
    public $levels = [
        'DEBUG',
        'INFO',
        'NOTICE',
        'WARNING',
        'ERROR',
        'CRITICAL',
        'ALERT',
        'EMERGENCY',
    ];

    /**
     * Preg_match_all() header pattern.
     * 
     * @var string
     */
    public $patternAll = "/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\].*/";

    /**
     * Preg_match() datetime pattern.
     * 
     * @var string
     */
    public $patternSingle = "/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]/";

    /**
     * Get an array of all of the log files.
     * 
     * @param string $path
     *
     * @return array
     */
    public function getLogFiles($path)
    {
        $list = glob($path.'/*.log');

        if ($list === false) {
            return [];
        }

        foreach ($list as $file) {
            $logFiles[] = file_get_contents($file);
        }

        return $logFiles;
    }

    /**
     * Compile an array of log files into a collection.
     * 
     * @param array $logFiles
     *
     * @return \Illuminate\Support\Collection
     */
    public function compile($logFiles)
    {
        $logFiles = implode(' ', $logFiles);

        preg_match_all($this->patternAll, $logFiles, $compiledLogs);

        return $this->collectAndTransform($compiledLogs[0]);
    }

    /**
     * Create a collection from an array and transform the log headers.
     * 
     * @param array $compiledLogs
     *
     * @return \Illuminate\Support\Collection
     */
    public function collectAndTransform($compiledLogs)
    {
        $collection = collect($compiledLogs);

        $collection->transform(function ($item, $key) {

            $old = $item;

            preg_match($this->patternSingle, $old, $output);

            foreach ($this->levels as $level) {
                if (strpos($old, '.'.$level)) {
                    $logLevel = $level;
                    break;
                }
            }

            $new['key'] = $key;
            $new['level'] = $logLevel;
            $new['datetime'] = str_replace(['[', ']'], '', $output[0]);
            $new['message'] = str_replace(["local.{$level}:", $output[0]], '', $old);

            return $new;
        });

        return $collection;
    }
}
