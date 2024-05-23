<!DOCTYPE html>
<html lang="fa">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title> مدیریت نظرات کاربران </title>
  <link rel="stylesheet" type="text/css" href="./style/style.css" />
</head>

<body dir="rtl">
  <header class="header"></header>

  <?php
  $serverName = "localhost"; // آدرس سرور MySQL
  $username = "root"; // نام کاربری MySQL
  $password = ""; // رمز عبور MySQL
  $dbname = "profile-user"; // نام پایگاه داده

  // اتصال به پایگاه داده
  $conn = new mysqli($serverName, $username, $password, $dbname);

  // بررسی اتصال
  if ($conn->connect_error) {
    die("به دیتا بیس متصل نشد " . $conn->connect_error);
  }

  // پردازش حذف
  if (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $sqlDelete = "DELETE FROM comments WHERE id = ?";
    $stmt = $conn->prepare($sqlDelete);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
  }

  // دریافت داده‌های جستجو
  $name = isset($_GET['name']) ? $_GET['name'] : '';
  $email = isset($_GET['email']) ? $_GET['email'] : '';

  // ساختار اصلی جستجو
  $sqlRead = "SELECT * FROM comments WHERE 1=1";
  if (!empty($name)) {
    $sqlRead .= " AND name LIKE '%" . $conn->real_escape_string($name) . "%'";
  }
  if (!empty($email)) {
    $sqlRead .= " AND email LIKE '%" . $conn->real_escape_string($email) . "%'";
  }

  $result = $conn->query($sqlRead);

  echo "<div class='form-search-data'>
  <form action='' method='GET'>
    <div>
      <label for='name'>نام کاربر</label>
      <input type='text' id='name' name='name' placeholder='نام کاربر را وارد نمایید' value='" . htmlspecialchars($name, ENT_QUOTES) . "'>
    </div>
    <div>
      <label for='email'>ایمیل کاربر</label>
      <input type='text' name='email' id='email' placeholder='ایمیل کاربر را وارد نمایید' value='" . htmlspecialchars($email, ENT_QUOTES) . "'>
    </div>
    <div>
      <button type='submit' class='btn-search'>جستجو</button>
    </div>
  </form>
  </div>";

  if ($result->num_rows > 0) {
    echo "<table class='table rwd-table table-hover table-striped table-responsive'>
    <thead>
    <tr>
          <th>ردیف</th>
          <th>نام</th>
          <th>ایمیل </th>
          <th>نظر</th>       
          <th>توضیحات</th>
          <th>حذف</th>
    </tr>
    </thead>
    <tbody>";

    $row_num = 1;
    while ($row = $result->fetch_assoc()) {
      if (!empty($row["name"]) && !empty($row["email"]) && !empty($row["subject"]) && !empty($row["message"])) {
        echo "<tr>";
        echo "<td class='text-capitalize'>$row_num</td>";
        echo "<td class='text-capitalize' data-th='نام'>" . htmlspecialchars($row["name"], ENT_QUOTES) . "</td>";
        echo "<td class='text-capitalize' data-th='ایمیل'>" . htmlspecialchars($row["email"], ENT_QUOTES) . "</td>";
        echo "<td class='text-capitalize' data-th='نظر'>" . htmlspecialchars($row["subject"], ENT_QUOTES) . "</td>";
        echo "<td class='text-capitalize' data-th='توضیحات'>" . htmlspecialchars($row["message"], ENT_QUOTES) . "</td>";
        echo "<td class='text-capitalize'>
                <form method='POST' action='' onsubmit='return confirm(\"آیا مطمئن هستید؟\");'>
                  <input type='hidden' name='delete_id' value='" . $row["id"] . "'>
                  <button type='submit' class='btn-delete'>حذف</button>
                </form>
              </td>";
        echo "</tr>";

        $row_num++;
      }
    }

    echo "</tbody></table>";
  } else {
    echo "هیچ ردیفی یافت نشد.";
  }

  $conn->close();
  ?>

</body>

</html>