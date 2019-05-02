<?php

namespace application\controllers;

class MainController extends \core\Controller {

    const AUTH_PROTECTED_METHODS = [
        'testAuthAction',
        'createTaskAction'
    ];

    // -------------------------

    public function indexAction() {
        $index = \core\View::render("main/index.php", [], true);
        \core\View::render("main/template.php", ['title' => 'Index', 'body_content' => $index]);
    }

    // -------------------------

    private function auth($email, $password) {

        $result = new \stdClass();

        if (!empty($email) && !empty($password)) {
            $user = \application\models\pdo\User::findByEmail($email);
            
            if (!empty($user)) {
                $hash = $user->password;

                $is_valid = password_verify($password, $hash);

                session_start();

                if ($is_valid) {
                    $_SESSION['user'] = $user;
                    
                    $result->status = 'success';
                    $result->details = 'password is valid';
                }
                else {
                    $_SESSION['user'] = null;

                    $result->status = 'failed';
                    $result->details = 'password is not valid';
                }
            }
            else {
                $result->status = 'failed';
                $result->details = 'empty user';
            }
        }
        else {
            $result->status = 'failed';
            $result->details = 'empty email or password';
        }

        return $result;
    }

    // -------------------------

    public function loginAction() {
        if (!empty($_POST)) {
            // check credentials

            $email = $_POST['email'];
            $password = $_POST['password'];
            $result = $this->auth($email, $password);
            var_dump($result);
        }
        else {
            // display login form
            $login_user = \core\View::render("main/login_user.php", [], true);
            \core\View::render("main/template.php", ['title' => 'Login registered user', 'body_content' => $login_user]);
        }
    }

    // -------------------------

    public function logoutAction() {
        session_start();
        
        if (!empty($_SESSION['user'])) {
            $_SESSION['user'] = null;
            echo 'logged out';
        }
    }

    // -------------------------

    public function regUserAction() {
        if (!empty($_POST)) {
            // register new user
            
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['password'];

            $result = false;
            if (!empty($name) && !empty($email) && !empty($password) && !empty(filter_var($email, FILTER_VALIDATE_EMAIL))) {
                $result = \application\models\pdo\User::create($name, $email, $password);
            }
            var_dump($result);
        }
        else {
            // display register form
            $reg_user = \core\View::render("main/reg_user.php", [], true);
            \core\View::render("main/template.php", ['title' => 'Register new user', 'body_content' => $reg_user]);
        }
    }

    // --------------------------------

    public function createTaskAction() {

        session_start();
        
        if (!empty($_POST)) {
            // create new task

            $name = $_POST['name'];
            $body = $_POST['body'];
            $target_time = $_POST['target_time'];
            $user_id = $_POST['user_id'];
            $csrf_token = $_POST['csrf_token'];

            if (isset($_POST['parent_id'])) {
                $parent_id = $_POST['parent_id'];
            }

            $result = false;
            if (!empty($name) && !empty($csrf_token)) {

                if (empty($parent_id)) {
                    $parent_id = null;
                }

                if ($csrf_token === $_SESSION['csrf_token']) {
                    $result = \application\models\pdo\Task::create($name, $body, $parent_id, $target_time, $user_id);
                }
            }

            if ($result) {
                echo "new task created, id = $result";
            }
        }
        else {
            // display create task form

            $top_level_tasks = \application\models\pdo\Task::getTopLevelTasks();
            $users_names_ids = \application\models\pdo\User::getNamesWithIds();

            $csrf_token = bin2hex(random_bytes(32));
            $_SESSION['csrf_token'] = $csrf_token;
            
            $create_task = \core\View::render("main/create_task.php",
            ['parents' => $top_level_tasks, 'users' => $users_names_ids, 'csrf_token' => $csrf_token],
            true);
            \core\View::render("main/template.php", ['title' => 'Create new task', 'body_content' => $create_task]);
        }
    }

    // ----------------------------

    public function listTasksAction($params) {
        if (isset($params['page']) && $params['page'] >= 1) {
            $page = $params['page'];
        }
        else {
            $page = 1;
        }

        $tasks_list = \core\View::render("main/tasks_list.php", ['page' => $page], true);
        \core\View::render("main/template.php", ['title' => 'List of tasks', 'body_content' => $tasks_list]);
    }

    // -----------------------------------

    public function listTasksAjaxAction($params) {
        if (isset($params['page']) && $params['page'] >= 1) {
            $page = $params['page'];
        }
        else {
            $page = 1;
        }

        $tasks = \application\models\pdo\Task::getAllTasks($page);

        foreach ($tasks as $key => $t) {
            // get parent name
            $parent = \application\models\pdo\Task::getById($t->parent_id);

            if ($parent !== null) {
                $tasks[$key]->parent_name = $parent->name;
            }
            else {
                $tasks[$key]->parent_name = '';
            }

            // get user name
            $user = \application\models\pdo\User::getById($t->user_id);

            if ($user !== null) {
                $tasks[$key]->user_name = $user->name;
            }
            else {
                $tasks[$key]->user_name = '';
            }
        }

        $pages_num = \application\models\pdo\Task::getNumOfPages();

        $response = new \stdClass();
        $response->tasks = $tasks;
        $response->page = $page;
        $response->pages_num = $pages_num;

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    // -----------------------------------

    public function editTaskAction($params) {
        if (isset($params['id'])) {
            $task_id = $params['id'];
            $task = \application\models\pdo\Task::getById($task_id);

            if ($task !== null) {
                $top_level_tasks = \application\models\pdo\Task::getTopLevelTasks();

                // display edit task form
                $edit_task = \core\View::render("main/edit_task.php", ['task' => $task, 'parents' => $top_level_tasks], true);
                \core\View::render("main/template.php", ['title' => 'Edit task', 'body_content' => $edit_task]);
            }
        }
        elseif (!empty($_POST)) {
            $task_id = $_POST['task_id'];
            $target_time = $_POST['target_time'];
            $name = $_POST['name'];
            $body = $_POST['body'];
            $parent_id = $_POST['parent_id'];
            $status = $_POST['status'];

            $task = new \stdClass();
            $task->id = $task_id;
            $task->target_time = $target_time;
            $task->name = $name;
            $task->body = $body;
            $task->parent_id = $parent_id;
            $task->status = $status;

            // save updated task
            $result = \application\models\pdo\Task::save($task);

            if ($result) {
                echo "task modified";
            }
        }
    }

    // ------------------------------

    public function closeTaskAction($params) {
        if (isset($params['id'])) {
            $task_id = $params['id'];
            $result = \application\models\pdo\Task::close($task_id);

            if ($result) {
                echo "task closed";
            }
        }
    }

    // ------------------------------

    public function seedUsersAction() {

        for ($n = 1; $n <= 10; $n ++) {
            $name = "user$n";
            $email = "email$n@mail.com";
            $password = "1";

            $user_id = \application\models\pdo\User::create($name, $email, $password);
            echo "$user_id\n";
        }
    }

    // ------------------------------

    public function seedTasksAction() {

        $users = \application\models\pdo\User::getNamesWithIds();

        if (count($users) >= 1) {
            for ($n = 1; $n <= 30; $n ++) {
                $name = "task $n";
                $body = "body $n";
                $parent_id = null;
                $target_time = "2019-04-13 08:$n:00";
    
                $rand_key = array_rand($users);
                $user_id = $users[$rand_key]->id;
                
                $task_id = \application\models\pdo\Task::create($name, $body, $parent_id, $target_time, $user_id);
                echo "$task_id\n";
            }
        }
    }

    // ================================

    public function testAuthAction() {
        print_r($_SESSION['user']);
    }
    
	public function helloAction() {
		echo "#hello from main controller!#";
	}
	
	protected function before($method) {
		//echo $method;exit;

        // check if auth protected
        if (in_array($method, self::AUTH_PROTECTED_METHODS)) {

            session_start();
            
            if (empty($_SESSION['user'])) {
                header("Location: /main/login");
            }
        }
	}
	
	protected function after() {
		#echo '@@after';
	}
}
