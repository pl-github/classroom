<?php

namespace Code\PhpAnalyzerBundle;

class ReflectionService
{
    /**
     * Get class name for file
     *
     * @param string $fileName
     * @return string
     */
    public function getClassNameForFile($fileName)
    {
        $tokens = token_get_all(file_get_contents($fileName));

        $namespaceParts = array();
        $classnameParts = array();

        $tokenCount = count($tokens);
        for ($i = 0; $i < $tokenCount; $i++) {
            if ($tokens[$i][0] === T_NAMESPACE) {
                // skip whitespace
                $i += 2;
                while ($tokens[$i] !== ';') {
                    $namespaceParts[] = $tokens[$i][1];
                    $i++;
                }
            }
            if ($tokens[$i][0] === T_CLASS) {
                // skip whitespace
                $i += 2;
                while ($tokens[$i][0] !== T_WHITESPACE) {
                    $classnameParts[] = $tokens[$i][1];
                    $i++;
                }
            }
            if ($tokens[$i][0] === T_INTERFACE) {
                // skip whitespace
                $i += 2;
                while ($tokens[$i][0] !== T_WHITESPACE) {
                    $classnameParts[] = $tokens[$i][1];
                    $i++;
                }
            }
            if ($tokens[$i][0] === T_TRAIT) {
                // skip whitespace
                $i += 2;
                while ($tokens[$i][0] !== T_WHITESPACE) {
                    $classnameParts[] = $tokens[$i][1];
                    $i++;
                }
            }
        }

        $classname = '';
        if ($namespaceParts) {
            $classname = implode('', $namespaceParts) . '\\';
        }
        $classname .= implode('', $classnameParts);

        return $classname;
    }
}
