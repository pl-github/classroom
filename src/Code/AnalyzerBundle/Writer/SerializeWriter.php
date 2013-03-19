<?php

namespace Code\AnalyzerBundle\Writer;

use Code\AnalyzerBundle\Model\ResultModel;

class SerializeWriter implements WriterInterface
{
    /**
     * @inheritDoc
     */
    public function write(ResultModel $result, $targetDir, $baseFilename)
    {
        $filename = $targetDir . '/' . $baseFilename. '.serialized';
        $data = serialize($result);

        file_put_contents($filename, $data);

        return $filename;
    }
}
