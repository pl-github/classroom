<?php

namespace Code\AnalyzerBundle\Loader;

use Code\AnalyzerBundle\Model\ResultModel;

class SerializeLoader implements LoaderInterface
{
    /**
     * @inheritDoc
     */
    public function load($filename)
    {
        $data = file_get_contents($filename);
        $result = unserialize($data);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function supports($filename)
    {
        return 'serialized' === pathinfo($filename, PATHINFO_EXTENSION);
    }
}
