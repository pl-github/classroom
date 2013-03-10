<?php

namespace Code\CopyPasteDetectionBundle\Phpcpd;

use Code\CopyPasteDetectionBundle\Phpcpd\Model\DuplicationModel;
use Code\CopyPasteDetectionBundle\Phpcpd\Model\FileModel;
use Code\CopyPasteDetectionBundle\Phpcpd\Model\PmdCpdModel;

class PhpcpdParser
{
    /**
     * Parse phpcpd file
     *
     * @param string $filename
     * @return PmdCpdModel
     */
    public function parse($filename)
    {
        $xml = simplexml_load_file($filename);

        $duplications = new PmdCpdModel();
        foreach ($xml->duplication as $duplicationNode)
        {
            $duplicationAttributes = $duplicationNode->attributes();

            $lines = (string)$duplicationAttributes['lines'];
            $tokens = (string)$duplicationAttributes['tokens'];

            $codefragment = (string)$duplicationNode->codefragment;

            $duplication = new DuplicationModel($lines, $tokens, $codefragment);

            foreach ($duplicationNode->file as $fileNode)
            {
                $fileAttributes = $fileNode->attributes();

                $path = (string)$fileAttributes['path'];
                $line = (string)$fileAttributes['line'];

                $duplication->addFile(new FileModel($path, $line));
            }

            $duplications->addDuplication($duplication);
        }

        return $duplications;
    }
}
