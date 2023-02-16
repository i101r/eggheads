<? 

class CTestTask {


    function getUser(){
        $mysqli = new mysqli("localhost", "my_user", "my_password", "world");
        $id = $_GET['id'];
        $res = $mysqli->query('SELECT * FROM users WHERE u_id='. $id);
        $user = $res->fetch_assoc();

        return $user;
    }

    function getQuestions(){
        $questionsQ = $mysqli->query('SELECT * FROM questions WHERE catalog_id='. $catId);
        
        $result = array();
        
        while ($question = $questionsQ->fetch_assoc()) {
            $userQ = $mysqli->query('SELECT name, gender FROM users WHERE id='. $question['user_id']);
            $user = $userQ->fetch_assoc();
            $result[] = array('question'=>$question, 'user'=>$user);
            $userQ->free();
        }

        $questionsQ->free();

    }

    function getUserBillInfo(){
            // 3. Напиши SQL-запрос
            // Имеем следующие таблицы:
            // users — контрагенты
            // id
            // name
            // phone
            // email
            // created — дата создания записи
            // orders — заказы
            // id
            // subtotal — сумма всех товарных позиций
            // created — дата и время поступления заказа (Y-m-d H:i:s)
            // city_id — город доставки
            // user_id

            // Необходимо выбрать одним запросом следующее (следует учесть, что будет включена опция only_full_group_by в MySql):
            // Имя контрагента
            // Его телефон
            // Сумма всех его заказов
            // Его средний чек
            // Дата последнего заказа

    }

    
}

?>