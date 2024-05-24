<?php

declare(strict_types=1);

namespace app\command;

use app\service\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class UserManage extends Command {
    protected static $defaultName = 'user:manage';

    protected static $defaultDescription = '管理账号';

    protected InputInterface $input;

    protected OutputInterface $output;

    protected QuestionHelper $helper;

    protected function configure(): void {
    }

    public function __construct(private UserService $userService) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $this->input  = $input;
        $this->output = $output;
        $this->helper = $this->getHelper('question');

        $operationQuestion = new ChoiceQuestion('请选择操作类型：', ['用户列表', '重置密码', '创建用户', '退出']);
        while (true) {
            $choice = $this->helper->ask($input, $output, $operationQuestion);
            $this->output->writeln("\n\n--------------{$choice}--------------");

            switch ($choice) {
                case '用户列表':
                    $this->showUsers();
                    break;
                case '重置密码':
                    $this->resetPassword();
                    break;
                case '创建用户':
                    $this->createUser();
                    break;
                case '退出':
                    return self::SUCCESS;
            }
        }

        // $this->showUsers($output);

        // $userNameQuestion = new Question('请输入用户名：');
        // $username         = $helper->ask($input, $output, $userNameQuestion);
        // dump($username);

        return self::SUCCESS;
    }

    private function showUsers(): void {
        $table = new Table($this->output);
        $list  = $this->userService->users();
        $table->setHeaders(['ID', '用户名', '昵称', '邮箱', '手机', '状态', '登录时间']);
        $showData = [];
        foreach ($list as $user) {
            $showData[] = [$user['id'], $user['username'], $user['nickname'], $user['email'], $user['mobile'], $user['status'], $user['login_at']];
        }
        $table->setRows($showData);
        $table->render();
    }

    private function resetPassword(): void {
        $usernameQuestion = new Question('请输入用户名：');
        $usernameQuestion->setValidator(function ($answer) {
            if (empty($answer)) {
                throw new \RuntimeException('用户名不能为空。');
            }

            return $answer;
        });
        $username = $this->helper->ask($this->input, $this->output, $usernameQuestion);
        $user     = $this->userService->getUserByUsername($username);
        if (empty($user)) {
            $this->output->writeln("\n<error>用户 {$username} 不存在。</error>\n");
        }

        $passwordQuestion = new Question('请输入密码：');
        $password         = $this->helper->ask($this->input, $this->output, $passwordQuestion);
        $passwordQuestion = new Question('请输入密码：');
        $passwordQuestion->setValidator(function ($answer) {
            if (empty($answer)) {
                throw new \RuntimeException('密码不能为空。');
            }

            return $answer;
        });

        $this->userService->update($user['id'], ['password' => $password]);
        $this->output->writeln("\n<info>用户 {$username} 密码更新成功。</info>\n");
    }

    /**
     * 创建用户
     *
     * @author Jarvis
     * @date   2024-05-18 05:23
     */
    private function createUser(): void {
        $usernameQuestion = new Question('请输入用户名：');
        $usernameQuestion->setValidator(function ($answer) {
            if (empty($answer)) {
                throw new \RuntimeException('用户名不能为空。');
            }

            return $answer;
        });

        $passwordQuestion = new Question('请输入密码：');
        $passwordQuestion->setValidator(function ($answer) {
            if (empty($answer)) {
                throw new \RuntimeException('密码不能为空。');
            }

            return $answer;
        });

        $username = $this->helper->ask($this->input, $this->output, $usernameQuestion);
        $password = $this->helper->ask($this->input, $this->output, $passwordQuestion);

        try {
            $this->userService->create($username, $password);
        } catch (\Throwable $th) {
            $this->output->writeln("\n创建用户失败：<error>{$th->getMessage()}</error>\n");

            return;
        }

        $this->output->writeln("\n<info>用户 {$username} 创建成功。</info>\n");
    }
}
