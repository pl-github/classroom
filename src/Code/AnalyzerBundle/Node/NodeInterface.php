<?php

namespace Code\AnalyzerBundle\Node;

interface NodeInterface
{
    /*+
     * Return id
     *
     * @return string
     */
    public function getId();

    /*+
     * Return name
     *
     * @return string
     */
    public function getName();

    /**
     * Return full qualified name
     *
     * @return string
     */
    public function getFullQualifiedName();

    /**
     * Return parent node reference
     *
     * @return NodeReference
     */
    public function getParentNodeReference();
}
