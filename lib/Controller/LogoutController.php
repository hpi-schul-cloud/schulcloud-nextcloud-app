<?php

namespace OCA\Schulcloud\Controller;

use Error;
use OC\Authentication\Events\AppPasswordCreatedEvent;
use OC\Authentication\Token\IProvider;
use OC\Authentication\Token\IToken;
use OCA\SocialLogin\Service\ProviderService;
use OCP\AppFramework\ApiController;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\OCS\OCSForbiddenException;
use OCP\Authentication\Exceptions\CredentialsUnavailableException;
use OCP\Authentication\Exceptions\PasswordUnavailableException;
use OCP\Authentication\LoginCredentials\IStore;
use OCP\IRequest;
use OCP\IUserSession;
use OCP\Security\ISecureRandom;
use Psr\Log\LoggerInterface;

class LogoutController extends ApiController
{

    private IUserSession $session;

    private ISecureRandom $random;

    private $tokenProvider;

    private IStore $credentialStore;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct($appName, IRequest $request, IUserSession $session, ProviderService $providerService, ISecureRandom $random,
                                IProvider $tokenProvider,
                                IStore $credentialStore,
                                LoggerInterface $logger)
    {
        parent::__construct(
            $appName,
            $request,
            'GET',
            '',
            1728000);
        $this->session = $session;
        $this->providerService = $providerService;
        $this->random = $random;
        $this->tokenProvider = $tokenProvider;
        $this->credentialStore = $credentialStore;
        $this->logger = $logger;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function logout(): array
    {
        $this->session->logout();
        return array('message' => 'Logout successful');
    }

    /**
     * @PublicPage
     * @NoAdminRequired
     * @NoCSRFRequired
     * @UseSession
     */
    public function getAppPassword(): DataResponse {
        if (!$this->session->isLoggedIn()) {
            $this->providerService->handleCustom('custom_oidc', 'SchulcloudNextcloud');
            $this->logger->info('No session.');
        }

        $this->logger->error('LoggedIn user: ', ['data' => $this->session->getUser()->getUID()]);
        try {
            $credentials = $this->credentialStore->getLoginCredentials();
        } catch (CredentialsUnavailableException $e) {
            throw new OCSForbiddenException();
        }

        try {
            $password = $credentials->getPassword();
        } catch (PasswordUnavailableException $e) {
            $password = null;
        }

        $userAgent = $this->request->getHeader('USER_AGENT');

        $token = $this->random->generate(72, ISecureRandom::CHAR_UPPER . ISecureRandom::CHAR_LOWER . ISecureRandom::CHAR_DIGITS);

        $generatedToken = $this->tokenProvider->generateToken(
            $token,
            $credentials->getUID(),
            $credentials->getLoginName(),
            $password,
            $userAgent,
            IToken::PERMANENT_TOKEN
        );
        $this->logger->error('Token: ', ['data' => $generatedToken]);

        // dispatch event

        return new DataResponse(['apppassword' => $token]);
    }
}
