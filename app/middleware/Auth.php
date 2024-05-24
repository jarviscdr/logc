<?php

declare(strict_types=1);

namespace app\middleware;

use app\service\UserService;
use DateTimeImmutable;
use Lcobucci\Clock\FrozenClock;
use Lcobucci\JWT\JwtFacade;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint;
use ReflectionClass;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class Auth implements MiddlewareInterface {
    private Constraint\SignedWith $signedWith;

    public function __construct(private UserService $userService) {
        $jwtKey           = InMemory::base64Encoded(env('JWT_ACCESS_SECRET_KEY'));
        $this->signedWith = new Constraint\SignedWith(new Sha256(), $jwtKey);
    }

    public function process(Request $request, callable $next): Response {
        $ctlReference = new ReflectionClass($request->controller);
        $noNeedLogin  = $ctlReference->getProperty('noNeedLogin');
        if (in_array($request->action, $noNeedLogin->getDefaultValue())) {
            return $next($request);
        }

        $newAccessToken = '';
        $accessToken    = $request->header('access_token');
        $refreshToken   = $request->header('refresh_token');
        if (empty($accessToken) && empty($refreshToken)) {
            $accessToken  = $request->cookie('access_token');
            $refreshToken = $request->cookie('refresh_token');
        }

        if (empty($accessToken) && empty($refreshToken)) {
            BE('请登录', 401);
        }

        $accessTokenInfo = $this->parseAccessToken($accessToken);

        if (empty($accessTokenInfo)) {
            $refreshTokenInfo = $this->parseRefreshToken($refreshToken);
            if (empty($refreshTokenInfo)) {
                BE('登录过期', 401);
            }
            $newAccessToken = $this->userService->generateToken($refreshTokenInfo->claims()->get('user'), '+ 10 minutes');

        }

        /**
         * @var Response
         */
        $response = $next($request);

        if (! empty($newAccessToken)) {
            $response->withHeader('access_token', $newAccessToken->toString())
                ->cookie('access_token', $newAccessToken->toString());
        }

        return $response;
    }

    // 解析授权token
    private function parseAccessToken($accessToken) {
        try {
            $accessTokenInfo = (new JwtFacade())->parse(
                $accessToken, $this->signedWith,
                new Constraint\StrictValidAt(
                    new FrozenClock(new DateTimeImmutable())
                ),
                new Constraint\PermittedFor('auth')
            );

            return $accessTokenInfo;
        } catch (\Throwable $th) {
            return false;
        }
    }

    // 解析刷新token
    private function parseRefreshToken($refreshToken) {
        try {
            $refreshTokenInfo = (new JwtFacade())->parse(
                $refreshToken, $this->signedWith,
                new Constraint\StrictValidAt(
                    new FrozenClock(new DateTimeImmutable())
                ),
                new Constraint\PermittedFor('refresh')
            );

            return $refreshTokenInfo;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
