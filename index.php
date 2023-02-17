<?
require_once($_SERVER['DOCUMENT_ROOT'].'/CTestFix.php');

$arMessage=[];

switch($_REQUEST['action']) {
    case 'InitDataBase':
        (new CTestFix)->initDbData();
        $arMessage[]='Database Init';
        break;
    case 'getUser':
        $arUser=(new CTestFix)->getUser();
        if($arUser){
            $arMessage[]='User: '.implode(' ,',$arUser);
        }
        else{
            $arMessage[]='User: not found';
        }
        break;
    case 'getQuestionsByCat':
        $catid=intval($_REQUEST['id']);
        
        $arQuestions=(new CTestFix)->getQuestionsByCat2($catid);
        
        if($arQuestions){
            $arMessage[]='Questions: ';
            foreach($arQuestions as $arQuestion){
                if($arQuestion['question']){
                    $arQuestion=array_merge($arQuestion['question'],$arQuestion['user']);
                }
                
                $arMessage[]=implode(', ',$arQuestion);
                
            }
        }
        else{
            $arMessage[]='Questions: not found';
        }
        break;
    case 'getUserBillInfo':
        $userid=intval($_REQUEST['id']);
        $arUserBillInfo=(new CTestFix)->getUserBillInfo($userid);
        
        if($arUserBillInfo){
            
            $arMessage[]=implode(', ',$arUserBillInfo);
        }
        else{
            $arMessage[]='User Bills: not found';
        }
        
        break;
}    

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <script async="" src="js/script.js"></script>
        <style>
            .msgs{
                width:500px;
                height:300px;
                padding:30px;
            }
        </style>
    </head>
    <body>
        <form action="/" method="GET">
            <input type="text" name="id" value="<?=$_REQUEST['id']?$_REQUEST['id']:'1'?>">
            <input type="button" id="user-bill-info" value="User Bill Info" >  
            <input type="submit" name="action" value="InitDataBase" >  
            <input type="submit" name="action" value="getUser" >  
            <input type="submit" name="action" value="getQuestionsByCat" >  
            <input type="submit" name="action" value="getUserBillInfo" >  
        </form>
        <div class="msgs">
            <?foreach($arMessage as $msg):?>
                <div class="msg"><?=$msg;?></div>
            <?endforeach;?>    
        </div>

    </body>
</html>

