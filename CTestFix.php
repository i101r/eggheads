<?
require_once($_SERVER['DOCUMENT_ROOT'].'/CTestTask.php');

class CTestFix extends CTestTask{

    var $arDB=[
        'host'=>'localhost',
        'user'=>'my_user',
        'passwd'=>'my_password',
        'dbname'=>'world',
    ];

    var $arQueriesTemp=[
        "GET_QUESIONS"=>"SELECT * FROM questions WHERE catalog_id=#ID#",
        "GET_USER"=>"SELECT * FROM users WHERE u_id=#ID#",
        "GET_USER2"=>"SELECT name, gender FROM users WHERE u_id=#ID#",
        "USER_BILL_INFO"=>"SELECT user.name user_name, user.phone user_phone, sum(orders.subtotal) bill_total, avg(orders.subtotal) bill_avg, max(orders.created) bill_last_date FROM users INNER JOIN orders ON orders.user_id=user.id WHERE user.id=#ID# GROUP BY user.name",

    ];

    var $db;

    function __construct(){
        $this->dbConn();
    }

    function dbConn(){
        $this->db= new mysqli($this->arDb['host'], $this->arDb['user'], $this->arDb['passwd'], $this->arDb['dbname']);
    }

    function query($query){
        $res=$this->db->query($query);
        
        while($res->fetch_assoc()){
            $res[]=$res->fetch_assoc();
        }

        if(count($res)==1) return $res[0];
        
        return $res;
    }

    function getQuery($temp,$id){
        return str_replace($temp,$id,$this->arQueriesTemp[$temp]);
    }

    function getUser(){
        
        $id = intval($_GET['id']);

        if(!$id) return false;

        $q=$this->getQuery('GET_USER2',$id);

        $user = $this->query($q);

        return $user;
    }

    function getQuestionsByCat($catId){

        $catId=intval($catId);

        if(!$catId) return false;

        $q=$this->getQuery('GET_QUESIONS',$catId);
        
        $arQuestions= $this->query($q);
        
        foreach($arQuestions as $arQuestion) {
            $q=$this->getQuery('GET_USER2',$arQuestion['user_id']);
            
            $user = $this->query($q);

            $arRes[] = [
                'question'=>$question, 
                'user'=>$user
            ];

        }

        return $arRes;
    }

    function getUserBillInfo($uid){
        $arUser=$this->query($this->getQuery('USER_BILL_INFO',$uid));
        return $arUser;
    }
}

?>