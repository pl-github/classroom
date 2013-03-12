<?php
/**
 * Fmdb User Security Core User FOSUserBundle Provider
 *
 * @package   FMDB
 * @author    Marcus Stöhr <marcus.stoehr@filmmusik-info.de>
 * @copyright 2013 R&S Filmmusik Informations GbR (http://www.filmmusik-info.de)
 */
namespace Code\UserBundle\Security\Core\User;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;

class FOSUBUserProvider extends BaseClass
{
    /**
     * {@inheritDoc}
     */
    public function connect($user, UserResponseInterface $response)
    {
        // on connect - get the access token
        $serviceAccessTokenName = $response->getResourceOwner()->getName() . 'AccessToken';
        $serviceAccessTokenSetter = 'set' . ucfirst($serviceAccessTokenName);
var_dump($serviceAccessTokenName);
var_dump($serviceAccessTokenSetter);
        die;
        $user->$serviceAccessTokenSetter($response->getAccessToken());

        // you may want to get extra data... put code here.
        $this->userManager->updateUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $username = $response->getUsername();

        $responseProperty = $this->getProperty($response);
        $userFind = array($responseProperty => $username);

        $user = $this->userManager->findUserBy($userFind);

        // when the user is registrating
        if (null === $user) {
            $service = $response->getResourceOwner()->getName();

            // hand creation of user over to custom provider
            return $this->customProviders[$service]->handleResponse($response, $service);
        }

        // if user exists - go with the HWIOAuth way
        $user = parent::loadUserByOAuthUserResponse($response);

        $serviceName = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($serviceName) . 'AccessToken';

        // update access token
        $user->$setter($response->getAccessToken());

        return $user;
    }
}
