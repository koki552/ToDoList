<?php

// データベースの接続情報
define( 'DB_HOST', 'localhost');
define( 'DB_USER', 'root');
define( 'DB_PASS', '');
define( 'DB_NAME', 'todo_list');

// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

session_start();

// 投稿の登録
if( !empty($_POST['btn_submit']) ) {

  // カテゴリーの入力チェック
  if( $_POST['category']=='Category'  ){
      $error_message[] = 'Please enter the category.';
  } else {
      $category = $_POST['category'];
  }

    // 表示名の入力チェック
    if( empty($_POST['todo']) ) {
        $error_message[] = 'Please enter todo.';
    } else {
        $clean['todo'] = htmlspecialchars( $_POST['todo'], ENT_QUOTES);
    }

    // 日付の入力チェック
    if( empty($_POST['deadline'])) {
        $error_message[] = 'Please enter the deadline';
    } else {
        $deadline = date($_POST['deadline']);
    }
   
    if( empty($error_message) ) {

   // データベースに接続
   $mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);

   // 接続エラーの確認
   if( $mysqli->connect_errno) {
       $error_message[] = '書き込みに失敗しました。エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
   } else {
       // 文字コード設定
       $mysqli->set_charset('utf8');

       // 書き込み日時を取得
       $now_date = date("Y-m-d H:i:s");

       // 初期値0表示
       $status = 0;

       // データを登録するSQL作成
       $sql = "INSERT INTO todo (todo, deadline, post_date, status, category ) VALUES ( '$clean[todo]', '$deadline' , '$now_date', '$status', '$category')";

       // データを登録
       $res = $mysqli->query($sql);

       if($res) {
           $success_message = 'SUCCESS';
       } else {
           $error_message[] = 'ERROR';
       }

       // データベースの検索を閉じる
       $mysqli->close();
   }
}
}

// 完了ボタンでstatusを1に更新
if( isset($_POST['btn_complete']) ) {

  // データベースに接続
  $mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);


  // 接続エラーの確認
if( $mysqli->connect_errno) {
  $error_message[] = 'データの読み込みに失敗しました。エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
} else {

  // 書き込み日時を取得
  $now_date = date("Y-m-d H:i:s");

  $sql = "UPDATE `todo` SET `post_date`= '$now_date',`status`= 1 WHERE id = $_POST[btn_complete]";
 
  $res = $mysqli->query($sql);
}
$mysqli->close();
}

// 未完了リスト表示
// データベースに接続
$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);

// 接続エラーの確認
if( $mysqli->connect_errno) {
    $error_message[] = 'データの読み込みに失敗しました。エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
} else {
    $sql = "SELECT id, todo, deadline, post_date, status, category FROM todo WHERE status = 0 ORDER BY deadline ASC";
    $res = $mysqli->query($sql);

    if($res) {
        $message_array = $res->fetch_all(MYSQLI_ASSOC);
    }

    $mysqli->close();
}

// 完了リストに表示
// データベースに接続
$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);

// 接続エラーの確認
if( $mysqli->connect_errno) {
    $error_message[] = 'データの読み込みに失敗しました。エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
} else {
    $sql = "SELECT id, todo, deadline, post_date, status, category FROM `todo` WHERE status = 1 and `post_date` > sysdate() - interval 1 day ORDER BY post_date DESC";
    $res = $mysqli->query($sql);

    if($res) {
        $complete_array = $res->fetch_all(MYSQLI_ASSOC);
    }

    $mysqli->close();
}

// 投稿を削除する
if( isset($_POST['btn_delete']) ) {

  $clean['todo'] = htmlspecialchars( $_POST['todo'], ENT_QUOTES);

  // データベースに接続
  $mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);

  // 接続エラーの確認
      if( $mysqli->connect_errno) {
       $error_message[] = 'データベースの接続に失敗しました。エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
      } else {
          $sql = "DELETE FROM todo WHERE id = $_POST[btn_delete]";
          $res = $mysqli->query($sql);
      }    
      $mysqli->close();

       // 更新に成功したら一覧に戻る
       if( $res ) {
          header("Location: ./index.php");
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

    <?php if( !empty($success_message) ): ?>
      <p class="success_message"><?php echo $success_message; ?></p>
    <?php endif; ?>

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
        <select class="custom-select" name="category" required>
          <option selected>Category</option>
          <option>Bisiness</option>
          <option>Private</option>
          <option>Other</option>
        </select>
      </div>

      <div class="col-md-4 mb-3">
        <label for="validationCustom02">To Do</label>
        <input type="text" class="form-control" id="validationCustom02" name="todo" placeholder="To Do" value="" required>
      </div>

      <div class="col-md-4 mb-3">
        <label for="validationCustom03">Deadline</label>
        <input type="date" class="form-control" id="validationCustom03" name="deadline" placeholder="Deadline" value="" required>
      </div>

    </div>  
      <input type="submit" class="btn btn-outline-primary" name="btn_submit" value="Submit">
  </form>
  
<hr>

    <table class="table">
    <thead class="thead-dark">
    <tr>
      <th scope="col" style="width:5%">#</th>
      <th scope="col" style="width:17.5%">Deadline</th>
      <th scope="col" style="width:17.5%">Category</th>
      <th scope="col" style="width:30%">To Do</th>
      <th scope="col" style="width:30%"></th>
    </tr>
    </thead>
    <tbody>
    <tr>
      <?php if( !empty( $message_array) ): ?>
      <?php foreach( $message_array as $value ): ?>
      <th scope="row"></th>
      <td><?php echo date('Y/m/d' , strtotime($value['deadline'])); ?></td>
      <td><?php echo $value['category']; ?></td>
      <td><?php echo $value['todo']; ?></td>
      <td>
      <form method="post" id="button_a">
      <button type="submit" class="btn btn-outline-primary btn-sm" name="btn_complete" value="<?php echo $value['id']; ?>">
      <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check2" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
      <path fill-rule="evenodd" d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
      </svg>Complete</button>

      <a href="edit.php?todo_id=<?php echo $value['id']; ?>" class="btn btn-outline-success btn-sm">
      <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-pencil" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
      <path fill-rule="evenodd" d="M11.293 1.293a1 1 0 0 1 1.414 0l2 2a1 1 0 0 1 0 1.414l-9 9a1 1 0 0 1-.39.242l-3 1a1 1 0 0 1-1.266-1.265l1-3a1 1 0 0 1 .242-.391l9-9zM12 2l2 2-9 9-3 1 1-3 9-9z"/>
      <path fill-rule="evenodd" d="M12.146 6.354l-2.5-2.5.708-.708 2.5 2.5-.707.708zM3 10v.5a.5.5 0 0 0 .5.5H4v.5a.5.5 0 0 0 .5.5H5v.5a.5.5 0 0 0 .5.5H6v-1.5a.5.5 0 0 0-.5-.5H5v-.5a.5.5 0 0 0-.5-.5H3z"/>
      </svg>Edit</a>

      <button type="submit" class="btn btn-outline-danger btn-sm" name="btn_delete" value="<?php echo $value['id']; ?>">
      <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-trash" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
      <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
      <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
      </svg>Delete</button>
      </form>
      </td>
      </tr>
      <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
      </table>

      <hr>

      <!-- 完了リストに表示 -->
      <h3>Completed</h3>
    <table class="table">
    <thead class="thead-light">
    <tr>
      <th scope="col" style="width:5%">#</th>
      <th scope="col" style="width:30%">Deadline</th>
      <th scope="col" style="width:30%">Category</th>
      <th scope="col" style="width:35%">To Do</th>
    </tr>
    </thead>
    <tbody>
    <tr>
      <?php if( !empty( $complete_array) ): ?>
      <?php foreach( $complete_array as $value ): ?>
      <th scope="row"></th>
      <td><?php echo date('Y/m/d' , strtotime($value['deadline'])); ?></td>
      <td><?php echo $value['category']; ?></td>
      <td><?php echo $value['todo']; ?></td>
      </tr>
      <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
      </table>
      
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    
  </body>
</html>