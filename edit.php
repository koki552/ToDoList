<?php

// データベースの接続情報
define( 'DB_HOST', 'localhost');
define( 'DB_USER', 'root');
define( 'DB_PASS', '');
define( 'DB_NAME', 'todo_list');

// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

session_start();

if( !empty($_GET['todo_id']) && empty($_POST['todo_id'])) {

  $todo_id = (int)htmlspecialchars($_GET['todo_id'], ENT_QUOTES);
  
  // データベースに接続
  $mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);

// 接続エラーの確認
if( $mysqli->connect_errno) {
  $error_message[] = 'データベースの接続に失敗しました。エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
} else {

  // データの読み込み
  $sql = "SELECT * FROM todo WHERE id = $todo_id";
  $res = $mysqli->query($sql);

  if( $res ) {
      $message_data = $res->fetch_assoc();
  } else {

      // データが読み込めなかったら一覧に戻る
      header("Location: ./index.php");
  }

  $mysqli->close();
 }  

} elseif( !empty($_POST['todo_id']) ) {

  $todo_id = (int)htmlspecialchars( $_POST['todo_id'], ENT_QUOTES);

  // TODO入力チェック
  if( empty($_POST['todo']) ) {
      $error_message[] = 'Please enter todo.';
  } else {
      $todo = htmlspecialchars( $_POST['todo'], ENT_QUOTES);
  }

  // 期限日入力チェック
  if( empty($_POST['deadline'])) {
      $error_message[] = 'Please enter the deadline';
  } else {
      $deadline = date($_POST['deadline']);
  }

  // カテゴリー入力チェック
  if( $_POST['category']=='Category'){
    $error_message[] = 'Please enter the category.';
  } else {
    $category = $_POST['category'];
  }

  if( empty($error_message) ) {
      
      // データベースに接続
      $mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);

      // 接続エラーの確認
      if( $mysqli->connect_errno) {
       $error_message[] = 'データの接続に失敗しました。エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
      } else {
          $sql = "UPDATE todo SET todo = '$todo', deadline= '$deadline', category='$category' WHERE id = $todo_id " ;
          $res = $mysqli->query($sql);
      }    
      $mysqli->close();

      // 更新に成功したら一覧に戻る
      if( $res ) {
          header("Location: ./index.php");
      }
  }
}

?>

<!doctype html>
<html lang="ja">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

    <title>To Do List</title>
    <style>
    table.table {
    counter-reset: ranking;
    }
    table.table tbody th:before {
    counter-increment: ranking;
    content:  counter(ranking) ; 
    }

    .success_message {
    margin-bottom: 20px;
    padding: 10px;
    color: #48b400;
    border-radius: 10px;
    border: 1px solid #4dc100;
    }

    .error_message {
    margin-bottom: 20px;
    padding: 10px;
    color: #ef072d;
    list-style-type: none;
    border-radius: 10px;
    border: 1px solid #ff5f79;
    }

    #button_a{
      float: right;
    }

    </style>
  </head>
  <body>
  <!-- Content here -->
    <div class="container">

    <h1>To Do List</h1>

    <?php if( !empty($error_message) ): ?>
    <ul class="error_message">
    <?php foreach( $error_message as $value ): ?>
    <li><?php echo $value; ?></li>
    <?php endforeach; ?> 
    </ul>  
    <?php endif; ?>

  <form method="post" class="needs-validation">
    <div class="form-row">

      <div class="col-md-4 mb-3">
        <label for="validationCustom01">Category</label>
        <select class="custom-select" name="category" value="<?php if( !empty($message_data['category']) ) { echo $message_data['category']; } ?>" required>
          <option selected>Category</option>
          <option>Bisiness</option>
          <option>Private</option>
          <option>Other</option>
        </select>
      </div>

      <div class="col-md-4 mb-3">
        <label for="validationCustom02">To Do</label>
        <input type="text" class="form-control" id="validationCustom02" name="todo" placeholder="To Do" value="<?php if( !empty($message_data['todo']) ) { echo $message_data['todo']; } ?>" required>
      </div>

      <div class="col-md-4 mb-3">
        <label for="validationCustom03">Deadline</label>
        <input type="date" class="form-control" id="validationCustom03" name="deadline" placeholder="Deadline" value="<?php if( !empty($message_data['deadline']) ) { echo $message_data['deadline']; } ?>" required>
      </div>

    </div>  
      <input type="submit" class="btn btn-outline-primary" name="btn_submit" value="Update">
      <input type="hidden" name="todo_id" value="<?php echo $message_data['id']; ?>">
      <button type="button" class="btn btn-outline-secondary" onclick="location.href='index.php'">Cancel</button>
  </form>     
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    
  </body>
</html>