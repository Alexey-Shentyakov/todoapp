# todoapp

использован паттерн MVC

application - модели, контроллеры, виды, конфиг

core - базовые классы

public - докрут вебсервера

tests - phpunit тесты

## тесты
./phpunit --bootstrap tests/autoload.php tests/UserTest

./phpunit --bootstrap tests/autoload.php tests/TaskTest

## зависимости

jquery

bootstrap

аутентификация через php сессии

защищенные методы контроллера указываются в константном массиве AUTH_PROTECTED_METHODS

## пользователи

regUserAction() - создание пользователя

loginAction() - логин

logoutAction() - выход

## задачи

createTaskAction() - создание задачи

editTaskAction() - редактирование задачи

closeTaskAction() - закрытие задачи

listTasksAction() - просмотр задач с пагинацией


