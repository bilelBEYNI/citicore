<?php
// src/Security/LoginFormAuthenticator.php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    private RouterInterface $router;
    private HttpClientInterface $httpClient;
    private string $recaptchaSecret;

    public function __construct(RouterInterface $router, HttpClientInterface $httpClient, string $recaptchaSecret)
    {
        $this->router          = $router;
        $this->httpClient      = $httpClient;
        $this->recaptchaSecret = $recaptchaSecret;
    }

    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'login'
            && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $data    = $request->request->get('login_form', []);
        $cin     = $data['cin'] ?? '';
        $passwd  = $data['password'] ?? '';
        $captcha = $request->request->get('g-recaptcha-response', '');

        // 1) Vérification reCAPTCHA **AVANT** toute validation des credentials
        if (empty($captcha) || !$this->verifyRecaptcha($captcha)) {
            throw new CustomUserMessageAuthenticationException('Veuillez valider le reCAPTCHA.');
        }

        // 2) Création du Passport (User + Password + CSRF)
        return new Passport(
            new UserBadge($cin),
            new PasswordCredentials($passwd),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): RedirectResponse
    {
        // Redirection selon rôle
        $roles = $token->getRoleNames();
        $route = in_array('ROLE_ADMIN', $roles, true)
            ? 'admin_dashboard'
            : (in_array('ROLE_PARTICIPANT', $roles, true) ? 'participant_dashboard' : 'home');

        // Si l'utilisateur tentait d'accéder à une page protégée
        if ($path = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($path);
        }

        return new RedirectResponse($this->router->generate($route));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->router->generate('login');
    }

    private function verifyRecaptcha(string $token): bool
    {
        $resp = $this->httpClient->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
            'body' => [
                'secret'   => $this->recaptchaSecret,
                'response' => $token,
            ],
        ]);
        $data = $resp->toArray();

        return !empty($data['success']) && $data['success'] === true;
    }
}