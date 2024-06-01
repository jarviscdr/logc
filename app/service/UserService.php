<?php

declare(strict_types=1);

namespace app\service;

use app\model\User;
use DateTimeImmutable;
use DateTimeZone;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\JwtFacade;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

class UserService extends BaseService {
    public DateTimeZone $dateTimeZone;

    public InMemory $jwtKey;

    public function __construct(public JwtFacade $jwtFacade) {
        $this->dateTimeZone = new \DateTimeZone('Asia/Shanghai');
        $this->jwtKey       = InMemory::base64Encoded(config('app.jwt_access_secret_key'));
    }

    /**
     * 加密密码
     *
     * @author Jarvis
     * @date   2024-05-18 01:18
     */
    public function encryptPassword(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * 检查密码是否正确
     *
     * @author Jarvis
     * @date   2024-05-18 01:18
     */
    public function checkPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    /**
     * 创建新用户
     *
     * @param string $username
     * @param string $password
     *
     * @author Jarvis
     * @date   2024-05-18 01:30
     */
    public function create($username, $password): User {
        $exist = User::where('username', $username)->count();
        if ($exist) {
            BE('用户名已存在');
        }

        $user = new User([
            'username' => $username,
            'password' => $this->encryptPassword($password),
        ]);

        if (! $user->save()) {
            BE('创建用户失败');
        }

        return $user;
    }

    /**
     * 更新用户信息
     *
     * @param int   $uid
     * @param array $updateData
     *
     * @author Jarvis
     * @date   2024-05-18 01:35
     */
    public function update($uid, $updateData): User {
        $user = User::find($uid);
        if (empty($user)) {
            BE('用户不存在');
        }

        // 更新用户昵称 nickname
        if (! empty($updateData['nickname'])) {
            $user->setAttribute('nickname', $updateData['nickname']);
        }

        // 更新用户密码 password
        if (! empty($updateData['password'])) {
            $user->setAttribute('password', $this->encryptPassword($updateData['password']));
        }

        // 更新用户头像 avatar
        if (! empty($updateData['avatar'])) {
            $user->setAttribute('avatar', $updateData['avatar']);
        }

        // 更新用户邮箱 email
        if (! empty($updateData['email'])) {
            $user->setAttribute('email', $updateData['email']);
        }

        // 更新用户手机号 mobile
        if (! empty($updateData['mobile'])) {
            $user->setAttribute('mobile', $updateData['mobile']);
        }

        // 更新用户状态 status
        if (! empty($updateData['status'])) {
            $user->setAttribute('status', $updateData['status']);
        }

        // 保存用户信息
        if (! $user->save()) {
            BE('更新用户信息失败');
        }

        return $user;
    }

    /**
     * 用户列表
     *
     * @param array  $where
     * @param array  $field
     * @param string $orderBy
     *
     * @author Jarvis
     * @date   2024-05-18 01:40
     */
    public function users($where = [], $field = ['*'], $orderBy = 'id'): \Illuminate\Database\Eloquent\Collection {
        $query = User::select($field);
        if (! empty($where)) {
            $query->where($where);
        }
        $query->orderBy($orderBy, 'desc');
        $users = $query->get();

        return $users;
    }

    /**
     * 根据用户名获取用户信息
     *
     * @param string $username
     *
     * @author Jarvis
     * @date   2024-05-18 06:20
     */
    public function getUserByUsername($username): User {
        $user = User::where('username', $username)->first();
        if (empty($user)) {
            BE('用户不存在');
        }

        return $user;
    }

    /**
     * 生成JWT-Token
     *
     * @param  array                          $data
     * @param  string                         $expires
     * @param  string                         $permittedFor
     * @return \Lcobucci\JWT\UnencryptedToken
     *
     * @author Jarvis
     * @date   2024-05-23 14:54
     */
    public function generateToken($data, $expires, $permittedFor = 'auth') {
        $expiresAt = new DateTimeImmutable($expires, $this->dateTimeZone);
        $token     = $this->jwtFacade->issue(new Sha256(), $this->jwtKey,
            static fn (
                Builder $builder
            ): Builder => $builder
                ->issuedBy('logc.jarviscdr.com')
                ->permittedFor($permittedFor)
                ->expiresAt($expiresAt)
                ->withClaim('user', $data)
        );

        return $token;
    }

    /**
     * 登录
     *
     * @param string $username
     * @param string $password
     *
     * @author Jarvis
     * @date   2024-05-18 06:41
     */
    public function login($username, $password) {
        $user = $this->getUserByUsername($username);
        if ($user['status'] != 1) {
            BE('用户已禁止登录');
        }
        if (! $this->checkPassword($password, $user->password)) {
            BE('用户名或密码错误');
        }

        $user->setAttribute('login_at', date('Y-m-d H:i:s'));
        if (! $user->save()) {
            BE('登录失败');
        }

        $claims = [
            'id'       => $user->id,
            'username' => $user->username,
        ];

        $accessToken  = $this->generateToken($claims, '+ 10 minutes');
        $refreshToken = $this->generateToken($claims, '+ 7 days', 'refresh');

        return [
            'access_token'       => $accessToken->toString(),
            'access_expires_at'  => $accessToken->claims()->get('exp')->format('Y-m-d H:i:s'),
            'refresh_token'      => $refreshToken->toString(),
            'refresh_expires_at' => $refreshToken->claims()->get('exp')->format('Y-m-d H:i:s'),
        ];
    }
}
