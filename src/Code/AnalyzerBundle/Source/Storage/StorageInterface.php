<?php

namespace Code\AnalyzerBundle\Source\Storage;

interface StorageInterface
{
    /**
     * Return content
     *
     * @return string
     */
    public function getContent();


    /**
     * Return content as array
     * @return array
     */
    public function getContentAsArray();
}
