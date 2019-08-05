<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
</head>
<body>
    
<?PHP
    $name = "";
    $edit = "";
    $comment = "";

    require_once('password.php');

$pdo = new PDO($dsn,$user,$password);//DBに接続するためのデータ。


    
if(!empty($_POST['name']) && !empty($_POST['comment'] && !empty($_POST['pass']))){
	$edit = $_POST['edit'];
    $name = $_POST['name'];
    $comment = $_POST['comment'];
    $password = $_POST['pass'];
    
    if($edit == 0){//編集フォームに何も送られていない場合
        $sql = $pdo -> prepare("INSERT INTO tbtest (name, comment,password) VALUES (:name, :comment,:password)");
        //:name :commentなど:がつくのはバインド変数。bindparamで変数を入れる。
        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $sql -> bindParam(':password', $password, PDO::PARAM_STR);
        $sql -> execute(); //実行しなさいという命令
    }else{
        $sql = 'update tbtest set name=:name,comment=:comment where id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':id', $edit, PDO::PARAM_INT);
        $stmt->execute();
    }
}
    
if(!empty($_POST['delete'])){
    $delete = $_POST['delete'];
    $stll = $pdo -> prepare('select password from tbtest where id=:id');
	$stll -> bindParam(':id',$delete,PDO::PARAM_INT);
    $stll -> execute();
    
    $results = $stll -> fetchAll();
    $pass = $results[0]['password'];
    if($pass == $_POST['pass2']){
        $sql = 'delete from tbtest where id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt -> bindParam(':id', $delete, PDO::PARAM_INT);
        $stmt -> execute();
    }
}


if(!empty($_POST['edit'])){
    $edit = $_POST['edit'];
    $id = 0;//あとで追加
    $stll = $pdo -> prepare('select password,id,name,comment from tbtest where id=:id');
    $stll -> bindParam(':id',$edit,PDO::PARAM_INT);
    $stll -> execute();
    
    
    $results = $stll -> fetchAll();
    $pass = $results[0]['password'];
	$name = $results[0]['name'];
	$comment = $results[0]['comment'];
    if($pass == $_POST['pass3']){
        $id = $edit;
    }
}


print <<< EOF
    
<form method="post" action="mission_5-1.php">
名前:<input type='text' name='name' value='$name'>
<!--　編集フォームに入力された場合に使う隠しフォーム　-->
<input type='hidden' name='edit' value='$id'> 
コメント:<input type='text' name='comment' value='$comment'>
パスワード:<input type='text' name='pass' value=''>
<input type='submit' value='送信'>
</form>

<!-- 削除のフォーム -->
<form action = 'mission_5-1.php' method = 'POST'>
<br>
削除したい投稿番号:<input type='text' name='delete' value=''>
パスワード:<input type='text' name='pass2' value=''>
<input type='submit' value='削除'>
</form>

<!-- 編集のフォーム -->
<form action = 'mission_5-1.php' method = 'POST'>
<br>
編集したい投稿番号:<input type='text' name='edit' value=''>
パスワード:<input type='text' name='pass3' value=''>
<input type='submit' value='編集'><br><br>
</form>

EOF;


$sql = 'SELECT * FROM tbtest';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();//fetchALL全データを配列に置換。行を含む配列を返す。
foreach ($results as $row){
    echo $row['id'].',';
    echo $row['name'].',';
    echo $row['comment'].',';
	 echo $row['date'].',';
    echo $row['password'];
    echo "<hr>";
}
?>
</body>
</html>