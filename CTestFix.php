<?
require_once($_SERVER['DOCUMENT_ROOT'].'/CTestTask.php');

class CTestFix extends CTestTask{
    var $db;
    
    var $arDb=[
        'host'=>'localhost',
        'user'=>'dev3',
        'passwd'=>'C6C2Chkd8qFOea9C',
        'dbname'=>'world',
    ];

    var $arQueriesTemp=[
        "GET_QUESTIONS_USER"=>"SELECT * FROM questions INNER JOIN users ON questions.user_id=users.id WHERE catalog_id=#ID#;",
        "GET_QUESTIONS"=>"SELECT * FROM questions WHERE catalog_id=#ID#;",
        "GET_USER"=>"SELECT * FROM users WHERE u_id=#ID#;",
        "GET_USER2"=>"SELECT name, gender FROM users WHERE id=#ID#;",
        "USER_BILL_INFO"=>"SELECT ANY_VALUE(users.name) user_name, ANY_VALUE(users.phone) user_phone, sum(orders.subtotal) bill_total, avg(orders.subtotal) bill_avg, max(orders.created) bill_last_date FROM users INNER JOIN orders ON orders.user_id=users.id WHERE users.id=#ID# GROUP BY users.name;",
        "CREATE_TABLE"=>"CREATE TABLE IF NOT EXISTS #NAME# (#FIELDS# ,primary key (#PRIMARY_KEY#));",
        "INSERT"=>"INSERT into #TABLE_NAME# (#FIELDS#) values (#VALUES#);",
        "ONLY_FULL_GROUP_BY"=>"SET SESSION sql_mode='ONLY_FULL_GROUP_BY';",

    ];
    
    var $arTables=[
        'users'=>[
            'NAME'=>'users',
            'PRIMARY_KEY'=>'id',
            'FIELDS'=>[
                'id'=>'int(11) NOT NULL AUTO_INCREMENT',
                'phone'=>'varchar(255)',
                'email'=>'varchar(255)',
                'gender'=>'varchar(255)',
                'name'=>'varchar(255)',
                'created'=>'date',
            ],
        ],
        'orders'=>[
            'PRIMARY_KEY'=>'id',
            'NAME'=>'orders',
            'FIELDS'=>[
                'id'=>'int(11) NOT NULL AUTO_INCREMENT',
                'subtotal'=>'decimal(11)',
                'city_id'=>'int(11)',
                'user_id'=>'int(11)',
                'created'=>'date',
            ],
        ],
        'questions'=>[
            'PRIMARY_KEY'=>'id',
            'NAME'=>'questions',
            'FIELDS'=>[
                'id'=>'int(11) NOT NULL AUTO_INCREMENT',
                'catalog_id'=>'int(11)',
                'user_id'=>'int(11)',
                'text'=>'varchar(255)',
                'created'=>'date',
            ],
        ]
    ];

   

    function __construct(){
        $this->dbConn();
    }

    function initDbData(){
        $this->initTables();
        $this->initUsers();
        $this->initOrders();
        $this->initQuestions();
    }
    
    function initTables(){
        foreach($this->arTables as $table){
            $this->createTable($table);
        }
    }
    
    function genPhone(){
        $f='+7';
        $s=' ('.rand(111,999).') ';
        $t=rand(111,999).'-'.rand(11,99).'-'.rand(11,99);
        return $f.$s.$t;
    }
    
    function initUsers(){
        for($i=1;$i<10;$i++){
            $this->addUser([
                'phone'=>$this->genPhone(),
                'email'=>'user'.$i.'@test.ru',
                'gender'=>(rand(1,10)%2)?'male':'female',
                'name'=>'user_'.$i,
                'created'=>date('Y-m-d H:i:s'),
                
            ]);
        }
    }
    
    function initQuestions(){
        for($i=1;$i<10;$i++){
            $this->addItem('questions',[
                'catalog_id'=>rand(1,3),
                'text'=>'question'.$i,
                'user_id'=>$i,
                'created'=>date('Y-m-d H:i:s'),
                
            ]);
        }
    }
    
    function initOrders(){
        $orderid=1;
        for($i=1;$i<10;$i++){
            for($j=1;$j<10;$j++){
                $this->addOrder([
                    // 'id'=>$orderid,
                    'subtotal'=>rand(1000,9999),
                    'city_id'=>rand(1,1000),
                    'user_id'=>$i,
                    'created'=>date('Y-m-d H:i:s'),
                ]);
                
                $orderid++;
            }
        }
    }

    function createTable($arTable){
        

        foreach($arTable['FIELDS'] as $key=>$field){
            $arFields[]=$key.' '.$field;
        }

        $arTable['FIELDS']=implode(',',$arFields);
        
        $q=$this->getQuery('CREATE_TABLE',$arTable);

        $this->query($q);
    }
        
    function addOrder($arOrder){
        $this->addItem('orders',$arOrder);
    }
    
    function addUser($arUser){
        $this->addItem('users',$arUser);
    }
    
    function addItem($table,$arItem){
        $table=$table;
        foreach($this->arTables[$table]['FIELDS'] as $fkey=>$field){
            $arFields[$fkey]=$fkey;
            $arValues[$fkey]=$arItem[$fkey]?'"'.$arItem[$fkey].'"':'NULL';
        }
        
        $arQuery=[
            "TABLE_NAME"=>$table,
            "FIELDS"=>implode(',',$arFields),
            "VALUES"=>implode(',',$arValues)
        ];
        
        $q=$this->getQuery('INSERT',$arQuery);
        $this->query($q);
        
    }
    
    function dbConn(){
        $this->db= new mysqli(
            $this->arDb['host'], 
            $this->arDb['user'], 
            $this->arDb['passwd'], 
            $this->arDb['dbname']
        );
        
        if ($this->db->connect_error) {
            die('Connect Error (' . $this->db->connect_errno . ') ' . $this->db->connect_error);
        }
        
        $this->query($this->getQuery('ONLY_FULL_GROUP_BY'));
        
    }

    function query($query,$obj=false){
        $res=$this->db->query($query);
   
        if (gettype ($res)=='object'){
            if($obj) return $res;

            while($row=$res->fetch_assoc()){
                $arRows[]=$row;
            }
        }
        else{
            return $res;
        }

        if(is_array($arRows) && count($arRows)==1) return $arRows[0];
        
        return $arRows;
    }

    function getQuery($temp,$arVars=[]){
        $q=$this->arQueriesTemp[$temp];
        
        foreach($arVars as $key=>$var){
            $q=str_replace("#$key#",$var,$q);
        }
        
        return $q;
    }

    function getUser(){
        
        $id = intval($_GET['id']);

        if(!$id) return false;
        $q=$this->getQuery('GET_USER2',["ID"=>$id]);

        $user = $this->query($q);

        return $user;
    }

    function getQuestionsByCat2($catId){
        
        $catId=intval($catId);

        if(!$catId) return false;

        $q=$this->getQuery('GET_QUESTIONS_USER',["ID"=>$catId]);
        
        $arQuestions= $this->query($q);
        
        return $arQuestions;
        
    }
    
    function getQuestionsByCat($catId){

        $catId=intval($catId);

        if(!$catId) return false;

        $q=$this->getQuery('GET_QUESTIONS',["ID"=>$catId]);
        
        $oReq= $this->query($q,true);

        if(!$oReq) return false;
        
        while($arQuestion=$oReq->fetch_assoc()) {
            $q=$this->getQuery('GET_USER2',["ID"=>$arQuestion['user_id']]);
            
            $arUser = $this->query($q);

            $arRes[] = [
                'question'=>$arQuestion, 
                'user'=>$arUser
            ];

        }

        return $arRes;
    }

    function getUserBillInfo($uid){
        $q=$this->getQuery('USER_BILL_INFO',["ID"=>$uid]);
        $arUser=$this->query($q);
        return $arUser;
    }
}

?>