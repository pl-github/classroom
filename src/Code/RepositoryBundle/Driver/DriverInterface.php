<?php

namespace Code\RepositoryBundle\Driver;

interface DriverInterface
{
    /**
     * Checkout
     *
     * @param string $checkoutDirectory
     */
    public function checkout($checkoutDirectory);

    /**
     * Return latest commit ID
     *
     * @return string
     */
    public function getLastCommit($checkoutDirectory);
}
